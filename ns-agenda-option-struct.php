<?php
/*Triggered on plugin activation*/
/* create default array struct for current year */
function ns_agenda_create_option_on_plugin_activation( $plugin, $network_activation ) {
	update_option( 'ns_agenda_is_hourly', 'true');
	update_option( 'ns_agenda_custom_hour_week', '1');
	$ns_year = date('Y');
	$option = get_option('ns_agenda_option_struct_'.$ns_year);
	if($option) return;
	else {
			$timestamp = strtotime('next Sunday');
			$arr_days_start = array();
			$arr_days_end = array();
			for ($i = 0; $i < 7; $i++) {
				$days = strftime('%A', $timestamp);
				$begin_hour = "00:00";
				$end_hour = "24:00";
				$arr_days_start[$days] = $begin_hour;
				$arr_days_end[$days] = $end_hour;
				$timestamp = strtotime('+1 day', $timestamp);
			}
			update_option('ns_agenda_week_hour_start',$arr_days_start);
			update_option('ns_agenda_week_hour_end',$arr_days_end);
			
			$date_arr = ns_agenda_create_hourly_array($ns_year);
			
			update_option( 'ns_agenda_option_struct_'.$ns_year, $date_arr);
		}
}
add_action('activated_plugin', 'ns_agenda_create_option_on_plugin_activation', 10, 2 );

/************ CREATE OUTLOOK PRODUCT ********************/
function ns_agenda_create_outlook_prod_on_plugin_activation( $plugin, $network_activation ) {
	$objProduct = new WC_Product();
	
	$objProduct->set_name("Outlook");
	$objProduct->set_status("publish");  // can be publish,draft or any wordpress post status
	$objProduct->set_catalog_visibility('visible'); // add the product visibility status
	$objProduct->set_virtual(true);
	$product_id = $objProduct->save(); 
	update_post_meta($product_id, 'ns_agenda_hourly_price', 1);
	update_post_meta($product_id, '_bookable', 'yes');
	update_post_meta($product_id, 'ns_agenda_quantity', 1);
	
	update_option('outlook_prod_id', $product_id);	// Saving outlook product id into option for future operations.
}

add_action('activated_plugin', 'ns_agenda_create_outlook_prod_on_plugin_activation', 10, 2 );

/************DELETE OUTLOOK PRODUCT ON DEACTIVATE PLUGIN ********************/
function delete_prod_on_plugin_deactivation(  $plugin, $network_activation ) {
	$outlook_prod_id = get_option('outlook_prod_id');
    wp_delete_post($outlook_prod_id, true);
}
add_action( 'deactivated_plugin', 'delete_prod_on_plugin_deactivation', 10, 2 );

/************UPDATE OPTION BOOKING TYPE********************/

update_option( 'ns_agenda_is_hourly', 'true');

/************on change option backend -> change struct *******************/
function ns_agenda_update_booking_array($option, $old_value, $value){
	
	$date_arr = array();
	$args     = array( 'post_type' => 'ns_agenda', 'post_status' => 'publish', 'posts_per_page' => -1 );
	$bookings = get_posts( $args );
	
	if((($option == 'ns_agenda_week_hour_start') || ($option == 'ns_agenda_week_hour_end') ) && $old_value != $value ) {
		/*foreach ($bookings as $book) {
		 	wp_trash_post( $book->ID);
		 } */
		$struct_years = get_option ('ns_agenda_year_struct', array());
		foreach ($struct_years as $ns_year) {
			$date_arr = ns_agenda_update_avaiability_hour($ns_year);
			update_option( 'ns_agenda_option_struct_'.$ns_year, $date_arr);
		}
		
	  }
}
add_action('updated_option', 'ns_agenda_update_booking_array',10, 3);


