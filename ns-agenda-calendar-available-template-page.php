<?php
/** Template Name: Template Calendar Booking Available
*/
session_start();
get_header();

include 'ns-agenda-custom-modal.php';

$is_hourly = true;

$client_id = get_option('ns_agenda_outlook_client_id');
$client_secret = get_option('ns_agenda_outlook_client_secret');
$redirect_uri = get_permalink();

// OUTLOOK integrations after login redirect
if(isset($_GET['code'])){
	$code = $_GET['code'];
	
	$outlook = new OutlookIntegration($redirect_uri);
	$token = $outlook->authorization($code);
	if($token != 'error') {
		$_SESSION['accessToken'] = $token;
	}
	header("Location: $redirect_uri");
}

?>

	<div class="ns-template-calendar-page-container">
		<div class="ns-agenda-loader"></div> 
		<?php
		if(current_user_can('administrator')) {
			if(isset($_SESSION['accessToken'])){
			?>
			<div class="ns-template-calendar-export-row">
				<p>
					<button id="ns-agenda-import-outlook" class="ns-btn-front">Import Outlook events</button>
				</p>
				<div class="ns-agenda-loader ns-agenda-loader-import"></div> 
				
			</div>
			<div class="ns-template-calendar-export-row">
				<p>
					<button id="ns-agenda-export-outlook" class="ns-btn-front">Export to Outlook calendar</button>
				</p>
				<div class="ns-agenda-loader ns-agenda-loader-export"></div> 
			</div>
			<?php
			}
			else {
				if($client_id){
				?>
				<div class="ns-template-calendar-export-row">
					<p><a href="https://login.microsoftonline.com/common/oauth2/v2.0/authorize/?client_id=<?php echo $client_id;?>&redirect_uri=<?php echo $redirect_uri; ?>&response_type=code&scope=Calendars.Read">Outlook authentication</a></p>
				</div>
				<?php
				}
			}
		}
		?>
		<?php
			$calendar = new Calendar(true, false);		 
			echo $calendar->show(false);	
		?>
		
		<div class="ns-booking-calendar-form">
			<h3>Book Online</h3>
			<div class="ns-inner-booking-calendar-form">
				<form role="form" method="post">
					
					<div class="ns-form-group">				
						<label>Reservation Date</label>
						<input class="ns-border-radius ns-calendar-datepicker" id="ns-front-start-date" type="text" name="ns-front-start-date" readonly>
					</div>
							
					<?php 	
						$starting_day = get_option('ns_agenda_starting_year_period_opt');
						$ending_day = get_option('ns_agenda_ending_year_period_opt');
					?>	

						<div class="ns-form-group">
							<label>Description</label>
							<select class="ns-border-radius" id="ns-front-end-products" name="ns-front-end-products">
								<option></option>
							</select>
						</div>
						<div class="ns-form-group">
							<label>Starting Hour</label>
							<select id="ns-front-start-hour" name="ns-front-start-hour" required>
								<option></option>
							</select>
							<!--<input class="ns-border-radius" placeholder="10:00" type="text" name="ns-front-start-hour" pattern="[0-9]{2}:[0-9]{2}" title="ES: 09:30" required>-->
						</div>
						<div class="ns-form-group">
							<label>Ending Hour</label>
							<select id="ns-front-end-hour" name="ns-front-end-hour" required>
								<option></option>
							</select>
							<!--<input class="ns-border-radius" placeholder="12:00" type="text" name="ns-front-end-hour" pattern="[0-9]{2}:[0-9]{2}" title="ES: 09:30" required>-->						
						<?php 
						$start_week_h = get_option('ns_agenda_week_hour_start');
						$end_week_h = get_option('ns_agenda_week_hour_end');
						?>
							<div class="ns-tooltip">Time Frames
  								<span class="tooltiptext"><?php foreach ($start_week_h as $key => $value) {
  									if($value != '')
  									echo $key. ': '. $value. '-'. $end_week_h[$key]. '<br>';
  								else echo $key. ': Not Available'. '<br>';
  								} ?></span>
							</div>

						</div>	

					
					<button class="ns-btn-front" type="submit" name="ns-front-submit">Submit</button>
				</form>
			</div>
		</div>
			
		<div class="ns-agenda-calendar-legend-container">
			<div class="ns-agenda-calendar-legend-inner">
				<div class="ns-agenda-outer-box-legend">
					<div class="ns-agenda-legend-box ns-agenda-all-available-legend">

					</div>	
					<span>All Available</span>		
				</div>
				<div class="ns-agenda-outer-box-legend">
					<div class="ns-agenda-legend-box ns-agenda-some-available-legend">
				
					</div>
					<span>Available</span>
				</div>
				<div class="ns-agenda-outer-box-legend">
					<div class="ns-agenda-legend-box ns-agenda-not-available-legend">
				
					</div>
					<span>Not Available</span>
				</div>
			</div>
		</div>

	</div>

	<div id="ns-modal-agenda-notification-booking" class="ns-agenda-modal">			 
		<div id="ns-modal-agenda-booking" class="ns-agenda-modal-content">				
			<div id="ns-success-notification-modal" class=" ns-display-none">
				<div class="ns-img-container-booking-modal">
					<img src=<?php echo BOOKING_NS_PLUGIN_DIR_URL.'img/check-symbol.png';?>>
				</div>
				<div class="ns-button-dialog-field">
					<div>
						<h4>Product added to cart. Click on Checkout button or add more booking.</h4>	
						<button class="ns-btn-front" type="button" onclick="window.location.href='<?php echo get_permalink(get_page_by_title('Agenda')->ID); ?>'">Ok</button>				
					</div>
				</div>
			</div>
			
			<div id="ns-not-avaiable-notification-modal" class=" ns-display-none">
				<div class="ns-img-container-booking-modal">
					<img src=<?php echo BOOKING_NS_PLUGIN_DIR_URL.'img/large-exclamation-mark.png';?>>
				</div>
				<div class="ns-button-dialog-field">
					<div>
						<h4 id="ns-not-avaiable-title">Product not avaiable in choosen period</h4>	
						<button class="ns-btn-front-alert" type="button" onclick="window.location.href='<?php echo get_permalink(get_page_by_title('Agenda')->ID); ?>'">Ok</button>				
					</div>
				</div>
			</div> 
			
			<div id="ns-empty-field-notification-modal" class=" ns-display-none">
				<div class="ns-img-container-booking-modal">
					<img src=<?php echo BOOKING_NS_PLUGIN_DIR_URL.'img/large-exclamation-mark-default.png';?>>
				</div>
				<div class="ns-button-dialog-field">
					<div>
						<h4>Empty Fields</h4>	
						<button class="ns-btn-front" type="button" onclick="window.location.href='<?php echo get_permalink(get_page_by_title('Agenda')->ID); ?>'">Ok</button>				
					</div>
				</div>
			</div> 
		</div>
	</div>

	<?php

	if(isset($_POST['ns-front-submit'])){
		ns_agenda_save_booking_front();
	}

	function ns_agenda_save_booking_front(){
		$is_hourly = true;

		$is_custom_hourly = true;
		

		$start_date = null;
		$start_hour = null;
		$end_hour = null;
		$prod = null;
		$page_post = "";
		
		/*Variables used to get the valid period set by admin backend*/
		// $start_validity_period = get_option('ns_agenda_starting_year_period_opt');
		// $start_validity_period =   strtotime($start_validity_period);
	  
		// $end_validity_period = get_option('ns_agenda_ending_year_period_opt');
		// $end_validity_period = strtotime($end_validity_period);
		
		$error = false;
		
		/*if(isset($_POST['ns-front-booking-name']))
		{
			 $booking_name = sanitize_text_field($_POST['ns-front-booking-name']);
		}
		else{
			$error = true;
		}
		*/
		if(isset($_POST['ns-front-start-date']))
		{
			 $start_date = sanitize_text_field($_POST['ns-front-start-date']);
			 $start_date = strtotime($start_date);
			
			//converting dates to check if data has beeen injected with a date in the past
			$current_date = time();
			if($start_date < $current_date){
				echo '<script language="javascript">';
					echo 'jQuery("#ns-modal-agenda-notification-booking").css("display","block");';
					echo 'jQuery("#ns-not-avaiable-title").empty();';
					echo 'jQuery("#ns-not-avaiable-title").append("You cannot book in the past!");';
					echo 'jQuery("#ns-not-avaiable-notification-modal").removeClass("ns-display-none");';
				echo '</script>';
				return false;
			}
			
			
			$start_date = sanitize_text_field($_POST['ns-front-start-date']);

			/*else{
				echo '<script language="javascript">';
					echo 'jQuery("#ns-modal-notification-booking").css("display","block");';
					echo 'jQuery("#ns-not-avaiable-title").empty();';
					echo 'jQuery("#ns-not-avaiable-title").append("Selected date is out of customer days disponibility period");';
					echo 'jQuery("#ns-not-avaiable-notification-modal").removeClass("ns-display-none");';
				echo '</script>';
				return;
			}*/
		}
		else{
			$error = true;
		}
		
		if($is_hourly){
			if(isset($_POST['ns-front-start-hour']))
			{
				 $start_hour = sanitize_text_field($_POST['ns-front-start-hour']);
			}
			else{
				$error = true;
			}
			
			if(isset($_POST['ns-front-end-hour']))
			{
				 $end_hour = sanitize_text_field($_POST['ns-front-end-hour']);
			}
			else{
				$error = true;
			}
		}
			
		
		if(isset($_POST['ns-front-end-products']))
		{
			 $prod = sanitize_text_field($_POST['ns-front-end-products']);
			 //$page_post = get_page_by_title($prod, OBJECT, 'product');
		}
		else{
			$error = true;
		}
				
		if($error){
			echo '<script language="javascript">';
					echo 'jQuery("#ns-modal-agenda-notification-booking").css("display","block");';
					echo 'jQuery("#ns-empty-field-notification-modal").removeClass("ns-display-none");';
			echo '</script>';
			return $error;
		}	
		else{	
			$is_ok = ns_agenda_check_booking_prod_avaiability_back_hour($start_date, $start_date,$start_hour, $end_hour, $prod,false);	
			if($is_ok){
				echo 'Booking inserted';
			}
			else{
				echo '<script>';
					echo 'jQuery("#ns-modal-agenda-notification-booking").css("display","block");';
					echo 'jQuery("#ns-not-avaiable-notification-modal").removeClass("ns-display-none");';
				echo '</script>';
				return true;
			}
		}
		
		//all inputs are set, create a new booking post
			 //insert post and get the id
			/*$post = array(
				'post_title' =>  $booking_name,
				'post_status' => 'publish',
				'post_type' => 'ns_agenda',
			);
		
			$post_id = wp_insert_post($post);
			$error = update_post_meta($post_id, "date_in", $start_date);
			$error = update_post_meta($post_id, "date_out", $start_date);
			$error = update_post_meta($post_id, "hour_in", $start_hour);
			$error = update_post_meta($post_id, "hour_out", $end_hour);
			$error = update_post_meta($post_id, "product_name", $prod);*/

			$booking_quantity = ns_agenda_quantity($start_date, $start_date, $start_hour, $end_hour);
			//update_post_meta($post_id, 'ns_agenda_quantity', $booking_quantity);
			
			//update_post_meta($post_id, "product_id", $page_post->ID);
			/*if is a bookable product update the price with bookable price before insert into cart*/
			$is_bookable = get_post_meta($prod, '_bookable',true);
			if($is_bookable == 'yes'){
				 $type_select = 'hourly';
    			
				//$type_select = get_post_meta($page_post->ID, 'ns_booking_select_type', true);
				$booked_price = get_post_meta($prod, 'ns_agenda_'.$type_select.'_price', true);
				update_post_meta($prod, '_price', $booked_price );
			}
			
			$cart_item_data = array(
					//"ns_agenda_id"=>$post_id,
					"ns_agenda_name"=>$booking_name,
				 	"ns_agenda_date_in"=>$start_date,
				  	"ns_agenda_date_out"=>$start_date,
				   	"ns_agenda_hour_in"=>$start_hour,
				   	"ns_agenda_hour_out"=>$end_hour
				   );

			global $woocommerce;
            $add_key = $woocommerce->cart->add_to_cart($prod, $booking_quantity, $variation_id = 0, $variation = array(), $cart_item_data);
           /* $cart_key_opt = get_option('ns_agenda_cart_key', array());
            $cart_key_opt[$add_key] = $post_id;
            update_option('ns_agenda_cart_key', $cart_key_opt);*/
            
			echo '<script>';
				echo 'jQuery("#ns-modal-agenda-notification-booking").css("display","block");';
				echo 'jQuery("#ns-success-notification-modal").removeClass("ns-display-none");';
				echo 'jQuery("body").addClass("ns-agenda-modal-open");';
			echo '</script>';
			
	}
	
	//update every day product on ns_agenda_option_struct option
	//si può togliere
	/*function ns_update_booking_prod_avaiability($start_d, $end_d, $woo_id){
		$option = get_option('ns_agenda_option_struct');
		$begin = new DateTime($start_d);
		$end = new DateTime($end_d);
		$end->add(new DateInterval('P1D'));	//adding 1 day to let the next foreach to get all days in interval

		$daterange = new DatePeriod($begin, new DateInterval('P1D'), $end);
		$is_ok = true;
		foreach($daterange as $day){
			$day = $day->format('d-m-Y');
			
			if($option[$day][$woo_id] == 0){
				$is_ok = false;
				echo '<pre>'; print_r($option[$day]); echo '</pre>';
			}
			else{
				echo 'else';
				echo '<pre>'; print_r($option[$day]); echo '</pre>';
				$var = $option[$day][$woo_id];
				$option[$day][$woo_id] = $var - (1);
			}
		}
		if($is_ok) update_option( 'ns_agenda_option_struct', $option );
		return $is_ok;
	}*/

get_footer();
?>