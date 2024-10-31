<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
?>
<div class="ns-backend-container">
	<div class="wrap">
		<h3>Email you want to receive cancelled booking</h3>
		<table>
			<tr>
			<th>Email</th>
			<td>
			<input class="ns-backend-input" id="ns_agenda_cancelled_agenda_email" name="ns_agenda_cancelled_agenda_email" value="<?php echo get_option('ns_agenda_cancelled_agenda_email'); ?>"/>
			</td>
			<span id="ns-not-val-email" class="ns-display-none">Email is not valid</span>
			<span id="ns-valid-email" class="ns-display-none">Email is valid</span>
			<td>
			<div class="ns-tooltip"> (?) <span class="tooltiptext">By default, mail will be sent to administrator user.</span></div>
			</td>
			</tr>
		</table>
	</div>
</div>