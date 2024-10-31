<?php
/*
Plugin Name: NS Booking Agenda
Plugin URI: https://www.nsthemes.com/
Description: This plugin allow to organize and manage booking
Version: 1.3.5
Author: NsThemes
Author URI: http://www.nsthemes.com
License: GNU General Public License v2.0
License URI: http://www.gnu.org/licenses/gpl-2.0.html
 */


if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/** 
 * @author        PluginEye
 * @copyright     Copyright (c) 2019, PluginEye.
 * @version         1.0.0
 * @license       https://www.gnu.org/licenses/gpl-3.0.html GNU General Public License Version 3
 * PLUGINEYE SDK
*/

require_once('plugineye/plugineye-class.php');
$plugineye = array(
    'main_directory_name'       => 'ns-booking-agenda',
    'main_file_name'            => 'ns-agenda-page.php',
    'redirect_after_confirm'    => 'admin.php?page=ns-booking-agenda%2Fns-admin-options-pro%2Fns_admin_option_dashboard.php',
    'plugin_id'                 => '222',
    'plugin_token'              => 'NWNmZTdkNDc1MDFmZGZjY2I2MzYyYTlhNzhlMTExMTk3YzRmOTM4Mzc1NDlmNmJmZmM5YzI5NjYxZDIxM2VlZmI5ZDJkZjYxNzZmZDc=',
    'plugin_dir_url'            => plugin_dir_url(__FILE__),
    'plugin_dir_path'           => plugin_dir_path(__FILE__)
);

$plugineyeobj222 = new pluginEye($plugineye);
$plugineyeobj222->pluginEyeStart();      

if ( ! defined( 'BOOKING_NS_PLUGIN_DIR' ) )
    define( 'BOOKING_NS_PLUGIN_DIR', plugin_dir_path( __FILE__ ) );

if ( ! defined( 'BOOKING_NS_PLUGIN_DIR_URL' ) )
    define( 'BOOKING_NS_PLUGIN_DIR_URL', plugin_dir_url( __FILE__ ) );


/* *** plugin options *** */
require_once( BOOKING_NS_PLUGIN_DIR.'/ns-agenda-options.php');


require_once( plugin_dir_path( __FILE__ ).'ns-admin-options-pro/ns-admin-options-setup.php');
require_once( plugin_dir_path( __FILE__ ).'ns-agenda-calendar.php');
require_once( plugin_dir_path( __FILE__ ).'ns-agenda-custom-post.php');
require_once( plugin_dir_path( __FILE__ ).'ns-agenda-option-struct.php');
require_once( plugin_dir_path( __FILE__ ).'ns-agenda-product-functions.php');
require_once( plugin_dir_path( __FILE__ ).'ns-agenda-ajax-functions.php');
require_once( plugin_dir_path( __FILE__ ).'outlook-integration/outlook-Integration.php');
require_once( plugin_dir_path( __FILE__ ).'outlook-integration/async-outlook-import.php');
require_once( plugin_dir_path( __FILE__ ).'outlook-integration/async-outlook-export.php');

global $post;

/*******SINGLE BOOKING *****************/
function ns_agenda_load_template($template) {
    global $post;
    // Is this a "my-custom-post-type" post?
    if ($post->post_type == "ns_agenda"){
        //Your plugin path 
       // $plugin_path = plugin_dir_path( __FILE__ );
        // The name of custom post type single template
        $template_name = 'single-agenda.php';
        // A specific single template for my custom post type exists in theme folder? Or it also doesn't exist in my plugin?
        if($template === get_stylesheet_directory() . '/' . $template_name
            || !file_exists(BOOKING_NS_PLUGIN_DIR . $template_name)) {
            //Then return "single.php" or "single-my-custom-post-type.php" from theme directory.
            return $template;
        }
        // If not, return my plugin custom post type template.
        return BOOKING_NS_PLUGIN_DIR . $template_name;
    }
    //This is not my custom post type, do nothing with $template
    return $template;
}
add_filter('single_template', 'ns_agenda_load_template');
/*************************************/

/**CREATE DEFAULT PAGE ON INIT*/
add_action( 'init', 'ns_agenda_create_default_page' );
function ns_agenda_create_default_page(){
	if(!get_page_by_title('agenda', 'OBJECT', 'page')){
		$args = array('post_title' => 'agenda', 'post_status' => 'publish', 'post_type' => 'page');
		$page_id = wp_insert_post($args);
		update_post_meta( $page_id, '_wp_page_template', 'calendar-template-page.php' );
	}
}


