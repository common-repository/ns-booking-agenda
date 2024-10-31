jQuery( document ).ready(function() {
	/*Ajax for filter backend products*/
	/*jQuery('#ns-category-booking').change(function($) {
		var ns_selected_category_booking_prod = jQuery(this).val();
		
		
		jQuery.ajax({
			url : nsfilterbookingcat.ajax_url,
			type : 'post',
			data : {
				action : 'ns_filter_booking_field',
				ns_filter_cat :  ns_selected_category_booking_prod
			},
			success : function( response ) {
				jQuery('#ns-products-booking').empty();
				if(jQuery('#ns-products-booking-saved').val() != ''){
					var value = '<option value="'+jQuery('#ns-products-booking-saved').val()+'">'+jQuery('#ns-products-booking-saved').val()+'</options>';
					jQuery('#ns-products-booking').append(value);
					jQuery('#ns-products-booking-saved').val('');
				}
				else{
					jQuery('#ns-products-booking').append(response);
				}
				
				
			}
		});
	}).change();	//onready and on change working*/

/*	jQuery.ajax({
			url : nsfilterbookingcatfront.ajax_url,
			type : 'post',
			data : {
				action : 'ns_filter_booking_field_front'
				
			},
			success : function( response ) {
				jQuery('#ns-products-booking').empty();
				jQuery('#ns-products-booking').append(response);				
			},
			error: function(errorThrown){
				alert(errorThrown.responseText);
			} 
		});//onready and on change working


	   */


  	/*Filter from product to get avaiability*/
jQuery("#ns-starting-booking-date").change(function() {
  		var prodname = jQuery('#ns-products-booking').val();
		var date_f = jQuery('#ns-starting-booking-date').val();
  		jQuery.ajax({
			url : nsfilterprodtemplateform.ajax_url,
			type : 'post',
			data : {	
				action : 'ns_filter_prod_template_form',
				prod_name : prodname,	
				date : date_f
			},
			success : function( response ) {
				jQuery('#ns-products-booking').empty();
				jQuery('#ns-products-booking').append('<option></option>');
				jQuery('#ns-products-booking').append(response);
								
			},
			error: function(errorThrown){
				alert(errorThrown.responseText);
			} 
		});
});

/*Filter on product and get available hour*/
jQuery("#ns-products-booking").change(function() {
  		var prodid = jQuery('#ns-products-booking').val();
		var date_f = jQuery('#ns-starting-booking-date').val();
  		jQuery.ajax({
			url : nsfilterstarthourbyprodtemplateform.ajax_url,
			type : 'post',
			data : {	
				action : 'ns_filter_start_hour_by_prod_template_form',
				prod_id : prodid,	
				date : date_f
			},
			success : function( response ) {
				jQuery('#ns-starting-booking-hour').empty();
				jQuery('#ns-starting-booking-hour').append('<option></option>');
				jQuery('#ns-starting-booking-hour').append(response);
			},
			error: function(errorThrown){
				alert(errorThrown.responseText);
			} 
		});
});

/*Filter on product and get available ending hour*/
jQuery("#ns-starting-booking-hour").change(function() {
  		var prodid = jQuery('#ns-products-booking').val();
		var date_f = jQuery('#ns-starting-booking-date').val();
		var st_hour = jQuery('#ns-starting-booking-hour').val();
  		jQuery.ajax({
			url : nsfilterendhourbystarthourtemplateform.ajax_url,
			type : 'post',
			data : {	
				action : 'ns_filter_end_hour_by_start_hour_template_form',
				prod_id : prodid,	
				date : date_f,
				start_hour: st_hour
			},
			success : function( response ) {
			
				jQuery('#ns-ending-booking-hour').empty();
				jQuery('#ns-ending-booking-hour').append('<option></option>');
				jQuery('#ns-ending-booking-hour').append(response);
					
			},
			error: function(errorThrown){
				alert(errorThrown.responseText);
			} 
		});
});

	
	/*Ajax for update product info on backend*/
	/*jQuery('#ns-products-booking').change(function($) {
		var ns_selected_products_booking = jQuery(this).val();
		
		
		jQuery.ajax({
			url : nsupdateprodinfo.ajax_url,
			type : 'post',
			data : {
				action : 'ns_update_booking_prod_desc',
				ns_update_desc :  ns_selected_products_booking
			},
			success : function( response ) {
				jQuery('.ns-selected-booking-prod-info').empty();
				jQuery('.ns-selected-booking-prod-info').append(response);
				
			}
		});
	});*/

	jQuery('#_bookable').change(function(){
    if (jQuery(this).is(':checked')) {
       jQuery('#_virtual').prop( "checked", true );
 	 }
 	 else{
 	 	jQuery('#_virtual').prop( "checked", false);
 	 }
	}); 
   
   //datepicker
   var dateToday = new Date(); 
    jQuery( function() {
		jQuery( ".ns-calendar-datepicker" ).datepicker({
		  dateFormat: "dd-mm-yy",
		  minDate: dateToday
		});
	} );
	
	/*Option TAB 3 check Email*/
	jQuery('#ns_cancelled_booking_email').change(function () {
		var regex = /^([a-zA-Z0-9_.+-])+\@(([a-zA-Z0-9-])+\.)+([a-zA-Z0-9]{2,4})+$/;
		var email = jQuery('#ns_cancelled_booking_email').val();
		regex.test(email);
		jQuery('#ns-not-val-email').addClass('ns-display-none');
		jQuery('#ns-valid-email').addClass('ns-display-none');
	    if (!(regex.test(email))) {
			jQuery('#ns_cancelled_booking_email').css('border', '1px solid #a34747');
			jQuery('#ns-not-val-email').removeClass('ns-display-none');
			jQuery('#submit').attr('disabled', true);
		}
		else{
			jQuery('#ns_cancelled_booking_email').css('border', '1px solid #78dd61');
			jQuery('#ns-valid-email').removeClass('ns-display-none');
			jQuery('#submit').attr('disabled', false);
		}
	});

	var daysofWeek = ['Sunday','Monday','Tuesday','Wednesday','Thursday','Friday','Saturday'];
	jQuery( ".ns_booking_week_hour_end" ).change(function() {
 		 for (var i=0; i < daysofWeek.length; i++) {
			var ids = '#ns_booking_week_hour_start_' + daysofWeek[i];
			var ide =  '#ns_booking_week_hour_end_' + daysofWeek[i];
			if (jQuery(ids).val() > jQuery(ide).val()  && jQuery(ids).val() != '') { alert('the end time not be before the star time');}
	}
});

	jQuery( ".ns_booking_week_hour_start" ).change(function() {
 		 for (var i=0; i < daysofWeek.length; i++) {
			var ids = '#ns_booking_week_hour_start_' + daysofWeek[i];
			var ide =  '#ns_booking_week_hour_end_' + daysofWeek[i];
			if (jQuery(ids).val() > jQuery(ide).val() && jQuery(ide).val() != '' ) { alert('the end time not be before the star time');}
	}
});

});