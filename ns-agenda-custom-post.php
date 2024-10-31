<?php
/*Booking Custom Post*/
function ns_agenda_create_custom_post_agenda() {

	register_post_type( 'ns_agenda',
	// CPT Options
		array(
			'labels' => array(
				'name' => __( 'Agenda' ),
				'singular_name' => __( 'Agenda' )
			),
			'public' => true,
			'has_archive' => true,
			'rewrite' => array('slug' => 'ns_agenda'),
		)
	);
}

// Hooking up our function to theme setup
add_action( 'init', 'ns_agenda_create_custom_post_agenda' );
function ns_agenda_add_custom_meta_box_agenda() {
   add_meta_box(
       'booking_name',       // $id
       'Agenda',                  // $title
       'ns_agenda_show_custom_agenda',  // $callback
       'ns_agenda',                 // $page
       'normal',                  // $context
       'high'                     // $priority
   );
}

add_action('init', 'ns_agenda_remove_editor');
function ns_agenda_remove_editor() {
    remove_post_type_support( 'ns_agenda', 'editor' );
}

add_action('add_meta_boxes', 'ns_agenda_add_custom_meta_box_agenda');

/*Callback to show backend booking page*/
function ns_agenda_show_custom_agenda() {
	global $post;

    wp_nonce_field( basename( __FILE__ ), 'wpse_our_nonce' );
?>
<div class="ns-booking-table-container">
	<table class="ns-booking-table ns-booking-table-margin">
    	<tr>
    		<th>
    			<label for="ns-starting-booking-date">Starting Date</label>
    		</th>
    		<td>
    			<input class="ns-calendar-datepicker" id="ns-starting-booking-date" name="ns-starting-booking-date" value="<?php echo get_post_meta($post->ID, 'date_in',true); ?>"/>
    		</td>
    	</tr>
      <tr>
          <th>
              <label for="ns-products-booking">Products</label>
          </th>
          <td>
              <select class="" id="ns-products-booking" name="ns-products-booking">
                <option></option>
              </select>
          </td>
          <input class="" id="ns-products-booking-saved" name="ns-products-booking-saved" value="<?php echo get_post_meta($post->ID, 'product_name',true); ?>" hidden="true"/>
      </tr>
		<tr>
    		<th>
    			<label for="ns-starting-booking-hour">Starting hour</label>
    		</th>
    		<td>
    			<select class="" id="ns-starting-booking-hour" name="ns-starting-booking-hour"> <!-- value="<?php echo get_post_meta($post->ID, 'hour_in',true); ?>"-->
          <option></option>
          </select>
    		</td>
			<th>
    			<label for="ns-ending-booking-hour">Ending hour</label>
    		</th>
    		<td>
    			<select class="" id="ns-ending-booking-hour" name="ns-ending-booking-hour"> <!--value="<?php echo get_post_meta($post->ID, 'hour_out',true); ?>"-->
             <option></option>
          </select>
    		</td>
    	</tr>
      <tr>
      <?php
            $booking_state = get_post_meta($post->ID, 'ns_agenda_state', true); //true ensures you get just one value instead of an array
    ?>
      <th>
          <label for="ns-booking-state">State</label>
        </th>
        <td>
          <select class="" id="ns-booking-state" name="ns-booking-state">
            <option value="not_paid" <?php selected( $booking_state, 'not_paid' ); ?>>Not Paid</option>
            <option value="Paid" <?php selected( $booking_state, 'Paid' ); ?>>Paid</option>
        </select>
        </td>
      </tr>
	</table>
</div>
	
<?php	
}