add_filter( 'page_template', 'ns_agenda_page_template' );
function ns_agenda_page_template( $page_template )
{
   if ( is_page( 'agenda' ) ) {
       $page_template =  plugin_dir_path( __FILE__ ).'/ns-agenda-calendar-available-template-page.php';
   }
   return $page_template;
}

/******************************/




/************************MY BOOKING CALENDAR TAB***************/
// 1. Register new endpoint to use for My Account page
// Note: Resave Permalinks or it will give 404 error
 
function ns_agenda_add_my_booking_calendar_endpoint() {
    add_rewrite_endpoint( 'my-agenda', EP_ROOT | EP_PAGES );
}
 
add_action( 'init', 'ns_agenda_add_my_booking_calendar_endpoint' );
 
 
// ------------------
// 2. Add new query var
 
function ns_agenda_my_query_vars( $vars ) {
    $vars[] = 'my-agenda';
    return $vars;
}
 
add_filter( 'query_vars', 'ns_agenda_my_query_vars', 0 );
 
 
// ------------------
// 3. Insert the new endpoint into the My Account menu
 
function ns_agenda_add_my_link_my_account( $items ) {
    $items['my-agenda'] = 'My Agenda Calendar';
    return $items;
}
 
add_filter( 'woocommerce_account_menu_items', 'ns_agenda_add_my_link_my_account' );
 
 
// ------------------
// 4. Add content to the new endpoint
 
function ns_agenda_my_content() {
	  include 'ns-agenda-custom-modal.php';
	  $calendar = new Calendar(false, false);     
      echo $calendar->show(true);
}
 
add_action( 'woocommerce_account_my-agenda_endpoint', 'ns_agenda_my_content' );

/************************DELETE MY BOOKING CONTACT FORM**********************/
function ns_agenda_contact_form_endpoint() {
    add_rewrite_endpoint( 'contact-form-my-agenda', EP_ROOT | EP_PAGES );
}
 
add_action( 'init', 'ns_agenda_contact_form_endpoint' );
 
 
function ns_agenda_contact_form_query_vars( $vars ) {
    $vars[] = 'contact-form-my-agenda';
    return $vars;
}
 
add_filter( 'query_vars', 'ns_agenda_contact_form_query_vars', 0 );


function ns_agenda_contact_form_link_my_account( $items ) {
    $items['contact-form-my-agenda'] = 'Agenda Contact Form';
    return $items;
}
 
add_filter( 'woocommerce_account_menu_items', 'ns_agenda_contact_form_link_my_account' );
 
 
function ns_agenda_contact_form_content() {
	$current_user = wp_get_current_user();  
	$args = array(
			'author'        =>  $current_user->ID,
			'orderby'       =>  'post_date',
			'order'         =>  'ASC',
			'post_type'		=>	'ns_agenda',
			'post_status'		=>	'publish',
			'posts_per_page' => -1
		);
        
	$agenda_posts = get_posts( $args );	//all agenda posts of current user
	
	?>
		<form id="ns-contact-form" role="form" method="post">
			<div class="ns-form-group">
				<label>Your Username</label><br>
				<input class="ns-border-radius" name="ns-contact-form-personal-username" type="text" value="<?php echo $current_user->user_nicename; ?>" readonly>
			</div>
			<div class="ns-form-group">
				<label>Your Email</label><br>
				<input class="ns-border-radius" name="ns-contact-form-personal-email" type="text" value="<?php echo $current_user->user_email; ?>" readonly>
			</div>
			<div class="ns-form-group">
				<label>Booking to Cancel</label><br>
				<select class="ns-border-radius" id="ns-contact-form-personal-select-booking" name="ns-contact-form-personal-select-booking">
					<?php
						foreach($agenda_posts as $booking){
							echo '<option value="'.$booking->ID.'">'.$booking->post_title.'</option>';
						}
					?>
				</select>
			</div>
			<button class="ns-btn-front" type="submit" name="ns-contact-form-personal-submit">Send Request</button>
		</form>
	<?php
	
	if(isset($_POST['ns-contact-form-personal-submit'])){
		$username = '';
		$email = '';
		$agenda_id = '';
		if(isset($_POST['ns-contact-form-personal-username'])){
			$username = sanitize_text_field($_POST['ns-contact-form-personal-username']);
		}
		if(isset($_POST['ns-contact-form-personal-email'])){
			$email = sanitize_text_field($_POST['ns-contact-form-personal-email']);
		}
		if(isset($_POST['ns-contact-form-personal-select-booking'])){
			$agenda_id = sanitize_text_field($_POST['ns-contact-form-personal-select-booking']);
		}
		
		if($username != '' && $email != '' && $agenda_id != ''){
			$to = get_option('ns_agenda_cancelled_agenda_email');
			if($to == false){
				$blogusers = get_users('role=Administrator');
				//print_r($blogusers);
				foreach ($blogusers as $user) {
					$to = $user->user_email;
				  }  
			}
			$subject = 'Booking Cancellation';
			$body = 'User '.$username.' ('.$email.') want to cancel the booking with ID: '.$booking_id;
			 
			wp_mail( $to, $subject, $body);
		}
	}
}
 
