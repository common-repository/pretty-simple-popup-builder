<?php

/**
 * Define the supporting functions.
 *
 * @since   0.2.6
 *
 * @package Pretty_Simple_Popup\Functions
 */
// Don't allow this file to be accessed directly.
if ( !defined( 'WPINC' ) ) {
    die;
}
/**
 * Localization
 *
 * @since 0.1.0
 */
function psp_load_textdomain() {
    load_plugin_textdomain( 'pretty-simple-popup', false, plugin_basename( dirname( __FILE__ ) ) . '/includes/languages' );
}

add_action( 'plugins_loaded', 'psp_load_textdomain' );
/**
 * Prints the minimum age.
 *
 * @return void
 * @see   psp_get_minimum_age();
 *
 * @since 0.1.0
 */
function psp_minimum_age() {
    echo psp_get_minimum_age();
}

/**
 * Returns the all-important verification form.
 * You can filter this if you like.
 *
 * @return string
 * @since 0.1
 */
function psp_get_verify_form() {
    global $psp_fs;
    //$input_type = psp_get_input_type();
    // $submit_button_label = apply_filters( 'psp_form_submit_label', __( 'Enter Site &raquo;', 'pretty-simple-popup' ) );
    $form = '';
    $form .= '<form id="psp-verify-form">';
    do_action( 'psp_form_before_inputs' );
    //_psp_popup_template display options
    if ( get_option( '_psp_popup_template', 'free-1' ) == 'free-1' || get_option( '_psp_popup_template', 'free-1' ) == 'prem-1' || get_option( '_psp_popup_template', 'free-1' ) == 'prem-2' ) {
        // Selected Button Label Option
        $psp_ctabtn_label = psp_get_custom_agreebutton_text();
        $psp_ctabtn_link = psp_get_ctabutton_link();
        $psp_ctabtn_style = 'style="background-color:' . psp_get_agree_btn_background_color() . ';color:' . getContrastYIQ( ltrim( psp_get_agree_btn_background_color(), '#' ) ) . ';"';
        if ( $psp_ctabtn_label ) {
            $form .= '<div class="psp_buttons"><a href="' . $psp_ctabtn_link . '"><input type="button" name="confirm_age" id="psp_confirm_age" value="' . $psp_ctabtn_label . '" ' . $psp_ctabtn_style . ' /></a></div>';
            $form .= '<div class="psp_buttons_sep"></div>';
        }
    }
    do_action( 'psp_form_after_inputs' );
    $form .= '</form><svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" viewBox="0 0 12 12" data-testid="PSPCloseButton" id="psp-close-x" style="fill:' . getContrastYIQ( ltrim( psp_get_box_color(), '#' ) ) . ';"><defs><path id="psp-close-tiny" d="M7.426 6.001l4.278-4.279A1.008 1.008 0 1010.278.296L6 4.574 1.723.296A1.008 1.008 0 10.295 1.722l4.278 4.28-4.279 4.277a1.008 1.008 0 101.427 1.426L6 7.427l4.278 4.278a1.006 1.006 0 001.426 0 1.008 1.008 0 000-1.426L7.425 6.001z"></path></defs><use fill-rule="evenodd" xlink:href="#psp-close-tiny"></use></svg>';
    return apply_filters( 'psp_verify_form', $form );
}

/**
 * Get the cookie duration.
 *
 * This lets us know how long to keep a visitor's
 * verified cookie.
 *
 * @return int $cookie_duration The cookie duration.
 * @since 0.1.0
 *
 */
function psp_get_cookie_duration() {
    $cookie_duration = get_option( '_psp_cookie_duration', 720 );
    /**
     * Filter the cookie duration.
     *
     * @param int $cookie_duration The cookie duration.
     *
     * @since 0.1.0
     *
     */
    $cookie_duration = (int) apply_filters( 'psp_cookie_duration', $cookie_duration );
    return $cookie_duration;
}