/*Saves the booking post on 'publish' click*/
function ns_agenda_save_meta_fields_agenda( $post_id, $post, $update ) {
 // verify nonce
  if (!isset($_POST['wpse_our_nonce']) || !wp_verify_nonce($_POST['wpse_our_nonce'], basename(__FILE__)))
      return 'nonce not verified';

  // check autosave
  if ( wp_is_post_autosave( $post_id ) )
      return 'autosave';

  //check post revision
  if ( wp_is_post_revision( $post_id ) )
      return 'revision';

  // check permissions
  if ( 'ns_agenda' == $_POST['post_type'] ) {
      if ( ! current_user_can( 'edit_page', $post_id ) )
          return 'cannot edit page';
      } elseif ( ! current_user_can( 'edit_post', $post_id ) ) {
          return 'cannot edit post';
  }

  $title = "";
  $in_date = "";
  //$en_date = "";
  $in_hour = "";
  $en_hour = "";
  $product = "";
  $prod_category = "";
  $prod_price = "";
  $page_post = "";
  $booking_state = "";

  //$start_validity_period = get_option('ns_agenda_starting_year_period_opt');
 // $start_validity_period =   strtotime($start_validity_period);
  $date_up = get_post_meta($post_id,'date_in',true);
  if( $date_up != ''){
    $hour_in_up = get_post_meta($post_id,'hour_in',true);
    $hour_out_up = get_post_meta($post_id,'hour_out',true);
    $prod_id_up = get_post_meta($post_id,'product_id',true);
    ns_agenda_update_booking_prod_avaiability_back_hour($date_up, $date_up,$hour_in_up,$hour_out_up, $prod_id_up, true);
  }

  //$end_validity_period = get_option('ns_agenda_ending_year_period_opt');
 // $end_validity_period = strtotime($end_validity_period);
  
    if(isset($_POST['ns-starting-booking-date']))
    {
        //$in_date = $_POST['ns-starting-booking-date'];
		//$in_date = strtotime($in_date);
		//check if selected date is in the admin set period (range) of validity
		//if(($in_date > $start_validity_period) && ($in_date < $end_validity_period)){
			$in_date = $_POST['ns-starting-booking-date'];	
		//}
		/*else{
			wp_delete_post($post_id);
			return;
		}*/
    }     
   
    if(isset($_POST["ns-starting-booking-hour"]))
    {
        $in_hour = $_POST["ns-starting-booking-hour"];
    }   
    
    if(isset($_POST["ns-ending-booking-hour"]))
    {
        $en_hour = $_POST["ns-ending-booking-hour"];
    }   

    if(isset($_POST["ns-booking-state"]))
    {
        $booking_state = $_POST["ns-booking-state"];
    }   
    
    if(isset($_POST['ns-products-booking']))
    {
         $product = $_POST['ns-products-booking'];
		// $page_post = get_page_by_title($product, OBJECT, 'product');
		$product_name = get_the_title($product);
		// $prod_price = get_post_meta($page_post->ID, '_sale_price', true);
    }   
	
	if(isset($_POST['ns-category-booking']))
    {
         $prod_category = $_POST['ns-category-booking'];
    }   

    update_option('prima_update', $page_post);
	$is_ok = ns_agenda_update_booking_prod_avaiability_back_hour($in_date, $in_date,$in_hour,$en_hour, $product, false);
	   update_option('dopo_update', $is_ok);
	if($is_ok){
		echo '<script>';
		echo 'alert("Booking Inserted")';
		echo '</script>';
	}
	else{
		echo '<script>';
		echo 'alert("Product not avaiable in choosen period")';
		echo '</script>';
		wp_delete_post($post_id);	//delete the current post cuz at this point post already exist only with name
		return;		
	}

	$booking_quantity = ns_agenda_quantity($in_date, $in_date,$in_hour, $en_hour);
	update_post_meta($post_id, 'ns_agenda_quantity', $booking_quantity);

	 update_post_meta($post_id, "date_in", $in_date);
	 //update_post_meta($post_id, "date_out", $en_date);
	 update_post_meta($post_id, "hour_in", $in_hour);
	 update_post_meta($post_id, "hour_out", $en_hour);
	 update_post_meta($post_id, "product_name", $product_name);
	 update_post_meta($post_id, "product_price", $prod_price);
	 //update_post_meta($post_id, "product_category", $prod_category);
	 update_post_meta($post_id, "product_id", /*$page_post->ID*/$product);
   update_post_meta($post_id, "ns_agenda_state", $booking_state);
	/*****************************************************************/
		
}
add_action( 'save_post', 'ns_agenda_save_meta_fields_agenda',10, 3 );
//add_action( 'new_to_publish', 'ns_agenda_save_meta_fields_agenda',10, 3);

/*TABLE BOOKING SORTING*/
add_filter( 'manage_ns_agenda_posts_columns', 'ns_agenda_set_custom_edit_agenda_columns' );

function ns_agenda_set_custom_edit_agenda_columns( $columns ) {
 $date = $colunns['date'];
unset( $columns['date'] );
 $columns['data_in'] = __( 'Date');
 $columns['hour_in'] = __( 'Start Time');
 $columns['hour_out'] = __( 'End Time' );
 $columns['product_name'] = __( 'Product');
 //$columns['product_price'] = __( 'Price');
 $columns['ns_agenda_state'] = __( 'Booking State');

 return $columns;
}


add_filter( 'manage_edit-ns_agenda_sortable_columns', 'ns_agenda_set_custom_agenda_sortable_columns' );

function ns_agenda_set_custom_agenda_sortable_columns( $columns ) {
 $columns['data_in']  = 'data_in';
 $columns['hour_in'] = 'hour_in';
 $columns['hour_out'] = 'hour_out';
 $columns['ns_agenda_state'] = 'ns_agenda_state';

 return $columns;
}

