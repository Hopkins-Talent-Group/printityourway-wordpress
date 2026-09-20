<?php
/**
 * The Template for displaying the Zakeke configurator
 *
 * This template can be overridden by copying it to yourtheme/zakeke-configurator.php.
 *
 * HOWEVER, on occasion Zakeke will need to update template files and you
 * (the theme developer) will need to copy the new files to your theme to
 * maintain compatibility. We try to do this as little as possible, but it does
 * happen. When this occurs the version of the template file will be bumped and
 * the readme will list any important changes.
 */

if (!defined('ABSPATH')) {
	exit; // Exit if accessed directly
}

if (!isset($final_atts)) {
	return;
}

?>
<div id="zakeke-configurator-container">
	<iframe id="zakeke-configurator-frame"
			src="<?php echo esc_url(apply_filters('zakeke_configurator_url', 'https://configurator.zakeke.com/')); ?>"
			data-hj-allow-iframe="" allow="clipboard-read; clipboard-write; fullscreen; web-share; accelerometer; magnetometer; autoplay; encrypted-media; gyroscope; picture-in-picture; camera *; xr-spatial-tracking; microphone;"></iframe>
	<script type="application/javascript">
		window.zakekeConfiguratorConfig = <?php echo json_encode($final_atts); ?>;
	</script>
</div>
