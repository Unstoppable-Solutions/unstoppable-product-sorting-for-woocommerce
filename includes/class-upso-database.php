<?php
/**
 * Database Class
 *
 * Handles database operations for product sort orders.
 *
 * @package Unstoppable_Product_Sorting
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * UPSO_Database Class
 */
class UPSO_Database {

    /**
     * Table name (without prefix).
     *
     * @var string
     */
    private static $table_name = 'upso_product_order';

    /**
     * Get full table name with WordPress prefix.
     *
     * @return string
     */
    public static function get_table_name() {
        global $wpdb;
        return $wpdb->prefix . self::$table_name;
    }

    /**
     * Create the custom table for storing product sort orders.
     *
     * @return bool
     */
    public static function create_table() {
        global $wpdb;

        $table_name      = self::get_table_name();
        $charset_collate = $wpdb->get_charset_collate();

        $sql = "CREATE TABLE IF NOT EXISTS {$table_name} (
            id BIGINT(20) UNSIGNED NOT NULL AUTO_INCREMENT,
            product_id BIGINT(20) UNSIGNED NOT NULL,
            category_id BIGINT(20) UNSIGNED NOT NULL DEFAULT 0,
            sort_order INT(11) NOT NULL DEFAULT 0,
            created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
            updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            PRIMARY KEY (id),
            UNIQUE KEY unique_product_category (product_id, category_id),
            KEY idx_product_id (product_id),
            KEY idx_category_id (category_id),
            KEY idx_sort_order (sort_order),
            KEY idx_category_sort (category_id, sort_order)
        ) {$charset_collate};";

        require_once ABSPATH . 'wp-admin/includes/upgrade.php';
        dbDelta( $sql );