add_action( 'woocommerce_account_contact-form-my-agenda_endpoint', 'ns_agenda_contact_form_content' );

/**************************************************************/

function ns_agenda_remove_error_cookies(){
	ob_start();
}
add_action('init','ns_agenda_remove_error_cookies');

/* Add to the functions.php file of your theme */
add_filter( 'woocommerce_order_button_text', 'ns_agenda_custom_order_button_text' ); 

function ns_agenda_custom_order_button_text() {
    return __( 'Book Now', 'woocommerce' ); 
}

add_action( 'woocommerce_before_calculate_totals', 'ns_agenda_add_custom_price' );

function ns_agenda_add_custom_price( $cart_object ) {
    foreach ( $cart_object->cart_contents as $key => $value ) {
    	$prod_id = $value['product_id'];
    	$is_bookable = get_post_meta($prod_id, '_bookable',true);
    	if($is_bookable == 'yes'){
				$booked_price = get_post_meta($prod_id, 'ns_agenda_hourly_price', true);
            	$value['data']->price = $booked_price;
        }
       
    }
}

function ns_agenda_change_product_html( $price_html, $product ) {
	$is_bookable = get_post_meta($product->get_id(), '_bookable',true);
    	if($is_bookable == 'yes'){
				$booked_price = get_post_meta($product->get_id(), 'ns_agenda_hourly_price', true);
            	$price_html = '<span class="amount">' . wc_price( $booked_price ) . '</span>';	
        }	
	return $price_html;
}
add_filter( 'woocommerce_get_price_html', 'ns_agenda_change_product_html', 10, 2 );

// Change the cart prices if a unit_price is set
function ns_agenda_change_product_price_cart( $price, $cart_item, $cart_item_key ) {
	$prod_id = $cart_item['product_id'];
	$is_bookable = get_post_meta($prod_id, '_bookable',true);
    	if($is_bookable == 'yes'){
				$booked_price = get_post_meta($prod_id, 'ns_agenda_hourly_price', true);
            	$price =  wc_price( $booked_price );	
        }	
	return $price;
}	
add_filter( 'woocommerce_cart_item_price', 'ns_agenda_change_product_price_cart', 10, 3 );


function ns_agenda_force_individual_cart_items( $cart_item_data, $product_id ){

$is_bookable = get_post_meta($product_id, '_bookable',true);
    	if($is_bookable == 'yes'){
				 $unique_cart_item_key = md5( microtime().rand() );
 				 $cart_item_data['unique_key'] = $unique_cart_item_key;	
        }	
 

  return $cart_item_data;

}

add_filter( 'woocommerce_add_cart_item_data','ns_agenda_force_individual_cart_items', 10, 2 );

