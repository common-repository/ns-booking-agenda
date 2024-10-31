<?php
/** Template Name: Template Calendar Booking
*/
get_header();

?>

	<div class="ns-template-calendar-page-container">
		<?php	
			$calendar = new Calendar(false, false);		 
			echo $calendar->show(true);
		?>
		
		<div class="ns-booking-calendar-form">
			<h3>Add booking event</h3>
			<div class="ns-inner-booking-calendar-form">
				<form role="form" method="post">
					<div class="ns-form-group">
						<label>Booking Name</label>
						<input class="ns-border-radius" type="text" name="ns-front-booking-name">
					</div>
					<div class="ns-form-group">
						<label>Starting Date</label>
						<input class="ns-border-radius ns-calendar-datepicker" type="text" name="ns-front-start-date">
					</div>
					<div class="ns-form-group">
						<label>Ending Date</label>
						<input class="ns-border-radius ns-calendar-datepicker" type="text" name="ns-front-end-date">
					</div>
					<div class="ns-form-group">
						<label>Starting Hour</label>
						<input class="ns-border-radius" type="number" name="ns-front-start-hour">
					</div>
					<div class="ns-form-group">
						<label>Ending Hour</label>
						<input class="ns-border-radius" type="number" name="ns-front-end-hour">
					</div>
					
					<?php
						$all_existent_cat = get_terms( array(
																'taxonomy' => 'product_cat',
																'hide_empty' => false,
															));	
														
						$cat = get_post_meta($post->ID, 'product_category', true);						
					?>
					<div class="ns-form-group ns-border-top">
						<label>Category</label>
						<select class="ns-border-radius" id="ns-front-end-category" name="ns-front-end-category">
							<?php	
							foreach($all_existent_cat as $cat_obj){											
								echo '<option value="'.$cat_obj->name.'">'.$cat_obj->name.'</option>';
							}				
						?>			
						</select>
					</div>
					<div class="ns-form-group">
						<label>Products</label>
						<select class="ns-border-radius" id="ns-front-end-products" name="ns-front-end-products">
							<option></option>
						</select>
					</div>
					<button class="ns-btn-front" type="submit" name="ns-front-submit">Submit</button>
				</form>
			</div>
		</div>
	</div>


<?php
get_footer();
?>