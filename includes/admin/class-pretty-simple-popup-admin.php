<?php

/**
 * Define the admin class
 *
 * @since   0.2.6
 *
 * @package Pretty_Simple_Popup\Admin
 */
// Don't allow this file to be accessed directly.
if ( !defined( 'WPINC' ) ) {
    die;
}
/**
 * The admin class.
 *
 * @since 0.2.6
 */
final class Pretty_Simple_Popup_Admin {
    /**
     * The only instance of this class.
     *
     * @since  0.2.6
     * @access protected
     */
    protected static $instance = null;

    /**
     * Construct the class!
     *
     * @return void
     * @since 0.2.6
     *
     */
    public function __construct() {
        global $psp_fs;
        $this->version = Pretty_Simple_Popup::VERSION;
        /**
         * The settings callbacks.
         */
        require PSP_PLUGIN_DIR_PATH . 'includes/admin/settings.php';
        // default stock age option
        $optionVerify1 = '_psp_user_age_verify_option';
        // $optionVerify1 = $optionVerify2 = '';
        if ( empty( get_option( $optionVerify1 ) ) ) {
            //update_option( $optionVerify1, 1 );
        }
        // set disable verification on initial install
        $optionVerify2 = '_psp_always_verify';
        if ( empty( get_option( $optionVerify2 ) ) ) {
            update_option( $optionVerify2, 'disabled' );
        }
        if ( $psp_fs->is_not_paying() ) {
            add_action( 'admin_enqueue_scripts', array($this, 'psp_beacon_header_free') );
            // Add Helpscout Free Beacon code
        }
        // Enqueue the script.
        add_action( 'admin_enqueue_scripts', array($this, 'enqueue_scripts') );
        // Add the settings page.
        add_action( 'admin_menu', array($this, 'add_settings_page') );
        // Add and register the settings sections and fields.
        add_action( 'admin_init', array($this, 'register_settings') );
        add_action( 'admin_init', array($this, 'default_settings') );
        // Add the "Settings" link to the plugin row.
        add_filter(
            'plugin_action',
            array($this, 'add_settings_link'),
            10,
            2
        );
        psp_load_plugin_textdomain();
        // Only load with post-specific stuff if enabled.
        if ( 'content' == get_option( '_psp_require_for' ) ) {
            // Add a "restrict" checkbox to individual posts/pages.
            add_action( 'post_submitbox_misc_actions', array($this, 'add_submitbox_checkbox') );
            // Save the "restrict" checkbox value.
            add_action( 'save_post', array($this, 'save_post') );
        }
    }

