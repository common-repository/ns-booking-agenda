<?php
/**********AJAX Calls**********/

/*Service for ajax call to update product description on backend after filtering*/
add_action( 'wp_ajax_nopriv_ns_update_booking_prod_desc', 'ns_agenda_update_booking_prod_desc' );
add_action( 'wp_ajax_ns_update_booking_prod_desc', 'ns_agenda_update_booking_prod_desc' );
function ns_agenda_update_booking_prod_desc(){
	
	if(isset($_POST["ns_update_desc"])){
		$prod_name = sanitize_text_field($_POST["ns_update_desc"]);
		
		$post_product_selected = get_page_by_title( $prod_name, OBJECT, 'product');
		$post_product_selected_meta = get_post_meta($post_product_selected->ID);
		$img_url = wp_get_attachment_image_src($post_product_selected_meta['_thumbnail_id'][0]);
		if($img_url == ''){
			$img_url = BOOKING_NS_PLUGIN_DIR_URL.'img/placeholder.png';
		}
		else{
			$img_url = $img_url[0];
		}
		echo '<div class="ns-inner-prod-info">';
			echo '<h3>Product information :</h3>';
			echo '<img src="'.$img_url.'"></img>';	
			echo '<div id="ns-text-info-prod">';
				echo '<div class="ns-text-info-prod-div-marg" style="margin-top: 0px;">';
					echo '<div><label>Sale Price :</label></div>';
					echo '<div><span>'.$post_product_selected_meta['_sale_price'][0].'</span></div>';			
				echo '</div>';
				echo '<div class="ns-text-info-prod-div-marg">';	
					echo '<div><label>Regular Price :</label></div>';
					echo '<div><span>'.$post_product_selected_meta['_regular_price'][0].'</span></div>';			
				echo '</div>';
				echo '<div class="ns-text-info-prod-div-marg">';
					echo '<div><label>Height :</label></div>';		
					echo '<div><span>'.$post_product_selected_meta['_height'][0].'</span></div>';			
				echo '</div>';
				echo '<div class="ns-text-info-prod-div-marg">';
					echo '<div><label>Width :</label></div>';
					echo '<div><span>'.$post_product_selected_meta['_width'][0].'</span></div>';			
				echo '</div>';
				echo '<div class="ns-text-info-prod-div-marg">';
					echo '<div><label>Length :</label></div>';	
					echo '<div><span>'.$post_product_selected_meta['_length'][0].'</span></div>';			
				echo '</div>';				
				echo '<div class="ns-text-info-prod-div-marg">';
					echo '<div><label>Stock Status :</label></div>';	
					echo '<div><span>'.$post_product_selected_meta['_stock_status'][0].'</span></div>';			
				echo '</div>';
			echo '</div>';
		echo '</div>';
			die();
    }
	die();
}

/*This service for ajax returns all the bookable product inserted by the logged user. No Need to filter by category
add_action( 'wp_ajax_nopriv_ns_filter_booking_field_front', 'ns_agenda_filter_booking_field_front' );
add_action( 'wp_ajax_ns_filter_booking_field_front', 'ns_agenda_filter_booking_field_front' );
function ns_agenda_filter_booking_field_front(){
	
	if(isset($_POST["action"])){
		$bookable_prod = get_posts(array( 'posts_per_page' => -1, 'post_type' => 'product'));
		
		//loop over all post product with the selected category	
		foreach($bookable_prod as $prod_post){
		
			if(get_post_meta($prod_post->ID, '_bookable', true) == 'yes'){
				echo '<option value="'.$prod_post->post_title.'">'.$prod_post->post_title.'</option>'; 
			}	
							
		}
		die();
	}
	die(); 
}
*/

/*This service returns starting hour based on products selected*/
add_action( 'wp_ajax_nopriv_ns_filter_start_hour_by_prod_template_form', 'ns_filter_start_hour_by_prod_template_form' );
add_action( 'wp_ajax_ns_filter_start_hour_by_prod_template_form', 'ns_filter_start_hour_by_prod_template_form' );
function ns_filter_start_hour_by_prod_template_form(){
	if(isset($_POST["action"])){
		if(isset($_POST["date"]) && $_POST["date"] != ''){
			$id = sanitize_text_field($_POST['prod_id']);
			$date = sanitize_text_field($_POST["date"]);
			$year = explode('-',$date);
			$current_year = $year[2];
			$option = get_option('ns_agenda_option_struct_'.$current_year);
			$arr_date = $option[$date];
			$arr_starting_hour = array();
			foreach($arr_date as $hour => $h){
				if($h['available']){
					foreach($h as $key => $value){				
						$is_hour_disp = true;
						if($key != 'available'){
							if($key == $id && $value != 0){
								array_push($arr_starting_hour, $hour);				
							}	
						}							
					}
				}
			}
			if($is_hour_disp){
				foreach($arr_starting_hour as $hour){
					echo '<option value="'.$hour.'">'.$hour.'</option>';
				}
			}
			
		}
	}
}

