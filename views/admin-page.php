<?php
/**
 * Admin Page Template
 *
 * @package Unstoppable_Product_Sorting
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

// phpcs:disable WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedVariableFound -- Template variables.
$upso_current_tab = UPSO_Helpers::get_current_tab();
$upso_tabs        = UPSO_Helpers::get_tabs();
// phpcs:enable
?>
<div class="wrap ups-wrap">
    <h1 class="ups-page-title">
        <span class="dashicons dashicons-sort"></span>
        <?php esc_html_e( 'Unstoppable Product Sorting', 'unstoppable-product-sorting-for-woocommerce' ); ?>
    </h1>

    <nav class="ups-tabs-nav">
        <?php foreach ( $upso_tabs as $upso_tab_id => $upso_tab ) : ?>
            <?php
            // phpcs:disable WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedVariableFound -- Template variables.
            $upso_url       = UPSO_Helpers::get_admin_url( $upso_tab_id );
            $upso_is_active = $upso_current_tab === $upso_tab_id;
            // phpcs:enable
            ?>
            <a href="<?php echo esc_url( $upso_url ); ?>"
               class="ups-tab-link <?php echo $upso_is_active ? 'active' : ''; ?>">
                <span class="dashicons <?php echo esc_attr( $upso_tab['icon'] ); ?>"></span>
                <?php echo esc_html( $upso_tab['label'] ); ?>
            </a>
        <?php endforeach; ?>

        <div class="ups-header-links">
            <a href="https://wordpress.org/support/plugin/unstoppable-product-sorting/reviews/#new-post" target="_blank" class="ups-header-link">
                ⭐ <?php esc_html_e( 'Rate Plugin', 'unstoppable-product-sorting-for-woocommerce' ); ?>
            </a>
            <a href="https://unstoppablesolutions.app/support" target="_blank" class="ups-header-link">
                🧑‍💻 <?php esc_html_e( 'Get Support', 'unstoppable-product-sorting-for-woocommerce' ); ?>
            </a>
        </div>
    </nav>

    <div class="ups-tab-content">
        <?php
        switch ( $upso_current_tab ) {
            case 'categories':
                include UPSO_PLUGIN_DIR . 'views/template-parts/tab-category-products.php';
                break;
            case 'settings':
                include UPSO_PLUGIN_DIR . 'views/template-parts/tab-settings.php';
                break;
            case 'all-products':
            default:
                include UPSO_PLUGIN_DIR . 'views/template-parts/tab-all-products.php';
                break;
        }
        ?>
    </div>
</div>

<!-- Confirmation Modal -->
<div id="ups-confirm-modal" class="ups-modal" style="display: none;">
    <div class="ups-modal-overlay"></div>
    <div class="ups-modal-container">
        <div class="ups-modal-header">
            <h3 id="ups-modal-title"><?php esc_html_e( 'Save Changes?', 'unstoppable-product-sorting-for-woocommerce' ); ?></h3>
            <button type="button" class="ups-modal-close" aria-label="<?php esc_attr_e( 'Close', 'unstoppable-product-sorting-for-woocommerce' ); ?>">
                <span class="dashicons dashicons-no-alt"></span>
            </button>
        </div>
        <div class="ups-modal-body" id="ups-modal-content">
            <p><?php esc_html_e( 'Are you sure you want to save the new product order?', 'unstoppable-product-sorting-for-woocommerce' ); ?></p>
        </div>
        <div class="ups-modal-footer">
            <button type="button" class="button" id="ups-modal-cancel">
                <?php esc_html_e( 'Cancel', 'unstoppable-product-sorting-for-woocommerce' ); ?>
            </button>
            <button type="button" class="button button-primary" id="ups-modal-confirm">
                <?php esc_html_e( 'Save Changes', 'unstoppable-product-sorting-for-woocommerce' ); ?>
            </button>
        </div>
    </div>
</div>

<!-- Response Messages -->
<div id="ups-response" class="ups-response" style="display: none;"></div>
