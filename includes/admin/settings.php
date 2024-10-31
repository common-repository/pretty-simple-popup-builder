<?php

// Don't access this directly, please
if ( ! defined( 'ABSPATH' ) ) exit;

/**********************************************************/
/******************** General Settings ********************/
/**********************************************************/

/**
 * Prints the general settings section heading.
 *
 * @since 0.1
 */
function psp_settings_callback_section_general() {
	// Something should go here
}

/**
 * Prints the "require for" settings field.
 *
 * @since 0.2
 */
function psp_settings_callback_require_for_field() { ?>
	<fieldset>
		<legend class="screen-reader-text">
			<span><?php esc_html_e( 'Require verification for', 'pretty-simple-popup' ); ?></span>
		</legend>
		<label>
			<input type="radio" name="_psp_require_for" value="site" <?php checked( 'site', get_option( '_psp_require_for', 'site' ) ); ?>/>
			 <?php esc_html_e( 'Entire site', 'pretty-simple-popup' ); ?><br />
		</label>
		<br />
		<label>
			<input type="radio" name="_psp_require_for" value="content" <?php checked( 'content', get_option( '_psp_require_for', 'site' ) ); ?>/>
			 <?php esc_html_e( 'Specific content', 'pretty-simple-popup' ); ?>
		</label>
	</fieldset>
<?php }

/**
 * Prints the "who to verify" settings field.
 *
 * @since 0.1
 */
function psp_settings_callback_always_verify_field() {
	$option = get_option( '_psp_always_verify' );
	$checked =  checked( 'disabled', $option , false );
	if( ! $option && $option !==  'disabled' )
	{$checked =  checked( 'disabled', $option , false );}
	?>
	<fieldset>
		<legend class="screen-reader-text">
			<span><?php esc_html_e( 'Enable verification:', 'pretty-simple-popup' ); ?></span>
		</legend>
		<label>
			<input type="radio" name="_psp_always_verify" value="disabled" <?php echo $checked; ?>/>
			 <?php esc_html_e( 'Disable', 'pretty-simple-popup' ); ?>
		</label>
		<br />
		<label>
			<input type="radio" name="_psp_always_verify" value="admin-only" <?php checked( 'admin-only', get_option( '_psp_always_verify', 'admin-only' ) ); ?>/>
			 <?php esc_html_e( '[TESTING MODE] Admins only', 'pretty-simple-popup' ); ?>
		</label>
		<br />
		<label>
			<input type="radio" name="_psp_always_verify" value="guests" <?php checked( 'guests', get_option( '_psp_always_verify', 'guests' ) ); ?>/>
			 <?php esc_html_e( 'Show except to logged-in users', 'pretty-simple-popup' ); ?>
		</label>
		<br />
		<label>
			<input type="radio" name="_psp_always_verify" value="all" <?php checked( 'all', get_option( '_psp_always_verify', 'all' ) ); ?>/>
			 <?php esc_html_e( 'Show to all visitors', 'pretty-simple-popup' ); ?>
		</label>
	</fieldset>
<?php }


/**
 * Prints the popup delay timer drop-down field
 *
 * @since 0.1
 */
function psp_settings_callback_delay_timer_field() {	?>
    <fieldset>
        <legend class="screen-reader-text">
            <span><?php esc_html_e( 'Popup Display Delay Timer ' ); ?></span>
        </legend>
        <select name="_psp_delay_timer" id="_psp_delay_timer" class="regular-text">
			<?php $psp_delay_timer = get_option( '_psp_delay_timer', '5' ); ?>
            <option value="0" <?php echo ( $psp_delay_timer == "0" ) ? 'selected="selected"' : ''; ?>>No Delay</option>
            <option value="3" <?php echo ( $psp_delay_timer == "3" ) ? 'selected="selected"' : ''; ?>>3 seconds</option>
            <option value="5" <?php echo ( $psp_delay_timer == "5" ) ? 'selected="selected"' : ''; ?>>5 seconds</option>
            <option value="7" <?php echo ( $psp_delay_timer == "7" ) ? 'selected="selected"' : ''; ?>>7 seconds</option>
            <option value="10" <?php echo ( $psp_delay_timer == "10" ) ? 'selected="selected"' : ''; ?>>10 seconds</option>
            <option value="15" <?php echo ( $psp_delay_timer == "15" ) ? 'selected="selected"' : ''; ?>>15 seconds</option>
            <option value="20" <?php echo ( $psp_delay_timer == "20" ) ? 'selected="selected"' : ''; ?>>20 seconds</option>
            <option value="30" <?php echo ( $psp_delay_timer == "30" ) ? 'selected="selected"' : ''; ?>>30 seconds</option>
        </select>
    </fieldset>
<?php }

