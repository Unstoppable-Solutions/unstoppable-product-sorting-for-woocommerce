<?php
/**
 * Helpers Class
 *
 * Utility functions for the plugin.
 *
 * @package Unstoppable_Product_Sorting
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * UPSO_Helpers Class
 */
class UPSO_Helpers {

    /**
     * Check if WooCommerce is active.
     *
     * @return bool
     */
    public static function is_woocommerce_active() {
        return class_exists( 'WooCommerce' );
    }

    /**
     * Check if user has required permissions.
     *
     * @return bool
     */
    public static function user_has_permission() {
        if ( ! is_user_logged_in() ) {
            return false;
        }

        return current_user_can( 'manage_woocommerce' );
    }

    /**
     * Get product categories.
     *
     * @param array $args Optional. Arguments for get_terms.
     * @return array Array of product category terms.
     */
    public static function get_product_categories( $args = array() ) {
        $defaults = array(
            'taxonomy'   => 'product_cat',
            'hide_empty' => false,
            'orderby'    => 'name',
            'order'      => 'ASC',
        );

        $args = wp_parse_args( $args, $defaults );

        return get_terms( $args );
    }

    /**
     * Sanitize and validate sort orders.
     *
     * @param array $sort_orders Sort orders array.
     * @return array Sanitized sort orders.
     */
    public static function sanitize_sort_orders( $sort_orders ) {
        if ( ! is_array( $sort_orders ) ) {
            return array();
        }

        // Ensure numeric keys.
        $keys = array_keys( $sort_orders );
        if ( array_filter( $keys, 'is_numeric' ) === $keys ) {
            $sort_orders = array_combine(
                array_map( 'intval', $keys ),
                array_values( $sort_orders )
            );
        }

        // Sanitize values.
        $sort_orders = array_map( 'sanitize_text_field', wp_unslash( $sort_orders ) );
        $sort_orders = array_map( 'absint', $sort_orders );
        $sort_orders = array_filter(
            $sort_orders,
            function ( $value ) {
                return is_numeric( $value ) && $value > 0;
            }
        );

        return $sort_orders;
    }

    /**
     * Log debug message if WP_DEBUG is enabled.
     *
     * @param mixed  $message Message to log.
     * @param string $level   Log level (error, warning, info).
     */
    public static function log( $message, $level = 'info' ) {
        if ( ! defined( 'WP_DEBUG' ) || ! WP_DEBUG ) {
            return;
        }

        $prefix = '[UPS ' . strtoupper( $level ) . '] ';

        if ( is_array( $message ) || is_object( $message ) ) {
            $message = print_r( $message, true ); // phpcs:ignore WordPress.PHP.DevelopmentFunctions.error_log_print_r
        }

        error_log( $prefix . $message ); // phpcs:ignore WordPress.PHP.DevelopmentFunctions.error_log_error_log
    }

    /**
     * Get admin page URL.
     *
     * @param string $tab  Tab name.
     * @param array  $args Optional. Additional query args.
     * @return string Admin page URL.
     */
    public static function get_admin_url( $tab = '', $args = array() ) {
        $base_args = array( 'page' => 'upso-product-sorting' );

        if ( ! empty( $tab ) ) {
            $base_args['tab'] = $tab;
        }

        $args = array_merge( $base_args, $args );

        return add_query_arg( $args, admin_url( 'admin.php' ) );
    }

    /**
     * Get current tab.
     *
     * @return string Current tab slug.
     */
    public static function get_current_tab() {
        // phpcs:ignore WordPress.Security.NonceVerification.Recommended
        return isset( $_GET['tab'] ) ? sanitize_key( $_GET['tab'] ) : 'all-products';
    }

    /**
     * Get available tabs.
     *
     * @return array Tabs configuration.
     */
    public static function get_tabs() {
        return array(
            'all-products' => array(
                'label' => __( 'All Products', 'unstoppable-product-sorting-for-woocommerce' ),
                'icon'  => 'dashicons-grid-view',
            ),
            'categories'   => array(
                'label' => __( 'By Category', 'unstoppable-product-sorting-for-woocommerce' ),
                'icon'  => 'dashicons-category',
            ),
            'settings'     => array(
                'label' => __( 'Settings', 'unstoppable-product-sorting-for-woocommerce' ),
                'icon'  => 'dashicons-admin-generic',
            ),
        );
    }

    /**
     * Build category tree from flat array.
     *
     * @param array $categories Flat array of category terms.
     * @param int   $parent_id  Parent ID.
     * @return array Hierarchical category tree.
     */
    public static function build_category_tree( $categories, $parent_id = 0 ) {
        $branch = array();

        foreach ( $categories as $category ) {
            if ( $category->parent === $parent_id ) {
                $children = self::build_category_tree( $categories, $category->term_id );
                if ( $children ) {
                    $category->children = $children;
                }
                $branch[] = $category;
            }
        }

        return $branch;
    }

    /**
     * Check if a category or any of its descendants is selected.
     *
     * @param object $category Category term object.
     * @param int    $selected Selected term ID.
     * @return bool
     */
    public static function has_selected_descendant( $category, $selected ) {
        if ( $category->term_id === $selected ) {
            return true;
        }

        if ( isset( $category->children ) && ! empty( $category->children ) ) {
            foreach ( $category->children as $child ) {
                if ( self::has_selected_descendant( $child, $selected ) ) {
                    return true;
                }
            }
        }

        return false;
    }

    /**
     * Verify AJAX request.
     *
     * @param string $nonce_action Nonce action name.
     * @param string $nonce_name   Nonce field name.
     * @return bool|WP_Error True on success, WP_Error on failure.
     */
    public static function verify_ajax_request( $nonce_action = 'upso_ajax_nonce', $nonce_name = 'nonce' ) {
        // Verify nonce.
        if ( ! isset( $_POST[ $nonce_name ] ) || ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST[ $nonce_name ] ) ), $nonce_action ) ) {
            return new WP_Error( 'invalid_nonce', __( 'Security check failed.', 'unstoppable-product-sorting-for-woocommerce' ) );
        }

        // Verify permissions.
        if ( ! self::user_has_permission() ) {
            return new WP_Error( 'no_permission', __( 'You do not have permission to perform this action.', 'unstoppable-product-sorting-for-woocommerce' ) );
        }

        return true;
    }

    /**
     * Get product image HTML.
     *
     * @param WC_Product $product Product object.
     * @param string     $size    Image size.
     * @return string Image HTML.
     */
    public static function get_product_image( $product, $size = 'thumbnail' ) {
        if ( ! $product ) {
            return '';
        }

        $image = $product->get_image( $size );

        if ( empty( $image ) ) {
            $image = wc_placeholder_img( $size );
        }

        return $image;
    }

    /**
     * Format number with thousands separator.
     *
     * @param int $number Number to format.
     * @return string Formatted number.
     */
    public static function format_number( $number ) {
        return number_format_i18n( $number );
    }
}
