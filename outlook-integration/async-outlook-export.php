<?php

add_action( 'wp_ajax_nopriv_ns_agenda_export_outlook_events', 'ns_agenda_export_outlook_events' );
add_action( 'wp_ajax_ns_agenda_export_outlook_events', 'ns_agenda_export_outlook_events' );
function ns_agenda_export_outlook_events(){
	session_start();

	if(isset($_SESSION['accessToken'])){
		$token = $_SESSION['accessToken'];
		// echo $token;
		$args = array(
			'Authorization' => 'Bearer '.$token,
			'Content-type' => 'application/json'
		);
		
		// $posts = get_posts([
		  // 'post_type' => 'ns_agenda',
		  // 'post_status' => 'publish',
		  // 'numberposts' => -1
		// ]);
		
		$query_args = array(
			'post_type' => 'ns_agenda',
			'post_status' => 'publish',
			'posts_per_page' => -1,
			'meta_query' => array(
				array(
					'key'     => 'outlook_already_exported',
					'value'   => 'false',
					'compare' => '=',
				),
			),
		);
		$query = new WP_Query( $query_args );
		$posts = $query->posts;
		foreach($posts as $post) {
			$date_in = get_post_meta($post->ID, 'date_in', true);

			$start = get_post_meta($post->ID, 'hour_in', true);
			$end = get_post_meta($post->ID, 'hour_out', true);
			
			
			$start_date = date('Y-m-d\TH:i:s', strtotime($date_in.' '.$start));
			$end_date =  date('Y-m-d\TH:i:s', strtotime($date_in.' '.$end));
			
			$fields = [
				'subject'      => $post->post_title,
				'body' => ['contentType' => 'HTML', 'content' => $post->post_title],
				'start' => ["dateTime" => $start_date, "timeZone" => "Pacific Standard Time"],
				'end' => ["dateTime" => $end_date, "timeZone" => "Pacific Standard Time"]
			];

			$response = wp_remote_post( 'https://graph.microsoft.com/v1.0/me/calendar/events', array(
				'headers' => $args,
				'body'    => json_encode($fields)
				
			) );
			
			$body = json_decode(wp_remote_retrieve_body($response));
			
			// Setting outlook already exported to true, in this way this product will not be re-exported
			update_post_meta($post->ID, "outlook_already_exported", 'true');
		}
		echo 'Success';
	}
	else {
		echo 'Invalid token';
		unset($_SESSION['accessToken']);
	}

	die();
}
