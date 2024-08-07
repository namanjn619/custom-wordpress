jQuery(document).ready(function(){
	//load_store_notice();	

	/* 28/03/2022 */
	if(jQuery('body').hasClass('logged-in') && wdm_user_role.user_is=="administrator"){
		jQuery('.logged-in p.woocommerce-store-notice.demo_store').css({'top': '27px'});
		if(document.cookie.indexOf("store_notice") < 0){
			jQuery(".admin-bar.logged-in header#masthead").addClass('margin-added-loggedin');
		}else{
			jQuery(".admin-bar.logged-in header#masthead").removeClass('margin-added-loggedin');
		}
	}
	else {
		jQuery('.logged-in p.woocommerce-store-notice.demo_store').css({'top': '0px'});
		if(document.cookie.indexOf("store_notice") < 0){
			jQuery("header#masthead").addClass('margin-added-nonloggedin');
		}else{
			jQuery("header#masthead").removeClass('margin-added-nonloggedin');
		}
	}
	/* 28/03/2022 */




	jQuery('.woocommerce-store-notice').on('click','.woocommerce-store-notice__dismiss-link', function() {
		//load_store_notice();

		/* 28/03/2022 */
		jQuery(".admin-bar.logged-in header#masthead").removeClass('margin-added-loggedin');
		jQuery("header#masthead").removeClass('margin-added-nonloggedin');
		/* 28/03/2022 */
		
	});
});

function load_store_notice(){
	if( jQuery('.woocommerce-store-notice')[0] && jQuery('.woocommerce-store-notice').css('display')!='none' ){
	    var store_size = jQuery('.woocommerce-store-notice').outerHeight();
	    var abs_size = jQuery('#wpadminbar').outerHeight();
	    if ( undefined != abs_size ) {
	      jQuery('.woocommerce-store-notice').css({'top' : abs_size + 'px'}); 
	    }
	    if ( undefined != store_size ){
	          var  TopVal = store_size;
		      if ( undefined != abs_size ) {
			      TopVal = TopVal + abs_size;
			  }
	          jQuery('#masthead').css({'margin-top' : TopVal + 'px'});
	          jQuery('.woocommerce-store-notice').css({'position': 'fixed'});
	          var nav_size = jQuery('#masthead').outerHeight();
	          jQuery('#page').css({'margin-top': (store_size+nav_size) + 'px'});
	    }

	} else if( jQuery('.woocommerce-store-notice')[0] && jQuery('.woocommerce-store-notice').css('display')=='none' ){ 
	    var abs_size = jQuery('#wpadminbar').outerHeight();
        var nav_size = jQuery('#masthead').outerHeight();
	    
	    if ( undefined != abs_size ) {
	    	
	      jQuery('#masthead').css({'margin-top' : abs_size + 'px'});
	      jQuery('#page').css({'margin-top': (nav_size) + 'px'});
	    } else {
	      jQuery('#masthead').css({'margin-top' : '0px'});
	      jQuery('#page').css({'margin-top': nav_size + 'px'});
	    }
	}
}
