<?php
/**
 * Admin Class
 *
 * Handles admin functionality including menus, settings, and AJAX handlers.
 *
 * @package Unstoppable_Product_Sorting
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * UPSO_Admin Class
 */
class UPSO_Admin {

    /**
     * Constructor.
     */
    public function __construct() {
        $this->init_hooks();
    }

    /**
     * Initialize hooks.
     */
    private function init_hooks() {
        // Admin menu.
        add_action( 'admin_menu', array( $this, 'add_admin_menu' ), 20 );

        // Admin assets.
        add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_assets' ) );

        // Register settings.
        add_action( 'admin_init', array( $this, 'register_settings' ) );

        // AJAX handlers.
        add_action( 'wp_ajax_upso_save_sort_order', array( $this, 'ajax_save_sort_order' ) );
        add_action( 'wp_ajax_upso_load_more_products', array( $this, 'ajax_load_more_products' ) );

        // Add rearrange link to category row actions.
        add_filter( 'product_cat_row_actions', array( $this, 'add_category_rearrange_link' ), 10, 2 );

        // Handle new product added.
        add_action( 'save_post_product', array( $this, 'on_product_saved' ), 10, 3 );

        // Admin notices.
        add_action( 'admin_notices', array( $this, 'admin_notices' ) );

        // Dismiss notice AJAX.
        add_action( 'wp_ajax_upso_dismiss_notice', array( $this, 'ajax_dismiss_notice' ) );
    }

    /**
     * Add admin menu under Options Studio.
     */
    public function add_admin_menu() {
        add_submenu_page(
            'unstoppable-studio',
            __( 'Product Sorting', 'unstoppable-product-sorting-for-woocommerce' ),
            __( 'Product Sorting', 'unstoppable-product-sorting-for-woocommerce' ),
            'manage_woocommerce',
            'upso-product-sorting',
            array( $this, 'render_admin_page' )
        );
    }

    /**
     * Enqueue admin assets.
     *
     * @param string $hook Current admin page hook.
     */
    public function enqueue_assets( $hook ) {
        // phpcs:ignore WordPress.Security.NonceVerification.Recommended
        if ( ! isset( $_GET['page'] ) || 'upso-product-sorting' !== $_GET['page'] ) {
            return;
        }

        // Styles.
        wp_enqueue_style(
            'upso-admin',
            UPSO_PLUGIN_URL . 'assets/css/admin.css',
            array(),
            UPSO_VERSION
        );

        // SortableJS (bundled locally).
        wp_enqueue_script(
            'sortablejs',
            UPSO_PLUGIN_URL . 'assets/js/sortable.min.js',
            array(),
            '1.15.0',
            true
        );

        // Admin JS.
        wp_enqueue_script(
            'upso-admin',
            UPSO_PLUGIN_URL . 'assets/js/admin.js',
            array( 'jquery', 'sortablejs' ),
            UPSO_VERSION,
            true
        );

        wp_localize_script(
            'upso-admin',
            'upso_ajax',
            array(
                'ajax_url' => admin_url( 'admin-ajax.php' ),
                'nonce'    => wp_create_nonce( 'upso_ajax_nonce' ),
                'i18n'     => array(
                    'saving'         => __( 'Saving...', 'unstoppable-product-sorting-for-woocommerce' ),
                    'saved'          => __( 'Changes saved!', 'unstoppable-product-sorting-for-woocommerce' ),
                    'error'          => __( 'An error occurred. Please try again.', 'unstoppable-product-sorting-for-woocommerce' ),
                    'confirm_title'  => __( 'Save Changes?', 'unstoppable-product-sorting-for-woocommerce' ),
                    'confirm_text'   => __( 'Are you sure you want to save the new product order?', 'unstoppable-product-sorting-for-woocommerce' ),
                    'save_changes'   => __( 'Save Changes', 'unstoppable-product-sorting-for-woocommerce' ),
                    'cancel'         => __( 'Cancel', 'unstoppable-product-sorting-for-woocommerce' ),
                    'loading'        => __( 'Loading...', 'unstoppable-product-sorting-for-woocommerce' ),
                    'load_more'      => __( 'Load More Products', 'unstoppable-product-sorting-for-woocommerce' ),
                    'no_more'        => __( 'All products loaded', 'unstoppable-product-sorting-for-woocommerce' ),
                ),
                'debug'    => defined( 'WP_DEBUG' ) && WP_DEBUG,
            )
        );
    }

