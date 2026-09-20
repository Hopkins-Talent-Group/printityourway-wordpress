<?php

add_filter( 'wf_pklist_alter_product_name', 'wf_pklist_alter_product_name_zakeke', 10, 5 );
function wf_pklist_alter_product_name_zakeke($product_name, $template_type, $_product, $order_item, $order) {
	$zakeke_data = wc_get_order_item_meta( $order_item['zakeke_configurator_data'], 'zakeke_configurator_data' );
	if (isset($order_item['zakeke_configurator_data'])) {
		$zakeke_data = $order_item['zakeke_configurator_data'];

		$webservice = new Zakeke_Webservice();
		$info = $webservice->configurator_cart_info($zakeke_data['composition'], 1);

		$product_name .= '<br>';
		foreach ( $info['items'] as $item ) {
			if ( strpos( $item['attributeCode'], 'zakekePlatform' ) !== false ) {
				continue;
			}

			$product_name .= "<b>{$item['attributeName']}</b>: {$item['selectedOptionName']}<br>";
		}
	}

	return $product_name;
}