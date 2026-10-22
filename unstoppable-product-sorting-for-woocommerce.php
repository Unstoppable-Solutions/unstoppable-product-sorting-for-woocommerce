<?php
/**
 * Plugin Name: Unstoppable Product Sorting for WooCommerce
 * Plugin URI: https://unstoppablesolutions.app/plugins/unstoppable-product-sorting-for-woocommerce
 * Description: Take full control of your WooCommerce product display order with intuitive drag-and-drop sorting for shop pages and categories.
 * Version: 1.0.8
 * Author: Unstoppable Solutions
 * Author URI: https://unstoppablesolutions.app
 * Text Domain: unstoppable-product-sorting-for-woocommerce
 * Domain Path: /languages
 * Requires at least: 5.8
 * Tested up to: 7.1
 * Requires PHP: 7.4
 * WC requires at least: 7.0
 * WC tested up to: 10.4
 * Requires Plugins: woocommerce
 * License: GPL-2.0-or-later
 * License URI: https://www.gnu.org/licenses/gpl-2.0.html
 *
 * @package Unstoppable_Product_Sorting
 */

/*
Unstoppable Product Sorting for WooCommerce is free software: you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation, either version 2 of the License, or
any later version.

Unstoppable Product Sorting for WooCommerce is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with Unstoppable Product Sorting for WooCommerce. If not, see https://www.gnu.org/licenses/gpl-2.0.html.
*/

if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly.
}

/**
 * Define plugin constants
 */
if ( ! defined( 'UPSO_VERSION' ) ) {
    define( 'UPSO_VERSION', '1.0.8' );
}

if ( ! defined( 'UPSO_PLUGIN_DIR' ) ) {
    define( 'UPSO_PLUGIN_DIR', plugin_dir_path( __FILE__ ) );
}

if ( ! defined( 'UPSO_PLUGIN_URL' ) ) {
    define( 'UPSO_PLUGIN_URL', plugin_dir_url( __FILE__ ) );
}

if ( ! defined( 'UPSO_PLUGIN_BASENAME' ) ) {
    define( 'UPSO_PLUGIN_BASENAME', plugin_basename( __FILE__ ) );
}

if ( ! defined( 'UPSO_PLUGIN_SLUG' ) ) {
    define( 'UPSO_PLUGIN_SLUG', 'unstoppable-product-sorting-for-woocommerce' );
}

/*
 * ============================================================================
 * UNSTOPPABLE OPTIONS STUDIO - SHARED SHELL SYSTEM
 * ============================================================================
 *
 * This code creates the "Options Studio" parent menu that all Unstoppable Solutions
 * plugins share. Each plugin includes this same code block - the first plugin
 * to load creates the shell, others just add their submenus.
 * ============================================================================
 */