/*************************************************************/
function ns_agenda_update_avaiability_hour($year){
	$struct_years = get_option ('ns_agenda_year_struct', array());

	$hour_start = get_option('ns_agenda_week_hour_start');
	$hour_end = get_option('ns_agenda_week_hour_end');
	
	$option_struct = get_option('ns_agenda_option_struct_'.$year);
	
	foreach($option_struct as $date=>$day){
		$date_d = new DateTime($date);
		$begin_hour = $hour_start[$date_d->format("l")];
		$end_hour = $hour_end[$date_d->format("l")];
		foreach($day as $hour=>$products){
			if($begin_hour == '' || $end_hour == '') $option_struct[$date][$hour]['available'] = false;
			else {
				$tStart = strtotime($begin_hour);
				$tEnd = strtotime($end_hour);

				$tNow = $tStart;
				$tHour = strtotime($hour);	
				if($tHour >= $tStart && $tHour < $tEnd){
					$option_struct[$date][$hour]['available'] = true;
				}
				else{
					$option_struct[$date][$hour]['available'] = false;
				}
			}	
		}				
	}
	return $option_struct;
}
/************************CREATE HOURLY ARRAY******************/
function ns_agenda_create_hourly_array($year){
	
	$struct_years = get_option ('ns_agenda_year_struct', array());
	if(!in_array($year, $struct_years)) {
		 array_push($struct_years, $year);
		 update_option('ns_agenda_year_struct', $struct_years);
		}
	$starting_year = "01-01-".$year;
	$ending_year = "31-12-".$year;		
	
	$begin = new DateTime( $starting_year );
	$end = new DateTime( $ending_year );
	$end = $end->modify( '+1 day' );

	$args     = array( 'post_type' => 'product', 'post_status' => 'publish', 'posts_per_page' => -1 );
	$products = get_posts( $args ); 

	$interval = new DateInterval('P1D');
	$daterange = new DatePeriod($begin, $interval ,$end);
	$date_arr = array();

	$hour_start = get_option('ns_agenda_week_hour_start');
	$hour_end = get_option('ns_agenda_week_hour_end');
			
	foreach($daterange as $date){
		
		$begin_hour = $hour_start[$date->format("l")];
		$end_hour = $hour_end[$date->format("l")];
		if($begin_hour == '' || $end_hour == '') {	
			$begin_hour == '00:00';
			$end_hour == '24:00';
		}
		$tStart = strtotime($begin_hour);
		$tEnd = strtotime($end_hour);

		$tNow = $tStart;
			
		$arr_products = array();
			foreach($products as $prod){
				if (get_post_meta($prod->ID, '_bookable', true) == 'yes') {
					$prod_quantity = get_post_meta($prod->ID, 'ns_agenda_quantity', true);
					$product = wc_get_product($prod->ID);
					$arr_products[$prod->ID] = $prod_quantity;
					$arr_products['available'] = true;	
					}			
				}
			while($tNow <= $tEnd) {
					$date_arr[$date->format('d-m-Y')][date("H:i",$tNow)] = $arr_products;
					$tNow = strtotime('+1 hour',$tNow);
				}
		}
	return $date_arr;
}
/*************************************************************/

//update every day/hour product on ns_agenda_option_struct option 
function ns_agenda_update_booking_prod_avaiability_back_hour($start_d, $end_d,$start_t, $end_t, $woo_id, $is_delete){
	$d = explode('-', $start_d);
	$ns_year = $d[2];
	$option = get_option('ns_agenda_option_struct_'.$ns_year);
	if(!$option) {
		 $date_arr = ns_agenda_create_hourly_array($d[2]);
		 update_option( 'ns_agenda_option_struct_'.$ns_year, $date_arr);
		 $option = get_option('ns_agenda_option_struct_'.$ns_year);
		}
	$begin = new DateTime($start_d);
	$end = new DateTime($end_d);
	$end->add(new DateInterval('P1D'));	//adding 1 day to let the next foreach to get all days in interval
	$is_ok = true;
	if(ns_agenda_check_booking_prod_avaiability_back_hour($start_d, $end_d,$start_t, $end_t, $woo_id, $is_delete)) {
			$tStart = strtotime($start_t);
			$tEnd = strtotime($end_t);
			$tNow = $tStart;
			while($tNow < $tEnd) {
					$var = $option[$begin->format('d-m-Y')][date("H:i",$tNow)][$woo_id];
					if($is_delete && $var < get_post_meta($woo_id,'ns_agenda_quantity',true))
						$option[$begin->format('d-m-Y')][date("H:i",$tNow)][$woo_id] = $var + (1);
					else
						$option[$begin->format('d-m-Y')][date("H:i",$tNow)][$woo_id] = $var - (1);
					$tNow = strtotime('+1 hour',$tNow);
				}
				update_option( 'ns_agenda_option_struct_'.$ns_year, $option );	
		
	}	
	else { $is_ok = false; }
	return $is_ok;
}


