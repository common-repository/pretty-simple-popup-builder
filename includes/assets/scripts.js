jQuery(document).ready(function ($) {

	// Check for global variable settings
	let $body = $('body'),
		$pspoverlay = $('#psp-overlay-wrap'),
		$pspheader = $('#psp-overlay h1'),
		$pspsubhead = $('#psp-overlay p'),
		$delayTime = 5,
		$cookieLength = 1,
		$pspcookiename = 'psp-popup-displayed-1234';

	// Set tabindex for better one-tab ADA compliance
	$('#psp_confirm_age').attr("tabindex", 1);
	$('#psp_not_confirm_age').attr("tabindex", 1);
	$('#psp_verify_remember').attr("tabindex", 1);

	// get body classes to parse
	var classList = $body.attr('class').split(/\s+/);
	// loop each class to check if it's one to set
	$.each(classList, function(index, item) {
		// Add delay timer check here
		if (item.startsWith("psp-delay-")) {
			$delayTime = parseInt(item.substring(10), 10) * 1000;
		}
		// Add cookie length check here
		if (item.startsWith("psp-cookie-")) {
			$cookieLength = parseInt(item.substring(11), 10);
		}
		// Add cookie name check here
		if (item.startsWith("psp-popup-displayed-")) {
			$pspcookiename = item.trim();
			//console.log('+++ BODY Cookie name: ' + $pspcookiename);
		}
	});

	// Maybe check/clear old cookie names out?
	var arrCookies = document.cookie.split(/; */);
	$.each(arrCookies, function(index, item) {
		var cookieitem = item.split(/=/);
		$.each(cookieitem, function(index2, item2) {
			if (item2.startsWith("psp-popup-displayed-")) {
				$pspoldcookiename = item2.trim();
				if ( $pspoldcookiename != $pspcookiename ) {
					//console.log('-=- DELETE old cookie: ' + $pspoldcookiename);
					document.cookie=$pspoldcookiename + '=;Path=/;Expires=Thu, 01 Jan 1970 00:00:01 GMT;';
				} else {
					//console.log(' - FOUND Cookie EQUALS Current cookie: ' + $pspoldcookiename);
				}
			}
		});
	});

	if ( psp_ajax_object !== undefined && Cookies.get($pspcookiename) === undefined ) {
		//console.log('*** No cookie detected, set for displaying popup with delay time set: ' + $delayTime);
		// Add ajax check option body class check, if exists
		if ( $body.hasClass('psp-ajax-check') == true ) {
		// set status
		$.get( psp_ajax_object.ajax_url, { action: 'psp_get_status' })
			.success( function(resp) {
				let pspstatus = resp;
				psp_eval_overlay_display(pspstatus);
				});
		} else {
			if ( $body.hasClass('psp-disabled') == true ) pspstatus = 'disabled';
			if ( $body.hasClass('psp-admin-only') == true ) pspstatus = 'admin-only';
			if ( $body.hasClass('psp-guests') == true ) pspstatus = 'guests';
			if ( $body.hasClass('psp-all') == true ) pspstatus = 'all';
			psp_eval_overlay_display(pspstatus);
		}
	} else if ( $body.hasClass('psp-admin-only') == true ) { // If cookie set and in testing mode, still display popup
		//console.log(' ** Cookie detected but in testing mode, set for displaying popup with delay time set: ' + $delayTime);
		pspstatus = 'admin-only';
		psp_eval_overlay_display(pspstatus);
	}

	function psp_eval_overlay_display(pspstatus) {
		if ( $body.hasClass('psp-prem') == true ) {
			// Check for our Easy Age Verify plugin popup, wait for it if displayed
			if ($('#evav-overlay-wrap:visible').length == 1) {
				$("#evav_confirm_age").on('click touch', function () {
					setTimeout(function(){
						psp_eval_overlay_display(pspstatus);
					}, 500);
				});
				return;
			}
			// Check for our Mari Age Verify plugin popup, wait for it if displayed
			if ($('#emav-overlay-wrap:visible').length == 1) {
				$("#emav_confirm_age").on('click touch', function () {
					setTimeout(function(){
						psp_eval_overlay_display(pspstatus);
					}, 500);
				});
				return;
			}
		}
		var pspbuttonurl = $('.psp_buttons:first a').attr('href');
		if ( $.inArray( pspstatus , [ 'all', 'guests', 'admin-only' ]) > -1 ){
			if ( pspstatus === 'all' ) {
				setTimeout(function(){
					if (window.location.toString().includes(pspbuttonurl)) {
						console.log('PSP: URL matches button, popup skipped.');
					} else {
					// $body.css('position', 'fixed');
						$('#paint').fadeIn(500);
						$body.css('width', '100%');
						$pspoverlay.fadeIn();
						// psp_setcookie($cookieLength,$pspcookiename);
					}
				}, $delayTime);
			}
			else if ( $body.hasClass('logged-in') !== true && pspstatus === 'guests') {
				setTimeout(function(){
					if (window.location.toString().includes(pspbuttonurl)) {
						console.log('PSP: URL matches button, popup skipped.');
					} else {
					// $body.css('position', 'fixed');
					$body.css('width', '100%');
					$pspoverlay.fadeIn();
					// psp_setcookie($cookieLength,$pspcookiename);
				}
				 }, $delayTime);
			}
			else if ( $body.hasClass('logged-in') == true && $body.hasClass('administrator') == true && pspstatus === 'admin-only') {
				setTimeout(function(){
					if (window.location.toString().includes(pspbuttonurl)) {
						console.log('PSP: URL matches button, popup skipped.');
					} else {
						console.log('URL: ' + pspbuttonurl );
					// $body.css('position', 'fixed');
					$body.css('width', '100%');
					$pspoverlay.fadeIn();
					// $pspoverlay.effect("shake");
					// Disable cookie for ADMIN testing mode
					// psp_setcookie($cookieLength);
					}
				 }, $delayTime);
			}
		//console.log(' Displaying set with delay time set: ' + $delayTime);

		// Add click capture on all overlay elements
		if ($("#psp-overlay").length > 0){
			//console.log(' Add click captures, wait for click...');
			$('#psp-overlay a')
				.filter('[href^="http"], [href^="//"]')
				.not('[href*="' + window.location.host + '"]')
				.on('click touch', function (e)  {
					//console.log(' External Click Detected!');
					e.stopPropagation();
					e.preventDefault();
					$psphref = this.href;
					$body.css('width', '');
					psp_setcookie($cookieLength,$pspcookiename,pspstatus);
					$pspoverlay.fadeOut();
					setTimeout(function(){
						window.open($psphref,"_blank");
					}, 200);
				});
			$('#psp-overlay a')
				.filter('[href^="http"], [href^="//"]')
				.filter('[href*="' + window.location.host + '"]')
				.on('click touch', function (e)  {
					//console.log(' Internal Click Detected!');
					e.stopPropagation();
					e.preventDefault();
					$psphref = this.href;
					$body.css('width', '');
					psp_setcookie($cookieLength,$pspcookiename,pspstatus);
					$pspoverlay.fadeOut();
					setTimeout(function(){
						window.open($psphref,"_self");
					}, 200);
				});
			$('#psp-overlay a')
				.filter('[href^="/"]')
				.not('[href*="' + window.location.host + '"]')
				.on('click touch', function (e)  {
					//console.log(' Relative Click Detected!');
					e.stopPropagation();
					e.preventDefault();
					$psphref = this.href;
					$body.css('width', '');
					psp_setcookie($cookieLength,$pspcookiename,pspstatus);
					$pspoverlay.fadeOut();
					setTimeout(function(){
						window.open($psphref,"_self");
					}, 200);
				});
		}

		if ( $("#psp-close-x").length > 0 ){
			$("#psp-close-x").on('click touch', function (e) {
				$body.css('width', '');
				psp_setcookie($cookieLength,$pspcookiename,pspstatus);
				$pspoverlay.slideUp();
			});
		}
		// Close popup on overlay/background click
		if ( $("#psp-overlay-wrap").length > 0 ){
			$("#psp-overlay-wrap").on('click touch', function (e) {
				if (e.target !== this) {
					return;
				}
				// $body.css('position', '');
				$body.css('width', '');
				psp_setcookie($cookieLength,$pspcookiename,pspstatus);
				$pspoverlay.fadeOut();
			});
		}

		/* Disable element clicks for now, using overlay a filters above...

			// CTA button click, close overlay
			if ( $("#psp_confirm_age").length > 0 ){
				$("#psp_confirm_age").on('click touch', function (e) {
					console.log(' CTA Button Click Detected!');
					e.preventDefault();
					$psphref = $(this).parent('a').attr("href");
					$body.css('width', '');
					psp_setcookie($cookieLength,$pspcookiename,pspstatus);
					$pspoverlay.fadeOut();
					setTimeout(function() {
						window.open($psphref,"_self");
					}, 200);
				});
			}
			// IMG button click, close overlay
			if ( $(".psp-logo").length > 0 ){
				$(".psp-logo").on('click touch', function (e) {
					console.log(' Image Click Detected!');
					e.preventDefault();
					$psphref = $(this).parent('a').attr("href");
					$body.css('width', '');
					psp_setcookie($cookieLength,$pspcookiename,pspstatus);
					$pspoverlay.fadeOut();
					setTimeout(function() {
						window.open($psphref,"_self");
					}, 200);
				});
			}
			// Text link click, close overlay
			if ( $("#psp_moreinfo_link").length > 0 ){
				$("#psp_moreinfo_link").on('click touch', function (e) {
					console.log(' Text Link Click Detected!');
					e.preventDefault();
					$psphref = $(this).parent('a').attr("href");
					$body.css('width', '');
					psp_setcookie($cookieLength,$pspcookiename,pspstatus);
					$pspoverlay.fadeOut();
					setTimeout(function() {
						window.open($psphref,"_self");
					}, 200);
				});
			}
		 end of element clicks disabled */

		}
	}

	function psp_setcookie($cookieLength,$pspcookiename,pspstatus) {
		if ( $body.hasClass('logged-in') == true && $body.hasClass('administrator') == true && pspstatus === 'admin-only') {
			// skip setting cookie for admins
			//console.log('=== SKIP SET cookie for Testing Mode');
			return;
		} else {
			// add cookie length value here
			// let pspoptions = {path: location.pathname};
			let pspoptions = {path: '/'};
			// Sets expiration cookie in days
			pspoptions.expires = $cookieLength;
			//console.log('=== SET cookie: ' + $pspcookiename + ' with length: ' + $cookieLength + ' days');
			Cookies.set($pspcookiename, 1, pspoptions);
		}
	}

	function getContrast50(hexcolor){
		return (parseInt(hexcolor, 16) > 0xffffff/2) ? 'black':'white';
	}

});

