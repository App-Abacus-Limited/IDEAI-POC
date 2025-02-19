(function($){

	"use strict";

	$( document ).ready(function() {

		/**
		* General Functions
		*/

		// Initialize SemanticUI Menu Functions

		// radio buttons
		$('.ui.checkbox').checkbox();

		// accordions
		$('.ui.accordion').accordion();

		// Tab transition effect
		var previous = $('.ui.tab.segment.active');
	    $(".menu .item").tab({
	        onVisible: function (e) {
	            var current = $('.ui.tab.segment.active');
	            // hide the current and show the previous, so that we can animate them
	            previous.show();
	            current.hide();

	            // hide the previous tab - once this is done, we can show the new one
	            previous.find('.marketkingpro_attached_content_wrapper').css('opacity','0');
	            current.find('.marketkingpro_attached_content_wrapper').css('opacity','0');
	            setTimeout(function(){
	            	previous.hide();
	            	current.show();
	            	setTimeout(function(){
		            	current.find('.marketkingpro_attached_content_wrapper').css('opacity','1');
		            	// remember the current tab for next change
		            	previous = current;
		            },10);
	            },150);
	            
	        }
	    });
	    
		$('.ui.dropdown').dropdown();
	
		$('.message .close').on('click', function() {
		    $(this).closest('.message').transition('fade');
		});

		// On Submit (Save Settings), Get Current Tab and Pass The Tab as a Setting. 
		$('#marketkingpro_admin_form').on('submit', function() {
			let tabInput = document.querySelector('#marketkingpro_current_tab_setting_input');
		    tabInput.value = document.querySelector('.active').dataset.tab;
		    return true; 
		});


	});

})(jQuery);