/**
 * Prints the age verify promo
 *
 * @since 0.1
 */
function psp_settings_callback_ageverify_promo() {	?>
		<fieldset>
			<legend class="screen-reader-text">
				<span><?php esc_html_e( 'Upgrade to Age Verify Compatible', 'pretty-simple-popup' ); ?></span>
			</legend>
			<div class="psppremhovertip" title="Upgrade to Premium for Age Verify Compatibility">
			<?php printf(
                sprintf(
                __( 'Popup starts after verification when using our  <a target="_blank" href="%1$s">Easy Age Verify</a> and  <a target="_blank" href="%2$s">Marijuana Age Verify</a> plugins with Pretty Simple Popup Premium.', 'text-domain' ),
                esc_url('https://5starplugins.com/easy-website-age-verification/'),
                esc_url('https://5starplugins.com/marijuana-website-age-verification/')
                )
            ); ?>
			</div>
		</fieldset>
<?php }

/**
 * Prints the cookie length drop-down field
 *
 * @since 0.1
 */
function psp_settings_callback_delay_cookie_length() {	?>
    <input style="width:10%;display: inline-block;" type="number" min="1" max="999" name="_psp_cookie_length" id="_psp_cookie_length" placeholder="1" maxlength="3"
           value="<?php echo esc_attr( intval(get_option( '_psp_cookie_length' )) >= 1 ? intval(get_option( '_psp_cookie_length' )) : __( '1', 'pretty-simple-popup' ) ); ?>"
           class="regular-text"/> Days
<?php }


function _psp_popup_template_callback_field() { ?>
	<fieldset class="psp-age-header-option">
		<legend class="screen-reader-text">
			<span><?php esc_html_e( 'Select A Template:', 'pretty-simple-popup' ); ?></span>
		</legend>
	<div style="display: flex;">
  		<div style="flex: 50%;">
			<label>
				<input type="radio" name="_psp_popup_template" value="free-1" <?php checked( 'free-1', get_option( '_psp_popup_template', 'free-1' ) ); ?>/>
				<?php
				esc_html_e( 'Free Text Popup', 'pretty-simple-popup' );
				echo '<p><img style="width: 90%;" src="' . plugin_dir_url(__FILE__) . '../../includes/images/free-text-popup-thumbnail.jpg' . '"></p>';
				?>
			</label>
		</div>
		<div style="flex: 50%;">
			<label>
				<input type="radio" name="_psp_popup_template" value="free-2" <?php checked( 'free-2', get_option( '_psp_popup_template', 'free-1' ) ); ?>/>
				<?php
				esc_html_e( 'Free Image Popup', 'pretty-simple-popup' );
				echo '<p><img style="width: 90%;" src="' . plugin_dir_url(__FILE__) . '../../includes/images/free-image-popup-thumbnail.jpg' . '"></p>';
				?>
			</label>
		</div>
	</div>

		<br />
		<?php
		if(function_exists('psp_premium_popup_templates')) {
			echo psp_premium_popup_templates();
		} else {
			$premium_verify_html = '<br><strong>' . __( 'Premium Templates', 'pretty-simple-popup' ) . '</strong><p/><div style="display: flex;" class="psppremhovertip" title="Upgrade to Premium for additional templates with many additional design features">';
			$premium_verify_html .= '<div style="flex: 50%;"><label><input disabled type="radio" name="_psp_popup_template" value=""  /> ' . __( 'Designer Text Popup', 'pretty-simple-popup' ) . '<p><img style="width: 90%;" src="' . plugin_dir_url(__FILE__) . '../../includes/images/premium-text-popup-thumbnail.jpg' . '"></p></label></div>';

			$premium_verify_html .= '<div style="flex: 50%;"><label><input disabled type="radio" name="_psp_popup_template" value=""  /> ' . __( 'Designer Image Popup', 'pretty-simple-popup' ) . '<p><img style="width: 90%;" src="' . plugin_dir_url(__FILE__) . '../../includes/images/premium-image-popup-thumbnail.jpg' . '"></p></label></div></div>';
			echo $premium_verify_html;
		} ?>
	</fieldset>
<?php }