/**
 * Determines whether only certain content should be restricted.
 *
 * @return bool $only_content_restricted Whether the restriction is content-specific or site-wide.
 * @since 0.2.0
 *
 */
function psp_only_content_restricted() {
    $only_content_restricted = ( 'content' == get_option( '_psp_require_for' ) ? true : false );
    /**
     * Filter whether the restriction is content-specific or site-wide.
     *
     * @param bool $only_content_restricted
     *
     * @since 0.2.0
     *
     */
    $only_content_restricted = apply_filters( 'psp_only_content_restricted', $only_content_restricted );
    return (bool) $only_content_restricted;
}

/**
 * Determines if a certain piece of content is restricted.
 *
 * @return bool $is_restricted Whether a certain piece of content is restricted.
 * @since 0.2.0
 *
 */
function psp_content_is_restricted(  $id = null  ) {
    if ( is_null( $id ) ) {
        $id = get_the_ID();
    }
    $is_restricted = ( 1 == get_post_meta( $id, '_psp_needs_verify', true ) ? true : false );
    /**
     * Filter whether this content should be restricted.
     *
     * @param bool $is_restricted Whether this content should be restricted.
     * @param int  $id            The content's ID.
     *
     * @since 0.2.6
     *
     */
    $is_restricted = apply_filters( 'psp_is_restricted', $is_restricted, $id );
    return $is_restricted;
}

/**
 * This is the very important function that determines if a given visitor
 * needs to be verified before viewing the site. You can filter this if you like.
 *
 * @return bool
 * @since 0.1
 */
function psp_needs_verification() {
    // Assume the visitor needs to be verified
    $return = true;
    // If the site is restricted on a per-content basis, let 'em through
    if ( psp_only_content_restricted() ) {
        $return = false;
        // If the content being viewed is restricted, throw up the form
        if ( is_singular() && psp_content_is_restricted() ) {
            $return = true;
        }
    }
    // If not logged in Admins
    if ( get_option( '_psp_always_verify', 'admin-only' ) == 'admin-only' && !current_user_can( 'manage_options' ) ) {
        $return = false;
    }
    // If logged in users are exempt, and the visitor is logged in, let 'em through
    if ( get_option( '_psp_always_verify', 'guests' ) == 'guests' && is_user_logged_in() ) {
        $return = false;
    }
    // If logged in users are exempt, and the visitor is logged in, let 'em through
    if ( get_option( '_psp_always_verify', 'disabled' ) == 'disabled' ) {
        $return = false;
    }
    // Or, if there is a valid cookie let 'em through
    if ( isset( $_COOKIE['psp-age-verified'] ) || is_user_logged_in() ) {
        return (bool) apply_filters( 'psp_needs_verification', false );
    }
    return $return;
}

/***********************************************************
 ******************** Display Functions ********************
 ***********************************************************/
/**
 * Returns the form's input type, based on the settings.
 * You can filter this if you like.
 *
 * @return string
 * @since 0.1
 */
function psp_get_input_type() {
    return apply_filters( 'psp_input_type', get_option( '_psp_input_type', 'dropdowns' ) );
}

/**
 * Echoes the actual form
 *
 * @since 0.1
 * @echo  string
 */
function psp_verify_form() {
    echo psp_get_verify_form();
}

/**
 * Adds the user's WP role to the Body CSS Class for Testing Mode
 *
 * @since 1.1
 * @echo  string
 */
