<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/* *** add menu page and add sub menu page *** */
add_action( 'admin_menu', function()  {
    add_menu_page( 'Booking Agenda', 'Booking Agenda', 'manage_options', plugin_dir_path( __FILE__ ).'/ns_admin_option_dashboard.php', '', plugin_dir_url( __FILE__ ).'img/backend-sidebar-icon.png', 60);
	add_submenu_page(untrailingslashit( dirname( __FILE__ ) ).'/ns_admin_option_dashboard.php', 'How to install premium version', 'How to install premium version', 'manage_options', 'how-to-install-premium-version', function(){  wp_redirect('http://www.nsthemes.com/how-to-install-the-premium-version/'); exit; });
});

/* *** add style *** */
add_action( 'admin_enqueue_scripts', function() {
	wp_enqueue_style('ns-option-css-page-booking', plugin_dir_url( __FILE__ ) . 'css/ns-option-css-page.css');
	wp_enqueue_style('ns-option-css-custom-page-booking', plugin_dir_url( __FILE__ ) . 'css/ns-option-css-custom-page.css');
	wp_enqueue_style('ns-option-css-backend', plugin_dir_url( __FILE__ ) . 'css/ns-backend-style.css');
	wp_register_style('jquery-ui', 'http://ajax.googleapis.com/ajax/libs/jqueryui/1.8/themes/base/jquery-ui.css');
	wp_enqueue_style( 'jquery-ui' ); 
	wp_enqueue_script( 'ns-option-js-page-booking', plugins_url( '/js/ns-option-js-page.js' , __FILE__ ), array( 'jquery' ) );
	wp_enqueue_script( 'jquery-ui-datepicker', array( 'jquery' ) );
	wp_localize_script( 'ns-option-js-page-booking', 'nsfilterbookingcat', array( 'ajax_url' => admin_url( 'admin-ajax.php' ))); 
	wp_localize_script( 'ns-option-js-page-booking', 'nsupdateprodinfo', array( 'ajax_url' => admin_url( 'admin-ajax.php' ))); 
	wp_localize_script( 'ns-option-js-page-booking', 'nsfilterbookingcatfront', array( 'ajax_url' => admin_url( 'admin-ajax.php' ))); 
	wp_localize_script( 'ns-option-js-page-booking', 'nsfiltermodaldatefront', array( 'ajax_url' => admin_url( 'admin-ajax.php' )));
	wp_localize_script( 'ns-option-js-page-booking', 'nsfilterprodtemplateform', array( 'ajax_url' => admin_url( 'admin-ajax.php' )));
	wp_localize_script( 'ns-option-js-page-booking', 'nsfilterstarthourbyprodtemplateform', array( 'ajax_url' => admin_url( 'admin-ajax.php' )));
	wp_localize_script( 'ns-option-js-page-booking', 'nsfilterendhourbystarthourtemplateform', array( 'ajax_url' => admin_url( 'admin-ajax.php' )));
});


/*FrontEnd*/
add_action( 'wp_enqueue_scripts', function() {
	wp_enqueue_style('ns-option-css-page-booking', plugin_dir_url( __FILE__ ) . 'css/ns-option-css-page.css');
	wp_enqueue_style('ns-option-css-custom-page-booking', plugin_dir_url( __FILE__ ) . 'css/ns-option-css-custom-page.css');
	wp_register_style('jquery-ui', 'http://ajax.googleapis.com/ajax/libs/jqueryui/1.8/themes/base/jquery-ui.css');
	wp_enqueue_style( 'jquery-ui' );
	wp_enqueue_script( 'ns-option-js-page-frontend-booking', plugins_url( '/js/ns-option-js-frontend-page.js' , __FILE__ ), array( 'jquery' ) );
	wp_enqueue_script( 'ns-option-js-outlook-import', plugins_url( '/js/ns-outlook-import.js' , __FILE__ ), array( 'jquery' ) );	
	wp_enqueue_script( 'ns-option-js-outlook-export', plugins_url( '/js/ns-outlook-export.js' , __FILE__ ), array( 'jquery' ) );			
	wp_enqueue_script( 'jquery-ui-datepicker', array( 'jquery' ) );
	
	wp_localize_script( 'ns-option-js-page-frontend-booking', 'nsfilterbookingcatfront', array( 'ajax_url' => admin_url( 'admin-ajax.php' ))); 
	wp_localize_script( 'ns-option-js-page-frontend-booking', 'nsmodaldaysprodfront', array( 'ajax_url' => admin_url( 'admin-ajax.php' ))); 
	wp_localize_script( 'ns-option-js-page-frontend-booking', 'nsmodalpersonaldaysprodfront', array( 'ajax_url' => admin_url( 'admin-ajax.php' ))); 
	wp_localize_script( 'ns-option-js-page-frontend-booking', 'nsfiltermodaldatefront', array( 'ajax_url' => admin_url( 'admin-ajax.php' )));
	wp_localize_script( 'ns-option-js-page-frontend-booking', 'nsfilterprodtemplateform', array( 'ajax_url' => admin_url( 'admin-ajax.php' )));
	wp_localize_script( 'ns-option-js-page-frontend-booking', 'nsfilterstarthourbyprodtemplateform', array( 'ajax_url' => admin_url( 'admin-ajax.php' )));
	wp_localize_script( 'ns-option-js-page-frontend-booking', 'nsfilterendhourbystarthourtemplateform', array( 'ajax_url' => admin_url( 'admin-ajax.php' )));
	wp_localize_script( 'ns-option-js-page-frontend-booking', 'nsfilterbookingcatfront', array( 'ajax_url' => admin_url( 'admin-ajax.php' ))); 
	wp_localize_script( 'ns-option-js-outlook-import', 'nsoutlookimport', array( 'ajax_url' => admin_url( 'admin-ajax.php' ))); 
	wp_localize_script( 'ns-option-js-outlook-export', 'nsoutlookexport', array( 'ajax_url' => admin_url( 'admin-ajax.php' ))); 
	
});
?>