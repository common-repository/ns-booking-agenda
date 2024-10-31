jQuery( document ).ready(function($) {   
	
	$('#ns-agenda-import-outlook').on( "click", function() {
	  $('#ns-agenda-import-outlook').hide();
	  $('.ns-agenda-loader-import').show();
	  
	  jQuery.ajax({
			url : nsoutlookimport.ajax_url,
			type : 'post',
			data : {	
				action : 'ns_agenda_import_outlook_events',
			},
			success : function( response ) {
				$('#ns-agenda-import-outlook').show();
				$('.ns-agenda-loader-import').hide();
				alert(response);
					
			},
			error: function(errorThrown){
				alert(errorThrown.responseText);
				$('#ns-agenda-import-outlook').show();
				$('.ns-agenda-loader-import').hide();
			} 
		});
	});

});