add_filter( 'body_class', 'psp_output_role_body_class' );
function psp_output_role_body_class(  $classes  ) {
    global $psp_fs;
    $classes[] = psp_get_user_role();
    // Add verify option setting in body CSS to bypass JS ajax call
    array_push( $classes, 'psp-' . get_option( '_psp_always_verify' ) );
    // Add cookie length option setting in body CSS to bypass JS ajax call
    array_push( $classes, 'psp-delay-' . get_option( '_psp_delay_timer' ) );
    // Add delay timer option setting in body CSS to bypass JS ajax call
    array_push( $classes, 'psp-cookie-' . get_option( '_psp_cookie_length' ) );
    // Add AJAX option check, add to BODY Class if set to True, use in JS to test and perform AJAX call, otherwise bypass AJAX call
    array_push( $classes, get_option( '_psp_ajax_check' ) );
    // If cookie reset flag is true, generate new cookie name, update option, and pass to body CSS for parsing
    if ( get_option( '_psp_reset_cookie' ) === 'on' ) {
        update_option( '_psp_reset_cookie', 'false' );
        $psp_new_cookie_name = 'psp-popup-displayed-' . time();
        update_option( '_psp_new_cookie_name', $psp_new_cookie_name );
        array_push( $classes, get_option( '_psp_new_cookie_name' ) );
    } else {
        array_push( $classes, get_option( '_psp_new_cookie_name' ) );
    }
    return $classes;
}

function psp_get_user_role() {
    global $current_user;
    $user_roles = $current_user->roles;
    $user_roles_list = implode( " ", $user_roles );
    return $user_roles_list;
}

/***********************************************************/
/*************** User Registration Functions ***************/
/***********************************************************/
/**
 * Determines whether or not users need to verify their age before
 * registering for the site. You can filter this if you like.
 *
 * @return bool
 * @since 0.1
 */
//add_action( 'wp_ajax_nopriv_psp_user_age_verify', 'psp_user_age_verify' );
//add_action( 'wp_ajax_psp_user_age_verify', 'psp_user_age_verify' );
function psp_user_age_verify() {
    $is_verified = false;
    $error = 1;
    // Catch-all in case something goes wrong
    $error_msg_arr = array('');
    $optionID = (int) psp_age_verify_option();
    error_log( $optionID );
    if ( isset( $_POST['verifyConfirm'] ) && (int) $_POST['verifyConfirm'] == 1 ) {
        $is_verified = true;
    } else {
        echo esc_attr( $error_msg_arr[$optionID] );
        exit;
    }
    $is_verified = apply_filters( 'psp_passed_verify', $is_verified );
    if ( $is_verified == true ) {
        do_action( 'psp_was_verified' );
        if ( isset( $_POST['psp_verify_remember'] ) ) {
            $cookie_duration = time() + psp_get_cookie_duration() * 60;
        } else {
            $cookie_duration = 0;
        }
        echo 'verified';
        wp_die();
    } else {
        do_action( 'psp_was_not_verified' );
        echo esc_attr( $error_msg_arr[$optionID] );
        wp_die();
    }
}

/**
 * Get current options
 */
add_action( 'wp_ajax_nopriv_psp_get_status', 'psp_get_status' );
add_action( 'wp_ajax_psp_get_status', 'psp_get_status' );
function psp_get_status() {
    echo get_option( '_psp_always_verify' );
    wp_die();
}

function psp_confirmation_required() {
    if ( get_option( '_psp_membership', 1 ) == 1 ) {
        $return = true;
    } else {
        $return = false;
    }
    return (bool) apply_filters( 'psp_confirmation_required', $return );
}

/**
 * Make sure the user checked the box when registering.
 * If not, print an error. You can filter the error's text if you like.
 *
 * @return bool
 * @since 0.1
 */
function psp_register_check(  $login, $email, $errors  ) {
    if ( !isset( $_POST['_psp_confirm_age'] ) ) {
        $errors->add( 'empty_age_confirm', '<strong>ERROR</strong>: ' . apply_filters( 'psp_registration_error', __( 'Please confirm your age', 'pretty-simple-popup' ) ) );
    }
}

function psp_upgrade_url(  $params = array()  ) {
    $defaults = array(
        'checkout'      => 'true',
        'plan_id'       => 15099,
        'plan_name'     => 'premium',
        'billing_cycle' => 'annual',
        'licenses'      => 1,
    );
    $params = wp_parse_args( $params, $defaults );
    return add_query_arg( $params, psp_fs()->get_upgrade_url() );
}

