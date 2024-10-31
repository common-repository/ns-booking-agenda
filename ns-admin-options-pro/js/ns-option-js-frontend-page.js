jQuery( document ).ready(function() {   
   /*Ajax for filter frontend products*/

		/*jQuery.ajax({
			url : nsfilterbookingcatfront.ajax_url,
			type : 'post',
			data : {
				action : 'ns_filter_booking_field_front'
				
			},
			success : function( response ) {
				jQuery('#ns-front-end-products').empty();
				jQuery('#ns-front-end-products').append(response);				
			},
			error: function(errorThrown){
				alert(errorThrown.responseText);
			} 
		});//onready and on change working*/
   
   /*jQuery("#ns-front-start-date").change(function() {
	var date_filter = jQuery("#ns-front-start-date").val();
   jQuery.ajax({
			url : nsfiltermodaldatefront.ajax_url,
			type : 'post',
			data : {
				action : 'ns_filter_modal_date_front',
				date_filter : date_filter
				
			},
			success : function( response ) {
				jQuery('#ns-front-start-hour').empty();
				jQuery('#ns-front-start-hour').append('<option></option>');
				jQuery('#ns-front-start-hour').append(response);
								
			},
			error: function(errorThrown){
				alert(errorThrown.responseText);
			} 
		});//onready and on change working
});*/

/*Filter from product to get avaiability*/
jQuery("#ns-front-start-date").change(function() {
  		var prodname = jQuery('#ns-front-end-products').val();
		var date_f = jQuery('#ns-front-start-date').val();
  		jQuery.ajax({
			url : nsfilterprodtemplateform.ajax_url,
			type : 'post',
			data : {	
				action : 'ns_filter_prod_template_form',
				prod_name : prodname,	
				date : date_f
			},
			success : function( response ) {
				jQuery('#ns-front-end-products').empty();
				jQuery('#ns-front-end-products').append('<option></option>');
				jQuery('#ns-front-end-products').append(response);
								
			},
			error: function(errorThrown){
				alert(errorThrown.responseText);
			} 
		});
});

/*Filter on product and get available hour*/
jQuery("#ns-front-end-products").change(function() {
  		var prodid = jQuery('#ns-front-end-products').val();
		var date_f = jQuery('#ns-front-start-date').val();
  		jQuery.ajax({
			url : nsfilterstarthourbyprodtemplateform.ajax_url,
			type : 'post',
			data : {	
				action : 'ns_filter_start_hour_by_prod_template_form',
				prod_id : prodid,	
				date : date_f
			},
			success : function( response ) {
				jQuery('#ns-front-start-hour').empty();
				jQuery('#ns-front-start-hour').append('<option></option>');
				jQuery('#ns-front-start-hour').append(response);
			},
			error: function(errorThrown){
				alert(errorThrown.responseText);
			} 
		});
});

/*Filter on product and get available ending hour*/
jQuery("#ns-front-start-hour").change(function() {
  		var prodid = jQuery('#ns-front-end-products').val();
		var date_f = jQuery('#ns-front-start-date').val();
		var st_hour = jQuery('#ns-front-start-hour').val();
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
			
				jQuery('#ns-front-end-hour').empty();
				jQuery('#ns-front-end-hour').append('<option></option>');
				jQuery('#ns-front-end-hour').append(response);
					
			},
			error: function(errorThrown){
				alert(errorThrown.responseText);
			} 
		});
});

  	
	
	/*jQuery("#ns-front-start-hour").change(function() {
  		jQuery('#ns-front-end-hour').empty();
  		var sel = parseInt(jQuery('#ns-front-start-hour').val());
  		var r = 0;
  		jQuery('#ns-front-start-hour').find('option').each(function() {
    		var response = jQuery(this).val();
    		var rint = parseInt(response);
    		if(rint > sel){
  				//jQuery('#ns-front-end-hour').empty();
				jQuery('#ns-front-end-hour').append('<option>'+response+'</option>');
			}
			r = rint;
		});

	if (jQuery('#ns-front-start-hour').val() != ''){
		var r = r+1;
  		jQuery('#ns-front-end-hour').append('<option>'+r+':00</option>');	
	}
   });*/
   
   //Calendar Modal avaiable calendar + AJAX 
	jQuery(document).on("click", ".ns-open-modal", function () {			
		datebooking = jQuery(this).find('.booking-date').val();
		jQuery.ajax({
			type: "POST",
			url: nsmodaldaysprodfront.ajax_url,
			data: {
				action: 'ns_modal_days_prod_front',
				date: datebooking
			},
			success: function (output) {
				jQuery('.ns-modal-agenda-calendar-products-inner-container').empty();
				jQuery('.ns-modal-agenda-calendar-products-inner-container').append(output);
				jQuery('#ns-agenda-day-modal').show();
				jQuery("body").addClass("ns-agenda-modal-open");
				jQuery('#ns-modal-agenda-calendar-products').show();
				
			}
		 });

	});
	
	//Calendar Modal Personal Calendar + AJAX 
	jQuery(document).on("click", ".ns-open-personal-calendar-modal", function () {			
		datebooking = jQuery(this).find('.booking-date').val();
		jQuery.ajax({
			type: "POST",
			url: nsmodalpersonaldaysprodfront.ajax_url,
			data: {
				action: 'ns_modal_personal_days_prod_front',
				date: datebooking
			},
			success: function (output) {
				jQuery('.ns-modal-agenda-calendar-products-inner-container').empty();
				jQuery('.ns-modal-agenda-calendar-products-inner-container').append(output);
				jQuery('#ns-agenda-day-modal').show();
				jQuery('#ns-modal-agenda-calendar-products').show();
				
			}
		 });

	});
	
	jQuery('#ns-agenda-day-modal').click(function(e) {
		var clicked = jQuery(e.target); 	
		if (clicked.is('#ns-modal-agenda-calendar-products') || clicked.parents().is('#ns-modal-agenda-calendar-products')) {
			if(!clicked.is('a')){	//let user click on product link in the modal
				return;
			}
		}
		else{						//clicked on overlay so close the modal
			jQuery('#ns-agenda-day-modal').fadeOut(400, function() {
			});
			jQuery("body").removeClass("ns-agenda-modal-open");
		}
		
    });
   
   //datepicker
    var dateToday = new Date(); 
    jQuery( function() {
		jQuery( ".ns-calendar-datepicker" ).datepicker({
		  dateFormat: "dd-mm-yy",
		  minDate: dateToday
		});
	});
	
	/*Contact form*/
	jQuery("#ns-contact-form").submit(function() {
		var r = confirm("Clicking on 'Ok' button a notification will be sent to the customer in order to cancel your booking.\n\nAre you sure you want to delete this booking?");
		if (r == false) 
			return false;

		return true;
	});
	
	/*Legend section. Dinamically create legend div based on height and width of others div*/
	if(jQuery( window ).width() > 870){
		var calendarHeight = jQuery('#calendar').height();
		var calendarFormHeight = jQuery('.ns-booking-calendar-form').height();
		jQuery('.ns-agenda-calendar-legend-inner').height(calendarHeight - calendarFormHeight - 15);
		var boxHeight = jQuery('.ns-agenda-calendar-legend-inner').height();
		jQuery('.ns-agenda-legend-box').height(boxHeight/6);
		jQuery('.ns-agenda-legend-box').width(boxHeight/6);
	}
	
});