<?php
/**
 * Uninstall Script
 *
 * Removes all plugin data when the plugin is deleted.
 *
 * @package Unstoppable_Product_Sorting
 */

// If uninstall not called from WordPress, exit.
if ( ! defined( 'WP_UNINSTALL_PLUGIN' ) ) {
    exit;
}

global $wpdb;

// Delete custom table.
// phpcs:ignore WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedVariableFound
$upso_table_name = $wpdb->prefix . 'upso_product_order';
// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching, WordPress.DB.DirectDatabaseQuery.SchemaChange, WordPress.DB.PreparedSQL.InterpolatedNotPrepared, PluginCheck.Security.DirectDB.UnescapedDBParameter
$wpdb->query( "DROP TABLE IF EXISTS {$upso_table_name}" );

// Delete options.
delete_option( 'upso_db_version' );
delete_option( 'upso_affect_all_loops' );
delete_option( 'upso_install_date' );
delete_option( 'upso_welcome_notice_dismissed' );
delete_option( 'upso_review_notice_dismissed' );

// Clear any transients.
delete_transient( 'upso_product_count' );