add_filter( 'posts_clauses', 'ns_agenda_manage_wp_posts_be_qe_posts_clauses', 1, 2 );
function ns_agenda_manage_wp_posts_be_qe_posts_clauses( $pieces, $query ) {
  global $wpdb;

  if ( $query->is_main_query() && ( $orderby = $query->get( 'orderby' ) ) ) {
     $order = strtoupper( $query->get( 'order' ) );

     if ( ! in_array( $order, array( 'ASC', 'DESC' ) ) )
        $order = 'ASC';

     switch( $orderby ) {

        case 'data_in':

           $pieces[ 'join' ] .= " LEFT JOIN $wpdb->postmeta wp_rd ON wp_rd.post_id = {$wpdb->posts}.ID AND wp_rd.meta_key = 'date_in'";

           $pieces[ 'orderby' ] = "STR_TO_DATE( wp_rd.meta_value,'%d-%m-%Y' ) $order, " . $pieces[ 'orderby' ];

        break;

     }
  }
  return $pieces;
}

add_action( 'manage_ns_agenda_posts_custom_column' , 'ns_agenda_custom_agenda_column', 10, 2 );

function ns_agenda_custom_agenda_column( $column, $post_id ) {
    switch ( $column ) {

   // display start date
        case 'data_in' :
        echo get_post_meta( $post_id, 'date_in',true );
        break;

     // display start hour
        case 'hour_in' :
        echo get_post_meta( $post_id, 'hour_in',true );
        break;

     // display end hour
        case 'hour_out' :
        echo get_post_meta( $post_id, 'hour_out',true );
        break;

     // display booking state
        case 'ns_agenda_state' :
        $bs = get_post_meta( $post_id, 'ns_agenda_state',true );
        if($bs == "not_paid") $bs = "Not Paid";
        echo $bs;
        break;
		
	 // display booking product name
        case 'product_name' :
        echo get_post_meta( $post_id, 'product_name',true );
        break;

	 // display booking product_price
       /* case 'product_price' :
        echo get_post_meta( $post_id, 'product_price',true );
        break;*/
    }
}

/***********************/

/*************UPDATE DATA STRUCTURE ON DELETING POST***********/
add_action('wp_trash_post', 'ns_agenda_update_on_delete');
function ns_agenda_update_on_delete($post_id){
	
	$type = get_post_type( $post_id );
	if( $type == 'ns_agenda'){
		if(get_post_status( $post_id ) == 'publish'){
			$in_date = get_post_meta($post_id, 'date_in', true);
			$in_hour = get_post_meta($post_id, 'hour_in', true);
			$en_hour = get_post_meta($post_id, 'hour_out', true);
			//$en_date = get_post_meta($post_id, 'date_out', true);
			$product_id = get_post_meta($post_id, 'product_id', true);
			
			$is_ok = true;
			$is_ok = ns_agenda_update_booking_prod_avaiability_back_hour($in_date, $in_date,$in_hour,$en_hour, $product_id, true);
		}
		else{
			wp_delete_post( $post_id);
		}
	}
}

/**************************************************************/


/**********************SAVE CUSTOM POST*********************/
function ns_agenda_save_booking($user_name, $start_date, $start_hour, $end_hour, $prod_id, $prod_name, $already_outlook_exported='false'){
    //$current_user = wp_get_current_user();
    //all inputs are set, create a new booking post
    //insert post and get the id
      $post = array(
        'post_title' =>  $user_name,
        'post_status' => 'publish',
        'post_type' => 'ns_agenda',
      );
    
      $post_id = wp_insert_post($post);
      $error = update_post_meta($post_id, "date_in", $start_date);
      //$error = update_post_meta($post_id, "date_out", $end_date);
     // if($is_hourly){
        $error = update_post_meta($post_id, "hour_in", $start_hour);
        $error = update_post_meta($post_id, "hour_out", $end_hour);
      //} 
      //$error = update_post_meta($post_id, "product_category", $prod_cat);
      $error = update_post_meta($post_id, 'booking_state', 'Paid');
      
      $booking_quantity = ns_agenda_quantity($start_date, $start_date, $start_hour, $end_hour);
      update_post_meta($post_id, 'ns_agenda_quantity', $booking_quantity);
      
      update_post_meta($post_id, "product_id", $prod_id);
	  update_post_meta($post_id, "product_name", $prod_name);
	  if($already_outlook_exported == 'true') {
		  update_post_meta($post_id, "outlook_already_exported", 'true');
	  }
	  else {
		  update_post_meta($post_id, "outlook_already_exported", 'false');
	  }
      /*if is a bookable product update the price with bookable price before insert into cart*/
      $is_bookable = get_post_meta($prod_id, '_bookable',true);
      if($is_bookable == 'yes'){
        $booked_price = get_post_meta($prod_id, 'ns_agenda_hourly_price', true);
        update_post_meta($prod_id, '_price', $booked_price );     
      }
		
		//Gonna update booking post status to paid 
		update_post_meta($post_id, 'ns_agenda_state', 'Paid');
      
  }