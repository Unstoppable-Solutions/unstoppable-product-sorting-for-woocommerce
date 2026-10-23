# Unstoppable Product Sorting for WooCommerce - Development Roadmap

This document outlines the planned development priorities for Unstoppable Product Sorting for WooCommerce.

**Current Version:** 1.0.8  
**Last Updated:** October 23, 2026

---

## Phase 1: Immediate Priorities

Foundation work to ensure stability, security, and maintainability.

### 1.1 GitHub Repository Migration ✅
- **Description:** Migrate plugin source code to Unstoppable Solutions GitHub organization
- **Benefit:** Centralized version control and collaboration
- **Priority:** High
- **Complexity:** Small
- **Milestone:** 1.0.8
- **Status:** Completed
- **Source:** Previous Discussion

### 1.2 WordPress 7.1.3 Compatibility ✅
- **Description:** Test and confirm compatibility with WordPress 7.1.3
- **Benefit:** Users can update WordPress without compatibility concerns
- **Priority:** High
- **Complexity:** Small
- **Milestone:** 1.0.8
- **Status:** Completed
- **Source:** Code Review

### 1.3 Automated SVN Publishing ✅
- **Description:** GitHub Actions workflow for automatic WordPress.org deployment
- **Benefit:** Streamlined release process, reduced manual errors
- **Priority:** High
- **Complexity:** Medium
- **Milestone:** 1.0.8
- **Status:** Completed
- **Source:** Previous Discussion

### 1.4 WooCommerce Marketplace Submission
- **Description:** Submit plugin to the WooCommerce Marketplace
- **Benefit:** Increased visibility and distribution through official WooCommerce channels
- **Priority:** High
- **Complexity:** Medium
- **Milestone:** 1.1.0
- **Status:** In Progress
- **Source:** Previous Discussion

### 1.5 Plugin Check Workflow ✅
- **Description:** GitHub Actions workflow for automated WordPress plugin checks
- **Benefit:** Catch issues before release, ensure coding standards compliance
- **Priority:** Medium
- **Complexity:** Small
- **Milestone:** 1.0.8
- **Status:** Completed
- **Source:** Code Review

---

## Phase 2: Short-Term Improvements

Enhancements to existing functionality and user experience.

### 2.1 Undo/Redo Functionality
- **Description:** Add undo/redo buttons for sorting changes before saving
- **Benefit:** Users can easily revert mistakes without reloading the page
- **Priority:** High
- **Complexity:** Medium
- **Milestone:** 1.1.0
- **Status:** Proposed
- **Source:** New Suggestion

### 2.2 Keyboard Shortcuts
- **Description:** Add keyboard shortcuts for common actions (Ctrl+S to save, arrow keys to move)
- **Benefit:** Power users can work faster without mouse
- **Priority:** Medium
- **Complexity:** Small
- **Milestone:** 1.1.0
- **Status:** Proposed
- **Source:** New Suggestion

### 2.3 Search/Filter Products
- **Description:** Add search box to filter products in the sorting interface
- **Benefit:** Quickly find specific products in large catalogs
- **Priority:** High
- **Complexity:** Small
- **Milestone:** 1.1.0
- **Status:** Proposed
- **Source:** New Suggestion

### 2.4 Bulk Select Improvements
- **Description:** Add "Select All", "Select None", and range selection (Shift+Click)
- **Benefit:** Easier management of large product groups
- **Priority:** Medium
- **Complexity:** Small
- **Milestone:** 1.1.0
- **Status:** Proposed
- **Source:** New Suggestion

### 2.5 Visual Product Thumbnails
- **Description:** Show product thumbnail images in the sorting list
- **Benefit:** Easier product identification without reading titles
- **Priority:** Medium
- **Complexity:** Small
- **Milestone:** 1.1.0
- **Status:** Proposed
- **Source:** New Suggestion

### 2.6 Save Confirmation
- **Description:** Add visual confirmation when changes are saved successfully
- **Benefit:** Clear feedback that changes were persisted
- **Priority:** Low
- **Complexity:** Small
- **Milestone:** 1.1.0
- **Status:** Proposed
- **Source:** Code Review

### 2.7 Unsaved Changes Warning
- **Description:** Warn users before navigating away with unsaved changes
- **Benefit:** Prevent accidental loss of sorting work
- **Priority:** High
- **Complexity:** Small
- **Milestone:** 1.1.0
- **Status:** Proposed
- **Source:** New Suggestion

---

## Phase 3: Mid-Term Development

New features that expand plugin functionality.

### 3.1 Import/Export Sort Orders
- **Description:** Export sorting configuration to JSON/CSV, import on another site
- **Benefit:** Easy migration between staging and production, or between sites
- **Priority:** Medium
- **Complexity:** Medium
- **Milestone:** 1.2.0
- **Status:** Proposed
- **Source:** New Suggestion

### 3.2 Sorting Templates/Presets
- **Description:** Save and apply sorting presets (e.g., "Holiday Layout", "Sale Layout")
- **Benefit:** Quickly switch between seasonal or promotional arrangements
- **Priority:** Medium
- **Complexity:** Large
- **Milestone:** 1.2.0
- **Status:** Proposed
- **Source:** New Suggestion

