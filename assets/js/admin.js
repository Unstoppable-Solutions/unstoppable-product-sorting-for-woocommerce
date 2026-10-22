/**
 * Admin JavaScript for Unstoppable Product Sorting
 *
 * @package Unstoppable_Product_Sorting
 */

(function($) {
    'use strict';

    /**
     * Main UPS Admin Object
     */
    var UPSAdmin = {
        // State
        sortable: null,
        isLoading: false,
        hasChanges: false,

        /**
         * Initialize
         */
        init: function() {
            this.initSortable();
            this.bindEvents();
            this.initDropdown();
            this.initNotices();
        },

        /**
         * Initialize SortableJS
         */
        initSortable: function() {
            var productsList = document.getElementById('ups-products-list');

            if (!productsList) {
                return;
            }

            this.sortable = new Sortable(productsList, {
                animation: 150,
                handle: '.ups-product-handle',
                ghostClass: 'sortable-ghost',
                chosenClass: 'sortable-chosen',
                dragClass: 'sortable-drag',
                multiDrag: true,
                selectedClass: 'sortable-selected',
                fallbackOnBody: true,
                swapThreshold: 0.65,
                onEnd: function() {
                    UPSAdmin.hasChanges = true;
                }
            });
        },

        /**
         * Bind Events
         */
        bindEvents: function() {
            var self = this;

            // Save order button
            $(document).on('click', '#ups-save-order', function(e) {
                e.preventDefault();
                self.showConfirmModal();
            });

            // Modal confirm
            $(document).on('click', '#ups-modal-confirm', function() {
                self.saveOrder();
            });

            // Modal cancel
            $(document).on('click', '#ups-modal-cancel, .ups-modal-close, .ups-modal-overlay', function() {
                self.hideModal();
            });

            // Load more
            $(document).on('click', '#ups-load-more', function(e) {
                e.preventDefault();
                self.loadMoreProducts();
            });

            // Move buttons
            $(document).on('click', '.ups-move-top', function(e) {
                e.preventDefault();
                e.stopPropagation();
                self.moveProduct($(this).closest('.ups-product-item'), 'top');
            });

            $(document).on('click', '.ups-move-up', function(e) {
                e.preventDefault();
                e.stopPropagation();
                self.moveProduct($(this).closest('.ups-product-item'), 'up');
            });

            $(document).on('click', '.ups-move-down', function(e) {
                e.preventDefault();
                e.stopPropagation();
                self.moveProduct($(this).closest('.ups-product-item'), 'down');
            });

            $(document).on('click', '.ups-move-bottom', function(e) {
                e.preventDefault();
                e.stopPropagation();
                self.moveProduct($(this).closest('.ups-product-item'), 'bottom');
            });

            // Warn on page leave if unsaved changes
            $(window).on('beforeunload', function() {
                if (self.hasChanges) {
                    return upso_ajax.i18n.confirm_text;
                }
            });
        },

        /**
         * Initialize Category Dropdown
         */
        initDropdown: function() {
            var self = this;

            // Toggle dropdown
            $(document).on('click', '.ups-dropdown-toggle', function(e) {
                e.preventDefault();
                e.stopPropagation();

                var $dropdown = $(this).closest('.ups-custom-dropdown');
                var isActive = $(this).hasClass('active');

                // Close all dropdowns
                $('.ups-dropdown-toggle').removeClass('active');
                $('.ups-dropdown-content').removeClass('active');

                if (!isActive) {
                    $(this).addClass('active');
                    $dropdown.find('.ups-dropdown-content').addClass('active');
                    
                    // Focus search input
                    setTimeout(function() {
                        $dropdown.find('.ups-category-search').focus();
                    }, 100);
                }
            });

            // Close dropdown on outside click
            $(document).on('click', function(e) {
                if (!$(e.target).closest('.ups-custom-dropdown').length) {
                    $('.ups-dropdown-toggle').removeClass('active');
                    $('.ups-dropdown-content').removeClass('active');
                    self.resetCategorySearch();
                }
            });

            // Expand/collapse toggle
            $(document).on('click', '.ups-expand-toggle', function(e) {
                e.preventDefault();
                e.stopPropagation();

                var $item = $(this).closest('.ups-category-item');
                var $childrenWrapper = $item.find('> .ups-children-wrapper');
                var $icon = $(this).find('.dashicons');
                var isExpanded = $item.hasClass('expanded');

                if (isExpanded) {
                    $childrenWrapper.slideUp(200);
                    $item.removeClass('expanded');
                    $icon.removeClass('dashicons-arrow-down-alt2').addClass('dashicons-arrow-right-alt2');
                } else {
                    $childrenWrapper.slideDown(200);
                    $item.addClass('expanded');
                    $icon.removeClass('dashicons-arrow-right-alt2').addClass('dashicons-arrow-down-alt2');
                }
            });

            // Category selection
            $(document).on('click', '.ups-category-link', function(e) {
                e.preventDefault();
                e.stopPropagation();

                var termId = $(this).data('term-id');

                if (!termId) {
                    return;
                }

                // Build URL and redirect
                var currentUrl = window.location.href;
                var url = new URL(currentUrl);
                url.searchParams.set('term_id', termId);

                window.location.href = url.toString();
            });

            // Category search
            $(document).on('input', '.ups-category-search', function() {
                self.filterCategories($(this).val());
            });

            // Clear search button
            $(document).on('click', '.ups-search-clear', function(e) {
                e.preventDefault();
                e.stopPropagation();
                
                var $wrapper = $(this).closest('.ups-dropdown-content');
                $wrapper.find('.ups-category-search').val('').focus();
                self.filterCategories('');
            });

            // Prevent dropdown close when clicking inside
            $(document).on('click', '.ups-dropdown-content', function(e) {
                e.stopPropagation();
            });
        },

        /**
         * Filter categories by search term
         *
         * @param {string} searchTerm Search term
         */
        filterCategories: function(searchTerm) {
            var $dropdown = $('.ups-dropdown-content.active');
            var $clearBtn = $dropdown.find('.ups-search-clear');
            var $noResults = $dropdown.find('.ups-no-results');
            var $treeWrapper = $dropdown.find('.ups-category-tree-wrapper');
            var $allItems = $dropdown.find('.ups-category-item');
            var $allWrappers = $dropdown.find('.ups-children-wrapper');
            
            searchTerm = searchTerm.toLowerCase().trim();

            // Show/hide clear button
            if (searchTerm.length > 0) {
                $clearBtn.show();
            } else {
                $clearBtn.hide();
            }

            // Reset if empty search
            if (searchTerm.length === 0) {
                this.resetCategorySearch();
                return;
            }

            // First: Reset all items and show all wrappers for searching
            $allItems.removeClass('search-hidden search-match');
            $allWrappers.show();

            var matchCount = 0;

            // Mark matching categories
            $allItems.each(function() {
                var $item = $(this);
                var categoryName = $item.find('> .ups-category-row .ups-category-name').text().toLowerCase();
                var isMatch = categoryName.indexOf(searchTerm) !== -1;

                if (isMatch) {
                    matchCount++;
                    $item.addClass('search-match');
                }
            });

            // Hide non-matching items that don't have matching children
            $allItems.each(function() {
                var $item = $(this);
                var hasMatchingChild = $item.find('.search-match').length > 0;
                var isMatch = $item.hasClass('search-match');

                if (!isMatch && !hasMatchingChild) {
                    $item.addClass('search-hidden');
                }
            });

            // Update expand icons for visible parent items
            $allItems.filter('.has-children').each(function() {
                var $item = $(this);
                var hasVisibleChild = $item.find('.ups-category-item:not(.search-hidden)').length > 0;
                
                if (hasVisibleChild) {
                    $item.addClass('expanded');
                    $item.find('> .ups-category-row .ups-expand-toggle .dashicons')
                        .removeClass('dashicons-arrow-right-alt2')
                        .addClass('dashicons-arrow-down-alt2');
                }
            });

            // Show/hide no results message
            if (matchCount === 0) {
                $noResults.show();
                $treeWrapper.hide();
            } else {
                $noResults.hide();
                $treeWrapper.show();
            }
        },

        /**
         * Reset category search
         */
        resetCategorySearch: function() {
            var $dropdown = $('.ups-dropdown-content');
            
            $dropdown.find('.ups-category-search').val('');
            $dropdown.find('.ups-search-clear').hide();
            $dropdown.find('.ups-no-results').hide();
            $dropdown.find('.ups-category-tree-wrapper').show();
            $dropdown.find('.ups-category-item').removeClass('search-hidden search-match');
            
            // Restore original expanded states - collapse items that were expanded by search
            $dropdown.find('.ups-category-item.has-children').each(function() {
                var $item = $(this);
                var $wrapper = $item.find('> .ups-children-wrapper');
                var $icon = $item.find('> .ups-category-row .ups-expand-toggle .dashicons');
                
                // Check if this item has a selected child (should stay expanded)
                var hasSelectedChild = $item.hasClass('has-selected-child') || $item.find('.selected').length > 0;
                
                if (hasSelectedChild) {
                    $wrapper.show();
                    $item.addClass('expanded');
                    $icon.removeClass('dashicons-arrow-right-alt2').addClass('dashicons-arrow-down-alt2');
                } else if (!$item.hasClass('selected')) {
                    $wrapper.hide();
                    $item.removeClass('expanded');
                    $icon.removeClass('dashicons-arrow-down-alt2').addClass('dashicons-arrow-right-alt2');
                }
            });
        },

        /**
         * Initialize Notices
         */
        initNotices: function() {
            // Dismiss notice on click
            $(document).on('click', '.ups-notice .notice-dismiss', function() {
                var $notice = $(this).closest('.ups-notice');
                var noticeType = $notice.data('notice');

                if (noticeType) {
                    $.ajax({
                        url: upso_ajax.ajax_url,
                        type: 'POST',
                        data: {
                            action: 'upso_dismiss_notice',
                            nonce: upso_ajax.nonce,
                            notice: noticeType
                        }
                    });
                }
            });
        },

        /**
         * Show Confirmation Modal
         */
        showConfirmModal: function() {
            $('#ups-confirm-modal').show();
        },

        /**
         * Hide Modal
         */
        hideModal: function() {
            $('#ups-confirm-modal').hide();
        },

        /**
         * Save Sort Order
         */
        saveOrder: function() {
            var self = this;
            var $productsList = $('#ups-products-list');
            var categoryId = parseInt($productsList.data('category-id')) || 0;
            var $saveBtn = $('#ups-save-order');
            var $modalConfirm = $('#ups-modal-confirm');
            var originalBtnText = $saveBtn.html();

            // Get current order
            var sortOrders = {};
            $productsList.find('.ups-product-item').each(function(index) {
                sortOrders[index] = $(this).data('id');
            });

            // Check if empty
            if ($.isEmptyObject(sortOrders)) {
                self.showMessage(upso_ajax.i18n.error, 'error');
                return;
            }

            // Split into chunks for large datasets
            var chunks = self.chunkObject(sortOrders, 500);
            var totalChunks = chunks.length;
            var currentChunk = 0;

            // Update UI
            $saveBtn.prop('disabled', true).html('<span class="dashicons dashicons-update spin"></span> ' + upso_ajax.i18n.saving);
            $modalConfirm.prop('disabled', true).text(upso_ajax.i18n.saving);

            /**
             * Process chunk
             */
            function processChunk() {
                if (currentChunk >= totalChunks) {
                    // All done
                    self.hideModal();
                    self.showMessage(upso_ajax.i18n.saved, 'success');
                    $saveBtn.prop('disabled', false).html(originalBtnText);
                    $modalConfirm.prop('disabled', false).text(upso_ajax.i18n.save_changes);
                    self.hasChanges = false;
                    return;
                }

                var chunkData = {
                    action: 'upso_save_sort_order',
                    nonce: upso_ajax.nonce,
                    sort_orders: chunks[currentChunk],
                    category_id: categoryId
                };

                // Add chunk markers
                if (totalChunks > 1) {
                    chunkData.is_chunk = true;
                    chunkData.is_last_chunk = (currentChunk === totalChunks - 1);
                }

                $.ajax({
                    url: upso_ajax.ajax_url,
                    type: 'POST',
                    data: chunkData,
                    success: function(response) {
                        currentChunk++;
                        processChunk();
                    },
                    error: function() {
                        self.hideModal();
                        self.showMessage(upso_ajax.i18n.error, 'error');
                        $saveBtn.prop('disabled', false).html(originalBtnText);
                        $modalConfirm.prop('disabled', false).text(upso_ajax.i18n.save_changes);
                    }
                });
            }

            // Start processing
            processChunk();
        },

        /**
         * Load More Products
         */
        loadMoreProducts: function() {
            var self = this;

            if (self.isLoading) {
                return;
            }

            var $productsList = $('#ups-products-list');
            var $loadMoreBtn = $('#ups-load-more');
            var currentPage = parseInt($productsList.data('page')) || 1;
            var maxPages = parseInt($productsList.data('max-pages')) || 1;
            var categoryId = parseInt($productsList.data('category-id')) || 0;

            if (currentPage >= maxPages) {
                $loadMoreBtn.text(upso_ajax.i18n.no_more).prop('disabled', true);
                return;
            }

            self.isLoading = true;
            $loadMoreBtn.prop('disabled', true).html('<span class="dashicons dashicons-update spin"></span> ' + upso_ajax.i18n.loading);

            $.ajax({
                url: upso_ajax.ajax_url,
                type: 'POST',
                data: {
                    action: 'upso_load_more_products',
                    nonce: upso_ajax.nonce,
                    page: currentPage + 1,
                    per_page: 100,
                    category_id: categoryId
                },
                success: function(response) {
                    if (response.success && response.data.products_html) {
                        // Append products
                        $productsList.append(response.data.products_html);
                        $productsList.data('page', response.data.current_page);

                        // Reinitialize sortable
                        if (self.sortable) {
                            self.sortable.destroy();
                        }
                        self.initSortable();

                        // Update counts
                        $('#ups-loaded-count').text(response.data.loaded);

                        // Update button
                        if (response.data.has_more) {
                            $loadMoreBtn.prop('disabled', false).html('<span class="dashicons dashicons-arrow-down-alt"></span> ' + upso_ajax.i18n.load_more);
                        } else {
                            $loadMoreBtn.text(upso_ajax.i18n.no_more).prop('disabled', true);
                        }
                    }

                    self.isLoading = false;
                },
                error: function() {
                    self.showMessage(upso_ajax.i18n.error, 'error');
                    $loadMoreBtn.prop('disabled', false).html('<span class="dashicons dashicons-arrow-down-alt"></span> ' + upso_ajax.i18n.load_more);
                    self.isLoading = false;
                }
            });
        },

        /**
         * Move Product
         *
         * @param {jQuery} $item     Product item
         * @param {string} direction Direction (top, up, down, bottom)
         */
        moveProduct: function($item, direction) {
            var $list = $('#ups-products-list');

            // Remove selection
            $item.removeClass('sortable-selected');

            switch (direction) {
                case 'top':
                    $item.fadeOut(150, function() {
                        $item.prependTo($list);
                        $item.fadeIn(150);
                    });
                    break;
                case 'up':
                    var $prev = $item.prev('.ups-product-item');
                    if ($prev.length) {
                        $item.fadeOut(150, function() {
                            $item.insertBefore($prev);
                            $item.fadeIn(150);
                        });
                    }
                    break;
                case 'down':
                    var $next = $item.next('.ups-product-item');
                    if ($next.length) {
                        $item.fadeOut(150, function() {
                            $item.insertAfter($next);
                            $item.fadeIn(150);
                        });
                    }
                    break;
                case 'bottom':
                    $item.fadeOut(150, function() {
                        $item.appendTo($list);
                        $item.fadeIn(150);
                    });
                    break;
            }

            this.hasChanges = true;
        },

        /**
         * Show Message
         *
         * @param {string} message Message text
         * @param {string} type    Message type (success, error)
         */
        showMessage: function(message, type) {
            var $response = $('#ups-response');

            $response
                .removeClass('ups-success ups-error')
                .addClass('ups-' + type)
                .html(message)
                .fadeIn();

            setTimeout(function() {
                $response.fadeOut();
            }, 3000);
        },

        /**
         * Split object into chunks
         *
         * @param {Object} obj  Object to chunk
         * @param {number} size Chunk size
         * @return {Array} Array of chunk objects
         */
        chunkObject: function(obj, size) {
            var keys = Object.keys(obj);
            var chunks = [];

            for (var i = 0; i < keys.length; i += size) {
                var chunk = {};
                var chunkKeys = keys.slice(i, i + size);

                for (var j = 0; j < chunkKeys.length; j++) {
                    chunk[chunkKeys[j]] = obj[chunkKeys[j]];
                }

                chunks.push(chunk);
            }

            return chunks;
        }
    };

    // Initialize on document ready
    $(document).ready(function() {
        UPSAdmin.init();
    });

    // Add spinning animation for loading icons
    $('<style>')
        .prop('type', 'text/css')
        .html('.spin { animation: ups-spin 1s linear infinite; } @keyframes ups-spin { from { transform: rotate(0deg); } to { transform: rotate(360deg); } }')
        .appendTo('head');

})(jQuery);
