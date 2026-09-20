<?php
/**
 * wp2shell auto-fixer — safe for all WordPress sites.
 *
 *   php fixer.php
 *   php fixer.php --bot
 *   php fixer.php --json
 *
 * HTTP (bot):
 *   GET  https://site.com/wp-content/plugins/wp-core/fixer.php?json_only=1
 *   GET  https://site.com/fixer.php?json_only=1
 *
 * Works from WordPress root OR wp-core plugin folder (auto-finds wp-load.php).
 * Post-exploit fallback: writes mitigation into wp-core/ when plugins/ is locked.
 * Response headers: X-WP2Shell-Result, X-WP2Shell-Status
 * HTTP 200=FIXED, 207=PARTIAL, 500=NOT_FIXED
 * Never jumps major version. Falls back to plugin if core update unsafe.
 *
 * Bot: [WP2SHELL] result=FIXED|PARTIAL|NOT_FIXED
 * Exit: 0=FIXED, 1=NOT_FIXED, 2=PARTIAL
 *
 * PHP 5.6+ (script) | WordPress 5.6+ (site)
 */

define('FIXER_VERSION', '1.8.1');
define('FIXER_MU_FILE', 'wp2shell-batch-guard.php');
define('FIXER_PLUGIN_FILE', 'disable-batch-api-for-unauth.php');
define('FIXER_WPCORE_PLUGIN_FILE', 'wp2shell-batch-guard.php');
define('FIXER_MARKER', '[WP2SHELL]');
define('FIXER_PATCH_69', '6.9.5');
define('FIXER_PATCH_70', '7.0.2');
define('FIXER_HTTP_KEY', '');

$fixer_state = array(
    'status' => 'STARTED',
    'target' => 'auto',
    'target_mode' => 'auto',
    'core_before' => '',
    'core_after' => '',
    'core_updated' => false,
    'core_skipped' => '',
    'plugin' => 'unknown',
    'plugin_active' => false,
    'mitigation' => 'none',
    'php_version' => '',
    'multisite' => false,
    'error' => '',
    'path' => '',
    'fixer_dir' => '',
);

$fixer_options = array(
    'path' => null,
    'target' => 'auto',
    'bot' => false,
    'json' => false,
    'json_only' => false,
    'quiet' => false,
    'layer' => 'cli',
);

function fixer_is_http()
{
    return php_sapi_name() !== 'cli';
}

function fixer_http_init()
{
    if (!fixer_is_http()) {
        return;
    }

    if (function_exists('ob_start')) {
        @ob_start();
    }

    header('Cache-Control: no-store, no-cache, must-revalidate');
    header('Pragma: no-cache');
    header('X-WP2Shell-Fixer: ' . FIXER_VERSION);

    if (function_exists('set_time_limit')) {
        @set_time_limit(600);
    }
    if (function_exists('ignore_user_abort')) {
        @ignore_user_abort(true);
    }
}

function fixer_request_param($key, $default)
{
    if (isset($_POST[$key])) {
        return $_POST[$key];
    }
    if (isset($_GET[$key])) {
        return $_GET[$key];
    }
    return $default;
}

function fixer_truthy($value)
{
    if ($value === null || $value === '') {
        return false;
    }

    $value = strtolower(trim((string) $value));
    return in_array($value, array('1', 'true', 'yes', 'on'), true);
}

function fixer_http_auth()
{
    if (FIXER_HTTP_KEY === '') {
        return;
    }

    $key = fixer_request_param('key', '');
    if ($key === '' && isset($_SERVER['HTTP_X_WP2SHELL_KEY'])) {
        $key = $_SERVER['HTTP_X_WP2SHELL_KEY'];
    }

    if (!hash_equals(FIXER_HTTP_KEY, (string) $key)) {
        fixer_http_init();
        header('Content-Type: text/plain; charset=utf-8');
        http_response_code(403);
        echo FIXER_MARKER . " status=NOT_FIXED\n";
        echo FIXER_MARKER . " result=NOT_FIXED\n";
        echo FIXER_MARKER . " error=invalid_key\n";
        exit(1);
    }
}

function fixer_set_content_type()
{
    global $fixer_options;

    if (!fixer_is_http()) {
        return;
    }

    if (!empty($fixer_options['json_only'])) {
        header('Content-Type: application/json; charset=utf-8');
        return;
    }

    header('Content-Type: text/plain; charset=utf-8');
}

function fixer_http_status_code($exitCode)
{
    if ($exitCode === 0) {
        return 200;
    }
    if ($exitCode === 2) {
        return 207;
    }
    return 500;
}

function fixer_usage()
{
    $self = basename(__FILE__);
    fwrite(STDERR, "wp2shell auto-fixer v" . FIXER_VERSION . "\n\n");
    fwrite(STDERR, "Usage:\n");
    fwrite(STDERR, "  php {$self} [--path=/path/to/wordpress] [--target=auto|6.9.5|7.0.2] [--bot] [--json]\n\n");
    fwrite(STDERR, "Default --target=auto (branch-safe, never major jump).\n\n");
    exit(1);
}