    /**
     * Register settings.
     */
    public function register_settings() {
        register_setting(
            'upso_settings_group',
            'upso_affect_all_loops',
            array(
                'type'              => 'boolean',
                'sanitize_callback' => 'rest_sanitize_boolean',
                'default'           => false,
            )
        );
    }

    /**
     * Render admin page.
     */
    public function render_admin_page() {
        include UPSO_PLUGIN_DIR . 'views/admin-page.php';
    }

    /**
     * AJAX handler: Save sort order.
     */
    public function ajax_save_sort_order() {
        // Verify request.
        $verify = UPSO_Helpers::verify_ajax_request( 'upso_ajax_nonce', 'nonce' );
        if ( is_wp_error( $verify ) ) {
            wp_send_json_error( array( 'message' => $verify->get_error_message() ) );
        }

        // Get and sanitize data.
        // phpcs:ignore WordPress.Security.NonceVerification.Missing -- Nonce verified above via verify_ajax_request.
        $sort_orders = isset( $_POST['sort_orders'] ) ? map_deep( wp_unslash( $_POST['sort_orders'] ), 'absint' ) : array();
        // phpcs:ignore WordPress.Security.NonceVerification.Missing -- Nonce verified above via verify_ajax_request.
        $category_id = isset( $_POST['category_id'] ) ? absint( wp_unslash( $_POST['category_id'] ) ) : 0;
        // phpcs:ignore WordPress.Security.NonceVerification.Missing -- Nonce verified above via verify_ajax_request.
        $is_chunk = isset( $_POST['is_chunk'] ) && rest_sanitize_boolean( wp_unslash( $_POST['is_chunk'] ) );
        // phpcs:ignore WordPress.Security.NonceVerification.Missing -- Nonce verified above via verify_ajax_request.
        $is_last_chunk = isset( $_POST['is_last_chunk'] ) && rest_sanitize_boolean( wp_unslash( $_POST['is_last_chunk'] ) );

        // Validate sort orders format.
        $sort_orders = UPSO_Helpers::sanitize_sort_orders( $sort_orders );

        if ( empty( $sort_orders ) ) {
            wp_send_json_error( array( 'message' => __( 'No products to save.', 'unstoppable-product-sorting-for-woocommerce' ) ) );
        }

        // Update database.
        $result = UPSO_Database::bulk_update_orders( $sort_orders, $category_id );

        if ( ! $result ) {
            wp_send_json_error( array( 'message' => __( 'Failed to save product order.', 'unstoppable-product-sorting-for-woocommerce' ) ) );
        }

        // Update menu_order for backwards compatibility (only on last chunk or non-chunked).
        if ( ! $is_chunk || $is_last_chunk ) {
            if ( 0 === $category_id ) {
                $this->update_menu_order( $sort_orders );
            }
        }

        wp_send_json_success(
            array(
                'message' => __( 'Product order saved successfully!', 'unstoppable-product-sorting-for-woocommerce' ),
            )
        );
    }

    /**
     * Update menu_order in wp_posts for backwards compatibility.
     *
     * @param array $sort_orders Sort orders array.
     */
    private function update_menu_order( $sort_orders ) {
        global $wpdb;

        if ( empty( $sort_orders ) ) {
            return;
        }

        // Build CASE statement for batch update.
        $case_statements = array();
        $product_ids     = array();

        foreach ( $sort_orders as $new_order => $product_id ) {
            $product_id        = absint( $product_id );
            $new_order         = absint( $new_order );
            $case_statements[] = $wpdb->prepare( 'WHEN ID = %d THEN %d', $product_id, $new_order );
            $product_ids[]     = $product_id;
        }

        if ( empty( $case_statements ) ) {
            return;
        }

        $case_sql    = implode( ' ', $case_statements );
        $ids_sql     = implode( ',', array_map( 'absint', $product_ids ) );
        $posts_table = $wpdb->posts;

        // phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching, WordPress.DB.PreparedSQL.InterpolatedNotPrepared, WordPress.DB.PreparedSQL.NotPrepared
        $wpdb->query(
            "UPDATE {$posts_table}
            SET menu_order = CASE {$case_sql} ELSE menu_order END
            WHERE ID IN ({$ids_sql}) AND post_type = 'product'"
        );
    }

