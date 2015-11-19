(function( $ ) {
	'use strict';

	/**
	 * All of the code for your public-facing JavaScript source
	 * should reside in this file.
	 *
	 * Note that this assume you're going to use jQuery, so it prepares
	 * the $ function reference to be used within the scope of this
	 * function.
	 *
	 * From here, you're able to define handlers for when the DOM is
	 * ready:
	 *
	 * $(function() {
	 *
	 * });
	 *
	 * Or when the window is loaded:
	 *
	 * $( window ).load(function() {
	 *
	 * });
	 *
	 * ...and so on.
	 *
	 * Remember that ideally, we should not attach any more than a single DOM-ready or window-load handler
	 * for any particular page. Though other scripts in WordPress core, other plugins, and other themes may
	 * be doing this, we should try to minimize doing that in our own work.
	 */

	$( document ).ready(function() {
		
		var iscart = wc_add_to_cart_params["is_cart"] ;
	
		
		var val = $(".shipping_method").val();
		if ($(".shipping_method").attr("type")=="radio") val = $(".shipping_method:checked").val();
		
		if (val =="Fermo!Point"){
			//console.log($.cookie("fermopoint_session"));
			//$.cookie("fermopoint_session");
			if (iscart=="1"){
				$.ajax({
					type: 'POST',
					url: ajaxurl,
					dataType: 'json',
			
					'data':  { 
						action: 'resetfermopoint',
					}
				} );
			} else {
				fermopoint_collegamento(val);
			}

		}
	
	});
	
	$( document ).on( 'change', '.shipping_method, input[name^=shipping_method]', function() {
		
		var iscart  = wc_add_to_cart_params["is_cart"] ;
		
		if ($(this).val() =="Fermo!Point"){
			//$(".checkout-button").val("Scegli il Point di Fermo!Point e concludi l'ordine");
			//fermopoint_collegamento();			
			if (iscart=="1"){
				$.ajax({
					type: 'POST',
					url: ajaxurl,
					dataType: 'json',
			
					'data':  { 
						action: 'resetfermopoint',
					}
				} );
				
				$("#ship-to-different-address-checkbox").removeAttr("disabled");
				$("#fermopoint_input_shipping_field").hide();
				
			} else {
				fermopoint_collegamento($(this).val());
			}
		
		
			
		} else {
			$(".checkout-button").val("Concludi l'ordine");
			$("#ship-to-different-address-checkbox").removeAttr("disabled");
			$("#fermopoint_input_shipping_field").hide();
			
			
			
			//reset FermoPoint
			
			$.ajax({
				type: 'POST',
				url: ajaxurl,
				dataType: 'json',
		
				'data':  { 
					action: 'resetfermopoint',
				}
			} );
			
			
		}
	});
	
	$( document ).on( 'click', '.shipping_method, input[name^=shipping_method]', function() {
		
		var iscart  = wc_add_to_cart_params["is_cart"] ;
		//console.log(iscart);
		if ($(this).val() =="Fermo!Point"){
			//$(".checkout-button").val("Scegli il Point di Fermo!Point e concludi l'ordine");
			//fermopoint_collegamento();			
			if (iscart=="1"){
				
				
				$("#ship-to-different-address-checkbox").removeAttr("disabled");
				$("#fermopoint_input_shipping_field").hide();
			
			
				$.ajax({
					type: 'POST',
					url: ajaxurl,
					dataType: 'json',
			
					'data':  { 
						action: 'resetfermopoint',
					}
				} );
			} else {
				fermopoint_collegamento($(this).val());
			}
		
		
			
		} else {
			$(".checkout-button").val("Concludi l'ordine");
			$("#ship-to-different-address-checkbox").removeAttr("disabled");
			$("#fermopoint_input_shipping_field").hide();
			
			//reset FermoPoint
			
			$.ajax({
				type: 'POST',
				url: ajaxurl,
				dataType: 'json',
		
				'data':  { 
					action: 'resetfermopoint',
				}
			} );
			
			
		}
	});
	
	/*$( document ).on( 'click', '.checkout-button', function() {
		fermopoint_collegamento();
	});
	*/
	function fermopoint_collegamento(obj){
		//var shipping_methods = [];
			//--- Fermo!Point ---ons
			$("#ship-to-different-address-checkbox").attr("disabled","disabled");
			$("#ship-to-different-address-checkbox").attr("checked","checked");
			$("#fermopoint_input_shipping_field").show();
			$("#fermopoint_input_shipping").attr("disabled","disabled");
		
			var fermoPoint_ClientId="";
			var fermoPoint_ClientSecret="";
			var fermoPoint_Url="";
			//console.log($.cookie("fermopoint_invio"));
			
			if ((obj =="Fermo!Point") && (!$.cookie("fermopoint_invio"))){
				
					
							$.ajax({
								type: 'POST',
								url: ajaxurl,
								dataType: 'json',
						
								'data':  { 
									action: 'callfermopointapi',
								},
								success: function(results) {
									// uhm, maybe I don't even need this?
									
										//console.log(results);
										var result = results["result"];
										var obj = JSON.parse(result);
										
										var book_fermopoint = obj["Links"]["BookUrl"];
										//console.log(book_fermopoint);
										$(location).attr('href',book_fermopoint);
										//$("a.checkout-button").attr('href',book_fermopoint);
										
								},
								fail: function( jqXHR, textStatus, errorThrown ) {
									alert ('Could not get posts, server response: ' + textStatus + ': ' + errorThrown );
									console.log( 'Could not get posts, server response: ' + textStatus + ': ' + errorThrown );
								}
							} );
						
					
					
			}
		
		 
			
	}
	
})( jQuery );