function psp_display_upgrade_features() {
    $contents = '<table class="form-table psp-premium-features">
		<tr class="psp-premiumHead">
			<th class="psp-preBanner" scope="column" colspan=2>
				<h1>Unlock Premium Designs</h1>
			</th>
		</tr>
		<tr><td colspan=2><center><b>Early Adopter Special $1/month</b></center></td></tr>';
    foreach ( psp_premium_features() as $feature => $desc ) {
        $contents .= '<tr>
				<th class="psp-preBanner" width="30%" scope="column"><span class="dashicons dashicons-yes psp-premium"></span><span class="psp-premium-feature">' . $feature . '</span></th>
					<td width="70%" scope="column"><em>' . $desc . '</em>
				</th>
			</tr>';
    }
    $contents .= '<tr>
			<th style="text-align: center; padding-bottom: 20px;" scope="column" colspan="2"><a class="psp-btnBuy" href="' . esc_url( psp_upgrade_url() ) . '">Upgrade Now</a>
			</th>
		</tr>';
    if ( !psp_fs()->is_trial() ) {
        $contents .= '<tr>
			<th style="text-align: center; padding-bottom: 20px;" scope="column" colspan="2"><a class="psp-trialLink" href="' . esc_url( '/wp-admin/admin.php?trial=true&page=pretty-simple-popup-pricing' ) . '">' . __( 'Start 14-Day Free Trial', 'pretty-simple-popup' ) . '</a><span style="font-weight: 400;">' . __( '(risk free, no credit card)', 'pretty-simple-popup' ) . '</span></th></tr>';
    } else {
        $contents .= '<tr>
			<th style="text-align: center; padding-bottom: 20px;" scope="column" colspan="2">(On Free Trial Now)
			</th></tr>';
    }
    $contents .= '<tr>
		<td style="text-align: center; padding-bottom: 20px;" scope="column" colspan="2">
		*** Upgrade now to lock in special low rate and keep it on annual renewal ***
		<p/>
		Future features are included in your plan.
		</td></tr>';
    $contents .= '</table>';
    return $contents;
}

function psp_premium_features() {
    $features = array(
        __( 'Designer Templates', 'pretty-simple-popup' )    => __( 'More layouts and flexibility', 'pretty-simple-popup' ),
        __( 'Fullscreen Background', 'pretty-simple-popup' ) => __( 'Color background and opacity', 'pretty-simple-popup' ),
        __( 'Supporting Text Link', 'pretty-simple-popup' )  => __( 'Up conversion with link under button', 'pretty-simple-popup' ),
        __( 'Age Verify Compatible', 'pretty-simple-popup' ) => __( 'Popup starts after verification when using our other plugins', 'pretty-simple-popup' ),
        __( 'Premium Support', 'pretty-simple-popup' )       => __( 'World-class email support from the U.S.', 'pretty-simple-popup' ),
        __( 'Coming Soon!', 'pretty-simple-popup' )          => __( 'New templates, styles and features', 'pretty-simple-popup' ),
    );
    return $features;
}

/**
 * The message for current plugin users.
 */
function psp_fs_custom_connect_message_on_update(
    $message,
    $user_first_name,
    $plugin_title,
    $user_login,
    $site_link,
    $freemius_link
) {
    return sprintf(
        __( 'Hey %1$s' ) . '<br>' . __( 'To enjoy all of the features of this plugin and future updates, Five Star Plugins needs to connect %4$s to Freemius.', 'pretty-simple-popup' ),
        $user_first_name,
        '<b>' . $plugin_title . '</b>',
        '<b>' . $user_login . '</b>',
        $site_link,
        $freemius_link
    );
}

/**
 * The message for new plugin users.
 */
function psp_freemius_new_message(
    $message,
    $user_first_name,
    $plugin_title,
    $user_login,
    $site_link,
    $freemius_link
) {
    return sprintf(
        __( 'Hey %1$s' ) . '<br>' . __( 'To enjoy all of the features of this plugin and future updates, Five Star Plugins needs to connect %4$s to Freemius.', 'pretty-simple-popup' ),
        $user_first_name,
        '<b>' . $plugin_title . '</b>',
        '<b>' . $user_login . '</b>',
        $site_link,
        $freemius_link
    );
}