if ( ! function_exists( 'unstoppable_options_studio_init' ) ) {
    /**
     * Initialize the Options Studio shell menu.
     * This function is shared across all Unstoppable Solutions plugins.
     * Only the first plugin to load will create the parent menu.
     */
    function unstoppable_options_studio_init() {
        add_action( 'admin_menu', 'unstoppable_options_studio_create_menu', 9 );
        add_action( 'admin_enqueue_scripts', 'unstoppable_options_studio_enqueue_styles' );
        add_action( 'admin_bar_menu', 'unstoppable_options_studio_admin_bar', 100 );
        add_action( 'wp_enqueue_scripts', 'unstoppable_options_studio_enqueue_frontend_styles' );
    }

    /**
     * Create the Options Studio parent menu.
     */
    function unstoppable_options_studio_create_menu() {
        // Check if WooCommerce is active.
        if ( ! class_exists( 'WooCommerce' ) ) {
            return;
        }

        // Check if parent menu already exists (another Unstoppable plugin may have created it).
        global $menu;
        foreach ( $menu as $item ) {
            if ( isset( $item[2] ) && 'unstoppable-studio' === $item[2] ) {
                return; // Menu already exists.
            }
        }

        // Create the parent menu.
        add_menu_page(
            __( 'Options Studio', 'unstoppable-product-sorting-for-woocommerce' ),
            __( 'Options Studio', 'unstoppable-product-sorting-for-woocommerce' ),
            'manage_woocommerce',
            'unstoppable-studio',
            'unstoppable_options_studio_dashboard',
            'dashicons-screenoptions',
            56 // Position after WooCommerce.
        );

        // Add dashboard as first submenu (replaces the duplicate parent link).
        add_submenu_page(
            'unstoppable-studio',
            __( 'Dashboard', 'unstoppable-product-sorting-for-woocommerce' ),
            __( 'Dashboard', 'unstoppable-product-sorting-for-woocommerce' ),
            'manage_woocommerce',
            'unstoppable-studio',
            'unstoppable_options_studio_dashboard'
        );
    }

    /**
     * Options Studio Dashboard page.
     * Shows overview of all installed Unstoppable Solutions plugins.
     */
    function unstoppable_options_studio_dashboard() {
        $plugins = apply_filters( 'unstoppable_studio_registered_plugins', array() );
        ?>
        <div class="wrap unstoppable-studio-dashboard">
            <h1><?php esc_html_e( 'Options Studio', 'unstoppable-product-sorting-for-woocommerce' ); ?></h1>
            <p class="description"><?php esc_html_e( 'Your suite of WooCommerce enhancement tools by Unstoppable Solutions.', 'unstoppable-product-sorting-for-woocommerce' ); ?></p>

            <div class="unstoppable-studio-plugins" style="display: flex; flex-wrap: wrap; gap: 20px; margin-top: 20px;">
                <?php if ( empty( $plugins ) ) : ?>
                    <p><?php esc_html_e( 'No plugins registered yet.', 'unstoppable-product-sorting-for-woocommerce' ); ?></p>
                <?php else : ?>
                    <?php foreach ( $plugins as $plugin ) : ?>
                        <div class="unstoppable-plugin-card" style="background: #fff; border: 1px solid #ccd0d4; border-radius: 4px; padding: 20px; width: 300px;">
                            <h3 style="margin-top: 0;"><?php echo esc_html( $plugin['name'] ); ?></h3>
                            <p style="color: #666;"><?php echo esc_html( $plugin['description'] ); ?></p>
                            <?php if ( ! empty( $plugin['menu_slug'] ) ) : ?>
                                <a href="<?php echo esc_url( admin_url( 'admin.php?page=' . $plugin['menu_slug'] ) ); ?>" class="button button-primary">
                                    <?php esc_html_e( 'Open', 'unstoppable-product-sorting-for-woocommerce' ); ?>
                                </a>
                            <?php endif; ?>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>

            <div class="unstoppable-studio-footer" style="margin-top: 40px; padding-top: 20px; border-top: 1px solid #ccd0d4;">
                <p>
                    <a href="https://unstoppablesolutions.app" target="_blank"><?php esc_html_e( 'Visit Unstoppable Solutions', 'unstoppable-product-sorting-for-woocommerce' ); ?></a> |
                    <a href="https://unstoppablesolutions.app/support" target="_blank"><?php esc_html_e( 'Get Support', 'unstoppable-product-sorting-for-woocommerce' ); ?></a>
                </p>
            </div>
        </div>
        <?php
    }

    /**
     * Enqueue admin styles for Options Studio menu and admin bar.
     */
    function unstoppable_options_studio_enqueue_styles() {
        // Register a minimal handle for our inline styles.
        wp_register_style( 'unstoppable-options-studio', false );
        wp_enqueue_style( 'unstoppable-options-studio' );

        // Menu icon styles.
        $menu_css = '
            #adminmenu .toplevel_page_unstoppable-studio .wp-menu-image:before {
                color: #f0b849;
            }
            #adminmenu .toplevel_page_unstoppable-studio:hover .wp-menu-image:before,
            #adminmenu .toplevel_page_unstoppable-studio.current .wp-menu-image:before {
                color: #fff;
            }
        ';
        wp_add_inline_style( 'unstoppable-options-studio', $menu_css );

        // Admin bar styles (for admin pages).
        if ( is_admin_bar_showing() ) {
            $admin_bar_css = '
                #wpadminbar #wp-admin-bar-unstoppable-studio > .ab-item .ab-icon:before {
                    content: \'\f180\';
                    top: 2px;
                }
                #wpadminbar #wp-admin-bar-unstoppable-studio > .ab-item .ab-icon {
                    margin-right: 6px;
                }
                #wpadminbar #wp-admin-bar-unstoppable-studio > .ab-item {
                    color: #f0b849 !important;
                }
                #wpadminbar #wp-admin-bar-unstoppable-studio:hover > .ab-item {
                    color: #fff !important;
                    background: #32373c !important;
                }
            ';
            wp_add_inline_style( 'unstoppable-options-studio', $admin_bar_css );
        }
    }

    /**
     * Enqueue frontend styles for Options Studio admin bar.
     */
    function unstoppable_options_studio_enqueue_frontend_styles() {
        if ( ! is_admin_bar_showing() ) {
            return;
        }

        // Register a minimal handle for our inline styles.
        wp_register_style( 'unstoppable-options-studio-frontend', false );
        wp_enqueue_style( 'unstoppable-options-studio-frontend' );

        $admin_bar_css = '
            #wpadminbar #wp-admin-bar-unstoppable-studio > .ab-item .ab-icon:before {
                content: \'\f180\';
                top: 2px;
            }
            #wpadminbar #wp-admin-bar-unstoppable-studio > .ab-item .ab-icon {
                margin-right: 6px;
            }
            #wpadminbar #wp-admin-bar-unstoppable-studio > .ab-item {
                color: #f0b849 !important;
            }
            #wpadminbar #wp-admin-bar-unstoppable-studio:hover > .ab-item {
                color: #fff !important;
                background: #32373c !important;
            }
        ';
        wp_add_inline_style( 'unstoppable-options-studio-frontend', $admin_bar_css );
    }

    /**
     * Add Options Studio to WordPress admin bar.
     *
     * @param WP_Admin_Bar $wp_admin_bar Admin bar object.
     */
    function unstoppable_options_studio_admin_bar( $wp_admin_bar ) {
        // Only show for users who can manage WooCommerce.
        if ( ! current_user_can( 'manage_woocommerce' ) ) {
            return;
        }

        // Main Options Studio node.
        $wp_admin_bar->add_node(
            array(
                'id'    => 'unstoppable-studio',
                'title' => '<span class="ab-icon dashicons dashicons-screenoptions"></span>' . __( 'Options Studio', 'unstoppable-product-sorting-for-woocommerce' ),
                'href'  => admin_url( 'admin.php?page=unstoppable-studio' ),
                'meta'  => array(
                    'title' => __( 'Options Studio Dashboard', 'unstoppable-product-sorting-for-woocommerce' ),
                ),
            )
        );

        // Add registered plugins as sub-menu items.
        $plugins = apply_filters( 'unstoppable_studio_registered_plugins', array() );

        foreach ( $plugins as $plugin_id => $plugin ) {
            if ( ! empty( $plugin['menu_slug'] ) ) {
                $wp_admin_bar->add_node(
                    array(
                        'id'     => 'unstoppable-studio-' . sanitize_key( $plugin_id ),
                        'parent' => 'unstoppable-studio',
                        'title'  => $plugin['name'],
                        'href'   => admin_url( 'admin.php?page=' . $plugin['menu_slug'] ),
                    )
                );
            }
        }
    }
}

