<?php
/**
 * Plaid Integration Loader
 *
 * Include this file in your main plugin file to enable Plaid integration
 *
 * @since      1.0.0
 * @package    Budgetura
 * @subpackage Budgetura/includes
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
    die;
}

/**
 * Load Plaid Integration Classes
 */
function budgetura_load_plaid() {
    // Load Plaid integration class
    require_once plugin_dir_path( __FILE__ ) . 'class-budgetura-plaid.php';

    // Load REST API class
    require_once plugin_dir_path( __FILE__ ) . 'class-budgetura-rest-api.php';
}
add_action( 'plugins_loaded', 'budgetura_load_plaid' );

/**
 * Enqueue Plaid scripts and styles
 */
function budgetura_enqueue_plaid_scripts() {
    if ( ! is_user_logged_in() ) {
        return;
    }

    // Only load on account sync page
    if ( is_page() ) {
        global $post;
        if ( $post && has_shortcode( $post->post_content, 'budgetura_account_sync' ) ) {
            wp_enqueue_script(
                'budgetura-plaid',
                plugin_dir_url( dirname( __FILE__ ) ) . 'assets/js/budgetura-plaid.js',
                array( 'jquery' ),
                '1.0.0',
                true
            );
        }
    }
}
add_action( 'wp_enqueue_scripts', 'budgetura_enqueue_plaid_scripts' );

/**
 * Register shortcode for account sync page
 */
function budgetura_account_sync_shortcode() {
    ob_start();
    include plugin_dir_path( dirname( __FILE__ ) ) . 'templates/account-sync.php';
    return ob_get_clean();
}
add_shortcode( 'budgetura_account_sync', 'budgetura_account_sync_shortcode' );

/**
 * Setup cron job for automatic syncing
 */
function budgetura_setup_plaid_cron() {
    if ( ! wp_next_scheduled( 'budgetura_plaid_auto_sync' ) ) {
        $frequency = get_option( 'budgetura_plaid_sync_frequency', 'daily' );
        wp_schedule_event( time(), $frequency, 'budgetura_plaid_auto_sync' );
    }
}
add_action( 'wp', 'budgetura_setup_plaid_cron' );

/**
 * Cron job to auto-sync all users' Plaid accounts
 */
function budgetura_auto_sync_all_users() {
    $auto_sync = get_option( 'budgetura_plaid_auto_sync', 0 );

    if ( ! $auto_sync ) {
        return;
    }

    // Get all users who have linked Plaid accounts
    $users = get_users( array(
        'meta_key' => 'dd_plaid_accounts',
        'meta_compare' => 'EXISTS'
    ) );

    foreach ( $users as $user ) {
        Budgetura_Plaid::sync_user_accounts( $user->ID );
    }
}
add_action( 'budgetura_plaid_auto_sync', 'budgetura_auto_sync_all_users' );

/**
 * Add custom cron schedules
 */
function budgetura_add_cron_schedules( $schedules ) {
    $schedules['hourly'] = array(
        'interval' => 3600,
        'display' => __( 'Every Hour', 'budgetura' )
    );

    return $schedules;
}
add_filter( 'cron_schedules', 'budgetura_add_cron_schedules' );

/**
 * Clean up cron job on plugin deactivation
 */
function budgetura_deactivate_plaid_cron() {
    $timestamp = wp_next_scheduled( 'budgetura_plaid_auto_sync' );
    if ( $timestamp ) {
        wp_unschedule_event( $timestamp, 'budgetura_plaid_auto_sync' );
    }
}
register_deactivation_hook( __FILE__, 'budgetura_deactivate_plaid_cron' );
