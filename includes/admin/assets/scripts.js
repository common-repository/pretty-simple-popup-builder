jQuery(document).ready(function ($) {
// On Page Load:
	show_hide_template_settings($('input:radio[name=_psp_popup_template]:checked').val());
	$('input[id=_psp_ajax_check]').closest('table').hide();
	$('h2:contains("Optional Advanced Settings")').css( "cursor", "pointer" );

// On Radio Click:
	$('.psp-age-header-option input').on('click', function() {
		show_hide_template_settings($('input:radio[name=_psp_popup_template]:checked').val());
	});

// Flip fields to color picker UI
	$('#_psp_box_color').wpColorPicker();
	$('#_psp_overlay_color').wpColorPicker();
	$('#_psp_agree_btn_bgcolor').wpColorPicker();
	$('#_psp_disAgree_btn_bgcolor').wpColorPicker();

	var pspbtn = document.getElementById("psp-clear-cookie");
	if ( typeof pspbtn !== 'undefined' && pspbtn !== null ) {
		pspbtn.innerHTML = "No Cookie Set";
		pspbtn.disabled = true;
		var arrCookies = document.cookie.split(/; */);
		$.each(arrCookies, function(index, item) {
			var cookieitem = item.split(/=/);
			$.each(cookieitem, function(index2, item2) {
				if (item2.startsWith("psp-popup-displayed-")) {
					pspbtn.innerHTML = "Clear Your Cookie";
					pspbtn.disabled = false;
				}
			});
		});
	}

	/* Testing the Logo Image onLoad */
	var csl_logo_url_val = $("#psp_logo_field_id").val();
	csl_logo_testImage(csl_logo_url_val);

	function csl_logo_testImage(URL) {
		if (URL != "Select Image" && URL) {
			var tester = new Image();
			tester.onerror = csl_logo_imageNotFound;
			tester.src = URL;
		}
	}

	function csl_logo_imageNotFound() {
		alert("That image was not found.");
	}

	$('#psp_logo_button').click(function (e) {
		e.preventDefault();
		var csl_CustomSiteLogo_uploader = wp.media({
			title: 'Select or upload an image',
			button: {text: 'Select Image'},
			multiple: false
		}).on('select', function () {
			var attachment = csl_CustomSiteLogo_uploader.state().get('selection').first().toJSON();
			$('#psp_logo_field_id').val(attachment.url);
			$('.psp_logo_container').html("<IMG SRC='" + attachment.url + "'><BR>Save Changes to Set Image");
		}).open();
	});

	$('#psp_logo_delete_button').click(function (e) {
		e.preventDefault();
		$('#psp_logo_field_id').val('');
		$('.psp_logo_container').html("Save Changes to Remove Image");
	});

	$('h2:contains("Optional Advanced Settings")').on('click', function() {
		$('input[id=_psp_ajax_check]').closest('table').fadeToggle();
	});

	// Hide needed cookie name field
	//$('th:contains("Current Cookie Name")').hide();
	$('input[id=_psp_new_cookie_name]').closest('tr').hide();

	// Main Show/Hide Template toggle logic
	function show_hide_template_settings(templ_num) {
		// Image popup check
		if ( templ_num == 'free-2' || templ_num == 'prem-2' ) {
			var psp_submit_img_check = $("#psp_logo_field_id").val();
			if ( ! psp_submit_img_check || psp_submit_img_check == "Select Image" ) {
				$('.psp_logo_container').html('<p style="color:red;"><strong>Image template requires an image to be set.</strong></p>');
			}
		}
		if ( templ_num == 'prem-1' ) {
			var psp_submit_img_check = $("#psp_logo_field_id").val();
			if ( ! psp_submit_img_check || psp_submit_img_check == "Select Image" ) {
				$('.psp_logo_container').html('');
			}
		}

		// Submit Img Check
		if ( templ_num == 'free-2-off' || templ_num == 'prem-2-off' ) {
			$('#submit').click(function (e) {
				var psp_submit_img_check = $("#psp_logo_field_id").val();
				if ( ! psp_submit_img_check || psp_submit_img_check == "Select Image" ) {
					e.preventDefault();
					$('.psp_logo_container').html('<p style="color:red;"><strong>Image template requires an image to be set.</strong></p>');
					setTimeout(function(){
						$('.psp_logo_container').html('<p> </p>');
					}, 400);
					setTimeout(function(){
						$('.psp_logo_container').html('<p style="color:red;"><strong>Image template requires an image to be set.</strong></p>');
					}, 800);
				}
			});
		}

		// BANNER: Options f2 and p1-2 (not free-1)
		if ( templ_num !== 'free-1' ) {
			$('.psp_logo_outer').show();
			$('h2:contains("- IMAGE")').fadeIn();
			$('input[id=psp_logo_button]').closest('table').fadeIn();
		} else {
			$('h2:contains("- IMAGE")').fadeOut();
			$('input[id=psp_logo_button]').closest('table').fadeOut();
		}
		// BUTTON Templ f1, p1, p2
		if ( templ_num !== 'free-2' ) {
			$('h2:contains("- CTA BUTTON")').fadeIn();
			$('input[id=_psp_custom_agreebutton_text]').closest('table').fadeIn();
		} else {
			$('h2:contains("- CTA BUTTON")').fadeOut();
			$('input[id=_psp_custom_agreebutton_text]').closest('table').fadeOut();
		}
		// COPY and MODAL BOX Template Options free-1 and prem-1
		if ( templ_num == 'free-1' || templ_num == 'prem-1' ) {
			$('h2:contains("- COPY")').fadeIn();
			$('input[id=_psp_heading]').closest('table').fadeIn();
			$('h2:contains("- MODAL")').fadeIn();
			$('input[id=_psp_box_color]').closest('table').fadeIn();
			// Prem-1 Temp Only - Edge to Edge Option
			if ( templ_num == 'prem-1' ) {
				$('input[id=_psp_edge_to_edge_image]').closest('tr').fadeIn();
			} else {
				$('input[id=_psp_edge_to_edge_image]').closest('tr').fadeOut();
			}
		} else {
			$('h2:contains("- COPY")').fadeOut();
			$('input[id=_psp_heading]').closest('table').fadeOut();
			$('h2:contains("- MODAL")').fadeOut();
			$('input[id=_psp_box_color]').closest('table').fadeOut();
			$('input[id=_psp_edge_to_edge_image]').closest('tr').fadeOut();
		}
		// FULL SCREEN and TEXT LINK: Only Premium 3 and 4
		if ( templ_num == 'prem-1' || templ_num == 'prem-2' ) {
			$('h2:contains("- SUPPORTING TEXT LINK")').fadeIn();
			$('input[id=_psp_custom_disagreebutton_text]').closest('table').fadeIn();
			$('h2:contains("- FULL SCREEN")').fadeIn();
			$('input[id=_psp_overlay_color]').closest('table').fadeIn();
		} else {
			$('h2:contains("- SUPPORTING TEXT LINK")').fadeOut();
			$('input[id=_psp_custom_disagreebutton_text]').closest('table').fadeOut();
			$('h2:contains("- FULL SCREEN")').fadeOut();
			$('input[id=_psp_overlay_color]').closest('table').fadeOut();
		}
	}

	// show character count
	$.fn.maxLen = function (maxLen) {
		var elm = $(this);
		var textSelector = Math.random().toString(10).substr(2);
		if (maxLen == null)
			var maxLen = $(elm).attr('maxlength');

		$(elm).after('<div id="txt-length-left' + textSelector + '"></div>');
		elm.keypress(function (event) {
			var Length = elm.val().length + 1;
			var AmountLeft = maxLen - Length;
			$('#txt-length-left' + textSelector).html(AmountLeft + " Characters left");
			if (Length - 1 >= maxLen ) {
				$('#txt-length-left' + textSelector).html("0 Characters left");
				if (event.which != 8) {
					return false;
				}
			}
		});
	};
	var $body = $('body');
	$body.find('#_psp_custom_age_text').maxLen();
	$body.find('#_psp_custom_agreebutton_text').maxLen();
	$body.find('#_psp_custom_disagreebutton_text').maxLen();
	$body.find('#_psp_disagree_error_text').maxLen();
	$body.find('#_psp_headline').maxLen();
	$body.find('#_psp_description').maxLen();
	$body.find('#_psp_heading').maxLen();
	$body.find('#_psp_disclaimer').maxLen();

	$('.psppremhovertip').hover(function(e){ // Hover event
		var titleText = $(this).attr('title');
		$(this).data('tiptext', titleText).removeAttr('title');
		$('<p class="psppremtooltip" style="display: none; z-index:999; position: absolute; padding: 10px; color: #555; background-color: #fff; border: 1px solid #777;	box-shadow: 0 1px 3px 1px rgba(0,0,0,0.5); border-radius: 3px;"></p>').text(titleText).appendTo('body').css('top', (e.pageY - 10) + 'px').css('left', (e.pageX + 20) + 'px').fadeIn('slow');
	}, function(){ // Hover off event
		$(this).attr('title', $(this).data('tiptext'));
		$('.psppremtooltip').remove();
	}).mousemove(function(e){ // Mouse move event
		$('.psppremtooltip').css('top', (e.pageY - 10) + 'px').css('left', (e.pageX + 20) + 'px');
	});

	$('.pspoptionshovertip').hover(function(e){ // Hover event
		var titleText = $(this).attr('title');
		$(this).data('tiptext', titleText).removeAttr('title');
//		$('<p class="pspoptiontooltip"></p>').text(titleText).appendTo('body').css('top', (e.pageY - 4 ) + 'rem').css('left', (e.pageX + 0 ) + 'px').fadeIn('slow');
		$('<p class="pspoptiontooltip"></p>').text(titleText).appendTo('body').css('top', ($(this).offset().top  - 25) + 'px').css('left', ($(this).offset().left + 5 ) + 'px').fadeIn('slow');
	}, function(){ // Hover off event
		$(this).attr('title', $(this).data('tiptext'));
		$('.pspoptiontooltip').remove();
//	}).mousemove(function(e){ // Mouse move event
//		$('.pspoptiontooltip').css('top', (e.pageY - 10) + 'px').css('left', (e.pageX + 20) + 'px');
	});

});