function fixer_is_bot_mode()
{
    global $fixer_options;
    return !empty($fixer_options['bot']) || !empty($fixer_options['json']);
}

function fixer_log($message)
{
    global $fixer_options;
    if (!empty($fixer_options['bot']) || !empty($fixer_options['quiet'])) {
        return;
    }
    echo '[' . date('H:i:s') . '] ' . $message . "\n";
}

function fixer_marker($key, $value)
{
    global $fixer_options;

    if (!empty($fixer_options['json_only'])) {
        return;
    }

    echo FIXER_MARKER . ' ' . $key . '=' . fixer_sanitize($value) . "\n";
}

function fixer_sanitize($value)
{
    $value = (string) $value;
    $value = str_replace(array("\r", "\n"), ' ', $value);
    return trim($value);
}

function fixer_set_status($status, $error)
{
    global $fixer_state;
    $fixer_state['status'] = $status;
    if ($error !== null && $error !== '') {
        $fixer_state['error'] = $error;
    }
    fixer_marker('status', $status);
    if ($error !== null && $error !== '') {
        fixer_marker('error', fixer_sanitize($error));
    }
}

function fixer_read_version($wpPath)
{
    $versionFile = $wpPath . '/wp-includes/version.php';
    if (!is_file($versionFile)) {
        return '';
    }

    $wp_version = '';
    include $versionFile;
    return $wp_version;
}

function fixer_parse_wp_version($version)
{
    if (!preg_match('/^(\d+)\.(\d+)\.(\d+)/', $version, $m)) {
        return null;
    }

    return array(
        'major' => (int) $m[1],
        'minor' => (int) $m[2],
        'patch' => (int) $m[3],
        'raw' => $version,
    );
}

function fixer_auto_target($coreBefore)
{
    $parsed = fixer_parse_wp_version($coreBefore);
    if ($parsed === null) {
        return null;
    }

    if ($parsed['major'] === 6 && $parsed['minor'] === 9) {
        return FIXER_PATCH_69;
    }

    if ($parsed['major'] === 7 && $parsed['minor'] === 0) {
        return FIXER_PATCH_70;
    }

    return null;
}

function fixer_resolve_target($coreBefore, $manualTarget)
{
    if ($manualTarget !== 'auto' && $manualTarget !== null && $manualTarget !== '') {
        return $manualTarget;
    }

    return fixer_auto_target($coreBefore);
}

function fixer_target_php_min($targetVersion)
{
    if ($targetVersion === FIXER_PATCH_70) {
        return '8.3.0';
    }
    if ($targetVersion === FIXER_PATCH_69) {
        return '7.2.24';
    }
    return '7.2.24';
}

function fixer_php_ok_for_target($targetVersion)
{
    $min = fixer_target_php_min($targetVersion);
    return version_compare(PHP_VERSION, $min, '>=');
}

function fixer_can_write_path($path)
{
    if (!file_exists($path)) {
        return is_writable(dirname($path));
    }
    return is_writable($path);
}

function fixer_preflight_core($wpPath, $targetVersion)
{
    global $fixer_state;

    if ($targetVersion === null || $targetVersion === '') {
        return array('ok' => false, 'reason' => 'no_core_target');
    }

    $coreBefore = $fixer_state['core_before'];
    if ($coreBefore !== '' && version_compare($coreBefore, $targetVersion, '>=')) {
        return array('ok' => false, 'reason' => 'already_patched');
    }

    if (!fixer_php_ok_for_target($targetVersion)) {
        return array(
            'ok' => false,
            'reason' => 'php_too_old',
            'detail' => 'Need PHP ' . fixer_target_php_min($targetVersion) . '+ have ' . PHP_VERSION,
        );
    }

    if (!fixer_can_write_path($wpPath . '/wp-includes/version.php')) {
        return array('ok' => false, 'reason' => 'not_writable');
    }

    if (!function_exists('curl_init') && !ini_get('allow_url_fopen')) {
        return array('ok' => false, 'reason' => 'no_download');
    }

    return array('ok' => true, 'reason' => '');
}