/**
 * Load Localization files.
 *
 * Note: the first-loaded translation file overrides any following ones if the same translation is present.
 *
 * Locales are found in:
 * - WP_LANG_DIR/plugins/pretty-simple-popup-LOCALE.mo
 *
 * Example:
 * - WP_LANG_DIR/plugins/pretty-simple-popup-pt_PT.mo
 */
function psp_load_plugin_textdomain() {
    $locale = apply_filters( 'plugin_locale', get_locale(), 'pretty-simple-popup' );
    load_textdomain( 'pretty-simple-popup', WP_LANG_DIR . '/plugins/pretty-simple-popup-' . $locale . '.mo' );
    load_plugin_textdomain( 'pretty-simple-popup', false, dirname( plugin_basename( __FILE__ ) ) . '/languages/' );
}

/**
 * @return mixed|void
 */
function psp_age_verify_option() {
    return get_option( '_psp_user_age_verify_option' );
}

/**
 * Echoes the overlay disclaimer, which lives below the form.
 *
 * @since 0.1
 * @echo  string
 */
function psp_the_disclaimer() {
    echo psp_get_the_disclaimer();
}

/**
 * Returns the overlay disclaimer, which lives below the form.
 * You can filter this if you like.
 *
 * @return string|false
 * @since 0.1
 */
function psp_get_the_disclaimer() {
    $desc = apply_filters( 'psp_disclaimer', get_option( '_psp_disclaimer' ) );
    if ( !empty( $desc ) ) {
        return $desc;
    } else {
        return false;
    }
}

/**
 * Prints small label description
 *
 * @param $string
 *
 * @return string
 */
function psp_small(  $string  ) {
    return sprintf( '<br><small>%s</small>', $string );
}

function psp_get_cookie_validation() {
    return 'expires: ' . 420 * 60 . ',';
}

/**
 * Returns the overlay background color
 * You can filter this if you like.
 *
 * @return string
 * @since 0.1
 */
function psp_get_box_color() {
    if ( get_option( '_psp_box_color' ) ) {
        $color = get_option( '_psp_box_color' );
    } else {
        $color = '#EEEEEE';
    }
    return apply_filters( 'psp_box_color', $color );
}

/**
 * Returns the overlay background color
 * You can filter this if you like.
 *
 * @return string
 * @since 0.1
 */
function psp_get_overlay_color() {
    if ( get_option( '_psp_overlay_color' ) ) {
        $color = get_option( '_psp_overlay_color' );
    } else {
        $color = '#000000';
    }
    return apply_filters( 'psp_overlay_color', $color );
}

/**
 * Returns the overlay's background color
 * You can filter this if you like.
 *
 * @return string
 * @since 0.1
 */
function psp_get_agree_btn_background_color() {
    if ( get_option( '_psp_agree_btn_bgcolor' ) ) {
        $color = get_option( '_psp_agree_btn_bgcolor' );
    } else {
        $color = '#555555';
    }
    return apply_filters( 'psp_agree_btn_background_color', $color );
}

/**
 * Returns the overlay's background color
 * You can filter this if you like.
 *
 * @return string
 * @since 0.1
 */
function psp_get_disAgree_btn_background_color() {
    if ( get_option( '_psp_disAgree_btn_bgcolor' ) ) {
        $color = get_option( '_psp_disAgree_btn_bgcolor' );
    } else {
        $color = '#EEEEEE';
    }
    return apply_filters( 'psp_disAgree_btn_background_color', $color );
}

/**
 * Gets the CTA button text.
 *
 * @since 1.2
 * @echo  string
 */
function psp_get_custom_agreebutton_text() {
    if ( get_option( '_psp_custom_agreebutton_text' ) ) {
        return get_option( '_psp_custom_agreebutton_text' );
    } else {
        return '';
    }
}