function psp_clear_cookie() {
	var pspbtn = document.getElementById("psp-clear-cookie");
	console.log('Clearing cookie!');
	var arrCookies2 = document.cookie.split(/; */);
	jQuery.each(arrCookies2, function(index, item) {
		var cookieitem = item.split(/=/);
		jQuery.each(cookieitem, function(index2, item2) {
			if (item2.startsWith("psp-popup-displayed-")) {
				$pspoldcookiename = item2.trim();
				console.log('-=- Clearing out cookie: ' + $pspoldcookiename);
				document.cookie=$pspoldcookiename + '=;Path=/;Expires=Thu, 01 Jan 1970 00:00:01 GMT;';
			}
		});
	});
	console.debug('Cleared!');
	pspbtn.innerHTML = "Cookie Cleared";
	pspbtn.disabled = true;
	setTimeout(function(){
		pspbtn.innerHTML = "No Cookie Set";
	}, 4000);
	return false;
}

// Embed jQuery js-cookies file to remove remote call
/*!
 * JavaScript Cookie v2.2.1
 * Minified by jsDelivr using Terser v3.14.1.
 * Original file: /npm/js-cookie@2.2.1/src/js.cookie.js
 */
!function(e){var n;if("function"==typeof define&&define.amd&&(define(e),n=!0),"object"==typeof exports&&(module.exports=e(),n=!0),!n){var t=window.Cookies,o=window.Cookies=e();o.noConflict=function(){return window.Cookies=t,o}}}(function(){function e(){for(var e=0,n={};e<arguments.length;e++){var t=arguments[e];for(var o in t)n[o]=t[o]}return n}function n(e){return e.replace(/(%[0-9A-Z]{2})+/g,decodeURIComponent)}return function t(o){function r(){}function i(n,t,i){if("undefined"!=typeof document){"number"==typeof(i=e({path:"/"},r.defaults,i)).expires&&(i.expires=new Date(1*new Date+864e5*i.expires)),i.expires=i.expires?i.expires.toUTCString():"";try{var c=JSON.stringify(t);/^[\{\[]/.test(c)&&(t=c)}catch(e){}t=o.write?o.write(t,n):encodeURIComponent(String(t)).replace(/%(23|24|26|2B|3A|3C|3E|3D|2F|3F|40|5B|5D|5E|60|7B|7D|7C)/g,decodeURIComponent),n=encodeURIComponent(String(n)).replace(/%(23|24|26|2B|5E|60|7C)/g,decodeURIComponent).replace(/[\(\)]/g,escape);var f="";for(var u in i)i[u]&&(f+="; "+u,!0!==i[u]&&(f+="="+i[u].split(";")[0]));return document.cookie=n+"="+t+f}}function c(e,t){if("undefined"!=typeof document){for(var r={},i=document.cookie?document.cookie.split("; "):[],c=0;c<i.length;c++){var f=i[c].split("="),u=f.slice(1).join("=");t||'"'!==u.charAt(0)||(u=u.slice(1,-1));try{var a=n(f[0]);if(u=(o.read||o)(u,a)||n(u),t)try{u=JSON.parse(u)}catch(e){}if(r[a]=u,e===a)break}catch(e){}}return e?r[e]:r}}return r.set=i,r.get=function(e){return c(e,!1)},r.getJSON=function(e){return c(e,!0)},r.remove=function(n,t){i(n,"",e(t,{expires:-1}))},r.defaults={},r.withConverter=t,r}(function(){})});