/**
 * Prints the modal headline settings field.
 *
 * @since 0.1
 */
function psp_settings_callback_headline_field() { ?>
    <input name="_psp_heading" type="text" id="_psp_heading" placeholder="Your text here" maxlength="50"
           value="<?php echo wp_kses_post( empty( get_option( '_psp_heading' )) ? '' : get_option( '_psp_heading' ) ); ?>"
           class="regular-text"/>
<?php }

/**
 * Prints the modal description settings field.
 *
 * @since 0.1
 */
function psp_settings_callback_description_field() { ?>
<textarea name="_psp_description" id="_psp_description" placeholder="Your text here" maxlength="150" rows="5" class="regular-text"/><?php echo wp_kses_post( empty( get_option( '_psp_description' )) ? '' : get_option( '_psp_description' ) ); ?></textarea>
<?php }

/**
 * Prints the free-form custom agree button text field
 *
 * @since 1.2
 */
function psp_settings_callback_custom_agreebutton_text_field() { ?>
    <input name="_psp_custom_agreebutton_text" type="text" id="_psp_custom_agreebutton_text" placeholder="Your text here" maxlength="30"
           value="<?php echo wp_kses_post( empty( get_option( '_psp_custom_agreebutton_text' )) ? '' : get_option( '_psp_custom_agreebutton_text' ) ); ?>"
           class="regular-text"/>
<?php }

/**
 * Prints the CTA button link field
 *
 * @since 1.2
 */
function psp_settings_callback_ctabutton_link_field() { ?>
    <input name="_psp_ctabutton_link" type="text" id="_psp_ctabutton_link" placeholder="#"
           value="<?php echo wp_kses_post( empty( get_option( '_psp_ctabutton_link' )) ? '#' : get_option( '_psp_ctabutton_link' ) ); ?>"
           class="regular-text"/>
<?php }

/**
 * Prints the image link field
 *
 * @since 1.2
 */
function psp_settings_callback_image_link_field() { ?>
    <input name="_psp_image_link" type="text" id="_psp_image_link" placeholder=""
           value="<?php echo wp_kses_post( empty( get_option( '_psp_image_link' )) ? '' : get_option( '_psp_image_link' ) ); ?>"
           class="regular-text"/>
<?php }

/**
 * Prints the display settings section heading.
 *
 * @since 0.1
 */
function psp_settings_callback_section_display() {

	//echo '<p>' . esc_html__( 'These settings change the look of your overlay. You can use <code>%s</code> to display the minimum age number from the setting above.', 'pretty-simple-popup' ) . '</p>';
}

/**
 * Prints the box color settings field.
 *
 * @since 0.1
 */
function psp_settings_callback_box_color_field() { ?>
    <fieldset>
        <legend class="screen-reader-text">
            <span><?php esc_html_e( 'Box Color', 'pretty-simple-popup' ); ?></span>
        </legend>
		<?php $default_box_color = ' data-default-color="#FFFFFF"'; ?>
        <input type="text" name="_psp_box_color" id="_psp_box_color" value="<?php echo wp_kses_post( psp_get_box_color() ); ?>" <?php echo wp_kses_post($default_box_color); ?> />
    </fieldset>
<?php }

/**
 * Prints the Show Box settings field - HARDCODED FOR NOW based on template used.
 *
 * @since 1.0
 */
function psp_settings_callback_box_show_field() {    ?>
	<fieldset>
		<legend class="screen-reader-text">
			<span><?php esc_html_e( 'Show Modal Box', 'pretty-simple-popup' ); ?></span>
		</legend>
		<label>
			<input type="checkbox" name="_psp_box_show" value="psp_box_show" <?php checked( 'psp_box_show', get_option( '_psp_box_show', true ) ); ?>/>
			 <?php esc_html_e( 'Show Box:', 'pretty-simple-popup' ); ?>
		</label>
	</fieldset>
<?php }

/**
 * Prints the color settings for CTA button field. // Rename values for psp...
 *
 * @since 0.1
 */
