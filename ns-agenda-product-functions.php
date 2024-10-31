<?php
// First Register the Tab by hooking into the 'woocommerce_product_data_tabs' filter
add_filter( 'woocommerce_product_data_tabs', 'ns_agenda_add_my_custom_product_data_tab' );
function ns_agenda_add_my_custom_product_data_tab( $product_data_tabs ) {
  $product_data_tabs['ns-agenda-custom-tab'] = array(
    'label' => __( 'Booking Tab', 'woocommerce' ),
    'target' => 'ns_agenda_custom_product_data',
  );
  return $product_data_tabs;
}


// Next provide the corresponding tab content by hooking into the 'woocommerce_product_data_panels' action hook
add_action( 'woocommerce_product_data_panels', 'ns_agenda_add_my_custom_product_data_fields' );
function ns_agenda_add_my_custom_product_data_fields() {
  global $woocommerce, $post;
  ?>
  <!-- id below must match target registered in above add_my_custom_product_data_tab function -->
  <div id="ns_agenda_custom_product_data" class="panel woocommerce_options_panel">
    <?php
    
    woocommerce_wp_text_input( array( 
      'id'            => 'ns_agenda_hourly_price',  
      'label'         =>  __( 'Price', 'woocommerce' ),
      'description'   =>  __( 'Price of bookable product', 'woocommerce' ),
      'type' => 'text'
    ) ); 

    woocommerce_wp_text_input( array( 
      'id'            => 'ns_agenda_quantity',  
      'label'         =>  __( 'Quantity', 'woocommerce' ),
      'description'   =>  __( 'Quantity of bookable product', 'woocommerce' ),
      'type' => 'number'
    ) );?>
    <span class="description" style="margin-left: 24%; color: #990000;"><b>Descrease product quantity will trash all future bookings (from today)</b></span>
    <br>
  </div>
  <?php
}
add_action( 'woocommerce_process_product_meta', 'ns_agenda_add_custom_general_fields_save'); 
function ns_agenda_add_custom_general_fields_save( $post_id ){
    
    // Text Field
    $woocommerce_text_field = $_POST['ns_agenda_hourly_price'];
    if( !empty( $woocommerce_text_field ) )
        update_post_meta( $post_id, 'ns_agenda_hourly_price', esc_attr( $woocommerce_text_field ) );

        
    // Number Field
    $woocommerce_number_field = $_POST['ns_agenda_quantity'];
    if($woocommerce_number_field == '') $woocommerce_number_field = 1;
    if( !empty( $woocommerce_number_field ) )
        update_post_meta( $post_id, 'ns_agenda_quantity', esc_attr( $woocommerce_number_field ) );
        
        
    // Checkbox
    $woocommerce_checkbox = isset( $_POST['_bookable'] ) ? 'yes' : 'no';
    update_post_meta( $post_id, '_bookable', $woocommerce_checkbox );
    
   /**************UPDATE OPTION ON INSERT PRODUCT****************/
	   ns_agenda_update_booking_array_on_adding_product($post_id, $woocommerce_checkbox);
}




/*******************when update quantity product update option array********************************/
add_action( 'update_post_meta', 'ns_update_agenda_array_on_update_quantity', 10, 4 );
function ns_update_agenda_array_on_update_quantity( $meta_id, $post_id, $meta_key, $meta_value )
{
  if(get_post_type($post_id) != "product") {
        return;
    }
    if ( 'ns_agenda_quantity' != $meta_key ) {
        return;
    }
    $prev = get_post_meta($post_id,'ns_agenda_quantity',true);
    if ($meta_value >= $prev) {
      $new_booking = $meta_value - $prev;
  	  $years = get_option('ns_agenda_year_struct');
  	  foreach($years as $year){
  		  $option = get_option('ns_agenda_option_struct_'.$year);
  		  foreach ($option as $day => $hour) {
    			foreach ($hour as $key => $value) {
    			  $var = $value[$post_id];
    			  $var = $var + $new_booking;
    			  $value[$post_id] = $var;
    			  $option[$day][$key] = $value;
    			} 
  		  }
  		  update_option('ns_agenda_option_struct_'.$year, $option);
  	  }
    }

    else {
        $args = array('posts_per_page'   => -1, 'post_type'        => 'ns_agenda', 'post_status'      => 'publish' );
        $bookings = get_posts( $args );
        $now = new DateTime();
        $now = $now->getTimestamp();
        foreach ($bookings as $booking) {
          $start = get_post_meta($booking->ID,'date_in',true);
          $start = strtotime($start);
          if($start > $now){
            wp_trash_post( $booking->ID);
          }
      }

        $new_booking = $prev - $meta_value;
	      $years = get_option('ns_agenda_year_struct');
      	foreach($years as $year){
      		  $option = get_option('ns_agenda_option_struct_'.$year);
      		  
      		  foreach ($option as $day => $hour) {
      			  foreach ($hour as $key => $value) {
        			  $var = $value[$post_id];
        			  $var = $var - $new_booking;
        			  if($var < 0)  $var = 0;
        			  $value[$post_id] = $var;
        			  $option[$day][$key] = $value;
        			} 
      		  }
        update_option('ns_agenda_option_struct_'.$year, $option);
      	}
    }
}
/****************************************************************************/
add_action( 'update_post_meta', 'ns_agenda_update_array_on_update_bookable', 10, 4 );
function ns_agenda_update_array_on_update_bookable( $meta_id, $post_id, $meta_key, $meta_value )
{
  if(get_post_type($post_id) != "product") {
        return;
    }
  if ( '_bookable' != $meta_key ) {
        return;
    }

  if($meta_value != 'yes'){
      $years = get_option('ns_agenda_year_struct');
      foreach($years as $year){
          $option = get_option('ns_agenda_option_struct_'.$year);
          foreach ($option as $day => $hour) {
            foreach ($hour as $key => $value) {
              unset($value[$post_id]);
              $option[$day][$key] = $value;
            } 
          }
      }
    update_option('ns_agenda_option_struct_'.$year,$option);
  }
}