    /**
     * AJAX handler: Load more products.
     */
    public function ajax_load_more_products() {
        // Verify request.
        $verify = UPSO_Helpers::verify_ajax_request( 'upso_ajax_nonce', 'nonce' );
        if ( is_wp_error( $verify ) ) {
            wp_send_json_error( array( 'message' => $verify->get_error_message() ) );
        }

        // Get parameters.
        // phpcs:ignore WordPress.Security.NonceVerification.Missing -- Nonce verified above via verify_ajax_request.
        $page = isset( $_POST['page'] ) ? absint( wp_unslash( $_POST['page'] ) ) : 1;
        // phpcs:ignore WordPress.Security.NonceVerification.Missing -- Nonce verified above via verify_ajax_request.
        $per_page = isset( $_POST['per_page'] ) ? absint( wp_unslash( $_POST['per_page'] ) ) : 100;
        // phpcs:ignore WordPress.Security.NonceVerification.Missing -- Nonce verified above via verify_ajax_request.
        $category_id = isset( $_POST['category_id'] ) ? absint( wp_unslash( $_POST['category_id'] ) ) : 0;

        // Sanitize.
        $page     = max( 1, $page );
        $per_page = min( 200, max( 1, $per_page ) );

        // Build query.
        $args = array(
            'post_type'      => 'product',
            'posts_per_page' => $per_page,
            'paged'          => $page,
            'post_status'    => 'publish',
        );

        // Add category filter if specified.
        if ( $category_id > 0 ) {
            $args['tax_query'] = array( // phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_tax_query
                array(
                    'taxonomy'         => 'product_cat',
                    'terms'            => array( $category_id ),
                    'field'            => 'term_id',
                    'operator'         => 'IN',
                    'include_children' => true,
                ),
            );
        }

        // Add custom sorting.
        add_filter( 'posts_join', array( $this, 'filter_posts_join' ), 10, 2 );
        add_filter( 'posts_orderby', array( $this, 'filter_posts_orderby' ), 10, 2 );

        // Store category ID for filters.
        $this->current_category_id = $category_id;

        // Execute query.
        $products = new WP_Query( $args );

        // Remove filters.
        remove_filter( 'posts_join', array( $this, 'filter_posts_join' ), 10 );
        remove_filter( 'posts_orderby', array( $this, 'filter_posts_orderby' ), 10 );

        // Build HTML.
        ob_start();

        if ( $products->have_posts() ) {
            $serial_no = ( ( $page - 1 ) * $per_page ) + 1;

            while ( $products->have_posts() ) {
                $products->the_post();
                global $post;
                $product = wc_get_product( $post->ID );
                include UPSO_PLUGIN_DIR . 'views/template-parts/product-item.php';
                $serial_no++;
            }
        }

        $products_html = ob_get_clean();
        wp_reset_postdata();

        // Determine if there are more pages.
        $has_more     = $products->max_num_pages > $page;
        $loaded_count = min( $page * $per_page, $products->found_posts );

        wp_send_json_success(
            array(
                'products_html' => $products_html,
                'has_more'      => $has_more,
                'total'         => $products->found_posts,
                'loaded'        => $loaded_count,
                'current_page'  => $page,
            )
        );
    }

    /**
     * Current category ID for filters.
     *
     * @var int
     */
    private $current_category_id = 0;

    /**
     * Filter posts JOIN for custom sorting.
     *
     * @param string   $join  JOIN clause.
     * @param WP_Query $query Query object.
     * @return string
     */
    public function filter_posts_join( $join, $query ) {
        global $wpdb;

        $table_name = UPSO_Database::get_table_name();
        $join      .= $wpdb->prepare(
            " LEFT JOIN {$table_name} AS upso_order ON {$wpdb->posts}.ID = upso_order.product_id AND upso_order.category_id = %d", // phpcs:ignore WordPress.DB.PreparedSQL.InterpolatedNotPrepared
            $this->current_category_id
        );

        return $join;
    }

    /**
     * Filter posts ORDER BY for custom sorting.
     *
     * @param string   $orderby ORDER BY clause.
     * @param WP_Query $query   Query object.
     * @return string
     */
    public function filter_posts_orderby( $orderby, $query ) {
        global $wpdb;
        return "COALESCE(upso_order.sort_order, {$wpdb->posts}.menu_order, 9999) ASC, {$wpdb->posts}.post_title ASC";
    }

    /**
     * Add rearrange link to category row actions.
     *
     * @param array  $actions Row actions.
     * @param object $term    Term object.
     * @return array
     */
    public function add_category_rearrange_link( $actions, $term ) {
        $url                       = UPSO_Helpers::get_admin_url( 'categories', array( 'term_id' => $term->term_id ) );
        $actions['rearrange_link'] = '<a href="' . esc_url( $url ) . '">' . esc_html__( 'Sort Products', 'unstoppable-product-sorting-for-woocommerce' ) . '</a>';
        return $actions;
    }