// Embed jQuery js-cookies file to remove remote call
/*!
 * JavaScript Cookie v2.2.1
 * Minified by jsDelivr using Terser v3.14.1.
 * Original file: /npm/js-cookie@2.2.1/src/js.cookie.js
 */
!function(e){var n;if("function"==typeof define&&define.amd&&(define(e),n=!0),"object"==typeof exports&&(module.exports=e(),n=!0),!n){var t=window.Cookies,o=window.Cookies=e();o.noConflict=function(){return window.Cookies=t,o}}}(function(){function e(){for(var e=0,n={};e<arguments.length;e++){var t=arguments[e];for(var o in t)n[o]=t[o]}return n}function n(e){return e.replace(/(%[0-9A-Z]{2})+/g,decodeURIComponent)}return function t(o){function r(){}function i(n,t,i){if("undefined"!=typeof document){"number"==typeof(i=e({path:"/"},r.defaults,i)).expires&&(i.expires=new Date(1*new Date+864e5*i.expires)),i.expires=i.expires?i.expires.toUTCString():"";try{var c=JSON.stringify(t);/^[\{\[]/.test(c)&&(t=c)}catch(e){}t=o.write?o.write(t,n):encodeURIComponent(String(t)).replace(/%(23|24|26|2B|3A|3C|3E|3D|2F|3F|40|5B|5D|5E|60|7B|7D|7C)/g,decodeURIComponent),n=encodeURIComponent(String(n)).replace(/%(23|24|26|2B|5E|60|7C)/g,decodeURIComponent).replace(/[\(\)]/g,escape);var f="";for(var u in i)i[u]&&(f+="; "+u,!0!==i[u]&&(f+="="+i[u].split(";")[0]));return document.cookie=n+"="+t+f}}function c(e,t){if("undefined"!=typeof document){for(var r={},i=document.cookie?document.cookie.split("; "):[],c=0;c<i.length;c++){var f=i[c].split("="),u=f.slice(1).join("=");t||'"'!==u.charAt(0)||(u=u.slice(1,-1));try{var a=n(f[0]);if(u=(o.read||o)(u,a)||n(u),t)try{u=JSON.parse(u)}catch(e){}if(r[a]=u,e===a)break}catch(e){}}return e?r[e]:r}}return r.set=i,r.get=function(e){return c(e,!1)},r.getJSON=function(e){return c(e,!0)},r.remove=function(n,t){i(n,"",e(t,{expires:-1}))},r.defaults={},r.withConverter=t,r}(function(){})});