/*This service returns ending hour based on starting hour selected*/
add_action( 'wp_ajax_nopriv_ns_filter_end_hour_by_start_hour_template_form', 'ns_filter_end_hour_by_start_hour_template_form' );
add_action( 'wp_ajax_ns_filter_end_hour_by_start_hour_template_form', 'ns_filter_end_hour_by_start_hour_template_form' );
function ns_filter_end_hour_by_start_hour_template_form(){
	if(isset($_POST["action"])){
		if(isset($_POST["date"]) && $_POST["date"] != ''){
			$id = sanitize_text_field($_POST['prod_id']);
			$date = sanitize_text_field($_POST["date"]);
			$year = explode('-',$date);
			$current_year = $year[2];
			$option = get_option('ns_agenda_option_struct_'.$current_year);
			$arr_date = $option[$date];
			
			$start_hour = intval(sanitize_text_field($_POST['start_hour']));
			$arr_ending_hour = array();

			foreach($arr_date as $hour => $h){
				if(intval($hour) >= $start_hour){		
					if($h['available']){
						foreach($h as $key => $value){				
							$is_hour_disp = true;
							if($key != 'available'){
								if($key == $id && $value != 0){
									array_push($arr_ending_hour, $hour);				
								}	
								else if($key == $id && $value == 0){
									array_push($arr_ending_hour, $hour);
									array_push($arr_ending_hour, 'stop');									
								}								
							}							
						}
					}
				}
			}
			if($is_hour_disp){
				$prev_hour = '';
				$after_h = intval(end($arr_ending_hour)) + 1;
				$str_after = $after_h. ":00";
				array_push($arr_ending_hour, $str_after);
				array_shift($arr_ending_hour);
				foreach($arr_ending_hour as $hour){
					if($hour != 'stop'){
						echo '<option value="'.$hour.'">'.$hour.'</option>';
						$prev_hour = $hour;
					}
					else {
						/*$timestamp = strtotime($prev_hour) + 60*60;
						$time = date('H:i', $timestamp);
						echo '<option value="'.$time.'">'.$time.'</option>';*/
						die();	
					}			
				}
			}
			
		}
	}
}


/*This service for ajax returns all the bookable product inserted by the logged user. No Need to filter by category*/
add_action( 'wp_ajax_nopriv_ns_filter_modal_date_front', 'ns_agenda_filter_modal_date_front' );
add_action( 'wp_ajax_ns_filter_modal_date_front', 'ns_agenda_filter_modal_date_front' );
function ns_agenda_filter_modal_date_front(){
	
	if(isset($_POST["date_filter"]) && $_POST["date_filter"] != ''){
		$date = sanitize_text_field($_POST["date_filter"]);
		$hour_start = get_option('ns_agenda_week_hour_start');
		$hour_end = get_option('ns_agenda_week_hour_end');
		$date_d = new DateTime($date);
		$begin_hour = $hour_start[$date_d->format("l")];
		$end_hour = $hour_end[$date_d->format("l")];
		$tStart = strtotime($begin_hour);
		$tEnd = strtotime($end_hour);

		$tNow = $tStart;
		//loop over all post product with the selected category	
		while($tNow < $tEnd) {
					echo '<option value="'.date("H:i",$tNow).'">'.date("H:i",$tNow).'</option>'; 
					$tNow = strtotime('+1 hour',$tNow);
				}
		die();
	}
	die(); 
}

/*This service for ajax returns all the bookable product inserted by user, filtered by avaiability date hour*/
add_action( 'wp_ajax_nopriv_ns_filter_prod_template_form', 'ns_filter_prod_template_form' );
add_action( 'wp_ajax_ns_filter_prod_template_form', 'ns_filter_prod_template_form' );
function ns_filter_prod_template_form(){
	
	if(isset($_POST["date"]) && $_POST["date"] != ''){
		$date = sanitize_text_field($_POST["date"]);
		$year = explode('-',$date);
		$current_year = $year[2];
		$option = get_option('ns_agenda_option_struct_'.$current_year);
		$arr_date = $option[$date];
		
		$arr_products = array();
		foreach($arr_date as $hour => $h){
			$is_hour_disp = false;
			foreach($h as $key => $value){
				if($h['available']){
					$is_hour_disp = true;
					if($key != 'available'){
						if($value != 0){
							if(!in_array($key, $arr_products)){
								array_push($arr_products,$key);
							}	
						}
						
					}
				}	
			}
		}
		foreach($arr_products as $prod){
			$_product = wc_get_product( $prod );
			echo '<option value="'.$prod.'">'.$_product->name.'</option>';
		}
		die();
	}
	die(); 
}