function psp_settings_callback_agree_btn_bgcolor_field() { ?>
    <fieldset>
        <legend class="screen-reader-text">
            <span><?php esc_html_e( 'Button Color' ); ?></span>
        </legend>
		<?php $default_color = '#727272'; ?>
        <input type="text" name="_psp_agree_btn_bgcolor" id="_psp_agree_btn_bgcolor"
               value="<?php echo wp_kses_post( psp_get_agree_btn_background_color() ); ?>" <?php echo wp_kses_post($default_color); ?> />
    </fieldset>
<?php }

/**
 * Prints the Logo field.
 *
 * @since 0.1
 */
function psp_settings_callback_logo_field() {
	$psp_logo_options = get_option( '_psp_logo' );
	?>

    <fieldset class="wrap">
        <legend class="screen-reader-text">
            <span><?php esc_html_e( 'Upload Image' ); ?></span>
        </legend>
        <div class="psp_logo_outer" style="display:none;">
            <div class="psp_logo_fields_container">
                <input id="psp_logo_button" type="button" value="Add Image" class="button-secondary"/>
                <input id="psp_logo_field_id" class="regular-text code" type="hidden" name="_psp_logo"
                       value="<?php echo ! empty( esc_attr( $psp_logo_options ) ) ? ( esc_attr( $psp_logo_options ) ) : ( '' ); ?>" placeholder="Select Image"
                       readonly="readonly">
                <input id="psp_logo_delete_button" type="button" value="Clear Image" class="button-secondary"/>
            </div>
            <div class="psp_logo_container" >
				<?php echo ! empty( esc_attr( $psp_logo_options ) ) ? ( '<IMG SRC="' . esc_attr( $psp_logo_options ) . '" />' ) : ( '' ); ?>
            </div>
        </div>
    </fieldset>
<?php }

/**
 * Outputs the "cache-buster AJAX" option settings field.
 *
 * @since 1.6
 */
function psp_settings_callback_ajax_check() { ?>
	<fieldset>
		<legend class="screen-reader-text">
			<span><?php esc_html_e( 'Realtime Settings Check', 'pretty-simple-popup' ); ?></span>
		</legend>
		<label>
			<input type="checkbox" name="_psp_ajax_check" id="_psp_ajax_check" value="psp-ajax-check" <?php checked( 'psp-ajax-check', get_option( '_psp_ajax_check', true ) ); ?>>
			 <?php esc_html_e( 'Confirm "Enable Verification" settings before showing popup', 'pretty-simple-popup' ); ?>
		</label>
	</fieldset>
<?php }

/**
 * Outputs the reset cookie field.
 *
 * @since 1.6
 */
function psp_settings_callback_reset_cookie() { ?>
	<fieldset>
		<legend class="screen-reader-text">
			<span><?php esc_html_e( 'Reset cookie for all visitors', 'pretty-simple-popup' ); ?></span>
		</legend>
		<label>
			<input type="checkbox" name="_psp_reset_cookie" id="_psp_reset_cookie" <?php checked( 'on', get_option( '_psp_reset_cookie', true ) ); ?>>
			 <?php esc_html_e( 'Check this to generate a fresh cookie when saved.', 'pretty-simple-popup' ); ?>
		</label>
	</fieldset>
<?php }

/**
 * Outputs the cookie name field.
 *
 * @since 1.6
 */
function psp_settings_callback_cookie_name() { ?>
	<fieldset>
		<legend class="screen-reader-text">
			<span><?php esc_html_e( 'Cookie name (hidden)', 'pretty-simple-popup' ); ?></span>
		</legend>
		<label>
			<input type="text" name="_psp_new_cookie_name" id="_psp_new_cookie_name" value="<?php echo empty( get_option( '_psp_new_cookie_name' )) ? 'psp-popup-displayed-' . time() : get_option( '_psp_new_cookie_name' );?>">
		</label>
	</fieldset>
<?php }

/**
 * DISPLAY SETTINGS PAGE (2 Columns)
 *
 * @since 0.1
 */
