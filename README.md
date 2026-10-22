# Unstoppable Product Sorting for WooCommerce

[![WordPress Plugin Version](https://img.shields.io/wordpress/plugin/v/unstoppable-product-sorting-for-woocommerce.svg)](https://wordpress.org/plugins/unstoppable-product-sorting-for-woocommerce/)
[![WordPress Plugin Downloads](https://img.shields.io/wordpress/plugin/dt/unstoppable-product-sorting-for-woocommerce.svg)](https://wordpress.org/plugins/unstoppable-product-sorting-for-woocommerce/)
[![License: GPL v2+](https://img.shields.io/badge/License-GPLv2+-blue.svg)](https://www.gnu.org/licenses/gpl-2.0)

Drag-and-drop product sorting for WooCommerce shop pages and categories. Reorder your products exactly the way you want them displayed — no custom field hacks, no theme edits.

> **End users:** install from the WordPress Plugin Directory — [wordpress.org/plugins/unstoppable-product-sorting-for-woocommerce](https://wordpress.org/plugins/unstoppable-product-sorting-for-woocommerce/)

## Features

- **Drag-and-drop reordering** — sort products on the shop page and inside any category
- **Per-category ordering** — a product can sit high in one category and low in another
- **Visual sort interface** — see the actual products as you reorder them
- **Preserves on export/import** — order survives standard WooCommerce migrations
- **Works with the stock Shop page** — no shortcode required

## Development

### Release workflow

Releases are deployed to WordPress.org automatically by GitHub Actions:

1. Finish the feature work on `main`
2. Bump the version in `unstoppable-product-sorting-for-woocommerce.php` (header + any constant), `readme.txt` (Stable tag), and add a Changelog entry
3. Commit and push `main`
4. On GitHub, create a new **Release** tagged `vX.Y.Z` — the deploy workflow fires and pushes to WordPress.org SVN

### CI

Every push and pull request runs the official WordPress Plugin Check against the codebase.

## License

GPL-2.0-or-later. See the `License` header in `unstoppable-product-sorting-for-woocommerce.php`.

## Author

Developed and maintained by [Unstoppable Solutions](https://unstoppablesolutions.app).