/*Service for ajax call to fill days products modal*/
add_action( 'wp_ajax_nopriv_ns_modal_days_prod_front', 'ns_agenda_modal_days_prod_front' );
add_action( 'wp_ajax_ns_modal_days_prod_front', 'ns_agenda_modal_days_prod_front' );
function ns_agenda_modal_days_prod_front(){
	$date = '';
	if(isset($_POST["date"]) && $_POST["date"] != ''){
		$date = sanitize_text_field($_POST["date"]);

	
		$current_day = date('d-m-Y',strtotime($date));  
		$current_year = date('Y', strtotime($date));
		$arr_availability = get_option('ns_agenda_option_struct_'.$current_year);
		
		/************************HOURLY CASE****************************/
			?>
			<table>
				<tr>
					<th>Hour</th>
					<th>Product:</th>
				</tr>
				<?php	
				
				foreach ($arr_availability[$current_day] as $hour => $h) { 
					if(is_array($h)){	//check in case 'ns_booking_type_select' == 'booking_hourly' but 'ns_agenda_option_struct' is still set as daily struct
				?>
					
						<?php 
							echo '<tr '.'id="ns-agenda-td-modal'.intval($hour).'">';
						$s = false;		
						
						if($h['available']){
							$hour_a = strtotime($hour);
							 $hour_a = strtotime('+1 hour',$hour_a);
								echo '<td>'.$hour.'-'.date("H:i",$hour_a).'</td>';				
						}

						foreach ($h as $key => $value) {
							if($h['available']){
								if($key != 'available'){
									if($value != 0 || $value != null){								
										
											$s = $s.get_the_title($key).", ";	
										
									}

								}
							}						
						} 
						
						if($s){ 
								echo '<td>'.$s.'</td>'; 
																
							}
						else{
							echo '<script>jQuery("#ns-agenda-td-modal'.intval($hour).'").remove();</script>';
						}
						?>
					</tr>
			<?php	} $prev_hour = $hour;
			} ?>
			</table>
  <?php 
	}
 die();
}


/*Service for ajax call to fill personal booked products modal*/
add_action( 'wp_ajax_nopriv_ns_modal_personal_days_prod_front', 'ns_agenda_modal_personal_days_prod_front' );
add_action( 'wp_ajax_ns_modal_personal_days_prod_front', 'ns_agenda_modal_personal_days_prod_front' );
function ns_agenda_modal_personal_days_prod_front(){
	$date = '';
	if(isset($_POST["date"]) && $_POST["date"] != ''){
		$date = sanitize_text_field($_POST["date"]);

	
		$current_day = date('d-m-Y',strtotime($date));  
		$current_user = wp_get_current_user();   
		$args = array(
			'author'        =>  $current_user->ID,
			'orderby'       =>  'post_date',
			'order'         =>  'ASC',
			'post_type'		=>	'ns_agenda',
			'posts_per_page' => -1
		);
        
		$booking_posts = get_posts( $args );	//all booking posts of current user
		
		/*************************PERSONAL BOOKING CALENDAR CASE*************************/
		?>
		<table>
			<tr>
				<th>Booking:</th>
				<th>Description:</th>
				<th>Date:</th>
				<th>Booking starting hour:</th>
				<th>Booking ending hour:</th>
			</tr>
		<?php	
		foreach($booking_posts as $post){
			$date_in = get_post_meta($post->ID, 'date_in', true);
			$hour_in = get_post_meta($post->ID, 'hour_in', true);
			$hour_out = get_post_meta($post->ID, 'hour_out', true);
			$prod_name = get_post_meta($post->ID, 'product_name', true);
			
			$link = get_post_permalink($post->ID);
			if($date_in == $current_day){
				echo '<tr>
						<td>
							<a href="'.$link.'">BOOKING: '.$post->post_title.'</a>
						</td>
						<td>
							<p>'.$prod_name.'</p>
						</td>
						<td>
							<p>'.$date_in.'</p>
						</td>
						<td>
							<p>'.$hour_in.'</p>
						</td>
						<td>
							<p>'.$hour_out.'</p>
						</td>
					</tr>';
			}
							
		}
		?>
		</table>
		<?php
	}
	die();
}
?>