### 3.3 Scheduled Sorting
- **Description:** Schedule sort order changes to apply automatically at specific times
- **Benefit:** Automate promotional product placement for sales events
- **Priority:** Low
- **Complexity:** Large
- **Milestone:** 1.3.0
- **Status:** Proposed
- **Source:** New Suggestion

### 3.4 Tag-Based Sorting
- **Description:** Sort products by WooCommerce tags in addition to categories
- **Benefit:** More granular sorting control for complex catalogs
- **Priority:** Medium
- **Complexity:** Medium
- **Milestone:** 1.2.0
- **Status:** Proposed
- **Source:** New Suggestion

### 3.5 Attribute-Based Sorting
- **Description:** Sort products by custom attributes (color, size, brand)
- **Benefit:** Advanced sorting for stores with attribute-heavy products
- **Priority:** Low
- **Complexity:** Large
- **Milestone:** 1.3.0
- **Status:** Proposed
- **Source:** New Suggestion

### 3.6 Sorting Analytics
- **Description:** Track which sort orders perform best (requires WooCommerce analytics integration)
- **Benefit:** Data-driven decisions about product placement
- **Priority:** Low
- **Complexity:** Large
- **Milestone:** 1.3.0
- **Status:** Proposed
- **Source:** New Suggestion

### 3.7 REST API Endpoints
- **Description:** Add REST API for programmatic sorting management
- **Benefit:** Enable integrations with external tools and automation
- **Priority:** Medium
- **Complexity:** Medium
- **Milestone:** 1.2.0
- **Status:** Proposed
- **Source:** New Suggestion

---

## Phase 4: Long-Term Development

Major features for competitive differentiation and scalability.

### 4.1 AI-Powered Sorting Suggestions
- **Description:** Suggest optimal product order based on sales data and customer behavior
- **Benefit:** Automated optimization without manual effort
- **Priority:** Low
- **Complexity:** Large
- **Milestone:** 2.0.0
- **Status:** Proposed
- **Source:** New Suggestion

### 4.2 A/B Testing for Sort Orders
- **Description:** Test different sort orders and measure conversion impact
- **Benefit:** Scientific approach to product placement optimization
- **Priority:** Low
- **Complexity:** Large
- **Milestone:** 2.0.0
- **Status:** Proposed
- **Source:** New Suggestion

### 4.3 Multi-Store Sync
- **Description:** Sync sorting across multiple WooCommerce installations
- **Benefit:** Consistent product display across multi-site networks
- **Priority:** Low
- **Complexity:** Large
- **Milestone:** 2.0.0
- **Status:** Proposed
- **Source:** New Suggestion

### 4.4 Visual Grid Editor
- **Description:** Drag-and-drop sorting in a grid view matching shop layout
- **Benefit:** WYSIWYG editing that matches actual storefront
- **Priority:** Medium
- **Complexity:** Large
- **Milestone:** 2.0.0
- **Status:** Proposed
- **Source:** New Suggestion

### 4.5 Role-Based Sorting Permissions
- **Description:** Control which user roles can edit product sorting
- **Benefit:** Delegate sorting to shop managers without full admin access
- **Priority:** Low
- **Complexity:** Small
- **Milestone:** 1.3.0
- **Status:** Proposed
- **Source:** New Suggestion

### 4.6 WooCommerce Blocks Integration
- **Description:** Full compatibility with Gutenberg-based WooCommerce Blocks
- **Benefit:** Modern editor support and block-based layouts
- **Priority:** Medium
- **Complexity:** Medium
- **Milestone:** 1.3.0
- **Status:** Proposed
- **Source:** Code Review

---

## Version Milestones

| Version | Focus | Target |
|---------|-------|--------|
| 1.0.8 | Current stable release | ✅ Released |
| 1.1.0 | UX improvements, search, undo/redo | Q4 2026 |
| 1.2.0 | Import/export, templates, REST API | Q1 2027 |
| 1.3.0 | Advanced sorting, scheduling, permissions | Q2 2027 |
| 2.0.0 | AI features, A/B testing, visual editor | TBD |

---

## How to Contribute

We welcome feature requests and feedback! 

- **WordPress.org Support Forum:** [Support](https://wordpress.org/support/plugin/unstoppable-product-sorting-for-woocommerce/)
- **GitHub Issues:** [Report Issues](https://github.com/Unstoppable-Solutions/unstoppable-product-sorting-for-woocommerce/issues)
- **Website:** [unstoppablesolutions.app](https://unstoppablesolutions.app)

---

## Legend

**Priority:**
- High: Critical for user satisfaction or competitiveness
- Medium: Valuable enhancement, not urgent
- Low: Nice to have, future consideration

**Complexity:**
- Small: < 1 day of development
- Medium: 1-3 days of development
- Large: > 3 days of development

**Status:**
- Proposed: Under consideration
- Planned: Approved for development
- In Progress: Currently being developed
- Completed: Released

**Source:**
- Previous Discussion: From earlier conversations or user feedback
- Code Review: Identified during code analysis
- New Suggestion: Newly proposed feature