// Initialize the Options Studio shell.
unstoppable_options_studio_init();

/*
 * ============================================================================
 * END SHARED SHELL SYSTEM
 * ============================================================================
 */

/**
 * Main Plugin Class
 */
final class Unstoppable_Product_Sorting {

    /**
     * Single instance of the class.
     *
     * @var Unstoppable_Product_Sorting
     */
    private static $instance = null;

    /**
     * Admin class instance.
     *
     * @var UPSO_Admin
     */
    public $admin = null;

    /**
     * Get single instance of the class.
     *
     * @return Unstoppable_Product_Sorting
     */
    public static function get_instance() {
        if ( null === self::$instance ) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    /**
     * Constructor.
     */
    private function __construct() {
        $this->includes();
        $this->init_hooks();
    }

    /**
     * Include required files.
     */
    private function includes() {
        require_once UPSO_PLUGIN_DIR . 'includes/class-upso-helpers.php';
        require_once UPSO_PLUGIN_DIR . 'includes/class-upso-database.php';
        require_once UPSO_PLUGIN_DIR . 'includes/class-upso-admin.php';
    }

    /**
     * Initialize hooks.
     */
    private function init_hooks() {
        // Activation/Deactivation hooks.
        register_activation_hook( __FILE__, array( $this, 'activate' ) );
        register_deactivation_hook( __FILE__, array( $this, 'deactivate' ) );

        // Global hooks.
        add_action( 'plugins_loaded', array( $this, 'on_plugins_loaded' ) );
        add_action( 'before_woocommerce_init', array( $this, 'declare_hpos_compatibility' ) );

        // Register with Options Studio.
        add_filter( 'unstoppable_studio_registered_plugins', array( $this, 'register_with_studio' ) );

        // Plugin action links.
        add_filter( 'plugin_action_links_' . UPSO_PLUGIN_BASENAME, array( $this, 'plugin_action_links' ) );

        // Frontend sorting hooks.
        add_action( 'pre_get_posts', array( $this, 'sort_products_on_frontend' ), 999 );
        add_filter( 'woocommerce_shortcode_products_query', array( $this, 'sort_products_in_shortcode' ), 10, 2 );
    }

    /**
     * Plugin activation.
     */
    public function activate() {
        // Check if WooCommerce is active.
        if ( ! class_exists( 'WooCommerce' ) ) {
            deactivate_plugins( UPSO_PLUGIN_BASENAME );
            wp_die(
                esc_html__( 'Unstoppable Product Sorting for Woo requires WooCommerce to be installed and activated.', 'unstoppable-product-sorting-for-woocommerce' ),
                esc_html__( 'Plugin Activation Error', 'unstoppable-product-sorting-for-woocommerce' ),
                array( 'back_link' => true )
            );
        }

        // Create database table.
        UPSO_Database::create_table();

        // Run migration from legacy data if needed.
        $this->maybe_migrate_data();

        // Set install date for review notice.
        if ( ! get_option( 'upso_install_date' ) ) {
            update_option( 'upso_install_date', time() );
        }

        // Flush rewrite rules.
        flush_rewrite_rules();
    }

    /**
     * Plugin deactivation.
     */
    public function deactivate() {
        // Data is preserved for reactivation.
        // Full cleanup happens in uninstall.php.
        flush_rewrite_rules();
    }

    /**
     * Maybe migrate data from legacy storage.
     */
    private function maybe_migrate_data() {
        $db_version = get_option( 'upso_db_version', '0.0.0' );

        if ( version_compare( $db_version, '1.0.0', '<' ) ) {
            UPSO_Database::create_table();
            UPSO_Database::migrate_data();
            update_option( 'upso_db_version', '1.0.0' );
        }
    }

    /**
     * On plugins loaded.
     */
    public function on_plugins_loaded() {
        // Check if WooCommerce is active.
        if ( ! class_exists( 'WooCommerce' ) ) {
            add_action( 'admin_notices', array( $this, 'woocommerce_missing_notice' ) );
            return;
        }

        // Check database version and migrate if needed.
        $this->maybe_migrate_data();

        // Initialize admin.
        if ( is_admin() ) {
            $this->admin = new UPSO_Admin();
        }
    }

    /**
     * Declare HPOS compatibility.
     */
    public function declare_hpos_compatibility() {
        if ( class_exists( \Automattic\WooCommerce\Utilities\FeaturesUtil::class ) ) {
            \Automattic\WooCommerce\Utilities\FeaturesUtil::declare_compatibility( 'custom_order_tables', __FILE__, true );
        }
    }

    /**
     * Register with Options Studio.
     *
     * @param array $plugins Registered plugins.
     * @return array
     */
    public function register_with_studio( $plugins ) {
        $plugins['unstoppable-product-sorting-for-woocommerce'] = array(
            'name'        => __( 'Product Sorting', 'unstoppable-product-sorting-for-woocommerce' ),
            'description' => __( 'Drag-and-drop product sorting for WooCommerce.', 'unstoppable-product-sorting-for-woocommerce' ),
            'menu_slug'   => 'upso-product-sorting',
            'version'     => UPSO_VERSION,
        );
        return $plugins;
    }

    /**
     * Plugin action links.
     *
     * @param array $links Action links.
     * @return array
     */
    public function plugin_action_links( $links ) {
        $plugin_links = array(
            '<a href="' . esc_url( admin_url( 'admin.php?page=upso-product-sorting' ) ) . '">' . esc_html__( 'Sort Products', 'unstoppable-product-sorting-for-woocommerce' ) . '</a>',
            '<a href="' . esc_url( admin_url( 'admin.php?page=upso-product-sorting&tab=settings' ) ) . '">' . esc_html__( 'Settings', 'unstoppable-product-sorting-for-woocommerce' ) . '</a>',
        );
        return array_merge( $plugin_links, $links );
    }

    /**
     * WooCommerce missing notice.
     */
    public function woocommerce_missing_notice() {
        ?>
        <div class="error">
            <p>
                <strong><?php esc_html_e( 'Unstoppable Product Sorting for Woo', 'unstoppable-product-sorting-for-woocommerce' ); ?></strong>
                <?php esc_html_e( 'requires WooCommerce to be installed and activated.', 'unstoppable-product-sorting-for-woocommerce' ); ?>
            </p>
        </div>
        <?php
    }

    /**
     * Sort products on frontend.
     *
     * @param WP_Query $query Query object.
     */
    public function sort_products_on_frontend( $query ) {
        // Only target frontend main query.
        if ( is_admin() || ! $query->is_main_query() ) {
            return;
        }

        // Skip if user selected a specific sort order.
        if ( isset( $_GET['orderby'] ) ) { // phpcs:ignore WordPress.Security.NonceVerification
            return;
        }

        // Check settings for affected loops.
        $affect_all_loops = get_option( 'upso_affect_all_loops', false );

        if ( $affect_all_loops ) {
            $checker = ! is_admin();
        } else {
            $checker = is_tax( 'product_cat' ) && $query->is_main_query() && ! is_admin();
        }

        if ( $checker ) {
            $term = get_queried_object();
            if ( $term && is_a( $term, 'WP_Term' ) && 'product_cat' === $term->taxonomy ) {
                $this->apply_custom_sorting( $query, $term->term_id );
            }
        }
    }

    /**
     * Sort products in shortcode.
     *
     * @param array $query_args Query args.
     * @param array $attributes Shortcode attributes.
     * @return array
     */
    public function sort_products_in_shortcode( $query_args, $attributes ) {
        // Only apply if setting is enabled.
        if ( ! get_option( 'upso_affect_all_loops', false ) ) {
            return $query_args;
        }

        // Check if category shortcode.
        if ( isset( $query_args['tax_query'] ) && isset( $attributes['category'] ) ) {
            $term = get_term_by( 'slug', $attributes['category'], 'product_cat' );
            if ( $term ) {
                // Add filters for custom sorting.
                add_filter( 'posts_join', array( $this, 'join_sort_table' ), 10, 2 );
                add_filter( 'posts_orderby', array( $this, 'orderby_sort_order' ), 10, 2 );

                // Store term ID for use in filters.
                $this->current_category_id = $term->term_id;
            }
        }

        return $query_args;
    }

    /**
     * Apply custom sorting to query.
     *
     * @param WP_Query $query   Query object.
     * @param int      $term_id Term ID.
     */
    private function apply_custom_sorting( $query, $term_id ) {
        $this->current_category_id = $term_id;

        add_filter( 'posts_join', array( $this, 'join_sort_table' ), 10, 2 );
        add_filter( 'posts_orderby', array( $this, 'orderby_sort_order' ), 10, 2 );
    }

    /**
     * Current category ID for sorting.
     *
     * @var int
     */
    private $current_category_id = 0;

    /**
     * Join custom sort table.
     *
     * @param string   $join  JOIN clause.
     * @param WP_Query $query Query object.
     * @return string
     */
    public function join_sort_table( $join, $query ) {
        global $wpdb;

        if ( $this->current_category_id > 0 ) {
            $table_name = UPSO_Database::get_table_name();
            // phpcs:ignore WordPress.DB.PreparedSQL.InterpolatedNotPrepared, PluginCheck.Security.DirectDB.UnescapedDBParameter
            $join      .= $wpdb->prepare(
                " LEFT JOIN {$table_name} AS upso_order ON {$wpdb->posts}.ID = upso_order.product_id AND upso_order.category_id = %d",
                $this->current_category_id
            );
        }

        return $join;
    }

    /**
     * Order by sort order.
     *
     * @param string   $orderby ORDER BY clause.
     * @param WP_Query $query   Query object.
     * @return string
     */
    public function orderby_sort_order( $orderby, $query ) {
        global $wpdb;

        if ( $this->current_category_id > 0 ) {
            $orderby = "COALESCE(upso_order.sort_order, {$wpdb->posts}.menu_order, 9999) ASC, {$wpdb->posts}.post_title ASC";
        }

        return $orderby;
    }
}

/**
 * Get plugin instance.
 *
 * @return Unstoppable_Product_Sorting
 */
function upso_get_instance() {
    return Unstoppable_Product_Sorting::get_instance();
}

// Initialize plugin.
upso_get_instance();