//update every day/hour product on ns_agenda_option_struct option 
function ns_agenda_check_booking_prod_avaiability_back_hour($start_d, $end_d,$start_t, $end_t, $woo_id,$is_delete){
	if($is_delete) return true;
	$d = explode('-', $start_d);
	$ns_year = $d[2];
	$option = get_option('ns_agenda_option_struct_'.$ns_year);
	if(!$option) {
		$date_arr = ns_agenda_create_hourly_array($d[2]);
		update_option( 'ns_agenda_option_struct_'.$ns_year, $date_arr);
		$option = get_option('ns_agenda_option_struct_'.$ns_year);
		}
	$begin = new DateTime($start_d);
	$end = new DateTime($end_d);
	//$end->add(new DateInterval('P1D'));
	$end = $end->modify( '+1 day' );
	if($start_d != $end_d){ //check if set custom hour is the same day
				return false;
			}
	$is_ok = true;
	$tStart = strtotime($start_t);
	$tEnd = strtotime($end_t);
	
		if($tEnd < $tStart) { //check the start time is before end time
				return false;
			}
	$hour_start = get_option('ns_agenda_week_hour_start');
	$hour_end = get_option('ns_agenda_week_hour_end');
	$begin_hour = $hour_start[$begin->format("l")];
	$end_hour = $hour_end[$begin->format("l")];
	$begin_hour = strtotime($begin_hour);
	$end_hour = strtotime($end_hour);
	if ($tStart < $begin_hour || $tEnd > $end_hour) {
				return false;
			}
		$tNow = $tStart;
		while($tNow < $tEnd) {
					if($option[$begin->format('d-m-Y')][date("H:i",$tNow)][$woo_id] == 0 || !$option[$begin->format('d-m-Y')][date("H:i",$tNow)]['available']) {$is_ok = false;}
					$tNow = strtotime('+1 hour',$tNow);
				}
	return $is_ok;
}


//get the quantity of product for booking
function ns_agenda_quantity($start_d, $end_d,$start_t, $end_t){
		$quantity = 0;
		if($end_d == '') $end_d = $start_d;
		$begin = new DateTime($start_d."T".$start_t);
		$end = new DateTime($end_d."T".$end_t);
		$daterange = new DatePeriod($begin, new DateInterval('PT1H'), $end);
		foreach($daterange as $day){
			$quantity++;
		}
	return $quantity;
}

//when delete a bookable product update the array option removing it
add_action( 'trash_product', 'ns_agenda_update_booking_array_on_delete_product', 10, 2 );
function ns_agenda_update_booking_array_on_delete_product($post_id, $post){
	if(get_post_type($post_id) != "product") {
        return;
    }
    if(get_post_meta($post_id, '_bookable' ,true) != "yes") {
        return;
    }
    $struct_years = get_option ('ns_agenda_year_struct', array());
		foreach ($struct_years as $ns_year) {
		
			$option = get_option( 'ns_agenda_option_struct_'.$ns_year);
		
	 		foreach ($option as $day => $hour) {
				foreach ($hour as $key => $value) {
					
				unset($value[$post_id]);
				$option[$day][$key] = $value;
				
				}	
			}
	update_option('ns_agenda_option_struct_'.$ns_year,$option);
		}
	}


function ns_agenda_update_booking_array_on_adding_product($woo_id, $is_bookable){
	if($is_bookable == 'yes'){				//updating option struct only if the just inserted product is bookable
		$years = get_option('ns_agenda_year_struct');
		foreach($years as $year){
			$option = get_option('ns_agenda_option_struct_'.$year);
			$quantity = get_post_meta($woo_id, 'ns_agenda_quantity', true);
			foreach($option as $date => $day){
				foreach ($day as $hour => $prod) {
					$option[$date][$hour][$woo_id] = $quantity;
				}
			
			}	
			update_option( 'ns_agenda_option_struct_'.$year, $option);
			$opt = ns_agenda_update_avaiability_hour($year);
			update_option( 'ns_agenda_option_struct_'.$year, $opt);
		}
		
	}
}

?>