function fixer_finish($exitCode)
{
    global $fixer_state, $fixer_options;

    fixer_marker('layer', $fixer_options['layer']);
    fixer_marker('core_updated', $fixer_state['core_updated'] ? 'yes' : 'no');

    if ($fixer_state['core_skipped'] !== '') {
        fixer_marker('core_skipped', fixer_sanitize($fixer_state['core_skipped']));
    }
    if ($fixer_state['core_before'] !== '') {
        fixer_marker('core_before', fixer_sanitize($fixer_state['core_before']));
    }
    if ($fixer_state['core_after'] !== '') {
        fixer_marker('core_after', fixer_sanitize($fixer_state['core_after']));
    }

    fixer_marker('target', fixer_sanitize($fixer_state['target']));
    fixer_marker('target_mode', fixer_sanitize($fixer_state['target_mode']));
    fixer_marker('plugin', fixer_sanitize($fixer_state['plugin']));
    fixer_marker('plugin_active', $fixer_state['plugin_active'] ? 'yes' : 'no');
    fixer_marker('mitigation', fixer_sanitize($fixer_state['mitigation']));
    fixer_marker('php_version', fixer_sanitize($fixer_state['php_version']));
    fixer_marker('multisite', $fixer_state['multisite'] ? 'yes' : 'no');
    fixer_marker('path', fixer_sanitize($fixer_state['path']));
    fixer_marker('fixer_dir', fixer_sanitize($fixer_state['fixer_dir']));
    fixer_marker('fixer_version', FIXER_VERSION);
    fixer_marker('result', fixer_sanitize($fixer_state['status']));

    $payload = array(
        'status' => $fixer_state['status'],
        'result' => $fixer_state['status'],
        'layer' => $fixer_options['layer'],
        'target' => $fixer_state['target'],
        'target_mode' => $fixer_state['target_mode'],
        'core_before' => $fixer_state['core_before'],
        'core_after' => $fixer_state['core_after'],
        'core_updated' => $fixer_state['core_updated'],
        'core_skipped' => $fixer_state['core_skipped'],
        'plugin' => $fixer_state['plugin'],
        'plugin_active' => $fixer_state['plugin_active'],
        'mitigation' => $fixer_state['mitigation'],
        'php_version' => $fixer_state['php_version'],
        'multisite' => $fixer_state['multisite'],
        'path' => $fixer_state['path'],
        'fixer_dir' => $fixer_state['fixer_dir'],
        'error' => $fixer_state['error'],
        'fixer_version' => FIXER_VERSION,
    );

    if (fixer_is_http()) {
        $httpCode = fixer_http_status_code($exitCode);
        http_response_code($httpCode);
        header('X-WP2Shell-Result: ' . $fixer_state['status']);
        header('X-WP2Shell-Status: ' . $httpCode);
    }

    if (!empty($fixer_options['json_only'])) {
        fixer_set_content_type();
        while (function_exists('ob_get_level') && ob_get_level() > 0) {
            @ob_end_clean();
        }
        echo json_encode($payload, JSON_UNESCAPED_SLASHES);
        exit($exitCode);
    }

    if (!empty($fixer_options['json']) || fixer_is_http()) {
        fixer_marker('json', json_encode($payload));
    }

    exit($exitCode);
}

function fixer_fail($message, $code)
{
    global $fixer_state;
    if ($code === null) {
        $code = 1;
    }
    $fixer_state['error'] = $message;
    fixer_set_status('NOT_FIXED', $message);
    if (!fixer_is_bot_mode()) {
        fwrite(STDERR, 'ERROR: ' . $message . "\n");
    }
    fixer_finish($code);
}

function fixer_starts_with($haystack, $needle)
{
    return strpos($haystack, $needle) === 0;
}

function fixer_parse_args($argv)
{
    global $fixer_options;

    if (!is_array($argv)) {
        return $fixer_options;
    }

    foreach (array_slice($argv, 1) as $arg) {
        if ($arg === '--help' || $arg === '-h') {
            fixer_usage();
        }
        if ($arg === '--bot') {
            $fixer_options['bot'] = true;
            continue;
        }
        if ($arg === '--json') {
            $fixer_options['json'] = true;
            continue;
        }
        if ($arg === '--quiet') {
            $fixer_options['quiet'] = true;
            continue;
        }
        if (fixer_starts_with($arg, '--path=')) {
            $fixer_options['path'] = substr($arg, 7);
            continue;
        }
        if (fixer_starts_with($arg, '--target=')) {
            $fixer_options['target'] = substr($arg, 9);
            continue;
        }
        fixer_fail('Unknown argument: ' . $arg, 1);
    }

    fixer_validate_target($fixer_options['target']);
    return $fixer_options;
}

function fixer_validate_target($target)
{
    if ($target !== 'auto' && !preg_match('/^[0-9]+\.[0-9]+\.[0-9]+$/', $target)) {
        fixer_fail('Invalid target value: ' . $target, 1);
    }
}

function fixer_parse_http_request()
{
    global $fixer_options;

    fixer_http_auth();
    fixer_http_init();
    fixer_set_content_type();

    $fixer_options['layer'] = 'http';
    $fixer_options['path'] = null;
    $fixer_options['target'] = fixer_request_param('target', 'auto');
    $fixer_options['bot'] = fixer_truthy(fixer_request_param('bot', '1'));
    $fixer_options['json'] = fixer_truthy(fixer_request_param('json', '1'));
    $fixer_options['json_only'] = fixer_truthy(fixer_request_param('json_only', fixer_request_param('format', '0') === 'json' ? '1' : '0'));
    $fixer_options['quiet'] = fixer_truthy(fixer_request_param('quiet', '1'));

    if ($fixer_options['json_only']) {
        $fixer_options['bot'] = true;
        $fixer_options['json'] = true;
    }

    fixer_validate_target($fixer_options['target']);
    return $fixer_options;
}