/**
 * Gets the CTA button link URL.
 *
 * @since 1.2
 * @echo  string
 */
function psp_get_ctabutton_link() {
    if ( get_option( '_psp_ctabutton_link' ) ) {
        return get_option( '_psp_ctabutton_link' );
    } else {
        return '#';
    }
}

/**
 * Gets the custom disagree button text.
 *
 * @since 1.2
 * @echo  string
 */
function psp_get_custom_disagreebutton_text() {
    if ( get_option( '_psp_custom_disagreebutton_text' ) ) {
        return get_option( '_psp_custom_disagreebutton_text' );
    } else {
        return '';
    }
}

/**
 * Gets the Image link URL.
 *
 * @since 1.2
 * @echo  string
 */
function psp_get_image_link() {
    if ( get_option( '_psp_image_link' ) ) {
        return get_option( '_psp_image_link' );
    } else {
        return '';
    }
}

/**
 * Gets the More Info text link URL.
 *
 * @since 1.2
 * @echo  string
 */
function psp_get_moreinfo_link() {
    if ( get_option( '_psp_moreinfo_link' ) ) {
        return esc_url( get_option( '_psp_moreinfo_link' ) );
    } else {
        return '';
    }
}

/**
 * Echoes the custom agree button text
 *
 * @since 1.2
 * @echo  string
 */
function psp_custom_agreebutton_text() {
    printf( '%s', psp_get_custom_agreebutton_text() );
}

/**
 * Echoes the overlay headline, which lives above the description and above the form.
 *
 * @since 1.2
 * @echo  string
 */
function psp_custom_disagreebutton_text() {
    printf( '%s', psp_get_custom_disagreebutton_text() );
}

/**
 * Calculates text color based on background color, chooses white or black
 *
 * @since 0.1
 * @echo  string
 */
function getContrastYIQ(  $hexcolor  ) {
    //	$hexcolor = psp_get_overlay_color();
    $r = hexdec( substr( $hexcolor, 0, 2 ) );
    $g = hexdec( substr( $hexcolor, 2, 2 ) );
    $b = hexdec( substr( $hexcolor, 4, 2 ) );
    $yiq = ($r * 299 + $g * 587 + $b * 114) / 1000;
    return ( $yiq >= 128 ? 'black' : 'white' );
}

/**
 * Echoes the overlay headline, which lives above the description and above the form.
 *
 * @since 0.1
 * @echo  string
 */
function psp_the_age_heading() {
    if ( get_option( '_psp_popup_template', 'free-1' ) == 'free-1' || get_option( '_psp_popup_template', 'free-1' ) == 'prem-1' ) {
        printf( '<h1 style="color:' . getContrastYIQ( ltrim( psp_get_box_color(), '#' ) ) . ';">%s</h1>', psp_get_the_heading() );
    }
}

/**
 * Echoes the overlay description, which lives below the heading and above the form.
 *
 * @since 0.1
 * @echo  string
 */
function psp_the_desc() {
    if ( get_option( '_psp_popup_template', 'free-1' ) == 'free-1' || get_option( '_psp_popup_template', 'free-1' ) == 'prem-1' ) {
        $desc_text = str_replace( "\r\n", '<br>', trim( psp_get_the_desc() ) );
        printf( '<p style="color:' . getContrastYIQ( ltrim( psp_get_box_color(), '#' ) ) . ';">%s</p>', html_entity_decode( $desc_text ) );
    }
}

/**
 * Returns the overlay heading. You can filter this if you like.
 *
 * @return string
 * @since 0.1
 */
function psp_get_the_heading() {
    $psp_heading = apply_filters( 'psp_heading', get_option( '_psp_heading', __( 'Your text here', 'pretty-simple-popup' ) ) );
    if ( !empty( $psp_heading ) ) {
        return $psp_heading;
    } else {
        return false;
    }
}

/**
 * Returns the overlay description, which lives below the heading and above the form.
 * You can filter this if you like.
 *
 * @return string|false
 * @since 0.1
 */