//Get it from the session and add it to the cart variable
function ns_agenda_get_cart_items_from_session( $item, $values, $key ) {
	//$cart_key_opt = get_option('ns_agenda_cart_key');
	/*if($cart_key_opt != '')
		$cart_key_opt = get_option('ns_agenda_cart_key');
	else
		return $item;
	*/
    //if (array_key_exists($key, $cart_key_opt)) {
    if ( array_key_exists( 'ns_agenda_id', $values ) )
        $item[ 'ns_agenda_id' ] = $values['ns_agenda_id'];
    if ( array_key_exists( 'ns_agendaname', $values ) )
        $item[ 'ns_agenda_name' ] = $values['ns_agenda_name'];
    if ( array_key_exists( 'ns_agenda_date_in', $values ) )
        $item[ 'ns_agenda_date_in' ] = $values['ns_agenda_date_in'];
    if ( array_key_exists( 'ns_agenda_date_out', $values ) )
        $item[ 'ns_agenda_date_out' ] = $values['ns_agenda_date_out'];
    if ( array_key_exists( 'agenda_hour_in', $values ) )
        $item[ 'ns_agenda_hour_in' ] = $values['ns_agenda_hour_in'];
    if ( array_key_exists( 'ns_agenda_hour_in', $values ) )
        $item[ 'ns_agenda_hour_out' ] = $values['ns_agenda_hour_out'];
//}
    return $item;

}
add_filter( 'woocommerce_get_cart_item_from_session', 'ns_agenda_get_cart_items_from_session', 1, 3 );


add_filter('woocommerce_cart_item_name','ns_agenda_add_custom_session',1,3);
function ns_agenda_add_custom_session($product_name, $values, $cart_item_key ) {
$prod_id = $values['product_id'];
$string = $product_name;
if(get_post_meta($prod_id,'_bookable',true) == 'yes') {
	$string = $string . "<br />". $values['ns_agenda_name'];
	
	if($values['ns_agenda_date_in'] == $values['ns_agenda_date_out']) $string = $string . "<br /> Date: " . $values['ns_agenda_date_in'];
	
	else $string = $string . "<br /> From: " . $values['ns_agenda_date_in']. "<br /> To: " . $values['ns_agenda_date_out'];
	
	if($values['ns_agenda_hour_in'] != '' && $values['ns_agenda_hour_out'] != '') {
		$string = $string . "<br />" . $values['ns_agenda_hour_in']. " - " . $values['ns_agenda_hour_out'];
		}
    
	}
return $string;
}

add_action('woocommerce_add_order_item_meta','ns_add_values_to_order_item_meta',1,2);

  function ns_add_values_to_order_item_meta($item_id, $values)
  {
        global $woocommerce,$wpdb;
        $booking_id = $values['ns_agenda_id'];
        if(!empty($booking_id))
        {
            wc_add_order_item_meta($item_id,'_ns_agenda_id',$booking_id);  
        }
       
        //$booking_name = $values['ns_agenda_name'];
        if(!empty($booking_id))
        {
            wc_add_order_item_meta($item_id,'_ns_agenda_name',$booking_name);  
			wc_add_order_item_meta($item_id,'Name ',$booking_name);  //Key used to be showed in email order product section
        }

        $booking_date_in = $values['ns_agenda_date_in'];
        if(!empty($booking_date_in))
        {
            wc_add_order_item_meta($item_id,'_ns_agenda_date_in',$booking_date_in); 
			wc_add_order_item_meta($item_id,'Date ',$booking_date_in);  	//Key used to be showed in email order product section		
        }

        $booking_date_out = $values['ns_agenda_date_out'];
        if(!empty($booking_date_out))
        {
            wc_add_order_item_meta($item_id,'_ns_agenda_date_out',$booking_date_out);  
        }

        $booking_hour_in = $values['ns_agenda_hour_in'];
        if(!empty($booking_hour_in))
        {
            wc_add_order_item_meta($item_id,'_ns_agenda_hour_in',$booking_hour_in);  
			wc_add_order_item_meta($item_id,'From ',$booking_hour_in);  	//Key used to be showed in email order product section
        }

        $booking_hour_out = $values['ns_agenda_hour_out'];
        if(!empty($booking_hour_out))
        {
            wc_add_order_item_meta($item_id,'_ns_agenda_hour_out',$booking_hour_out);  
			wc_add_order_item_meta($item_id,'To ',$booking_hour_out); 		//Key used to be showed in email order product section
        }

  }
  
  
  

/* *** add link premium *** */
add_filter( 'plugin_action_links_' . plugin_basename(__FILE__), 'nsbookingagenda_add_action_links' );

function nsbookingagenda_add_action_links ( $links ) {	
 $mylinks = array('<a id="nsbookingagendalinkpremium" href="https://www.nsthemes.com/join-the-club/?ref-ns=2&campaign=BA-linkpremium" target="_blank">'.__( 'Join NS Club', 'ns-booking-agenda' ).'</a>');
return array_merge( $links, $mylinks );
}
?>