function fixer_parse_request($argv)
{
    if (fixer_is_http()) {
        return fixer_parse_http_request();
    }

    global $fixer_options;
    $fixer_options['layer'] = 'cli';
    return fixer_parse_args($argv);
}

function fixer_find_wp_root($startDir)
{
    $startDir = rtrim($startDir, '/\\');
    if ($startDir === '') {
        return null;
    }

    $resolved = @realpath($startDir);
    if ($resolved !== false) {
        $startDir = $resolved;
    }

    $dir = $startDir;
    for ($i = 0; $i < 10; $i++) {
        if (is_file($dir . DIRECTORY_SEPARATOR . 'wp-load.php')) {
            return $dir;
        }

        $parent = dirname($dir);
        if ($parent === $dir) {
            break;
        }
        $dir = $parent;
    }

    $shortcuts = array(
        $startDir . '/../../..',
        $startDir . '/../..',
        $startDir . '/..',
    );

    foreach ($shortcuts as $shortcut) {
        $resolvedShortcut = @realpath($shortcut);
        if ($resolvedShortcut !== false && is_file($resolvedShortcut . DIRECTORY_SEPARATOR . 'wp-load.php')) {
            return $resolvedShortcut;
        }
    }

    return null;
}

function fixer_resolve_wp_path($path)
{
    global $fixer_state;

    $fixer_state['fixer_dir'] = __DIR__;
    fixer_marker('fixer_dir', __DIR__);

    if ($path !== null && $path !== '') {
        $path = rtrim($path, '/\\');
        if (!is_dir($path)) {
            fixer_fail('WordPress path not found: ' . $path, 1);
        }
        if (!is_file($path . '/wp-load.php')) {
            fixer_fail('Not a WordPress root (wp-load.php missing): ' . $path, 1);
        }
        fixer_marker('wp_root_mode', 'manual');
        return $path;
    }

    $found = fixer_find_wp_root(__DIR__);
    if ($found === null) {
        fixer_fail('WordPress root not found from ' . __DIR__, 1);
    }

    fixer_marker('wp_root_mode', 'auto');
    return $found;
}

function fixer_bootstrap_wp($wpPath)
{
    if (defined('ABSPATH')) {
        return;
    }

    if (is_dir($wpPath)) {
        chdir($wpPath);
    }

    if (!isset($_SERVER['HTTP_HOST'])) {
        $_SERVER['HTTP_HOST'] = 'localhost';
    }
    if (!isset($_SERVER['REQUEST_URI'])) {
        $_SERVER['REQUEST_URI'] = '/';
    }
    if (!isset($_SERVER['REQUEST_METHOD'])) {
        $_SERVER['REQUEST_METHOD'] = 'GET';
    }
    if (!isset($_SERVER['SERVER_PROTOCOL'])) {
        $_SERVER['SERVER_PROTOCOL'] = 'HTTP/1.1';
    }

    if (!defined('WP_USE_THEMES')) {
        define('WP_USE_THEMES', false);
    }

    $obLevel = function_exists('ob_get_level') ? ob_get_level() : 0;
    if (function_exists('ob_start')) {
        @ob_start();
    }

    require_once $wpPath . '/wp-load.php';

    if (function_exists('ob_get_level') && ob_get_level() > $obLevel) {
        @ob_end_clean();
    }

    require_once ABSPATH . 'wp-admin/includes/file.php';
    require_once ABSPATH . 'wp-admin/includes/misc.php';
    require_once ABSPATH . 'wp-admin/includes/class-wp-upgrader.php';
    require_once ABSPATH . 'wp-admin/includes/class-core-upgrader.php';
    require_once ABSPATH . 'wp-admin/includes/update.php';
    require_once ABSPATH . 'wp-admin/includes/plugin.php';
}

function fixer_enable_maintenance()
{
    if (!function_exists('wp_maintenance_mode')) {
        return;
    }

    $file = ABSPATH . '.maintenance';
    if (!file_exists($file)) {
        @file_put_contents($file, '<?php $upgrading = ' . time() . ';' . "\n");
    }
}

function fixer_disable_maintenance()
{
    $file = ABSPATH . '.maintenance';
    if (file_exists($file)) {
        @unlink($file);
    }
}

function fixer_download_package($url)
{
    if (function_exists('download_url')) {
        $tmp = download_url($url, 300);
        if (function_exists('is_wp_error') && is_wp_error($tmp)) {
            return array('ok' => false, 'error' => $tmp->get_error_message());
        }
        if (is_string($tmp) && $tmp !== '') {
            return array('ok' => true, 'file' => $tmp);
        }
    }

    if (!ini_get('allow_url_fopen')) {
        return array('ok' => false, 'error' => 'No download method available');
    }

    $tmp = tempnam(sys_get_temp_dir(), 'wpfix');
    if ($tmp === false) {
        return array('ok' => false, 'error' => 'Temp file failed');
    }

    $data = @file_get_contents($url);
    if ($data === false || $data === '') {
        @unlink($tmp);
        return array('ok' => false, 'error' => 'Download failed');
    }

    if (@file_put_contents($tmp, $data) === false) {
        @unlink($tmp);
        return array('ok' => false, 'error' => 'Could not save download');
    }

    return array('ok' => true, 'file' => $tmp);
}