function psp_settings_page() { ?>
	<div class="wrap">
		<div class="psp-options-column1">
			<div class="psp-headerDiv" >
				<a href="https://5starplugins.com/" target="_blank"><img class="psp-headerImg" src="<?php echo plugin_dir_url(__FILE__) . '../../includes/images/banner.jpg';?>"></a>
			</div>

			<h1 class="psp-header-title"><?php esc_html_e( 'Pretty Simple Popup', 'pretty-simple-popup' ) ?></h1>

			<?php
			// If cookie reset flag is true, generate new cookie name, update option, and pass to body CSS for parsing
			if ( get_option( '_psp_reset_cookie' ) == 'on' ) {
				update_option('_psp_reset_cookie', 'false');
				update_option('_psp_new_cookie_name', 'psp-popup-displayed-' . time() );
			} ?>

			<?php settings_errors();

			if ( isset( $_GET['settings-updated'] ) ) {
				wp_cache_flush();
				psp_clear_cache();
			}
			?>

			<form action="options.php" method="post" class="psp-settings-form">
				<?php settings_fields( 'pretty-simple-popup' ); ?>
				<?php do_settings_sections( 'pretty-simple-popup' ); ?>

				<?php submit_button(); ?>
			</form>
		</div>
		<div class="psp-premium-column2">
			<?php
				if ( psp_fs()->is_not_paying() ) {
					echo psp_display_upgrade_features();
				}
			?>
		</div>
	</div>
	<div class="psp-footer-notes">
		<?php
			echo sprintf( '<div id="clear-cookie-option" style="padding: 10px;font-size:14px;">' . __( '<strong>Clear My Cookie:</strong> Popup stopped displaying after closing? <br/>', 'pretty-simple-popup' ));
			echo sprintf( __( 'Detects if a cookie is set in your browser from this plugin and clears it. Refresh this page to recheck.', 'pretty-simple-popup' ));?>
		<p/>
		<button id="psp-clear-cookie" onclick='return psp_clear_cookie();' disabled>No Cookie Set</button>
		<p/>
		</div>

		<?php if ( psp_fs()->is_not_paying() ) { ?>
			<p/><?php  echo __( '<strong>Need Help?</strong><br/>Click the', 'pretty-simple-popup') . ' <span class="dashicons dashicons-editor-help"></span> ' .  __( 'icon on the bottom right to search our <a target="_blank" href="https://support.5starplugins.com/collection/205-pretty-simple-popup"  target="_blank">Knowledge Base</a>', 'pretty-simple-popup' ); ?>
		<?php } else { ?>
			<p/><?php  echo __( 'Need Help? Click the', 'pretty-simple-popup') . ' <span class="dashicons dashicons-editor-help"></span> ' .  __( 'icon on the bottom right to search our <a target="_blank" href="https://support.5starplugins.com/collection/205-pretty-simple-popup">Knowledge Base</a> or to send an email for Premium support.', 'pretty-simple-popup' ); ?>
		<?php } ?>
			<p/><?php echo sprintf( __( 'Read: <a target="_blank" href="https://support.5starplugins.com/article/212-my-popup-isnt-showing"  target="_blank">My popup isn\'t showing</a>', 'pretty-simple-popup' ));?>
		<?php if ( psp_fs()->is_not_paying() ) { ?>
			<p/><?php echo sprintf( __( '<a href="/wp-admin/admin.php?page=pretty-simple-popup-contact">Contact Us</a> to to report a bug, suggest features, or ask a pre-sale question.', 'pretty-simple-popup' )); ?>
		<?php } else { ?>
			<p/><?php echo sprintf( __( '<a href="/wp-admin/admin.php?page=pretty-simple-popup-contact">Contact Us</a> to to report a bug, suggest features, ask a billing question, or request support.', 'pretty-simple-popup' )); ?>
		<?php } ?>
		<p/><?php echo sprintf( __('<a target="_blank" href="https://5starplugins.com/get-support/">Visit the Support Center</a> for more.', 'pretty-simple-popup')); ?>
		<p/><?php echo sprintf( __( 'Like this? <a href="http://wordpress.org/support/view/plugin-reviews/pretty-simple-popup/?rate=5#new-post"  target="_blank">Rate this plugin</a>', 'pretty-simple-popup' )); ?>
		<br/><?php echo sprintf( __( 'Developed by <a href="%s" target=_blank>5 Star Plugins</a> in San Diego, CA', 'pretty-simple-popup' ), esc_url('https://5starplugins.com/')); ?> <img class="footerLogo" src="<?php echo plugins_url( 'images/5StarPlugins_Logo80x80.png', dirname(__FILE__) );?>" width="20">
	</div>

<?php }

