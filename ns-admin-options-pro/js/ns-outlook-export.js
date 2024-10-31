jQuery( document ).ready(function($) {   
	
	$('#ns-agenda-export-outlook').on( "click", function() {
		
	  $('#ns-agenda-export-outlook').hide();
	  $('.ns-agenda-loader-export').show();
	  jQuery.ajax({
			url : nsoutlookimport.ajax_url,
			type : 'post',
			data : {	
				action : 'ns_agenda_export_outlook_events',
			},
			success : function( response ) {
				// console.log(response);
				$('#ns-agenda-export-outlook').show();
				$('.ns-agenda-loader-export').hide();
				console.log(response);
					
			},
			error: function(errorThrown){
				alert(errorThrown.responseText);
				$('#ns-agenda-export-outlook').show();
				$('.ns-agenda-loader-export').hide();
			} 
		});
	});

});