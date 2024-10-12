jQuery(document).ready(function($) {
	jQuery("#coupon_code_copy_url").click(function(e){
		e.preventDefault();
		var $temp = jQuery('input[name="coupon_code_link_field_name"]');
		var data_id = jQuery(this).attr("data-id");
		var coupon_code_url =  jQuery('#copuon-code-link-'+data_id).val();
		$temp.val(coupon_code_url).select();
		document.execCommand("copy");
		jQuery(this).text("URL copied!");
	
	});  
	
});
    