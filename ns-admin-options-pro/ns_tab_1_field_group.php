<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
?>
<div class="ns-backend-container">
	<div class="wrap">
		<div style="border-bottom: 1px solid #e3e3e3; margin-bottom: 20px;">
			<h3>Plugin Setup</h3>
			<!--<p class="description" style="color:#990000">Changing any of the following option will erase all the previous booking</p>-->
		</div>

	<div id="ns-backend-custom-hour-table">
		<table>
			<tr valign="top">
				<th scope="row">
					 
				</th>
			   <td>
					<input type="text" name="ns_agenda_booking_custom_hour_week" id="ns_agenda_booking_custom_hour_week" value="1" hidden> 
			   </td>

			</tr>
			<tr class="ns_booking_week_hour_start"> 
				<th>
					<label></label>
					<div class="ns-tooltip"> (?)
						<span class="tooltiptext" id="ns-tooltip-hour-daily-table">Choose custom hour on days of the week.  A blank field will disable the specified day.
						</span>
					</div>
				</th>
				<th>From</th>
				<th>To</th>
			</tr>
			<?php $option_start = get_option('ns_agenda_week_hour_start'); 
				$timestamp = strtotime('next Sunday');
				for ($i = 0; $i < 7; $i++) {
					$days = strftime('%A', $timestamp);
					 ?>
			<tr>
				<th class="ns_booking_week_hour_start"><?php echo $days ?></th>
				<td>
				<select name="ns_agenda_week_hour_start[<?php echo $days ?>]" id="ns_agenda_week_hour_start_<?php echo $days ?>" class="ns_agenda_week_hour_start" > 
			<option value="" <?php selected( $option_start[$days], "");?>></option>
				<?php 
					$begin_hour = "00:00";
					$end_hour = "24:00";
					$tStart = strtotime($begin_hour);
					$tEnd = strtotime($end_hour);
					$tNow = $tStart;
					while($tNow < $tEnd) {
							echo "<option value=\"".date("H:i",$tNow).'"'. selected( $option_start[$days], date("H:i",$tNow)  ).">".date("H:i",$tNow)."</option>";
							$tNow = strtotime('+1 hour',$tNow);
						}
				?>
				</select>
				</td>
				<td>
				<?php $option_end = get_option('ns_agenda_week_hour_end'); ?>
			<select name="ns_agenda_week_hour_end[<?php echo $days ?>]" id="ns_agenda_week_hour_end_<?php echo $days ?>" class="ns_booking_week_hour_end">
			<option value="" <?php selected( $option_end[$days], "");?>></option> 
				<?php 
					$begin_hour = "00:00";
					$end_hour = "24:00";
					$tStart = strtotime($begin_hour);
					$tEnd = strtotime($end_hour);
					$tNow = $tStart;
					while($tNow < $tEnd) {
							echo "<option value=\"".date("H:i",$tNow).'"'. selected( $option_end[$days], date("H:i",$tNow), date("H:i",$tNow)  ).">".date("H:i",$tNow)."</option>";
							$tNow = strtotime('+1 hour',$tNow);
						}
				?>
				<option value="24:00" <?php selected( $option_end[$days], "24:00");?>>24:00</option>
				</select>
				</td>
			</tr>
			<?php	$timestamp = strtotime('+1 day', $timestamp);
				}	
		?>
			
		</table>
	</div>

	</div>

</div>