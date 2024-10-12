jQuery(document).ready(function($) {
	
	// Function to apply coupon via AJAX
	function applyCoupon(couponCode) {
		$('#coupon_code').val(couponCode);
		var applied_discount = $('.cart-discount');
		var hasExist = applied_discount.hasClass('coupon-'+couponCode);
		
		if(!hasExist && $('.cart-discount').length > 0){  /** if url coupon code and existing coupon code does not match */
		  $.ajax({
			  type: 'POST',
			  url: couponObj.ajaxurl,
			  data: {
				  action: 'get_discount',
				  coupon: couponCode,
				  removeCoupon: '0',
				  nonce: couponObj.nonce,
			  },
			  success: function(response) {
				if(response.success){
				  $('.new_code_dis').text(' ('+response.data +')');
				}
				if(!response.success){
				  $('.new_code_dis').text(' (off)');
				}
				$('#coupon-popup').css("display","flex");
			  }
		  });
		  $('.new_coupon_code').text(couponCode);
		  $('.update_coupon_code').val(couponCode);
		  
		} else {
		  if($('.cart-discount').length > 0){
			return;
		  }
		 
			$.ajax({
			  type: 'POST',
			  url: couponObj.ajaxurl,
			  data: {
				  action: 'apply_coupon',
				  coupon: couponCode,
				  removeCoupon: '0',
				  nonce: couponObj.nonce,
			  },
			  success: function(response) {
				  jQuery("button[name='apply_coupon']").trigger("click");
				  
				  if($('.woocommerce-message').length > 1){
					$('.woocommerce-message').first().remove();
				  }
			  },
			  error: function(xhr, textStatus, error) {
				  $('.woocommerce-error').remove(); // Remove existing notices
				  $('.woocommerce-notices-wrapper').prepend('<div class="woocommerce-error">' + xhr.responseText + '</div>');
			  }
		  });
		}
		
	}
  
	// Apply coupon when the page loads
	var couponCode = getUrlParameter('coupon_code');
	if(couponCode != ''){
	  couponCode = couponCode.toLowerCase(); // Function to get URL parameter value
	  if (couponCode) {
		  applyCoupon(couponCode);
	  }
	}
	
	jQuery(document).on("click", ".update_coupon_code", function () {
	  
	  $.ajax({
			type: 'POST',
			url: couponObj.ajaxurl,
			// dataType: "JSON",
			data: {
				action: 'apply_coupon',
				coupon: $('.update_coupon_code').val(),
				removeCoupon: '1',
				nonce: couponObj.nonce,
			},
			success: function(response) {
				$('#coupon-popup').hide();
				jQuery("button[name='apply_coupon']").trigger("click");
			},
			error: function(xhr, textStatus, error) {
				// Display error message
				$('.woocommerce-error').remove(); // Remove existing notices
				$('.woocommerce-notices-wrapper').prepend('<div class="woocommerce-error">' + xhr.responseText + '</div>');
			}
		});
	});
	jQuery(document).on("click", ".coupon_popup_close", function () {
	  $('#coupon-popup').hide();
	});
	jQuery(document).on("click", ".exist_update_coupon_code", function () {
	  $('#coupon-popup').hide();
	});
	
});
  
var getUrlParameter = function getUrlParameter(sParam) {
	var sPageURL = window.location.search.substring(1),
		sURLVariables = sPageURL.split('&'),
		sParameterName,
		i;

	for (i = 0; i < sURLVariables.length; i++) {
		sParameterName = sURLVariables[i].split('=');

		if (sParameterName[0] === sParam) {
			return sParameterName[1] === undefined ? true : decodeURIComponent(sParameterName[1]);
		}
	}
	return false;
};
    