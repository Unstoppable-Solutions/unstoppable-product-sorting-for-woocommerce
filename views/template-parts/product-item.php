<?php
/**
 * Product Item Template
 *
 * @package Unstoppable_Product_Sorting
 *
 * @var WC_Product $product   Product object.
 * @var int        $serial_no Serial number.
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

if ( ! isset( $product ) || ! $product ) {
    return;
}

// phpcs:disable WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedVariableFound -- Template variables.
$upso_product_id    = $product->get_id();
$upso_product_name  = $product->get_name();
$upso_product_sku   = $product->get_sku();
$upso_product_stock = $product->is_in_stock();
$upso_product_price = $product->get_price_html();
$upso_edit_link     = get_edit_post_link( $upso_product_id );
$upso_view_link     = get_permalink( $upso_product_id );
// phpcs:enable
?>
<div class="ups-product-item" data-id="<?php echo esc_attr( $upso_product_id ); ?>">
    <div class="ups-product-handle">
        <span class="dashicons dashicons-menu"></span>
    </div>

    <div class="ups-product-image">
        <?php echo wp_kses_post( UPSO_Helpers::get_product_image( $product, 'thumbnail' ) ); ?>
    </div>

    <div class="ups-product-details">
        <div class="ups-product-name"><?php echo esc_html( $upso_product_name ); ?></div>
        <div class="ups-product-meta">
            <span class="ups-product-sku">
                <?php esc_html_e( 'SKU:', 'unstoppable-product-sorting-for-woocommerce' ); ?>
                <?php echo $upso_product_sku ? esc_html( $upso_product_sku ) : '—'; ?>
            </span>
            <?php if ( $upso_product_stock ) : ?>
                <span class="ups-product-stock ups-stock-instock">
                    <span class="dashicons dashicons-yes-alt"></span>
                    <?php esc_html_e( 'In Stock', 'unstoppable-product-sorting-for-woocommerce' ); ?>
                </span>
            <?php else : ?>
                <span class="ups-product-stock ups-stock-outofstock">
                    <span class="dashicons dashicons-dismiss"></span>
                    <?php esc_html_e( 'Out of Stock', 'unstoppable-product-sorting-for-woocommerce' ); ?>
                </span>
            <?php endif; ?>
        </div>
    </div>

    <div class="ups-product-price">
        <?php echo wp_kses_post( $upso_product_price ); ?>
    </div>

    <div class="ups-product-actions">
        <button type="button" class="ups-action-btn ups-move-top" title="<?php esc_attr_e( 'Move to top', 'unstoppable-product-sorting-for-woocommerce' ); ?>">
            <span class="dashicons dashicons-arrow-up-alt"></span>
        </button>
        <button type="button" class="ups-action-btn ups-move-up" title="<?php esc_attr_e( 'Move up', 'unstoppable-product-sorting-for-woocommerce' ); ?>">
            <span class="dashicons dashicons-arrow-up-alt2"></span>
        </button>
        <button type="button" class="ups-action-btn ups-move-down" title="<?php esc_attr_e( 'Move down', 'unstoppable-product-sorting-for-woocommerce' ); ?>">
            <span class="dashicons dashicons-arrow-down-alt2"></span>
        </button>
        <button type="button" class="ups-action-btn ups-move-bottom" title="<?php esc_attr_e( 'Move to bottom', 'unstoppable-product-sorting-for-woocommerce' ); ?>">
            <span class="dashicons dashicons-arrow-down-alt"></span>
        </button>
        <a href="<?php echo esc_url( $upso_view_link ); ?>" class="ups-action-link ups-view-link" target="_blank" title="<?php esc_attr_e( 'View product', 'unstoppable-product-sorting-for-woocommerce' ); ?>">
            <span class="dashicons dashicons-visibility"></span>
        </a>
        <a href="<?php echo esc_url( $upso_edit_link ); ?>" class="ups-action-link ups-edit-link" target="_blank" title="<?php esc_attr_e( 'Edit product', 'unstoppable-product-sorting-for-woocommerce' ); ?>">
            <span class="dashicons dashicons-edit"></span>
        </a>
    </div>
</div>
