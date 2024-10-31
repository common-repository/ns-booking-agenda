<?php	


if( ! defined( 'WP_UNINSTALL_PLUGIN' ) ) 			exit();	

	delete_option('ns_agenda_is_hourly');
	delete_option('ns_agenda_starting_year_period_opt');
	delete_option('ns_agenda_ending_year_period_opt');
	$struct_years = get_option ('ns_agenda_year_struct', array());
		foreach ($struct_years as $ns_year) {
			delete_option( 'ns_agenda_option_struct_'.$ns_year);
		}
	delete_option('ns_agenda_year_struct');
	delete_option('ns_agenda_week_hour_start');
	delete_option('ns_agenda_week_hour_end');
	delete_option('ns_agenda_cancelled_agenda_email');
	delete_option('ns_agenda_custom_hour_week');
	delete_option('ns_agenda_cart_key');
	$args     = array( 'post_type' => 'ns_agenda', 'posts_per_page' => -1 );
	$bookings = get_posts( $args );
		foreach ($bookings as $book) {
		 	wp_delete_post( $book->ID);
		 } 


?>