// Built-in clear cache function for various supported plugins and systems
if( ! function_exists('psp_clear_cache') ) {
	function psp_clear_cache() {
		$psp_cleared = FALSE;
		// WP Cloudlfare Super Page Cache purge CF cache
		if ( has_action('swcfpc_purge_cache')) {
			do_action("swcfpc_purge_cache");
			echo "<div class='notice notice-success is-dismissible'><p>WP Cloudflare Super Page cache cleared (may take 30+ seconds).</p></div>";
			$psp_cleared = TRUE;
		}
		// WP Rocket
		if ( function_exists( 'rocket_clean_domain' ) ) {
			rocket_clean_domain();
			echo "<div class='notice notice-success is-dismissible'><p>WP Rocket cache cleared.</p></div>";
			$psp_cleared = TRUE;
		}
		// W3 Total Cache : w3tc
		if ( function_exists( 'w3tc_pgcache_flush' ) ) {
			w3tc_pgcache_flush();
			echo "<div class='notice notice-success is-dismissible'><p>W3TC cache cleared.</p></div>";
			$psp_cleared = TRUE;
		}
		// WP Super Cache : wp-super-cache
		if ( function_exists( 'wp_cache_clear_cache' ) ) {
			wp_cache_clear_cache();
			echo "<div class='notice notice-success is-dismissible'><p>WP Super Cache cache cleared.</p></div>";
			$psp_cleared = TRUE;
		}
		// WP Fastest Cache
		if( function_exists('wpfc_clear_all_cache') ) {
			wpfc_clear_all_cache(true);
			echo "<div class='notice notice-success is-dismissible'><p>WP Fastest Cache cache cleared.</p></div>";
			$psp_cleared = TRUE;
		}
		// WPEngine
		if ( class_exists( 'WpeCommon' ) && method_exists( 'WpeCommon', 'purge_memcached' ) ) {
			WpeCommon::purge_memcached();
			WpeCommon::purge_varnish_cache();
			echo "<div class='notice notice-success is-dismissible'><p>WPEngine cache cleared.</p></div>";
			$psp_cleared = TRUE;
		}
		// SG Optimizer by Siteground
		if ( function_exists( 'sg_cachepress_purge_cache' ) ) {
			sg_cachepress_purge_cache();
			echo "<div class='notice notice-success is-dismissible'><p>SiteGround Optimizer cache cleared.</p></div>";
			$psp_cleared = TRUE;
		}
		// LiteSpeed
		if( class_exists('LiteSpeed_Cache_API') && method_exists('LiteSpeed_Cache_API', 'purge_all') ) {
			LiteSpeed_Cache_API::purge_all();
			echo "<div class='notice notice-success is-dismissible'><p>LiteSpeed cache cleared.</p></div>";
			$psp_cleared = TRUE;
		}
		// Cache Enabler
		if( class_exists('Cache_Enabler') && method_exists('Cache_Enabler', 'clear_total_cache') ) {
			Cache_Enabler::clear_total_cache();
			echo "<div class='notice notice-success is-dismissible'><p>Cache Enabler cache cleared.</p></div>";
			$psp_cleared = TRUE;
		}
		// Pagely
		if ( class_exists('PagelyCachePurge') && method_exists('PagelyCachePurge','purgeAll') ) {
			PagelyCachePurge::purgeAll();
			echo "<div class='notice notice-success is-dismissible'><p>Pagely cache cleared.</p></div>";
			$psp_cleared = TRUE;
		}
		// Autoptimize
		if( class_exists('autoptimizeCache') && method_exists( 'autoptimizeCache', 'clearall') ) {
			autoptimizeCache::clearall();
			echo "<div class='notice notice-success is-dismissible'><p>Autoptimize cache cleared.</p></div>";
			$psp_cleared = TRUE;
		}
		// Comet cache
		if( class_exists('comet_cache') && method_exists('comet_cache', 'clear') ) {
			comet_cache::clear();
			echo "<div class='notice notice-success is-dismissible'><p>Comet Cache cache cleared.</p></div>";
			$psp_cleared = TRUE;
		}
		// Hummingbird Cache
		if( class_exists('\Hummingbird\WP_Hummingbird') && method_exists('\Hummingbird\WP_Hummingbird', 'flush_cache') ) {
			\Hummingbird\WP_Hummingbird::flush_cache();
			echo "<div class='notice notice-success is-dismissible'><p>Hummingbird cache cleared.</p></div>";
			$psp_cleared = TRUE;
		}
		if (! $psp_cleared ) {
			echo "<div class='notice notice-success is-dismissible'><p>NOTE: Please be sure to clear any page caches for new settings to display.</p></div>";
		}

	}
}
