<?php
/**
 * Tab: Settings
 *
 * @package Unstoppable_Product_Sorting
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

$upso_affect_all_loops = get_option( 'upso_affect_all_loops', false );
?>

<div class="ups-content-area ups-settings-area">
    <?php // phpcs:ignore WordPress.Security.NonceVerification.Recommended ?>
    <?php if ( isset( $_GET['settings-updated'] ) ) : ?>
        <div class="notice notice-success is-dismissible">
            <p><strong><?php esc_html_e( 'Settings saved successfully!', 'unstoppable-product-sorting-for-woocommerce' ); ?></strong></p>
        </div>
    <?php endif; ?>

    <form action="options.php" method="post" class="ups-settings-form">
        <?php settings_fields( 'upso_settings_group' ); ?>

        <div class="ups-settings-section">
            <div class="ups-settings-card">
                <div class="ups-settings-card-header">
                    <span class="ups-settings-icon">🔄</span>
                    <h3><?php esc_html_e( 'Product Display Settings', 'unstoppable-product-sorting-for-woocommerce' ); ?></h3>
                </div>
                <div class="ups-settings-card-body">
                    <p class="ups-settings-description">
                        <?php esc_html_e( 'Choose where your custom product sort order should be applied.', 'unstoppable-product-sorting-for-woocommerce' ); ?>
                    </p>

                    <table class="form-table" role="presentation">
                        <tbody>
                            <tr>
                                <th scope="row"><?php esc_html_e( 'Apply Sorting To', 'unstoppable-product-sorting-for-woocommerce' ); ?></th>
                                <td>
                                    <fieldset>
                                        <p>
                                            <label>
                                                <input
                                                    type="radio"
                                                    name="upso_affect_all_loops"
                                                    value="0"
                                                    <?php checked( $upso_affect_all_loops, false ); ?>
                                                />
                                                <strong><?php esc_html_e( 'Category Pages Only', 'unstoppable-product-sorting-for-woocommerce' ); ?></strong>
                                                <span class="ups-recommended-badge"><?php esc_html_e( 'Recommended', 'unstoppable-product-sorting-for-woocommerce' ); ?></span>
                                            </label>
                                            <span class="description">
                                                <?php esc_html_e( 'Custom sort order only applies to product category archive pages.', 'unstoppable-product-sorting-for-woocommerce' ); ?>
                                            </span>
                                        </p>
                                        <p style="margin-top: 15px;">
                                            <label>
                                                <input
                                                    type="radio"
                                                    name="upso_affect_all_loops"
                                                    value="1"
                                                    <?php checked( $upso_affect_all_loops, true ); ?>
                                                />
                                                <strong><?php esc_html_e( 'All Product Loops', 'unstoppable-product-sorting-for-woocommerce' ); ?></strong>
                                            </label>
                                            <span class="description">
                                                <?php esc_html_e( 'Applies to category pages, shortcodes, and other product displays.', 'unstoppable-product-sorting-for-woocommerce' ); ?>
                                            </span>
                                        </p>
                                    </fieldset>
                                </td>
                            </tr>
                        </tbody>
                    </table>

                    <div class="ups-settings-submit">
                        <?php submit_button( __( 'Save Settings', 'unstoppable-product-sorting-for-woocommerce' ), 'primary', 'submit', false ); ?>
                    </div>
                </div>
            </div>
        </div>

        <div class="ups-settings-section">
            <div class="ups-settings-card ups-info-card">
                <div class="ups-settings-card-header">
                    <span class="ups-settings-icon">ℹ️</span>
                    <h3><?php esc_html_e( 'How It Works', 'unstoppable-product-sorting-for-woocommerce' ); ?></h3>
                </div>
                <div class="ups-settings-card-body">
                    <div class="ups-info-columns">
                        <div class="ups-info-column">
                            <h4><?php esc_html_e( 'Category Pages Only', 'unstoppable-product-sorting-for-woocommerce' ); ?></h4>
                            <p><?php esc_html_e( 'This is the recommended setting for most stores. Your custom product order will appear on:', 'unstoppable-product-sorting-for-woocommerce' ); ?></p>
                            <ul>
                                <li><?php esc_html_e( 'Product category archive pages', 'unstoppable-product-sorting-for-woocommerce' ); ?></li>
                            </ul>
                        </div>
                        <div class="ups-info-column">
                            <h4><?php esc_html_e( 'All Product Loops', 'unstoppable-product-sorting-for-woocommerce' ); ?></h4>
                            <p><?php esc_html_e( 'Choose this if you want sorting to apply everywhere, including:', 'unstoppable-product-sorting-for-woocommerce' ); ?></p>
                            <ul>
                                <li><?php esc_html_e( 'Product category pages', 'unstoppable-product-sorting-for-woocommerce' ); ?></li>
                                <li><?php esc_html_e( 'WooCommerce shortcodes', 'unstoppable-product-sorting-for-woocommerce' ); ?></li>
                                <li><code>[product_category category="slug"]</code></li>
                            </ul>
                        </div>
                    </div>

                    <div class="ups-info-note">
                        <span class="dashicons dashicons-info"></span>
                        <p>
                            <strong><?php esc_html_e( 'Note:', 'unstoppable-product-sorting-for-woocommerce' ); ?></strong>
                            <?php esc_html_e( 'Using "All Product Loops" may slightly impact performance on sites with many shortcodes or custom product queries.', 'unstoppable-product-sorting-for-woocommerce' ); ?>
                        </p>
                    </div>
                </div>
            </div>
        </div>

        <div class="ups-settings-section">
            <div class="ups-settings-card ups-info-card">
                <div class="ups-settings-card-header">
                    <span class="ups-settings-icon">⚙️</span>
                    <h3><?php esc_html_e( 'Troubleshooting', 'unstoppable-product-sorting-for-woocommerce' ); ?></h3>
                </div>
                <div class="ups-settings-card-body">
                    <h4><?php esc_html_e( 'Custom order not showing on Shop page?', 'unstoppable-product-sorting-for-woocommerce' ); ?></h4>
                    <ol>
                        <li><?php esc_html_e( 'Go to Appearance → Customize → WooCommerce → Product Catalog', 'unstoppable-product-sorting-for-woocommerce' ); ?></li>
                        <li><?php esc_html_e( 'Set "Default Product Sorting" to "Default sorting (custom ordering + name)"', 'unstoppable-product-sorting-for-woocommerce' ); ?></li>
                    </ol>

                    <h4><?php esc_html_e( 'Changes not saving with many products?', 'unstoppable-product-sorting-for-woocommerce' ); ?></h4>
                    <p><?php esc_html_e( 'If you have a large number of products and changes are not saving, contact your hosting provider to increase:', 'unstoppable-product-sorting-for-woocommerce' ); ?></p>
                    <ul>
                        <li><code>memory_limit</code></li>
                        <li><code>max_execution_time</code></li>
                    </ul>
                </div>
            </div>
        </div>
    </form>
</div>
