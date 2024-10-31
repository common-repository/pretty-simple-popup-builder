<?php

/**
 * Plugin Name: Pretty Simple Popup Builder
 * Description: Adds a pretty popup simply to any website. Turnkey setup takes a few clicks.
 * Author:      5 Star Plugins
 * Author URI:  https://5starplugins.com/
 * Version:     1.0.9
 *
 * Requires at least: 4.6
 * Requires PHP: 5.6
 *
 * Text Domain: pretty-simple-popup
 * Domain Path: /languages
 * License: GPLv2 or later
 *
 *
 * Copyright 2023 5 Star Plugins
 *
 * The following code is a derivative work of the code from Chase Wiseman, which is licensed GPLv2.
 * This code is then also licensed under the terms of the GPLv2.
 */
/**
 * The main plugin file.
 *
 * This file loads the main plugin class and gets things running.
 *
 * @since   1.0
 *
 * @package Pretty_Simple_Popup
 */
if ( !defined( 'WPINC' ) ) {
    die;
}
define( 'PSP_PLUGIN_DIR_PATH', plugin_dir_path( __FILE__ ) );
define( 'PSP_PLUGIN_FILE', plugin_basename( __FILE__ ) );
if ( function_exists( 'psp_fs' ) ) {
    psp_fs()->set_basename( false, __FILE__ );
    return;
}
// Don't allow this file to be accessed directly.
if ( !function_exists( 'psp_fs' ) ) {
    // Create a helper function for easy SDK access.
    function psp_fs() {
        global $psp_fs;
        if ( !isset( $psp_fs ) ) {
            // Include Freemius SDK.
            require_once PSP_PLUGIN_DIR_PATH . 'includes/freemius/start.php';
            $psp_fs = fs_dynamic_init( array(
                'id'              => '9009',
                'slug'            => 'pretty-simple-popup-builder',
                'type'            => 'plugin',
                'public_key'      => 'pk_c9bd03e3c0801bc34abc5b7a1f445',
                'is_premium'      => false,
                'premium_suffix'  => 'Premium',
                'has_addons'      => false,
                'has_paid_plans'  => true,
                'trial'           => array(
                    'days'               => 14,
                    'is_require_payment' => true,
                ),
                'has_affiliation' => 'all',
                'menu'            => array(
                    'slug' => 'pretty-simple-popup',
                ),
                'is_live'         => true,
            ) );
        }
        return $psp_fs;
    }

}
global $psp_fs;
// Init Freemius.
$psp_fs = psp_fs();
$psp_fs->add_filter(
    'connect_message_on_update',
    'psp_fs_custom_connect_message_on_update',
    10,
    6
);
$psp_fs->add_filter(
    'connect_message',
    'psp_freemius_new_message',
    10,
    6
);
// Signal that SDK was initiated.
do_action( 'psp_fs_loaded' );
if ( !function_exists( 'psp_fs_settings_url' ) ) {
    function psp_fs_settings_url() {
        return admin_url( 'admin.php?page=pretty-simple-popup' );
    }

}
$psp_fs->add_filter( 'connect_url', 'psp_fs_settings_url' );
$psp_fs->add_filter( 'after_skip_url', 'psp_fs_settings_url' );
$psp_fs->add_filter( 'after_connect_url', 'psp_fs_settings_url' );
$psp_fs->add_filter( 'after_pending_connect_url', 'psp_fs_settings_url' );
/**
 * The main class definition.
 */
require PSP_PLUGIN_DIR_PATH . 'includes/class-pretty-simple-popup.php';
// Get the plugin running.
add_action( 'plugins_loaded', array('Pretty_Simple_Popup', 'get_instance') );
// Check that the admin is loaded.
if ( is_admin() ) {
    /**
     * The admin class definition.
     */
    require PSP_PLUGIN_DIR_PATH . 'includes/admin/class-pretty-simple-popup-admin.php';
    // Get the plugin's admin running.
    add_action( 'plugins_loaded', array('Pretty_Simple_Popup_Admin', 'get_instance') );
}