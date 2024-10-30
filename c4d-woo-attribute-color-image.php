<?php
/*
Plugin Name: C4D Woocommerce Attribute Color and Image
Plugin URI: http://coffee4dev.com/
Description: Add color field and image filed for Attributes
Author: Coffee4dev.com
Author URI: http://coffee4dev.com/
Text Domain: c4d-woo-aci
Version: 2.0.0
*/

define('C4DWACI_PLUGIN_URI', plugins_url('', __FILE__));
add_action( 'wp_enqueue_scripts', 'c4d_woo_aci_safely_add_stylesheet_to_frontsite');
function c4d_woo_aci_safely_add_stylesheet_to_frontsite( $page ) {
	if(!defined('C4DPLUGINMANAGER')) {
		wp_enqueue_style( 'c4d-woo-aci-color-frontsite-style', C4DWACI_PLUGIN_URI.'/assets/default.css' );
		wp_enqueue_script( 'c4d-woo-aci-frontsite-plugin-js', C4DWACI_PLUGIN_URI.'/assets/default.js', array( 'jquery' ), false, true ); 
	}
}
add_action('woocommerce_product_after_variable_attributes', 'c4d_woo_aci_variable_fields', 10, 3 );
add_action('woocommerce_save_product_variation', 'c4d_woo_aci_save_variable_fields', 10, 2 );
// add_filter('post_thumbnail_html', 'c4d_woo_aci_post_thumbnail_html', 99, 5);
add_shortcode('c4d-woo-aci-color', 'c4d_woo_aci_shortcode_color');
add_shortcode('c4d-woo-aci-image', 'c4d_woo_aci_shortcode_image');
add_filter( 'plugin_row_meta', 'c4d_woo_aci_plugin_row_meta', 10, 2 );

function c4d_woo_aci_plugin_row_meta( $links, $file ) {
    if ( strpos( $file, basename(__FILE__) ) !== false ) {
        $new_links = array(
            'visit' => '<a href="http://coffee4dev.com">Visit Plugin Site</<a>',
            'forum' => '<a href="http://coffee4dev.com/forums/">Forum</<a>',
            'premium' => '<a href="http://coffee4dev.com">Premium Support</<a>'
        );
        
        $links = array_merge( $links, $new_links );
    }
    
    return $links;
}

function c4d_woo_aci_shortcode_image($attr) {
	global $product;
	$html = '';
	$child =  $product->get_children();
	if (count($child) > 0) {
		$items = $product->get_available_variations();
		$html .= '<div data-id="'.esc_attr($product->id).'" class="c4d-woo-aci__image">';
		foreach ($items as $item) {
			$html .= '<div class="c4d-woo-aci__image_item" data-id="'.esc_attr($item['variation_id']).'"
						data-src="'.esc_attr($item['image_src']).'" 
						data-alt="'.esc_attr($item['image_alt']).'"
						data-srcset="'.esc_attr($item['image_srcset']).'"
					></div>';
		}
		$html .= '</div>';
	}
    return $html;
}
function c4d_woo_aci_shortcode_color($attr){
	global $product;
	$child =  $product->get_children();
	$html = '';
	if (count($child) > 0) {
		$html .= '<div data-id="'.esc_attr($product->id).'" class="c4d-woo-aci__color">';
		foreach ($child as $id) {
			$color = get_post_meta( $id, '_c4d_color_field', true );
			$html .= '<span data-id="'.esc_attr($id).'" 
						class="c4d-woo-aci__color_item" style="background-color: '.esc_attr($color).';" 
						data-color="'.esc_attr($color).'"
					></span>';
		}
		$html .= '</div>';
		return $html;	
	}
}
function c4d_woo_aci_variable_fields( $loop, $variation_data, $variation ) {
?>
	<tr>
		<td>
			<?php
			// Text Field
			woocommerce_wp_text_input( 
				array( 
					'id'          => '_c4d_color_field['.$loop.']', 
					'label'       => esc_html__( 'Color', 'c4d-woo-aci' ), 
					'placeholder' => '#000000',
					'value'       => get_post_meta( $variation->ID, '_c4d_color_field', true )
				)
			);
			?>
		</td>
	</tr>
<?php }

function c4d_woo_aci_save_variable_fields( $vid, $i ) {
	if (isset( $_POST['variable_sku'] ) ) :
		$variable_sku      = sanitize_text_field($_POST['variable_sku']);
		$variable_post_id  = sanitize_text_field($_POST['variable_post_id']);
		$_text_field = sanitize_text_field($_POST['_c4d_color_field']);
		if ( isset( $_text_field[$i] ) ) {
			update_post_meta( $vid, '_c4d_color_field', stripslashes( $_text_field[$i] ) );
		}
	endif;
}