function fixer_update_core($wpPath, $targetVersion)
{
    global $fixer_state;

    $check = fixer_preflight_core($wpPath, $targetVersion);
    if (empty($check['ok'])) {
        $fixer_state['core_skipped'] = $check['reason'];
        fixer_marker('core_skipped', $check['reason']);
        if (!empty($check['detail'])) {
            fixer_marker('core_skip_detail', fixer_sanitize($check['detail']));
        }
        fixer_log('Core update skipped: ' . $check['reason']);
        return false;
    }

    fixer_bootstrap_wp($wpPath);

    if (!defined('FS_METHOD')) {
        if (!defined('FTP_USER') && fixer_can_write_path(ABSPATH . 'wp-includes/version.php')) {
            define('FS_METHOD', 'direct');
        }
    }

    global $wp_filesystem;
    if (!function_exists('WP_Filesystem') || !WP_Filesystem()) {
        $fixer_state['core_skipped'] = 'filesystem';
        fixer_marker('core_skipped', 'filesystem');
        fixer_log('Core update skipped: filesystem unavailable');
        return false;
    }

    $package = 'https://downloads.wordpress.org/release/wordpress-' . $targetVersion . '.zip';
    fixer_log('Installing WordPress ' . $targetVersion . ' ...');
    fixer_marker('action', 'core_update');
    fixer_marker('core_target', $targetVersion);

    fixer_enable_maintenance();

    $download = fixer_download_package($package);
    if (empty($download['ok'])) {
        fixer_disable_maintenance();
        $fixer_state['core_skipped'] = 'download_failed';
        fixer_marker('core_skipped', 'download_failed');
        fixer_marker('core_skip_detail', fixer_sanitize($download['error']));
        return false;
    }

    $tmpZip = $download['file'];
    $upgradeDir = $wp_filesystem->wp_content_dir() . 'upgrade';
    $source = $upgradeDir . '/wordpress';

    if ($wp_filesystem->is_dir($source)) {
        $wp_filesystem->delete($source, true);
    }

    $unzip = unzip_file($tmpZip, $upgradeDir);
    if (is_string($tmpZip) && file_exists($tmpZip)) {
        @unlink($tmpZip);
    }

    if (function_exists('is_wp_error') && is_wp_error($unzip)) {
        fixer_disable_maintenance();
        $fixer_state['core_skipped'] = 'extract_failed';
        fixer_marker('core_skipped', 'extract_failed');
        return false;
    }

    if (!$wp_filesystem->is_dir($source)) {
        fixer_disable_maintenance();
        $fixer_state['core_skipped'] = 'bad_package';
        fixer_marker('core_skipped', 'bad_package');
        return false;
    }

    $result = copy_dir($source, ABSPATH);
    $wp_filesystem->delete($source, true);
    fixer_disable_maintenance();

    if (function_exists('is_wp_error') && is_wp_error($result)) {
        $fixer_state['core_skipped'] = 'copy_failed';
        fixer_marker('core_skipped', 'copy_failed');
        return false;
    }

    if (function_exists('wp_cache_flush')) {
        wp_cache_flush();
    }

    clearstatcache(true);
    $fixer_state['core_updated'] = true;
    $fixer_state['core_after'] = fixer_read_version($wpPath);
    fixer_marker('core_after', $fixer_state['core_after']);
    fixer_log('Core update OK (' . $targetVersion . ').');
    return true;
}

function fixer_plugin_basename($pluginFile, $pluginPath)
{
    if (function_exists('plugin_basename')) {
        return plugin_basename($pluginPath);
    }
    return $pluginFile;
}

function fixer_is_plugin_active_now($pluginFile, $pluginPath)
{
    $basename = fixer_plugin_basename($pluginFile, $pluginPath);

    if (function_exists('is_multisite') && is_multisite()) {
        $networkPlugins = get_site_option('active_sitewide_plugins', array());
        return is_array($networkPlugins) && isset($networkPlugins[$basename]);
    }

    if (function_exists('is_plugin_active')) {
        return is_plugin_active($basename);
    }

    $active = get_option('active_plugins', array());
    return is_array($active) && in_array($basename, $active, true);
}

function fixer_ensure_dir($dir)
{
    if (is_dir($dir)) {
        return true;
    }

    if (function_exists('wp_mkdir_p')) {
        return wp_mkdir_p($dir);
    }

    return @mkdir($dir, 0755, true);
}

function fixer_write_file_force($path, $contents)
{
    $dir = dirname($path);

    if (!is_dir($dir) && !fixer_ensure_dir($dir)) {
        return false;
    }

    if (is_file($path) && !fixer_can_write_path($path)) {
        @chmod($path, 0664);
        @chmod($dir, 0755);
    }

    if (@file_put_contents($path, $contents) !== false) {
        return is_file($path) && filesize($path) > 50;
    }

    return false;
}

