=== Unstoppable Product Sorting for WooCommerce ===
Contributors: paramedic192
Donate link: https://unstoppablesolutions.app
Tags: woocommerce, product sorting, drag and drop, reorder, product order
Requires at least: 5.8
Tested up to: 7.1
Requires PHP: 7.4
Stable tag: 1.0.8
WC requires at least: 7.0
WC tested up to: 10.4
License: GPL-2.0-or-later
License URI: https://www.gnu.org/licenses/gpl-2.0.html

Take full control of your WooCommerce product display order with intuitive drag-and-drop sorting for shop pages and categories.

== Description ==

**Unstoppable Product Sorting for WooCommerce** gives you complete control over how products appear in your WooCommerce store. Use a simple drag-and-drop interface to arrange products exactly how you want them.

= Why Use This Plugin? =

* **Better Customer Experience** - Help customers find the right products faster by displaying them in a logical order
* **Promote Key Products** - Put your best-sellers, featured items, or seasonal products front and center
* **Increase Conversions** - Strategic product placement leads to more clicks and sales
* **Save Time** - No coding required, just drag and drop

= Features =

* **Drag-and-Drop Sorting** - Intuitive interface for easy product reordering
* **Sort All Products** - Arrange products on your main shop page
* **Sort by Category** - Set custom order for each product category
* **Multi-Select** - Click to select multiple products and move them together
* **Quick Actions** - Move products to top, up, down, or bottom with one click
* **Large Catalog Support** - Handles thousands of products with chunked processing
* **WooCommerce Shortcode Support** - Optionally apply sorting to shortcodes
* **HPOS Compatible** - Works with WooCommerce High-Performance Order Storage

= Part of Options Studio =

This plugin is part of the **Options Studio** suite by Unstoppable Solutions. When installed alongside other Unstoppable plugins, they all appear under a unified admin menu for easy access.

= Requirements =

* WordPress 5.8 or higher
* WooCommerce 7.0 or higher
* PHP 7.4 or higher

== Installation ==

1. Go to **Plugins → Add New** in your WordPress admin
2. Search for "Unstoppable Product Sorting"
3. Click **Install Now**, then **Activate**
4. Go to **Options Studio → Product Sorting** to start sorting products

= Manual Installation =

1. Download the plugin zip file
2. Go to **Plugins → Add New → Upload Plugin**
3. Upload the zip file and click **Install Now**
4. Activate the plugin

== Frequently Asked Questions ==

= How do I use this plugin? =

After activation, go to **Options Studio → Product Sorting**. You'll see all your products listed. Simply drag and drop them into your preferred order, then click "Save Changes".

= Can I sort products within specific categories? =

Yes! Click on the "By Category" tab, select a category from the dropdown, and arrange products within that category.

= Will this work with WooCommerce shortcodes? =

By default, sorting only applies to category archive pages. To enable sorting for shortcodes like `[product_category]`, go to Settings and enable "All Product Loops".

= What happens if I deactivate the plugin? =

Your custom product order is preserved in the database. Reactivating the plugin will restore your sorting. However, **deleting** the plugin will permanently remove all sorting data.

= My custom order isn't showing on the shop page. What do I do? =

1. Go to **Appearance → Customize → WooCommerce → Product Catalog**
2. Set "Default Product Sorting" to "Default sorting (custom ordering + name)"

= Can I sort variable products and their variations? =

This plugin sorts parent products only. Variation order within a variable product is controlled by WooCommerce's built-in variation sorting.

= Is this plugin compatible with page builders? =

The plugin works with standard WooCommerce templates. Some page builders have their own product query methods that may not use the standard sorting. Contact your page builder's support for compatibility questions.

== Screenshots ==

1. Main sorting interface with drag-and-drop functionality
2. Sort products by category
3. Settings page
4. Quick action buttons for moving products

== Changelog ==

= 1.0.8 =
* Tested and confirmed compatibility with WordPress 7.0

= 1.0.7 =
* Fixed expand arrow not revealing subcategories when clicked
* Fixed search showing parent category instead of matching subcategory
* Improved search to properly highlight and display matching subcategories

= 1.0.6 =
* Improved category dropdown with expandable tree structure
* Added category search box for quick filtering
* Subcategories now display nested under parent categories
* Better accessibility for stores with many subcategories
* Click-to-expand/collapse for category hierarchy

= 1.0.5 =
* Updated SortableJS library to version 1.15.7
* Fixed inline styles to use wp_add_inline_style() per WordPress guidelines

= 1.0.4 =
* Fixed input sanitization with proper wp_unslash() usage
* Improved PHPCS compliance comments

= 1.0.3 =
* Fixed text domain to match plugin slug for WordPress.org compliance
* Removed load_plugin_textdomain (handled automatically by WordPress.org)
* Code cleanup for WordPress.org submission

= 1.0.2 =
* Updated compatibility for WordPress 6.9
* Updated compatibility for WooCommerce 10.4
* Bundled SortableJS library locally for improved reliability

= 1.0.1 =
* Updated plugin name for WordPress.org trademark compliance
* Updated code prefixes to meet WordPress.org guidelines
* Improved code documentation and standards compliance

= 1.0.0 =
* Initial release
* Drag-and-drop product sorting
* Category-specific sorting
* Multi-select support
* Quick move actions (top, up, down, bottom)
* Load more pagination for large catalogs
* Settings for controlling affected loops
* Options Studio integration
* HPOS compatibility

== Upgrade Notice ==

= 1.0.7 =
Bug fixes for category expand/collapse and search functionality.

= 1.0.6 =
Improved category navigation with expandable tree and search. Better subcategory accessibility.

= 1.0.5 =
Updated SortableJS library and code improvements per WordPress.org review.

= 1.0.4 =
Security improvement: Better input sanitization.

= 1.0.3 =
Code improvements for WordPress.org compliance. No functionality changes.

= 1.0.2 =
Compatibility update for WordPress 6.9 and WooCommerce 10.4.

= 1.0.1 =
Code improvements for WordPress.org compliance. No functionality changes.

= 1.0.0 =
Initial release of Unstoppable Product Sorting for WooCommerce.

== Privacy Policy ==

This plugin does not collect, store, or transmit any personal data. All sorting data is stored locally in your WordPress database.

== Support ==

For support, please visit [Unstoppable Solutions Support](https://unstoppablesolutions.app/support) or use the WordPress.org support forum.
