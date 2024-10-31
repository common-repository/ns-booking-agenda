<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}


function ns_agenda_activate_set_options()
{
	add_option('ns_agenda_is_hourly','');
	add_option('ns_agenda_custom_hour_week', '');
	add_option('ns_agenda_week_hour_start', '');
	add_option('ns_agenda_week_hour_end', '');
	add_option('ns_agenda_cancelled_agenda_email','');
	add_option('ns_agenda_outlook_client_id','');
	add_option('ns_agenda_outlook_client_secret','');
}

register_activation_hook( __FILE__, 'ns_agenda_activate_set_options');



function ns_agenda_register_options_group()
{
	register_setting('ns_agenda_options_group', 'ns_agenda_is_hourly');
	register_setting('ns_agenda_options_group', 'ns_agenda_custom_hour_week');
	register_setting('ns_agenda_options_group', 'ns_agenda_week_hour_start');
	register_setting('ns_agenda_options_group', 'ns_agenda_week_hour_end');

	register_setting('ns_agenda_contact_option_group', 'ns_agenda_cancelled_agenda_email');
	
	register_setting('ns_export_outlook_options_group', 'ns_agenda_outlook_client_id');
	register_setting('ns_export_outlook_options_group', 'ns_agenda_outlook_client_secret');
}

add_action ('admin_init', 'ns_agenda_register_options_group');


?>