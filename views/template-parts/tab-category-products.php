<?php
/**
 * Tab: Category Products
 *
 * @package Unstoppable_Product_Sorting
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

// Get selected category.
$upso_selected_id   = 0;
$upso_selected_name = __( 'Select a Category', 'unstoppable-product-sorting-for-woocommerce' );

// phpcs:ignore WordPress.Security.NonceVerification.Recommended
if ( isset( $_GET['term_id'] ) && ! empty( $_GET['term_id'] ) ) {
    // phpcs:ignore WordPress.Security.NonceVerification.Recommended
    $upso_selected_id = absint( $_GET['term_id'] );
    $upso_term        = get_term( $upso_selected_id, 'product_cat' );
    if ( $upso_term && ! is_wp_error( $upso_term ) ) {
        $upso_selected_name = $upso_term->name;
    }
}

// Get all categories.
$upso_categories = UPSO_Helpers::get_product_categories();
$upso_category_tree = UPSO_Helpers::build_category_tree( $upso_categories );

/**
 * Render category tree recursively with expandable subcategories.
 *
 * @param array $categories Category tree.
 * @param int   $selected   Selected term ID.
 * @param int   $depth      Current nesting depth.
 */
function upso_render_category_tree( $categories, $selected, $depth = 0 ) {
    if ( empty( $categories ) ) {
        return;
    }

    $is_root = ( 0 === $depth );
    $list_class = $is_root ? 'ups-category-tree' : 'ups-category-children';
    
    echo '<ul class="' . esc_attr( $list_class ) . '">';
    
    foreach ( $categories as $category ) {
        $has_children       = isset( $category->children ) && ! empty( $category->children );
        $is_selected        = ( $selected === $category->term_id );
        $has_selected_child = $has_children && UPSO_Helpers::has_selected_descendant( $category, $selected ) && ! $is_selected;
        $is_expanded        = $has_selected_child || $is_selected;
        $classes            = array( 'ups-category-item' );

        if ( $has_children ) {
            $classes[] = 'has-children';
        }
        if ( $is_selected ) {
            $classes[] = 'selected';
        }
        if ( $has_selected_child ) {
            $classes[] = 'has-selected-child';
        }
        if ( $is_expanded && $has_children ) {
            $classes[] = 'expanded';
        }
        ?>
        <li class="<?php echo esc_attr( implode( ' ', $classes ) ); ?>" data-depth="<?php echo esc_attr( $depth ); ?>">
            <div class="ups-category-row">
                <?php if ( $has_children ) : ?>
                    <button type="button" class="ups-expand-toggle" aria-label="<?php esc_attr_e( 'Toggle subcategories', 'unstoppable-product-sorting-for-woocommerce' ); ?>">
                        <span class="dashicons <?php echo $is_expanded ? 'dashicons-arrow-down-alt2' : 'dashicons-arrow-right-alt2'; ?>"></span>
                    </button>
                <?php else : ?>
                    <span class="ups-expand-spacer"></span>
                <?php endif; ?>
                
                <a href="#"
                   class="ups-category-link"
                   data-term-id="<?php echo esc_attr( $category->term_id ); ?>"
                   data-term-name="<?php echo esc_attr( $category->name ); ?>">
                    <span class="ups-category-name"><?php echo esc_html( $category->name ); ?></span>
                    <span class="ups-category-count">(<?php echo esc_html( $category->count ); ?>)</span>
                </a>
            </div>
            <?php
            if ( $has_children ) {
                echo '<div class="ups-children-wrapper"' . ( $is_expanded ? '' : ' style="display:none;"' ) . '>';
                upso_render_category_tree( $category->children, $selected, $depth + 1 );
                echo '</div>';
            }
            ?>
        </li>
        <?php
    }
    echo '</ul>';
}
?>

<div class="ups-content-area">
    <div class="ups-category-filter">
        <div class="ups-custom-dropdown">
            <button type="button" class="ups-dropdown-toggle" id="ups-category-dropdown">
                <span class="dashicons dashicons-category"></span>
                <span class="ups-dropdown-text"><?php echo esc_html( $upso_selected_name ); ?></span>
                <span class="ups-dropdown-arrow dashicons dashicons-arrow-down-alt2"></span>
            </button>
            <div class="ups-dropdown-content">
                <div class="ups-category-search-wrapper">
                    <span class="dashicons dashicons-search"></span>
                    <input type="text" 
                           id="ups-category-search" 
                           class="ups-category-search" 
                           placeholder="<?php esc_attr_e( 'Search categories...', 'unstoppable-product-sorting-for-woocommerce' ); ?>"
                           autocomplete="off">
                    <button type="button" class="ups-search-clear" style="display:none;">
                        <span class="dashicons dashicons-no-alt"></span>
                    </button>
                </div>
                <div class="ups-category-tree-wrapper">
                    <?php upso_render_category_tree( $upso_category_tree, $upso_selected_id ); ?>
                </div>
                <div class="ups-no-results" style="display:none;">
                    <span class="dashicons dashicons-info-outline"></span>
                    <?php esc_html_e( 'No categories found', 'unstoppable-product-sorting-for-woocommerce' ); ?>
                </div>
            </div>
        </div>
    </div>

    <?php if ( $upso_selected_id > 0 ) : ?>
        <?php
        // Build query with custom sorting.
        $upso_category_id = $upso_selected_id;

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
            'tax_query'      => array( // phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_tax_query
                array(
                    'taxonomy'         => 'product_cat',
                    'terms'            => array( $upso_category_id ),
                    'field'            => 'term_id',
                    'operator'         => 'IN',
                    'include_children' => true,
                ),
            ),
        );

        // Apply filters.
        add_filter( 'posts_join', $upso_join_callback, 10, 1 );
        add_filter( 'posts_orderby', $upso_orderby_callback, 10, 1 );

        $upso_products = new WP_Query( $upso_args );

        // Remove filters.
        remove_filter( 'posts_join', $upso_join_callback, 10 );
        remove_filter( 'posts_orderby', $upso_orderby_callback, 10 );
        ?>

        <?php if ( $upso_products->have_posts() ) : ?>
            <div class="ups-toolbar">
                <div class="ups-product-count">
                    <?php
                    printf(
                        /* translators: %1$s: number of products, %2$s: category name */
                        esc_html__( 'Found %1$s products in "%2$s"', 'unstoppable-product-sorting-for-woocommerce' ),
                        '<strong>' . esc_html( UPSO_Helpers::format_number( $upso_products->found_posts ) ) . '</strong>',
                        '<em>' . esc_html( $upso_selected_name ) . '</em>'
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

            <?php
            wp_reset_postdata();
        else :
            ?>
            <div class="ups-no-products">
                <span class="dashicons dashicons-products"></span>
                <h3><?php esc_html_e( 'No Products Found', 'unstoppable-product-sorting-for-woocommerce' ); ?></h3>
                <p>
                    <?php
                    printf(
                        /* translators: %s: category name */
                        esc_html__( 'No products found in the "%s" category.', 'unstoppable-product-sorting-for-woocommerce' ),
                        esc_html( $upso_selected_name )
                    );
                    ?>
                </p>
            </div>
        <?php endif; ?>

    <?php else : ?>
        <div class="ups-select-category">
            <span class="dashicons dashicons-category"></span>
            <h3><?php esc_html_e( 'Select a Category', 'unstoppable-product-sorting-for-woocommerce' ); ?></h3>
            <p><?php esc_html_e( 'Choose a product category from the dropdown above to sort products within that category.', 'unstoppable-product-sorting-for-woocommerce' ); ?></p>
        </div>
    <?php endif; ?>
</div>
