<?php
/**
 * Tab: All Products
 *
 * @package Unstoppable_Product_Sorting
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

// Category ID (0 for global/all products).
$upso_category_id = 0;

// Build query with custom sorting.
$upso_join_callback = function ( $join ) use ( $upso_category_id ) {
    global $wpdb;
    $table_name = UPSO_Database::get_table_name();
    $join      .= $wpdb->prepare(
        " LEFT JOIN {$table_name} AS upso_order ON {$wpdb->posts}.ID = upso_order.product_id AND upso_order.category_id = %d", // phpcs:ignore WordPress.DB.PreparedSQL.InterpolatedNotPrepared
        $upso_category_id
    );
    return $join;
};

$upso_orderby_callback = function ( $orderby ) {
    global $wpdb;
    return "COALESCE(upso_order.sort_order, {$wpdb->posts}.menu_order, 9999) ASC, {$wpdb->posts}.post_title ASC";
};

$upso_args = array(
    'post_type'      => 'product',
    'posts_per_page' => 100,
    'post_status'    => 'publish',
);

// Apply filters.
add_filter( 'posts_join', $upso_join_callback, 10, 1 );
add_filter( 'posts_orderby', $upso_orderby_callback, 10, 1 );

$upso_products = new WP_Query( $upso_args );

// Remove filters.
remove_filter( 'posts_join', $upso_join_callback, 10 );
remove_filter( 'posts_orderby', $upso_orderby_callback, 10 );
?>

<div class="ups-content-area">
    <?php if ( $upso_products->have_posts() ) : ?>
        <div class="ups-toolbar">
            <div class="ups-product-count">
                <?php
                printf(
                    /* translators: %s: number of products */
                    esc_html__( 'Found %s products', 'unstoppable-product-sorting-for-woocommerce' ),
                    '<strong>' . esc_html( UPSO_Helpers::format_number( $upso_products->found_posts ) ) . '</strong>'
                );
                ?>
            </div>
            <div class="ups-toolbar-actions">
                <span class="ups-drag-hint">
                    <span class="dashicons dashicons-move"></span>
                    <?php esc_html_e( 'Drag products to reorder', 'unstoppable-product-sorting-for-woocommerce' ); ?>
                </span>
            </div>
        </div>

        <div class="ups-products-wrapper">
            <div id="ups-products-list"
                 class="ups-products-list"
                 data-page="1"
                 data-max-pages="<?php echo esc_attr( $upso_products->max_num_pages ); ?>"
                 data-category-id="<?php echo esc_attr( $upso_category_id ); ?>">
                <?php
                $upso_serial = 1;
                while ( $upso_products->have_posts() ) :
                    $upso_products->the_post();
                    global $post;
                    // phpcs:ignore WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedVariableFound -- Variables for template include.
                    $product   = wc_get_product( $post->ID );
                    // phpcs:ignore WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedVariableFound -- Variables for template include.
                    $serial_no = $upso_serial;
                    include UPSO_PLUGIN_DIR . 'views/template-parts/product-item.php';
                    $upso_serial++;
                endwhile;
                ?>
            </div>

            <?php if ( $upso_products->max_num_pages > 1 ) : ?>
                <div class="ups-load-more-container">
                    <button type="button" id="ups-load-more" class="button button-secondary">
                        <span class="dashicons dashicons-arrow-down-alt"></span>
                        <?php esc_html_e( 'Load More Products', 'unstoppable-product-sorting-for-woocommerce' ); ?>
                    </button>
                    <span class="ups-load-progress">
                        <?php
                        printf(
                            /* translators: %1$s: loaded count, %2$s: total count */
                            esc_html__( 'Showing %1$s of %2$s products', 'unstoppable-product-sorting-for-woocommerce' ),
                            '<span id="ups-loaded-count">' . esc_html( min( 100, $upso_products->found_posts ) ) . '</span>',
                            '<span id="ups-total-count">' . esc_html( $upso_products->found_posts ) . '</span>'
                        );
                        ?>
                    </span>
                </div>
            <?php endif; ?>
        </div>

        <div class="ups-footer">
            <div class="ups-footer-actions">
                <button type="button" id="ups-save-order" class="button button-primary button-large">
                    <span class="dashicons dashicons-yes"></span>
                    <?php esc_html_e( 'Save Changes', 'unstoppable-product-sorting-for-woocommerce' ); ?>
                </button>
            </div>
            <p class="ups-footer-hint">
                <?php esc_html_e( 'Tip: Click on multiple products to select them, then drag to move them together.', 'unstoppable-product-sorting-for-woocommerce' ); ?>
            </p>
        </div>

    <?php else : ?>
        <div class="ups-no-products">
            <span class="dashicons dashicons-products"></span>
            <h3><?php esc_html_e( 'No Products Found', 'unstoppable-product-sorting-for-woocommerce' ); ?></h3>
            <p><?php esc_html_e( 'Add some products to your WooCommerce store to start sorting them.', 'unstoppable-product-sorting-for-woocommerce' ); ?></p>
            <a href="<?php echo esc_url( admin_url( 'post-new.php?post_type=product' ) ); ?>" class="button button-primary">
                <?php esc_html_e( 'Add Product', 'unstoppable-product-sorting-for-woocommerce' ); ?>
            </a>
        </div>
    <?php endif; ?>
</div>

<?php
wp_reset_postdata();