        return true;
    }

    /**
     * Check if table exists.
     *
     * @return bool
     */
    public static function table_exists() {
        global $wpdb;

        $table_name = self::get_table_name();

        // phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching
        $result = $wpdb->get_var(
            $wpdb->prepare(
                'SHOW TABLES LIKE %s',
                $table_name
            )
        );

        return $result === $table_name;
    }

    /**
     * Migrate data from legacy storage (menu_order and postmeta).
     *
     * @return array Migration result.
     */
    public static function migrate_data() {
        global $wpdb;

        $result = array(
            'success'           => false,
            'global_migrated'   => 0,
            'category_migrated' => 0,
            'errors'            => array(),
        );

        try {
            // Ensure table exists.
            self::create_table();

            // Phase 1: Migrate global sorting from menu_order.
            $result['global_migrated'] = self::migrate_global_sorting();

            // Phase 2: Migrate category-specific sorting from postmeta.
            $result['category_migrated'] = self::migrate_category_sorting();

            $result['success'] = true;

        } catch ( Exception $e ) {
            $result['errors'][] = $e->getMessage();
            UPSO_Helpers::log( 'Migration error: ' . $e->getMessage(), 'error' );
        }

        return $result;
    }

    /**
     * Migrate global sorting from wp_posts.menu_order.
     *
     * @return int Number of records migrated.
     */
    private static function migrate_global_sorting() {
        global $wpdb;

        $table_name  = self::get_table_name();
        $posts_table = $wpdb->posts;

        // phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching, WordPress.DB.PreparedSQL.InterpolatedNotPrepared, PluginCheck.Security.DirectDB.UnescapedDBParameter
        $wpdb->query(
            "INSERT INTO {$table_name} (product_id, category_id, sort_order, created_at, updated_at)
            SELECT ID, 0, menu_order, NOW(), NOW()
            FROM {$posts_table}
            WHERE post_type = 'product' AND post_status = 'publish' AND menu_order > 0
            ON DUPLICATE KEY UPDATE sort_order = VALUES(sort_order), updated_at = NOW()"
        );

        return $wpdb->rows_affected;
    }

    /**
     * Migrate category-specific sorting from postmeta.
     *
     * @return int Number of records migrated.
     */
    private static function migrate_category_sorting() {
        global $wpdb;

        $table_name     = self::get_table_name();
        $postmeta_table = $wpdb->postmeta;

        // Look for any legacy sorting meta keys (from other plugins or previous data).
        // phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching, WordPress.DB.PreparedSQL.InterpolatedNotPrepared, PluginCheck.Security.DirectDB.UnescapedDBParameter
        $wpdb->query(
            "INSERT INTO {$table_name} (product_id, category_id, sort_order, created_at, updated_at)
            SELECT
                post_id,
                CAST(SUBSTRING(meta_key, 15) AS UNSIGNED),
                CAST(meta_value AS UNSIGNED),
                NOW(),
                NOW()
            FROM {$postmeta_table}
            WHERE meta_key LIKE 'upso_sortorder_%'
                AND meta_key REGEXP '^upso_sortorder_[0-9]+$'
                AND CAST(meta_value AS UNSIGNED) >= 0
            ON DUPLICATE KEY UPDATE sort_order = VALUES(sort_order), updated_at = NOW()"
        );

        return $wpdb->rows_affected;
    }

    /**
     * Get sort order for a product and category.
     *
     * @param int $product_id  Product ID.
     * @param int $category_id Category ID (0 for global).
     * @return int|null Sort order or null if not found.
     */
    public static function get_sort_order( $product_id, $category_id = 0 ) {
        global $wpdb;

        $table_name  = self::get_table_name();
        $product_id  = absint( $product_id );
        $category_id = absint( $category_id );

        // phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching, WordPress.DB.PreparedSQL.InterpolatedNotPrepared, PluginCheck.Security.DirectDB.UnescapedDBParameter
        $result = $wpdb->get_var(
            $wpdb->prepare(
                "SELECT sort_order FROM {$table_name} WHERE product_id = %d AND category_id = %d",
                $product_id,
                $category_id
            )
        );

        return null !== $result ? (int) $result : null;
    }

    /**
     * Set sort order for a product and category.
     *
     * @param int $product_id  Product ID.
     * @param int $category_id Category ID (0 for global).
     * @param int $sort_order  Sort order value.
     * @return bool True on success.
     */
    public static function set_sort_order( $product_id, $category_id, $sort_order ) {
        global $wpdb;

        $table_name  = self::get_table_name();
        $product_id  = absint( $product_id );
        $category_id = absint( $category_id );
        $sort_order  = absint( $sort_order );

        // phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching, WordPress.DB.PreparedSQL.InterpolatedNotPrepared, PluginCheck.Security.DirectDB.UnescapedDBParameter
        $result = $wpdb->query(
            $wpdb->prepare(
                "INSERT INTO {$table_name} (product_id, category_id, sort_order, created_at, updated_at)
                VALUES (%d, %d, %d, NOW(), NOW())
                ON DUPLICATE KEY UPDATE sort_order = %d, updated_at = NOW()",
                $product_id,
                $category_id,
                $sort_order,
                $sort_order
            )
        );

        return false !== $result;
    }

    /**
     * Bulk update sort orders for multiple products.
     *
     * @param array $sort_orders Array of sort_order => product_id mappings.
     * @param int   $category_id Category ID (0 for global).
     * @return bool True on success.
     */
    public static function bulk_update_orders( $sort_orders, $category_id = 0 ) {
        global $wpdb;

        if ( ! is_array( $sort_orders ) || empty( $sort_orders ) ) {
            return false;
        }

        $table_name  = self::get_table_name();
        $category_id = absint( $category_id );

        // Validate and sanitize input.
        $sort_orders = UPSO_Helpers::sanitize_sort_orders( $sort_orders );

        if ( empty( $sort_orders ) ) {
            return false;
        }

        try {
            // Build batch INSERT ... ON DUPLICATE KEY UPDATE query.
            $values      = array();
            $product_ids = array();

            foreach ( $sort_orders as $sort_order => $product_id ) {
                $product_id = absint( $product_id );
                $sort_order = absint( $sort_order );

                if ( $product_id > 0 ) {
                    $values[]      = $wpdb->prepare( '(%d, %d, %d, NOW(), NOW())', $product_id, $category_id, $sort_order );
                    $product_ids[] = $product_id;
                }
            }

            if ( empty( $values ) ) {
                return false;
            }

            $values_sql = implode( ',', $values );

            // phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching, WordPress.DB.PreparedSQL.InterpolatedNotPrepared, WordPress.DB.PreparedSQL.NotPrepared, PluginCheck.Security.DirectDB.UnescapedDBParameter
            $update_result = $wpdb->query(
                "INSERT INTO {$table_name} (product_id, category_id, sort_order, created_at, updated_at)
                VALUES {$values_sql}
                ON DUPLICATE KEY UPDATE sort_order = VALUES(sort_order), updated_at = NOW()"
            );

            if ( false === $update_result ) {
                UPSO_Helpers::log( 'Database error during bulk update: ' . $wpdb->last_error, 'error' );
                return false;
            }

            return true;

        } catch ( Exception $e ) {
            UPSO_Helpers::log( 'Exception in bulk_update_orders: ' . $e->getMessage(), 'error' );
            return false;
        }
    }

    /**
     * Get all products in a category with their sort orders.
     *
     * @param int $category_id Category ID (0 for global).
     * @return array Array of product sort orders.
     */
    public static function get_products_by_category( $category_id = 0 ) {
        global $wpdb;

        $table_name  = self::get_table_name();
        $category_id = absint( $category_id );

        // phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching, WordPress.DB.PreparedSQL.InterpolatedNotPrepared, PluginCheck.Security.DirectDB.UnescapedDBParameter
        $results = $wpdb->get_results(
            $wpdb->prepare(
                "SELECT * FROM {$table_name} WHERE category_id = %d ORDER BY sort_order ASC",
                $category_id
            ),
            ARRAY_A
        );

        return $results ? $results : array();
    }

    /**
     * Delete all sort orders for a product.
     *
     * @param int $product_id Product ID.
     * @return bool True on success.
     */
    public static function delete_product_orders( $product_id ) {
        global $wpdb;

        $table_name = self::get_table_name();
        $product_id = absint( $product_id );

        // phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching
        $result = $wpdb->delete(
            $table_name,
            array( 'product_id' => $product_id ),
            array( '%d' )
        );

        return false !== $result;
    }

    /**
     * Delete all data (for uninstall).
     *
     * @return bool True on success.
     */
    public static function delete_all_data() {
        global $wpdb;

        $table_name = self::get_table_name();

        // phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching, WordPress.DB.DirectDatabaseQuery.SchemaChange, WordPress.DB.PreparedSQL.InterpolatedNotPrepared, PluginCheck.Security.DirectDB.UnescapedDBParameter
        $wpdb->query( "DROP TABLE IF EXISTS {$table_name}" );

        // Delete options.
        delete_option( 'upso_db_version' );
        delete_option( 'upso_affect_all_loops' );
        delete_option( 'upso_install_date' );
        delete_option( 'upso_welcome_notice_dismissed' );
        delete_option( 'upso_review_notice_dismissed' );

        return true;
    }

    /**
     * Get migration status.
     *
     * @return array Migration status information.
     */
    public static function get_migration_status() {
        return array(
            'table_exists'          => self::table_exists(),
            'total_global_orders'   => self::count_orders( 0 ),
            'total_category_orders' => self::count_category_orders(),
            'db_version'            => get_option( 'upso_db_version', '0.0.0' ),
        );
    }

    /**
     * Count orders for a category.
     *
     * @param int $category_id Category ID (0 for global).
     * @return int Count.
     */
    public static function count_orders( $category_id = 0 ) {
        global $wpdb;

        $table_name = self::get_table_name();

        if ( ! self::table_exists() ) {
            return 0;
        }

        // phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching, WordPress.DB.PreparedSQL.InterpolatedNotPrepared, PluginCheck.Security.DirectDB.UnescapedDBParameter
        $count = $wpdb->get_var(
            $wpdb->prepare(
                "SELECT COUNT(*) FROM {$table_name} WHERE category_id = %d",
                absint( $category_id )
            )
        );

        return (int) $count;
    }

    /**
     * Count all category-specific orders.
     *
     * @return int Count.
     */
    private static function count_category_orders() {
        global $wpdb;

        $table_name = self::get_table_name();

        if ( ! self::table_exists() ) {
            return 0;
        }

        // phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching, WordPress.DB.PreparedSQL.InterpolatedNotPrepared, PluginCheck.Security.DirectDB.UnescapedDBParameter
        $count = $wpdb->get_var(
            "SELECT COUNT(*) FROM {$table_name} WHERE category_id > 0"
        );

        return (int) $count;
    }
}
