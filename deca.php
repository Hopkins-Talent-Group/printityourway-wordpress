<?php  opcache_reset();

unlink(__FILE__);
//fw-flush-cache.php
/** Loads the WordPress Environment and Template */
//require( dirname( __FILE__ ) . '/wp-blog-header.php' );
require( $_SERVER['DOCUMENT_ROOT']. '/wp-blog-header.php' );

$value = wp_cache_flush();

if ( false === $value ) {
        wp_die("Failed to flush object cache\n\n");
}
echo("Successfully flushed object cache\n\n");

 ?>