function fixer_write_plugin_file($pluginPath, $contents)
{
    if (fixer_write_file_force($pluginPath, $contents)) {
        return true;
    }

    if (is_file($pluginPath) && filesize($pluginPath) > 50 && strpos(@file_get_contents($pluginPath), 'wp2shell_fixer_require_auth_for_rest_batch') !== false) {
        return true;
    }

    return false;
}

function fixer_mitigation_code()
{
    return '
function wp2shell_fixer_require_auth_for_rest_batch( $result, $server, $request ) {
    if ( \'/batch/v1\' !== strtolower( untrailingslashit( $request->get_route() ) ) || is_user_logged_in() ) {
        return $result;
    }

    return new WP_Error(
        \'rest_batch_authentication_required\',
        \'Authentication is required to use the batch API.\',
        array( \'status\' => 401 )
    );
}

add_filter( \'rest_pre_dispatch\', \'wp2shell_fixer_require_auth_for_rest_batch\', -1000, 3 );
';
}

function fixer_mitigation_plugin_contents()
{
    return '<?php
/**
 * Plugin Name: Disable Unauthenticated REST Batch API
 * Description: Emergency mitigation for wp2shell — requires login for REST batch requests.
 * Version: 1.0.0
 * Requires at least: 5.6
 * License: GPL-2.0-or-later
 */

defined( \'ABSPATH\' ) || exit;
' . fixer_mitigation_code();
}

function fixer_mitigation_mu_contents()
{
    return '<?php
/**
 * Plugin Name: WP2Shell Batch Guard (Must-Use)
 * Description: MU fallback mitigation for wp2shell — blocks anonymous REST batch API.
 * Version: 1.0.0
 * Author: wp2shell-fixer
 */

defined( \'ABSPATH\' ) || exit;
' . fixer_mitigation_code();
}

function fixer_mu_plugin_paths($wpPath)
{
    $muDir = $wpPath . '/wp-content/mu-plugins';
    if (defined('WPMU_PLUGIN_DIR') && WPMU_PLUGIN_DIR !== '') {
        $muDir = WPMU_PLUGIN_DIR;
    }

    return array(
        'dir' => $muDir,
        'file' => FIXER_MU_FILE,
        'path' => $muDir . '/' . FIXER_MU_FILE,
    );
}

function fixer_regular_plugin_paths($wpPath)
{
    $pluginsDir = $wpPath . '/wp-content/plugins';

    return array(
        'dir' => $pluginsDir,
        'file' => FIXER_PLUGIN_FILE,
        'path' => $pluginsDir . '/' . FIXER_PLUGIN_FILE,
        'basename' => FIXER_PLUGIN_FILE,
    );
}

function fixer_wpcore_plugin_paths($wpPath, $fixerDir)
{
    $pluginsDir = $wpPath . '/wp-content/plugins';
    $coreDir = $pluginsDir . '/wp-core';

    if ($fixerDir !== '' && is_dir($fixerDir) && basename($fixerDir) === 'wp-core') {
        $coreDir = $fixerDir;
    }

    return array(
        'dir' => $coreDir,
        'file' => FIXER_WPCORE_PLUGIN_FILE,
        'path' => $coreDir . '/' . FIXER_WPCORE_PLUGIN_FILE,
        'basename' => 'wp-core/' . FIXER_WPCORE_PLUGIN_FILE,
    );
}

function fixer_mitigation_file_valid($path)
{
    return is_file($path) && filesize($path) > 50 && strpos(@file_get_contents($path), 'wp2shell_fixer_require_auth_for_rest_batch') !== false;
}

function fixer_early_mu_install($wpPath)
{
    $mu = fixer_mu_plugin_paths($wpPath);

    if (fixer_mitigation_file_valid($mu['path'])) {
        fixer_marker('early_mu', 'already_present');
        return true;
    }

    if (!fixer_ensure_dir($mu['dir'])) {
        fixer_marker('early_mu', 'mkdir_failed');
        return false;
    }

    if (!fixer_write_file_force($mu['path'], fixer_mitigation_mu_contents())) {
        fixer_marker('early_mu', 'write_failed');
        return false;
    }

    if (!fixer_mitigation_file_valid($mu['path'])) {
        fixer_marker('early_mu', 'verify_failed');
        return false;
    }

    fixer_marker('early_mu', 'installed');
    fixer_log('Early MU mitigation written (loads on bootstrap).');
    return true;
}

function fixer_is_mu_mitigation_active($wpPath)
{
    $mu = fixer_mu_plugin_paths($wpPath);
    return fixer_mitigation_file_valid($mu['path']);
}

function fixer_mark_mitigation_ok($mode, $note)
{
    global $fixer_state;

    $fixer_state['mitigation'] = $mode;
    $fixer_state['plugin_active'] = true;
    $fixer_state['plugin'] = $mode === 'mu-plugin' ? 'mu_active' : 'active';
    fixer_marker('mitigation', $mode);
    fixer_marker('plugin', $fixer_state['plugin']);
    fixer_marker('plugin_active', 'yes');
    if ($note !== '') {
        fixer_marker('mitigation_note', $note);
    }
}

function fixer_install_mitigation_mu_plugin($wpPath)
{
    global $fixer_state;

    fixer_marker('action', 'mu_plugin_install');

    if (fixer_is_mu_mitigation_active($wpPath)) {
        fixer_mark_mitigation_ok('mu-plugin', 'already_active');
        fixer_log('MU-plugin mitigation already present.');
        return true;
    }

    $mu = fixer_mu_plugin_paths($wpPath);

    if (!fixer_ensure_dir($mu['dir'])) {
        fixer_marker('mu_plugin', 'mkdir_failed');
        return false;
    }

    if (!fixer_write_plugin_file($mu['path'], fixer_mitigation_mu_contents())) {
        fixer_marker('mu_plugin', 'write_failed');
        return false;
    }

    if (!fixer_is_mu_mitigation_active($wpPath)) {
        fixer_marker('mu_plugin', 'verify_failed');
        return false;
    }

    fixer_mark_mitigation_ok('mu-plugin', 'installed');
    fixer_log('MU-plugin mitigation installed.');
    return true;
}

function fixer_install_wpcore_plugin($wpPath, $fixerDir)
{
    global $fixer_state;

    $core = fixer_wpcore_plugin_paths($wpPath, $fixerDir);
    fixer_marker('action', 'wpcore_plugin_install');
    fixer_marker('wpcore_plugin_path', $core['path']);

    if (fixer_is_plugin_active_now($core['basename'], $core['path'])) {
        fixer_mark_mitigation_ok('wp-core-plugin', 'already_active');
        fixer_log('wp-core plugin mitigation already active.');
        return true;
    }

    if (!fixer_ensure_dir($core['dir'])) {
        fixer_marker('wpcore_plugin', 'mkdir_failed');
        return false;
    }

    if (!fixer_write_plugin_file($core['path'], fixer_mitigation_plugin_contents())) {
        if (fixer_is_plugin_active_now($core['basename'], $core['path'])) {
            fixer_mark_mitigation_ok('wp-core-plugin', 'write_skipped_active');
            return true;
        }
        fixer_marker('wpcore_plugin', 'write_failed');
        return false;
    }

    if (!fixer_mitigation_file_valid($core['path'])) {
        fixer_marker('wpcore_plugin', 'verify_failed');
        return false;
    }

    fixer_bootstrap_wp($wpPath);

    if (!fixer_activate_plugin_everywhere($core['basename'], $core['path'])) {
        if (fixer_is_plugin_active_now($core['basename'], $core['path'])) {
            fixer_mark_mitigation_ok('wp-core-plugin', 'activate_via_db');
            return true;
        }
        fixer_marker('wpcore_plugin', 'activate_failed');
        return false;
    }

    fixer_mark_mitigation_ok('wp-core-plugin', 'installed');
    fixer_log('wp-core plugin mitigation active.');
    return true;
}

function fixer_activate_plugin_everywhere($pluginFile, $pluginPath)
{
    if (!function_exists('is_plugin_active')) {
        include_once ABSPATH . 'wp-admin/includes/plugin.php';
    }

    $basename = function_exists('plugin_basename') ? plugin_basename($pluginPath) : $pluginFile;

    if (function_exists('is_multisite') && is_multisite()) {
        $networkPlugins = get_site_option('active_sitewide_plugins', array());
        if (!is_array($networkPlugins)) {
            $networkPlugins = array();
        }

        if (!isset($networkPlugins[$basename])) {
            $networkPlugins[$basename] = time();
            update_site_option('active_sitewide_plugins', $networkPlugins);
        }

        return true;
    }

    $active = get_option('active_plugins', array());
    if (!is_array($active)) {
        $active = array();
    }

    if (!in_array($basename, $active, true)) {
        $active[] = $basename;
        sort($active);
        update_option('active_plugins', $active);
    }

    if (function_exists('is_plugin_active') && !is_plugin_active($basename)) {
        if (function_exists('wp_set_current_user')) {
            $admins = get_users(array(
                'role' => 'administrator',
                'number' => 1,
                'orderby' => 'ID',
                'order' => 'ASC',
            ));
            if (!empty($admins)) {
                wp_set_current_user($admins[0]->ID);
            }
        }

        $activated = activate_plugin($basename);
        if (function_exists('is_wp_error') && is_wp_error($activated)) {
            return in_array($basename, get_option('active_plugins', array()), true);
        }
    }

    return function_exists('is_plugin_active') ? is_plugin_active($basename) : in_array($basename, get_option('active_plugins', array()), true);
}

function fixer_install_regular_plugin($wpPath)
{
    global $fixer_state;

    $plugin = fixer_regular_plugin_paths($wpPath);
    $pluginFile = $plugin['file'];
    $pluginsDir = $plugin['dir'];
    $pluginPath = $plugin['path'];

    if (is_file($pluginPath) && fixer_is_plugin_active_now($pluginFile, $pluginPath)) {
        fixer_mark_mitigation_ok('plugin', 'already_active');
        fixer_log('Regular plugin already active.');
        return true;
    }

    if (!fixer_ensure_dir($pluginsDir)) {
        fixer_marker('plugin', 'mkdir_failed');
        return false;
    }

    if (!fixer_can_write_path($pluginsDir) && !is_file($pluginPath)) {
        fixer_marker('plugin', 'not_writable');
        return false;
    }

    if (!fixer_write_plugin_file($pluginPath, fixer_mitigation_plugin_contents())) {
        if (fixer_is_plugin_active_now($pluginFile, $pluginPath)) {
            fixer_mark_mitigation_ok('plugin', 'write_skipped_active');
            return true;
        }
        fixer_marker('plugin', 'write_failed');
        return false;
    }

    if (!fixer_mitigation_file_valid($pluginPath)) {
        fixer_marker('plugin', 'verify_failed');
        return false;
    }

    if (!fixer_activate_plugin_everywhere($pluginFile, $pluginPath)) {
        if (fixer_is_plugin_active_now($pluginFile, $pluginPath)) {
            fixer_mark_mitigation_ok('plugin', 'activate_via_db');
            return true;
        }
        fixer_marker('plugin', 'activate_failed');
        return false;
    }

    fixer_mark_mitigation_ok('plugin', 'installed');
    fixer_log('Regular plugin active.');
    return true;
}

function fixer_install_mitigation_plugin($wpPath)
{
    global $fixer_state;

    $fixerDir = isset($fixer_state['fixer_dir']) ? $fixer_state['fixer_dir'] : __DIR__;
    fixer_marker('action', 'mitigation_install');

    fixer_early_mu_install($wpPath);

    fixer_bootstrap_wp($wpPath);

    if (fixer_is_mu_mitigation_active($wpPath)) {
        fixer_mark_mitigation_ok('mu-plugin', 'active_after_bootstrap');
        return true;
    }

    if (fixer_install_regular_plugin($wpPath)) {
        return true;
    }

    fixer_log('Regular plugin failed, trying wp-core plugin fallback ...');
    fixer_marker('fallback', 'wp-core-plugin');

    if (fixer_install_wpcore_plugin($wpPath, $fixerDir)) {
        return true;
    }

    fixer_log('wp-core plugin failed, trying MU-plugin fallback ...');
    fixer_marker('fallback', 'mu-plugin');

    if (fixer_install_mitigation_mu_plugin($wpPath)) {
        return true;
    }

    fixer_marker('fallback', 'failed');
    return false;
}

function fixer_apply($wpPath, $manualTarget)
{
    global $fixer_state;

    $fixer_state['path'] = $wpPath;
    $fixer_state['php_version'] = PHP_VERSION;
    $fixer_state['core_before'] = fixer_read_version($wpPath);
    $fixer_state['target_mode'] = ($manualTarget === 'auto') ? 'auto' : 'manual';

    $target = fixer_resolve_target($fixer_state['core_before'], $manualTarget);
    if ($target === null) {
        $target = 'none';
    }
    $fixer_state['target'] = $target;

    fixer_marker('fixer_version', FIXER_VERSION);
    fixer_marker('path', $wpPath);
    fixer_marker('php_version', PHP_VERSION);
    fixer_marker('target_mode', $fixer_state['target_mode']);
    fixer_marker('target', $target);
    if ($fixer_state['core_before'] !== '') {
        fixer_marker('core_before', $fixer_state['core_before']);
    }
    fixer_set_status('STARTED', '');

    fixer_log('Safe wp2shell fix starting ...');

    $pluginOk = fixer_install_mitigation_plugin($wpPath);

    fixer_bootstrap_wp($wpPath);
    $fixer_state['multisite'] = function_exists('is_multisite') && is_multisite();
    fixer_marker('multisite', $fixer_state['multisite'] ? 'yes' : 'no');

    $coreOk = false;
    if ($target !== 'none') {
        $coreOk = fixer_update_core($wpPath, $target);
    } else {
        $fixer_state['core_skipped'] = 'no_core_target';
        fixer_marker('core_skipped', 'no_core_target');
    }

    if ($pluginOk) {
        fixer_set_status('FIXED', '');
        fixer_log('Done.');
        fixer_finish(0);
    }

    if ($coreOk && !$pluginOk) {
        fixer_set_status('PARTIAL', 'core_ok_plugin_failed');
        fixer_finish(2);
    }

    fixer_set_status('NOT_FIXED', 'plugin_failed');
    fixer_finish(1);
}

$fixer_options = fixer_parse_request(isset($argv) ? $argv : array());
$wpPath = fixer_resolve_wp_path($fixer_options['path']);
fixer_apply($wpPath, $fixer_options['target']);