function psp_get_the_desc() {
    $psp_desc = apply_filters( 'psp_description', get_option( '_psp_description', __( 'Your text here', 'pretty-simple-popup' ) ) );
    if ( !empty( $psp_desc ) ) {
        return $psp_desc;
    } else {
        return false;
    }
}

/**
 * Returns the cookie length field
 * You can filter this if you like.
 *
 * @return string|false
 * @since 0.1
 */
function psp_get_cookie_length() {
    if ( get_option( '_psp_cookie_length' ) ) {
        $psp_cookie_length = get_option( '_psp_cookie_length' );
    } else {
        $psp_cookie_length = "1";
    }
    return apply_filters( '_psp_cookie_length', $psp_cookie_length );
}

/**
 * Returns the delay timer field
 * You can filter this if you like.
 *
 * @return string|false
 * @since 0.1
 */
function psp_get_delay_timer() {
    if ( get_option( '_psp_delay_timer' ) >= 0 ) {
        $delay_timer = get_option( '_psp_delay_timer' );
    } else {
        $delay_timer = "5";
    }
    return apply_filters( 'psp_delay_timer', $delay_timer );
}

/**
 * Returns the transparency field
 * You can filter this if you like.
 *
 * @return string|false
 * @since 0.1
 */
function psp_get_transparency() {
    if ( get_option( '_psp_popup_template' ) == 'free-1' || get_option( '_psp_popup_template' ) == 'free-2' ) {
        $overlay_opacity = 0.5;
    } else {
        if ( get_option( '_psp_adjust_transparency' ) ) {
            $overlay_opacity = get_option( '_psp_adjust_transparency' );
        } else {
            $overlay_opacity = 0.5;
        }
    }
    return $overlay_opacity;
}

// IMAGE Display
/**
 * Returns the overlay's Logo
 * You can filter this if you like.
 *
 * @return string
 * @since 0.1
 */
function psp_get_logo() {
    if ( get_option( '_psp_logo' ) ) {
        $logo = get_option( '_psp_logo' );
    } else {
        $logo = '';
    }
    return $logo;
}

function psp_add_logo() {
    if ( get_option( '_psp_popup_template' ) == 'free-2' || get_option( '_psp_popup_template' ) == 'prem-1' || get_option( '_psp_popup_template' ) == 'prem-2' ) {
        $psp_image_html = '';
        if ( $logo = psp_get_logo() ) {
            if ( psp_get_image_link() ) {
                $psp_image_html = '<a href="' . psp_get_image_link() . '"><img class="psp-logo" alt="Popup Image" src="' . esc_html( $logo ) . '" /></a>';
            } else {
                $psp_image_html = '<img class="psp-logo-nolink" alt="Popup Image" src="' . esc_html( $logo ) . '" />';
            }
            if ( get_option( '_psp_edge_to_edge_image' ) != 'True' && get_option( '_psp_popup_template' ) == 'prem-1' ) {
                echo '<div id="psp-small-image">' . $psp_image_html . '</div>';
            } else {
                echo $psp_image_html;
            }
        }
    }
}

function psp_premium_overlay_style() {
    if ( get_option( '_psp_popup_template', 'free-1' ) == 'prem-1' || get_option( '_psp_popup_template', 'free-1' ) == 'prem-2' ) {
        $bg_clr = ltrim( psp_get_overlay_color(), '#' );
        $split_bg_clr = str_split( $bg_clr, 2 );
        return sprintf(
            "style='background:rgba(%s,%s,%s,%s);'",
            $r_bg_clr = hexdec( $split_bg_clr[0] ),
            $g_bg_clr = hexdec( $split_bg_clr[1] ),
            $b_bg_clr = hexdec( $split_bg_clr[2] ),
            psp_get_transparency()
        );
    }
}

add_filter( 'psp_before_form', 'psp_add_logo', 1 );
add_filter( 'psp_before_form', 'psp_the_age_heading', 2 );
add_filter( 'psp_before_form', 'psp_the_desc', 3 );