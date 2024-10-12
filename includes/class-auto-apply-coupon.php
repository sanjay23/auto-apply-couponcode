<?php
// Exit if accessed directly
if ( !defined( 'ABSPATH' ) ) exit;

if( !class_exists( 'Auto_Apply_Coupon' ) ) {
	class Auto_Apply_Coupon {

		function __construct() {
			add_action('wp_footer',array($this,'auto_apply_coupon_list'));
			add_action('wp_ajax_apply_coupon', array($this,'auto_apply_coupon_ajax'));
			add_action('wp_enqueue_scripts', array($this,'auto_apply_coupon_script'));
			add_action('wp_ajax_get_discount', array($this,'auto_apply_coupon_discount'));
			add_action('wp_ajax_nopriv_get_discount', array($this,'auto_apply_coupon_discount'));
		}

		public function auto_apply_coupon_script()
		{   
			wp_register_script('auto-apply-coupon-code-js',AUTO_APPLY_COUPON_INCLUDE_URL.'/js/auto-apply-coupon-code.js',array('jquery'),AUTO_APPLY_COUPON_VERSION);
  			$localize = array(
				'ajaxurl' => admin_url('admin-ajax.php'),
				'nonce' => wp_create_nonce('ajax-nonce')
			);
			wp_localize_script('auto-apply-coupon-code-js', 'couponObj', $localize);
			wp_enqueue_script('auto-apply-coupon-code-js');
			wp_enqueue_style('auto-apply-coupon-code-css', AUTO_APPLY_COUPON_INCLUDE_URL.'/css/auto-apply-coupon-code.css',array(),AUTO_APPLY_COUPON_VERSION);
		}

		public function auto_apply_coupon_list()
		{
			?>
			<div id="coupon-popup" class="coupon-popup overlay">
				<div class="main-coupon-wrap">
					<div class="popup">
						
						<div class="coupon_popup_close_wrap"><a class="coupon_popup_close close" href="javascript:void(0);">&#x2715;</a></div>
						<div class="coupon-body-wrap">
							<p class="other_coupon_code_wrap"><?php esc_html_e( 'Choose which discount you want to choose. (Saves you more.)', 'auto-apply-coupon' );?></p>
							<div class="coupon-body-inner-wrap"></div>
							<div class="other-apply-coupon">
								<?php 
								$symbol = get_woocommerce_currency_symbol();
								$code = '';
								$amount = '';
								foreach ( WC()->cart->get_coupons() as $code => $coupon ) {
									
									$code  = $coupon->code;
									$discount = $coupon->amount;
									if($coupon->discount_type == 'percent' && !empty($coupon->amount)){
										$subtotal =  WC()->cart->subtotal;
										$discount = ($subtotal * $coupon->amount) / 100;
									}
									$amount = $symbol.$discount." Off";
								}
								?>
							</div>
							<div class="coupon-body-inner-wrap">
								<button name="change_coupon" value="" class="exist_update_coupon_code common_coupon_code" type="button"><?php esc_html_e( 'Apply', 'auto-apply-coupon' );?> <span class="exist_coupon_code"><?php echo esc_attr($code);?></span><span> (<?php echo esc_attr($amount);?>)</span></button>
								<button name="change_coupon" value="" class="update_coupon_code common_coupon_code" type="button"><?php esc_html_e( 'Apply', 'auto-apply-coupon' );?> <span class="new_coupon_code"></span><span class="new_code_dis"></span></button>
							</div>
						</div>
					</div>
				</div>
			</div>
			<?php
		}

		/**
		 * Auto apply coupon code
		 */
		public function auto_apply_coupon_ajax()
		{
			if ( isset($_POST['nonce']) && ! wp_verify_nonce( sanitize_text_field(wp_unslash($_POST['nonce'])), 'ajax-nonce' ) ) {
				return;
			}
			if (isset($_POST['coupon']) && !empty($_POST['coupon'])) {
		
				$coupon_code = sanitize_text_field($_POST['coupon']);
				$removeCoupon = sanitize_text_field($_POST['removeCoupon']);
				if(!empty($removeCoupon)){
					if(wc_get_coupon_id_by_code($coupon_code) || !empty($amount)){
						foreach ( WC()->cart->get_coupons() as $code => $coupon ) {
							WC()->cart->remove_coupon( $coupon->code );
						}
					}
				}
				
				wc_clear_notices();
				wp_send_json_success(true);
				
			}
			wp_die();
		}

		/**
		 * Apply new coupon code
		 */
		public function auto_apply_coupon_discount()
		{
			if ( isset($_POST['nonce']) && ! wp_verify_nonce( sanitize_text_field(wp_unslash($_POST['nonce'])), 'ajax-nonce' ) ) {
				return;
			}
			if (isset($_POST['coupon']) && !empty($_POST['coupon'])) {
				$coupon_code = sanitize_text_field($_POST['coupon']);
				$args = array(
					'posts_per_page' => -1,
					'post_type'      => 'shop_coupon',
					'post_status'    => 'publish',
				);
			
				$coupons = get_posts($args);
				$symbol = get_woocommerce_currency_symbol();
				$amount = '';
				$subtotal = WC()->cart->subtotal;
				if ($coupons) {
					foreach ($coupons as $coupon) {
						if(strtolower($coupon->post_title) == strtolower($coupon_code)){
							$amount = get_post_meta($coupon->ID, 'coupon_amount', true);
							$discount_type = get_post_meta($coupon->ID, 'discount_type', true);
							if($discount_type == 'percent' && !empty($amount)){
								$amount = ($subtotal * $amount) / 100;
							}
							$amount = '$'.$amount." Off";
							break;
						}
					}			
				}
				if(!empty($amount)){
					wp_send_json_success($amount);
				} else{
					wp_send_json_error(__('Invalid coupon code.', 'auto-apply-coupon'));
				}
			}
		}
	}
}