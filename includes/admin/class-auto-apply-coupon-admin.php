<?php

// Exit if accessed directly
if ( !defined( 'ABSPATH' ) ) exit;

/**
 * @package Auto apply coupon admin class
 * @since 1.0.0
 */
if( !class_exists( 'Auto_Apply_Coupon_Admin' ) ) {

	class Auto_Apply_Coupon_Admin {

		function __construct() {
			add_action( 'add_meta_boxes', array($this,'add_auto_apply_coupon_meta_boxes'));		
			add_action('admin_enqueue_scripts', array($this,'auto_apply_coupon_admin_enqueue'));
		}
		public function add_auto_apply_coupon_meta_boxes()
		{
			add_meta_box( 'coupon_code_fields', __('Coupon Code Cart Link','auto-apply-coupon'), array($this,'add_auto_apply_coupon_code_fields'), 'shop_coupon', 'side', 'default' );
		}

		public function add_auto_apply_coupon_code_fields()
		{
			global $post;
			$post = get_post($post->ID);
			global $woocommerce;
			
			$title = $post->post_title;
			$url = wc_get_cart_url()."?coupon_code=".esc_html($title);
			?>
			<p style="border-bottom:solid 1px #eee;padding-bottom:13px;">
			<input type="text" style="width:250px;" id="copuon-code-link-<?php echo esc_attr($post->ID) ?>" name="coupon_code_link_field_name" value="<?php echo esc_url($url); ?>" <?php echo esc_html($title) ? "readonly" : "" ?>  >
			</p>
			<span class="button button-primary" id="coupon_code_copy_url"  data-id="<?php echo esc_attr($post->ID) ?>"><?php esc_html_e( 'Copy URL', 'auto-apply-coupon' );?></span>
			<?php 
	
		}

		public function auto_apply_coupon_admin_enqueue()
		{
			wp_register_script('auto-apply-coupon-admin-js',AUTO_APPLY_COUPON_INCLUDE_URL.'/js/auto-apply-coupon-admin.js',array('jquery'),AUTO_APPLY_COUPON_VERSION);
  			$localize = array(
				'ajaxurl' => admin_url('admin-ajax.php'),
			);
			wp_localize_script('auto-apply-coupon-admin-js', 'admincouponObj', $localize);
			wp_enqueue_script('auto-apply-coupon-admin-js');
		}
	} 
}