<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
?>

<div class="ns-backend-container">
	<div class="wrap">
		<h3>Outlook integration settings</h3>
		<table>
			<tr>
				<th>Client ID</th>
				<td>
					<input class="ns-backend-input" id="ns_agenda_outlook_client_id" name="ns_agenda_outlook_client_id" value="<?php echo get_option('ns_agenda_outlook_client_id'); ?>"/>
				</td>
				<td>
					<div class="ns-tooltip"> (?) <span class="tooltiptext">This is your Outlook AD Client ID application.</span></div>
				</td>
			</tr>
			<tr>
				<th>Client Secret</th>
				<td>
					<input class="ns-backend-input" id="ns_agenda_outlook_client_secret" name="ns_agenda_outlook_client_secret" value="<?php echo get_option('ns_agenda_outlook_client_secret'); ?>"/>
				</td>
				<td>
					<div class="ns-tooltip"> (?) <span class="tooltiptext">This is your Outlook AD Client Secret application.</span></div>
				</td>
			</tr>
		</table>
	</div>
</div>

<div class="ns-backend-container">
	<div class="wrap">
		<h3>Where can I find my Client ID and Client Secret?</h3>
		<ul style="list-style: inside;">
			<li>First of all, you need to register an app in Microsoft Azure Panel. Visit <a href="https://portal.azure.com/" target="_blank">this URL</a>.</li>
			<li>Click on Azure Active Directory and <b>App registrations</b>.</li>
			<li>Here you can create a new Application by click on <b>New Registration</b>. Lets call it 'Ns-Agenda'. Click on 'Register' and return to <b>App registrations</b> panel.</li>
			<li>Now you can notice your app 'Ns-Agenda' is listed here. Click on 'Ns-Agenda'.</li>
			<li>Here you can get the <b>Client ID</b> we need. <a href="<?php echo BOOKING_NS_PLUGIN_DIR_URL.'ns-admin-options-pro/img/example1.PNG';?>" target="_blank"><img style="width: 250px; margin-top: 25px; border: 2px solid #6BAA01;" src="<?php echo BOOKING_NS_PLUGIN_DIR_URL.'ns-admin-options-pro/img/example1.PNG';?>"></a></li>
			<li>Now we need to create a Client Secret. Click on 'Certificates & Secrets' tab.</li>
			<li>Under the 'Client secrets' section just click 'New client secret' to generate a <b>Client secret</b>. You can also specify a description and a expiration time. Clck 'Add' button.</li>
			<li>Now you can get the <b>Client Secret</b>. <a href="<?php echo BOOKING_NS_PLUGIN_DIR_URL.'ns-admin-options-pro/img/example2.PNG';?>" target="_blank"><img style="width: 250px; margin-top: 25px; border: 2px solid #6BAA01;" src="<?php echo BOOKING_NS_PLUGIN_DIR_URL.'ns-admin-options-pro/img/example2.PNG';?>"></a></li>
			<li>Few more things to go. Click 'Authentication' tab.</li>
			<li>On redirect URIs section select type 'Web' and type your agenda page full site URI (e.g. 'http://your-domain.com/agenda/'). Click on 'Save' button. </li>
			<li>On 'API permissions' tab we have to grant the priviege to access calendar to our plugin.</li>
			<li>Click 'Add a permissions' button.</li>
			<li>Click 'Microsoft Graph' button and 'Delegated permissions'. In the list below check 'Calendars.ReadWrite'. <a href="<?php echo BOOKING_NS_PLUGIN_DIR_URL.'ns-admin-options-pro/img/example3.PNG';?>" target="_blank"><img style="width: 250px; margin-top: 25px; border: 2px solid #6BAA01;" src="<?php echo BOOKING_NS_PLUGIN_DIR_URL.'ns-admin-options-pro/img/example3.PNG';?>"></a></li>
			<li>Setup is over. We are ready to go.</li>
		</ul>
	</div>
</div>

<div class="ns-backend-container">
	<div class="wrap">
		<h3>What now?</h3>
		<p>Once you have the Outlook account setted up copy the Client ID and Client Secret in the above inputs and click the save button.</p>
		<p>Now in your Agenda page you can click on <b>Outlook authentication</b> and insert your Microsoft Outlook email/password. Once the authentication is completed you will be redirected to the <b>agenda</b> page.</p>
		<p>You can now:</p>
		<ul style="list-style: inside;">
			<li>Import Outlook calendar events to Ns-Booking-Agenda plugin.</li>
			<li>Export your booking data to your Outlook Calendar.</li>
		<ul>
		
		<p><b>Important:</b> When you activate this plugin, a default bookable product, named 'Outlook', will be created to grant you the possibility to import into NS-Booking-Agenda the Outlook events.</p>
	</div>
</div>