    /**
     * Handle product saved.
     *
     * @param int     $post_id Post ID.
     * @param WP_Post $post    Post object.
     * @param bool    $update  Whether this is an update.
     */
    public function on_product_saved( $post_id, $post, $update ) {
        // Skip autosaves and revisions.
        if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
            return;
        }

        if ( wp_is_post_revision( $post_id ) ) {
            return;
        }

        // Get product categories.
        $terms = wp_get_post_terms( $post_id, 'product_cat' );

        // Get current menu_order.
        $menu_order = isset( $post->menu_order ) ? absint( $post->menu_order ) : 0;

        // Add product to custom table for each category if it doesn't exist.
        if ( $terms && ! is_wp_error( $terms ) ) {
            foreach ( $terms as $term ) {
                $existing_order = UPSO_Database::get_sort_order( $post_id, $term->term_id );
                if ( null === $existing_order ) {
                    UPSO_Database::set_sort_order( $post_id, $term->term_id, $menu_order );
                }
            }
        }

        // Add global sort order if it doesn't exist.
        $existing_global = UPSO_Database::get_sort_order( $post_id, 0 );
        if ( null === $existing_global ) {
            UPSO_Database::set_sort_order( $post_id, 0, $menu_order );
        }
    }

    /**
     * Display admin notices.
     */
    public function admin_notices() {
        // Only on plugin pages.
        // phpcs:ignore WordPress.Security.NonceVerification.Recommended
        if ( ! isset( $_GET['page'] ) || 'upso-product-sorting' !== $_GET['page'] ) {
            return;
        }

        // Welcome notice.
        $this->maybe_show_welcome_notice();

        // Review notice.
        $this->maybe_show_review_notice();
    }

    /**
     * Maybe show welcome notice.
     */
    private function maybe_show_welcome_notice() {
        if ( get_option( 'upso_welcome_notice_dismissed' ) ) {
            return;
        }

        ?>
        <div class="notice notice-info is-dismissible ups-notice" data-notice="welcome">
            <p>
                <strong><?php esc_html_e( 'Welcome to Unstoppable Product Sorting!', 'unstoppable-product-sorting-for-woocommerce' ); ?></strong>
                <?php esc_html_e( 'Drag and drop products to reorder them. Click "Save Changes" when done.', 'unstoppable-product-sorting-for-woocommerce' ); ?>
            </p>
        </div>
        <?php
    }

    /**
     * Maybe show review notice.
     */
    private function maybe_show_review_notice() {
        if ( get_option( 'upso_review_notice_dismissed' ) ) {
            return;
        }

        $install_date = get_option( 'upso_install_date' );
        if ( ! $install_date || time() < ( $install_date + ( 7 * DAY_IN_SECONDS ) ) ) {
            return;
        }

        ?>
        <div class="notice notice-info is-dismissible ups-notice" data-notice="review">
            <p>
                <?php esc_html_e( 'Enjoying Unstoppable Product Sorting? Please leave a review to help others discover the plugin!', 'unstoppable-product-sorting-for-woocommerce' ); ?>
                <a href="https://wordpress.org/support/plugin/unstoppable-product-sorting/reviews/#new-post" target="_blank" class="button button-primary" style="margin-left: 10px;">
                    <?php esc_html_e( 'Leave a Review', 'unstoppable-product-sorting-for-woocommerce' ); ?>
                </a>
            </p>
        </div>
        <?php
    }

    /**
     * AJAX handler: Dismiss notice.
     */
    public function ajax_dismiss_notice() {
        // Verify nonce.
        check_ajax_referer( 'upso_ajax_nonce', 'nonce' );

        // Verify permissions.
        if ( ! UPSO_Helpers::user_has_permission() ) {
            wp_send_json_error();
        }

        // phpcs:ignore WordPress.Security.NonceVerification.Missing -- Nonce verified above via check_ajax_referer.
        $notice = isset( $_POST['notice'] ) ? sanitize_key( wp_unslash( $_POST['notice'] ) ) : '';

        if ( empty( $notice ) ) {
            wp_send_json_error();
        }

        update_option( 'upso_' . $notice . '_notice_dismissed', true );

        wp_send_json_success();
    }
}
