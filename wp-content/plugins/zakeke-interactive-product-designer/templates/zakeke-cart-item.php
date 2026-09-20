<?php
/**
 * The Template for displaying the Zakeke designer
 *
 * This template can be overridden by copying it to yourtheme/zakeke.php.
 *
 * HOWEVER, on occasion Zakeke will need to update template files and you
 * (the theme developer) will need to copy the new files to your theme to
 * maintain compatibility. We try to do this as little as possible, but it does
 * happen. When this occurs the version of the template file will be bumped and
 * the readme will list any important changes.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

if ( ! isset( $zakeke_previews ) ) {
	return;
}

?>
<div style="width: 300px" class="zakeke-cart-previews">
	<div class="glide__track" data-glide-el="track">
		<ul class="glide__slides">
			<?php
			foreach ($zakeke_previews as $item) {
				?>
				<li class="glide__slide zakeke-cart-preview"
					data-url="<?php echo esc_attr($item->url); ?>"
					data-label="<?php echo esc_attr($item->label); ?>"
				>
					<img style="width: 130px" src="<?php echo esc_url($item->url); ?>" alt="<?php echo esc_attr($item->label); ?>" title="<?php echo esc_attr($item->label); ?>">
				</li>
				<?php
			}
			?>
		</ul>
	</div>

	<div data-glide-el="controls">
		<button class="slider__arrow prev glide__arrow" data-glide-dir="<">
			<svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24">
				<path d="M0 12l10.975 11 2.848-2.828-6.176-6.176H24v-3.992H7.646l6.176-6.176L10.975 1 0 12z"></path>
			</svg>
		</button>

		<button class="slider__arrow next glide__arrow" data-glide-dir=">">
			<svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24">
				<path d="M13.025 1l-2.847 2.828 6.176 6.176h-16.354v3.992h16.354l-6.176 6.176 2.847 2.828 10.975-11z"></path>
			</svg>
		</button>
	</div>
</div>
