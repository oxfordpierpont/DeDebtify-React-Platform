<?php
/**
 * Fired during plugin deactivation.
 *
 * This class defines all code necessary to run during the plugin's deactivation.
 *
 * @since      1.0.0
 * @package    Budgetura
 * @subpackage Budgetura/includes
 */

class Budgetura_Deactivator {

    /**
     * Plugin deactivation tasks.
     *
     * - Flush rewrite rules
     * - Clear any scheduled events
     * - Note: We do NOT delete user data on deactivation (only on uninstall)
     *
     * @since    1.0.0
     */
    public static function deactivate() {
        // Flush rewrite rules
        flush_rewrite_rules();

        // Clear any scheduled cron events
        $timestamp = wp_next_scheduled( 'budgetura_daily_snapshot' );
        if ( $timestamp ) {
            wp_unschedule_event( $timestamp, 'budgetura_daily_snapshot' );
        }

        $timestamp = wp_next_scheduled( 'budgetura_bill_reminders' );
        if ( $timestamp ) {
            wp_unschedule_event( $timestamp, 'budgetura_bill_reminders' );
        }
    }
}