    /**
     * Get the only instance of this class.
     *
     * @return object $instance The only instance of this class.
     * @since 0.2.6
     *
     */
    public static function get_instance() {
        if ( is_null( self::$instance ) ) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    /**
     * Add a direct link to the Age Verify settings page from the plugins page.
     *
     * @param array $actions The links beneath the plugin's name.
     * @param       $plugin_file
     *
     * @return array
     * @since 0.2.6
     *
     */
    public static function add_settings_link( $actions, $plugin_file ) {
        static $plugin;
        if ( !isset( $plugin ) ) {
            $plugin = plugin_basename( psp_PLUGIN_FILE );
        }
        if ( $plugin == $plugin_file ) {
            $settings_link = sprintf( '<a href="%s">%s</a>', $link = esc_url( add_query_arg( 'page', 'pretty-simple-popup', admin_url( 'admin.php' ) ) ), $menu_text = __( 'Settings', 'pretty-simple-popup' ) );
            array_unshift( $actions, $settings_link );
        }
        return $actions;
    }

    /**
     * Prevent cloning of this class.
     *
     * @return void
     * @since 0.2.6
     *
     */
    public function __clone() {
        _doing_it_wrong( __FUNCTION__, __( 'Cheatin&#8217; huh?', 'pretty-simple-popup' ), $this->version );
    }

    /**
     * Prevent unserializing of this class.
     *
     * @return void
     * @since 0.2.6
     *
     */
    public function __wakeup() {
        _doing_it_wrong( __FUNCTION__, __( 'Cheatin&#8217; huh?', 'pretty-simple-popup' ), $this->version );
    }

    /**
     * Add to the settings page.
     *
     * @return void
     * @since 0.2.6
     *
     */
    public function add_settings_page() {
        add_menu_page(
            'Pretty Simple Popup',
            'Pretty Simple Popup',
            'manage_options',
            'pretty-simple-popup',
            'psp_settings_page',
            'dashicons-slides'
        );
    }

    /**
     * Add and register the settings sections and fields.
     *
     * @return void
     * @since 0.2.6
     *
     */
    public function register_settings() {
        // General Section
        add_settings_section(
            'psp_settings_general',
            __( '1.) SET POPUP' . psp_small( 'Start showing the popup and decide how often.' ), 'pretty-simple-popup' ),
            'psp_settings_callback_section_general',
            'pretty-simple-popup'
        );
        // Set to Disabled or Who to verify (not logged in or all)
        add_settings_field(
            '_psp_always_verify',
            __( 'Enable Popup', 'pretty-simple-popup' ) . ' <span class="dashicons dashicons-info pspoptionshovertip" title="' . __( 'Disable turns off popup. Use Testing Mode during setup, then show when it\'s ready.', 'pretty-simple-popup' ) . '"></span>',
            'psp_settings_callback_always_verify_field',
            'pretty-simple-popup',
            'psp_settings_general'
        );
        register_setting( 'pretty-simple-popup', '_psp_always_verify', 'esc_attr' );
        // Adjust Delay Timer
        add_settings_field(
            '_psp_delay_timer',
            '<label for="_psp_delay_timer">' . __( 'Delay Showing For', 'pretty-simple-popup' ) . ' <span class="dashicons dashicons-info pspoptionshovertip" title="' . __( 'Set timer before popup opens on page load. Minimum 5 seconds recommended.', 'pretty-simple-popup' ) . '"></span></label>',
            'psp_settings_callback_delay_timer_field',
            'pretty-simple-popup',
            'psp_settings_general'
        );
        register_setting( 'pretty-simple-popup', '_psp_delay_timer', 'esc_attr' );
        if ( psp_fs()->is_not_paying() ) {
            // Add Age Verify Promo only in Free
            add_settings_field(
                '_psp_ageverify_promo',
                '<label class="psppremhovertip" title="Upgrade to Premium for Age Verify Compatibility" for="_psp_ageverify_promo">' . __( 'UPGRADE to Age Verify Compatible', 'pretty-simple-popup' ) . '</label>',
                'psp_settings_callback_ageverify_promo',
                'pretty-simple-popup',
                'psp_settings_general'
            );
        }
        // Adjust Cookie Length
        add_settings_field(
            '_psp_cookie_length',
            '<label for="_psp_cookie_length">' . __( 'Don\'t Display Again For', 'pretty-simple-popup' ) . ' <span class="dashicons dashicons-info pspoptionshovertip" title="' . __( 'Decide how frequently visitors see it (greater than 1). Sets cookie except in Testing Mode.', 'pretty-simple-popup' ) . '"></span></label>',
            'psp_settings_callback_delay_cookie_length',
            'pretty-simple-popup',
            'psp_settings_general'
        );
        register_setting( 'pretty-simple-popup', '_psp_cookie_length', 'esc_attr' );
        // Choose pop-up template
        add_settings_section(
            'psp_settings_template',
            __( '2.) SELECT A TEMPLATE' . psp_small( 'Choose a template to start designing.' ), 'pretty-simple-popup' ),
            'psp_settings_callback_section_display',
            'pretty-simple-popup'
        );
        add_settings_field(
            '_psp_popup_template',
            __( '', 'pretty-simple-popup' ),
            '_psp_popup_template_callback_field',
            'pretty-simple-popup',
            'psp_settings_template'
        );
        register_setting( 'pretty-simple-popup', '_psp_popup_template', 'esc_attr' );
        if ( psp_fs()->is_not_paying() ) {
            add_settings_section(
                'psp_settings_design_popup',
                __( '3.) DESIGN POPUP' . psp_small( 'Preview your popup in Testing Mode. Save changes then visit your site while logged in. Try the PREMIUM plugin for more options.' ), 'pretty-simple-popup' ),
                'psp_settings_callback_section_display',
                'pretty-simple-popup'
            );
        } else {
            add_settings_section(
                'psp_settings_design_popup',
                __( '3.) DESIGN POPUP' . psp_small( 'Preview your popup in Testing Mode. Save changes then visit your site while logged in.' ), 'pretty-simple-popup' ),
                'psp_settings_callback_section_display',
                'pretty-simple-popup'
            );
        }
        // Image header and selector
        add_action( 'admin_enqueue_scripts', function () {
            wp_enqueue_media();
        } );
        add_settings_section(
            'psp_settings_image',
            __( '- IMAGE', 'pretty-simple-popup' ),
            'psp_settings_callback_section_display',
            'pretty-simple-popup'
        );
        add_settings_field(
            '_psp_image_link',
            '<label for="_psp_image_link">' . __( 'Image Link', 'pretty-simple-popup' ) . ' <span class="dashicons dashicons-info pspoptionshovertip" title="' . __( 'Enter a URL to link the image (optional). SMARTlink automatically opens external URLs in a new window.', 'pretty-simple-popup' ) . '"></span>' . __( psp_small( '(URL)' ), 'pretty-simple-popup' ) . '</label>',
            'psp_settings_callback_image_link_field',
            'pretty-simple-popup',
            'psp_settings_image'
        );
        register_setting( 'pretty-simple-popup', '_psp_image_link', 'esc_attr' );
        add_settings_field(
            '_psp_logo',
            __( 'Upload Image' . psp_small( '' ), 'pretty-simple-popup' ),
            'psp_settings_callback_logo_field',
            'pretty-simple-popup',
            'psp_settings_image'
        );
        register_setting( 'pretty-simple-popup', '_psp_logo', 'esc_attr' );
        // Design Text Section
        add_settings_section(
            'psp_settings_copy',
            __( '- COPY', 'pretty-simple-popup' ),
            'psp_settings_callback_section_display',
            'pretty-simple-popup'
        );
        // Heading Text
        add_settings_field(
            '_psp_heading',
            '<label for="_psp_heading">' . __( 'Headline Text ', 'pretty-simple-popup' ) . ' <span class="dashicons dashicons-info pspoptionshovertip" title="' . __( 'SMARTtext automatically uses site fonts and sets the text color to black or white based on the background.', 'pretty-simple-popup' ) . '"></span>' . __( psp_small( '(max 50 characters)' ), 'pretty-simple-popup' ) . '</label>',
            'psp_settings_callback_headline_field',
            'pretty-simple-popup',
            'psp_settings_copy'
        );
        register_setting( 'pretty-simple-popup', '_psp_heading', 'wp_kses_post' );
        // Subhead Text
        add_settings_field(
            '_psp_description',
            '<label for="_psp_description">' . __( 'Body Text ', 'pretty-simple-popup' ) . ' <span class="dashicons dashicons-info pspoptionshovertip" title="' . __( 'SMARTtext automatically uses site fonts and sets the text color to black or white based on the background.', 'pretty-simple-popup' ) . '"></span>' . __( psp_small( '(max 150 characters)' ), 'pretty-simple-popup' ) . '</label>',
            'psp_settings_callback_description_field',
            'pretty-simple-popup',
            'psp_settings_copy'
        );
        register_setting( 'pretty-simple-popup', '_psp_description', 'wp_kses_post' );
        // Design Text and Button Section
        add_settings_section(
            'psp_settings_button',
            __( '- CTA BUTTON', 'pretty-simple-popup' ),
            'psp_settings_callback_section_display',
            'pretty-simple-popup'
        );
        // CTA Button Text
        add_settings_field(
            '_psp_custom_agreebutton_text',
            '<label for="_psp_custom_agreebutton_text">' . __( 'Button Text ', 'pretty-simple-popup' ) . ' <span class="dashicons dashicons-info pspoptionshovertip" title="' . __( 'SMARTtext automatically uses site fonts and sets the text color to black or white based on the background.', 'pretty-simple-popup' ) . '"></span>' . __( psp_small( '(max 30 characters)' ), 'pretty-simple-popup' ) . '</label>',
            'psp_settings_callback_custom_agreebutton_text_field',
            'pretty-simple-popup',
            'psp_settings_button'
        );
        register_setting( 'pretty-simple-popup', '_psp_custom_agreebutton_text', 'wp_kses_post' );
        // CTA Button Link
        add_settings_field(
            '_psp_ctabutton_link',
            '<label for="_psp_custom_agreebutton_text">' . __( 'Button Link ', 'pretty-simple-popup' ) . ' <span class="dashicons dashicons-info pspoptionshovertip" title="' . __( 'Enter your URL. SMARTlink automatically opens external links in a new window.', 'pretty-simple-popup' ) . '"></span>' . __( psp_small( '(URL)' ), 'pretty-simple-popup' ) . '</label>',
            'psp_settings_callback_ctabutton_link_field',
            'pretty-simple-popup',
            'psp_settings_button'
        );
        register_setting( 'pretty-simple-popup', '_psp_ctabutton_link', 'wp_kses_post' );
        // CTA Button Color
        add_settings_field(
            '_psp_agree_btn_bgcolor',
            __( 'Button Color ' . psp_small( '(HEX #)' ), 'pretty-simple-popup' ),
            'psp_settings_callback_agree_btn_bgcolor_field',
            'pretty-simple-popup',
            'psp_settings_button'
        );
        register_setting( 'pretty-simple-popup', '_psp_agree_btn_bgcolor', 'esc_attr' );
        // Box Section
        add_settings_section(
            'psp_settings_modal',
            __( '- MODAL', 'pretty-simple-popup' ),
            'psp_settings_callback_section_display',
            'pretty-simple-popup'
        );
        // Pop Up Box Show/Hide: Now Hardcoded ON text templates, OFF image templates.
        //	add_settings_field( '_psp_box_show',
        //		__( 'Show Box:', 'pretty-simple-popup' ),
        //		'psp_settings_callback_box_show_field',
        //		'pretty-simple-popup',
        //		'psp_settings_modal' );
        //	register_setting( 'pretty-simple-popup', '_psp_box_show', 'esc_attr' );
        // Pop Up Box Color
        add_settings_field(
            '_psp_box_color',
            __( 'Box Color' . psp_small( '(HEX #)' ), 'pretty-simple-popup' ),
            'psp_settings_callback_box_color_field',
            'pretty-simple-popup',
            'psp_settings_modal'
        );
        register_setting( 'pretty-simple-popup', '_psp_box_color', 'esc_attr' );
        // Hook into premium settings if on a trial or paid plan
        if ( function_exists( 'psp_premium_settings' ) ) {
            psp_premium_settings();
        }
        // Advanced Options
        add_settings_section(
            'psp_settings_advanced',
            __( 'Optional Advanced Settings >' . psp_small( '(Click to expand)' ), 'pretty-simple-popup' ),
            'psp_settings_callback_section_display',
            'pretty-simple-popup'
        );
        // AJAX Settings Check
        add_settings_field(
            '_psp_settings_ajax',
            __( 'Realtime Settings Check', 'pretty-simple-popup' ) . ' <span class="dashicons dashicons-info pspoptionshovertip" title="' . __( 'May help caching issues, adds delay to popup.', 'pretty-simple-popup' ) . '"></span>' . psp_small( '<a href="https://support.5starplugins.com/article/229-realtime-settings-check" target="_blank">Learn more</a>', 'pretty-simple-popup' ),
            'psp_settings_callback_ajax_check',
            'pretty-simple-popup',
            'psp_settings_advanced'
        );
        register_setting( 'pretty-simple-popup', '_psp_ajax_check', 'esc_attr' );
        // Reset cookie
        add_settings_field(
            '_psp_reset_cookie',
            __( 'Reset Cookie For Visitors', 'pretty-simple-popup' ) . ' <span class="dashicons dashicons-info pspoptionshovertip" title="' . __( 'Popup will start showing to all visitors again immediately.', 'pretty-simple-popup' ) . '"></span>',
            'psp_settings_callback_reset_cookie',
            'pretty-simple-popup',
            'psp_settings_advanced'
        );
        register_setting( 'pretty-simple-popup', '_psp_reset_cookie', 'esc_attr' );
        add_settings_field(
            '_psp_new_cookie_name',
            __( 'Current Cookie Name', 'pretty-simple-popup' ),
            'psp_settings_callback_cookie_name',
            'pretty-simple-popup',
            'psp_settings_advanced'
        );
        register_setting( 'pretty-simple-popup', '_psp_new_cookie_name', 'esc_attr' );
        do_action( 'psp_register_settings' );
    }

    /**
     * Adds default plugin settings
     */
    public function default_settings() {
        $defaults = array(
            '_psp_always_verify'   => 'disabled',
            '_psp_new_cookie_name' => 'psp-popup-displayed-1234',
            '_psp_reset_cookie'    => 'false',
        );
        $options = wp_parse_args( get_option( 'pretty-simple-popup' ), $defaults );
    }

    /**
     * Enqueue the scripts.
     *
     * @param string $page The current admin page.
     *
     * @return void
     * @since 0.2.6
     *
     */
    public function enqueue_scripts( $page ) {
        // toplevel_page_pretty-simple-popup
        if ( 'toplevel_page_pretty-simple-popup' != $page ) {
            return;
        }
        add_thickbox();
        wp_enqueue_style( 'wp-color-picker' );
        wp_enqueue_style(
            'psp-admin-styles',
            plugin_dir_url( __FILE__ ) . 'assets/styles.css',
            array(),
            filemtime( plugin_dir_path( __FILE__ ) . 'assets/styles.css' )
        );
        wp_enqueue_script(
            'psp-admin-scripts',
            plugin_dir_url( __FILE__ ) . 'assets/scripts.js',
            array('jquery', 'wp-color-picker'),
            filemtime( plugin_dir_path( __FILE__ ) . 'assets/scripts.js' )
        );
    }

    public function psp_beacon_header_prem( $page ) {
        if ( 'toplevel_page_pretty-simple-popup' != $page ) {
            return;
        }
        $beacon_html = '<script type="text/javascript">!function(e,t,n){function a(){var e=t.getElementsByTagName("script")[0],n=t.createElement("script");n.type="text/javascript",n.async=!0,n.src="https://beacon-v2.helpscout.net",e.parentNode.insertBefore(n,e)}if(e.Beacon=n=function(t,n,a){e.Beacon.readyQueue.push({method:t,options:n,data:a})},n.readyQueue=[],"complete"===t.readyState)return a();e.attachEvent?e.attachEvent("onload",a):e.addEventListener("load",a,!1)}(window,document,window.Beacon||function(){});';
        $beacon_html .= "window.Beacon('init', 'a03ffc49-a570-49c2-b2cf-b5fd891c4573');</script>";
        echo $beacon_html;
    }

    public function psp_beacon_header_free( $page ) {
        if ( 'toplevel_page_pretty-simple-popup' != $page ) {
            return;
        }
        $beacon_html = '<script type="text/javascript">!function(e,t,n){function a(){var e=t.getElementsByTagName("script")[0],n=t.createElement("script");n.type="text/javascript",n.async=!0,n.src="https://beacon-v2.helpscout.net",e.parentNode.insertBefore(n,e)}if(e.Beacon=n=function(t,n,a){e.Beacon.readyQueue.push({method:t,options:n,data:a})},n.readyQueue=[],"complete"===t.readyState)return a();e.attachEvent?e.attachEvent("onload",a):e.addEventListener("load",a,!1)}(window,document,window.Beacon||function(){});';
        $beacon_html .= "window.Beacon('init', '68f2f492-6800-4597-94dd-176041ce36a2');</script>";
        echo $beacon_html;
    }

    /**
     * Add a "restrict" checkbox to individual posts/pages.
     *
     * @return void
     * @since 0.2.6
     *
     */
    public function add_submitbox_checkbox() {
        ?>

        <div class="misc-pub-section verify-age">

			<?php 
        wp_nonce_field( 'psp_save_post', 'psp_nonce' );
        ?>

            <input type="checkbox" name="_psp_needs_verify" id="_psp_needs_verify" value="1" <?php 
        checked( 1, get_post_meta( get_the_ID(), '_psp_needs_verify', true ) );
        ?> />
            <label for="_psp_needs_verify" class="selectit">
				<?php 
        esc_html_e( 'Require age verification for this content', 'pretty-simple-popup' );
        ?>
            </label>

        </div><!-- .misc-pub-section -->

	<?php 
    }

    /**
     * Save the "restrict" checkbox value.
     *
     * @param int $post_id The current post ID.
     *
     * @return void
     * @since 0.2.6
     *
     */
    public function save_post( $post_id ) {
        if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
            return;
        }
        $nonce = ( isset( $_POST['psp_nonce'] ) ? $_POST['psp_nonce'] : '' );
        if ( !wp_verify_nonce( $nonce, 'psp_save_post' ) ) {
            return;
        }
        $needs_verify = ( isset( $_POST['_psp_needs_verify'] ) ? (int) $_POST['_psp_needs_verify'] : 0 );
        update_post_meta( $post_id, '_psp_needs_verify', $needs_verify );
    }

    /**
     * Prints small label description
     * @param $string
     *
     * @return string
     */
    public function small( $string ) : string {
        return sprintf( '<br><small>%s</small>', $string );
    }

}
