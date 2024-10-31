<?php

add_action( 'wp_ajax_nopriv_ns_agenda_import_outlook_events', 'ns_agenda_import_outlook_events' );
add_action( 'wp_ajax_ns_agenda_import_outlook_events', 'ns_agenda_import_outlook_events' );
function ns_agenda_import_outlook_events(){
	session_start();

	if(isset($_SESSION['accessToken'])){
		$token = $_SESSION['accessToken'];
		//echo $token;
		$args = array(
		  'headers' => array(
			'Authorization' => 'Bearer '.$token,
			'Content-type' => 'application/x-www-form-urlencoded'
		  )
		);
		
		$response = wp_remote_get( 'https://graph.microsoft.com/v1.0/me/calendar/events', $args);
		$body = json_decode(wp_remote_retrieve_body($response));
		foreach($body->value as $event){
			// Get simple day date
			$ts = strtotime($event->start->dateTime);
			$day = new DateTime("@$ts");
			$day_to_save = $day->format('d-m-Y'); 
			
			// Get starting event hour
			$start_hour_to_save = $day->format('H:i'); 
			
			// Get ending event hour
			$tsEnd = strtotime($event->end->dateTime);
			$dayEnd = new DateTime("@$tsEnd");
			$end_hour_to_save = $dayEnd->format('H:i'); 
			

			if(($start_hour_to_save == '00:00') && ($end_hour_to_save == '00:00')) {
				$end_hour_to_save = '24:00';
			}

			$current_user = wp_get_current_user();
			$outlook_product_id = get_option('outlook_prod_id');
			$post_prod = get_post($outlook_product_id);
			
			// Check if similar post already exists
			$query_args = array(
				'post_type' => 'ns_agenda',
				'post_status' => 'publish',
				'posts_per_page' => -1,
				'meta_query' => array(
					array(
						'key'     => 'date_in',
						'value'   => $day_to_save,
						'compare' => '=',
					),
					array(
						'key'     => 'hour_in',
						'value'   => $start_hour_to_save,
						'compare' => '=',
					),
					array(
						'key'     => 'hour_out',
						'value'   => $end_hour_to_save,
						'compare' => '=',
					),
					array(
						'key'     => 'product_id',
						'value'   => $post_prod->ID,
						'compare' => '=',
					),
				),
			);
			$query = new WP_Query( $query_args );
			$posts = $query->posts;
			// Dont allow duplicates
			if(empty($posts)){
				ns_agenda_save_booking($current_user->display_name, $day_to_save, $start_hour_to_save,$end_hour_to_save, $outlook_product_id, $post_prod->post_title, 'true');
				ns_agenda_update_booking_prod_avaiability_back_hour( $day_to_save, $day_to_save,  $start_hour_to_save, $end_hour_to_save, $outlook_product_id, false );
			}

		}
		echo 'Success';
	}
	else {
		echo 'Invalid token';
		unset($_SESSION['accessToken']);
	}

	die();
}