/******************************************************************************/
add_filter('product_type_options', 'ns_agenda_add_checkbox_bookable', 10, 1); 

	function ns_agenda_add_checkbox_bookable ($product_options) {

    /**
     * The available product type options array keys are:
     * 
     * virtual
     * downloadable
     */
     $product_options = array( 'virtual' =>
     										array( 	'id' => '_virtual', 
      												'wrapper_class' => 'show_if_simple',
      												'label' => __( 'Virtual', 'woocommerce' ),
      												'description' => __( 'Virtual products are intangible and aren\'t shipped.', 'woocommerce' ),
      												'default' => 'no' ),
      							'downloadable' => 
      										array(  'id' => '_downloadable',
      												'wrapper_class' => 'show_if_simple',
      												'label' => __( 'Downloadable', 'woocommerce' ),
      												'description' => __( 'Downloadable products give access to a file upon purchase.', 'woocommerce' ),
      												'default' => 'no' ),
      							'bookable' => 
      										array( 'id' => '_bookable',
      												'wrapper_class' => 'show_if_simple',
      												'label' => __( 'Bookable', 'woocommerce' ),
      												'description' => __( 'Bookable products can be managed through booking.', 'woocommerce' ),
      												'default' => 'no' ) 
      										);
    return $product_options;

}

add_action( 'woocommerce_thankyou', 'ns_agenda_custom_tracking' );

function ns_agenda_custom_tracking( $order_id ) {
	$order = new WC_Order( $order_id );
	/*if( $order->status == 'processing' ) {
        $order->update_status( 'completed' );
    }*/
	//if($order->status == 'processing' || $order->status == 'completed'){
		$order = wc_get_order( $order_id );


		// This is the order total
		$order->get_total();
	 
		$line_items = $order->get_items();

		// This loops over line items
		$id = '';
		$product_id = '';
		$product_name = '';
		$name = '';
		$date_in = '';
		$date_out = '';
		$hour_in = '';
		$hour_out = '';
		$user_name =  $order->get_billing_first_name();
		$user_lastname = $order->get_billing_last_name();
		//$user_email = $order->get_billing_email();
			
		$user_data = $user_name.' '.$user_lastname;
		
		
		foreach ( $line_items as $item_id => $item_data ) {
			$meta_data = $item_data->get_data();
			$ok = true;
			// This will be a product
			/*if(isset($item['item_meta']['_ns_agenda_id']))
				$id = $item['item_meta']['_ns_agenda_id'][0];
			else
				$ok = false;*/
			if(isset($meta_data['product_id' ])){
				$product_id = $meta_data['product_id' ];
				$product_name = get_the_title($product_id);
			}
			else
				$ok = false;
			/*if(isset($item['item_meta']['_ns_agenda_name']))
				$name = $item['item_meta']['_ns_agenda_name'][0];
			else
				$ok = false;*/
			if($item_data->get_meta( '_ns_agenda_date_in' ) != '')
				$date_in = $item_data->get_meta( '_ns_agenda_date_in' );
			else
				$ok = false;
			/*if($item_data->get_meta( '_ns_agenda_date_out' ) != '')
				$date_out = $item['item_meta']['_ns_agenda_date_out'][0];
			else
				$ok = false;*/
			if($item_data->get_meta( '_ns_agenda_hour_in' ) != '' )
				$hour_in = $item_data->get_meta( '_ns_agenda_hour_in' );
			
			if($item_data->get_meta( '_ns_agenda_hour_out' ) != '' )
				$hour_out = $item_data->get_meta( '_ns_agenda_hour_out' );
			
			if($ok){
				//call to a function to update every day product on ns_agenda_option_struct option
				//$is_ok = ns_update_booking_prod_avaiability($start_date, $end_date, $page_post->ID);
				
				$is_ok = ns_agenda_update_booking_prod_avaiability_back_hour($date_in, $date_in, $hour_in, $hour_out, $product_id, false);
				ns_agenda_save_booking($user_data,$date_in, $hour_in, $hour_out, $product_id, $product_name);		
				
			}
			
		}
		
	//}
	
}

?>