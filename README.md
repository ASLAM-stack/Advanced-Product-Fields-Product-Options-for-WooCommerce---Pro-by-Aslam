# Advanced Product Fields (Product Options) for WooCommerce — Pro by Aslam

[![WordPress](https://img.shields.io/badge/WordPress-6.0%2B-blue.svg?logo=wordpress&logoColor=white)](https://wordpress.org)
[![WooCommerce](https://img.shields.io/badge/WooCommerce-7.0%2B%20--%209.0%2B-purple.svg?logo=woocommerce&logoColor=white)](https://woocommerce.com)
[![HPOS](https://img.shields.io/badge/WooCommerce-HPOS%20Compatible-success.svg)](https://woo.com)
[![PHP](https://img.shields.io/badge/PHP-7.4%20--%208.3%2B-777BB4.svg?logo=php&logoColor=white)](https://php.net)
[![License](https://img.shields.io/badge/License-GPLv2%2B-orange.svg)](https://www.gnu.org/licenses/gpl-2.0.html)
[![Pro Features](https://img.shields.io/badge/Pro%20Features-100%25%20Free-red.svg)](https://github.com/ASLAM-stack/Advanced-Product-Fields-Product-Options-for-WooCommerce---Pro-by-Aslam)

A modern, high-performance, and feature-complete alternative to commercial WooCommerce product addon plugins. **Advanced Product Fields by Aslam** unlocks all commercial **Pro** capabilities for free—with built-in styling tailored specifically for modern food and restaurant themes like the **Barab Fast Food Restaurant WordPress Theme**.

---

## 🚀 Key Highlights

* **13+ Versatile Field Types**: Single-line Text, Multi-line Textarea, Number, Dropdown Select, Radio Buttons, Checkbox & Multi-Select Groups, Color Swatches, Image Swatches, Drag-and-Drop File Upload, Date Picker, Time Picker, Add-on Quantity Steppers (+/-), and Section Dividers.
* **5 Dynamic Pricing Models**: Flat fee, quantity-multiplied fee, percentage of base price, character-count pricing (engraving/embroidery with free character thresholds), and custom mathematical formulas (e.g. `[price] + [qty] * 2.5`).
* **Advanced Reactive Conditional Logic**: Build complex show/hide rules with multi-rule **AND (ALL)** or **OR (ANY)** logic that evaluates in real-time on the browser and verifies on the server.
* **Barab Theme Visual Styling**: Designed with signature fast-food aesthetics—bold typography, pill-shaped radio/checkbox cards, circular quantity steppers, vibrant color badges (`#EB1400`, `#3F9065`, `#F7F2E2`), and micro-interactions.
* **Live Order Receipt Breakdown**: Real-time restaurant check receipt card updating on the fly as customers toggle ingredients, extras, and portions.
* **Dedicated Style Customizer**: Manage global design under **WooCommerce > Field Styles**—fine-tune primary colors, border radii, font families, pre-made color themes (Barab Signature, Dark Gourmet, Fresh Green), or inject custom CSS.
* **High-Performance Order Storage (HPOS) Certified**: Compatible with WooCommerce 8.x and 9.x HPOS tables and legacy post-meta seamlessly.
* **Enterprise Security**: Strict nonce validation, capability checks (`manage_woocommerce`), sanitized inputs, safe mathematical formula sandboxing, and secure file upload verification with MIME type inspection and random file hashing.

---

## 📦 Supported Field Types

| Field Type | Description | Pro Feature Unlocked |
| :--- | :--- | :---: |
| **Text (Single Line)** | Custom engravings, names, short instructions | ✅ Included |
| **Textarea (Multi-Line)** | Special kitchen preparation notes or allergy warnings | ✅ Included |
| **Number** | Numeric parameters with minimum, maximum, and step increments | ✅ Included |
| **Select Dropdown** | Single or multi-select dropdown menu with live prices | ✅ Included |
| **Radio Buttons** | Fast-food pill selection cards with icons and price tags | ✅ Included |
| **Checkbox / Multi-Select** | Addon checkboxes (e.g., extra sauces, toppings, dips) | ✅ Included |
| **Color Swatches** | Interactive circular color swatches with active checkmarks | ✅ Included |
| **Image Swatches** | Visual cards showcasing crusts, meats, and drink sizes | ✅ Included |
| **File Upload** | AJAX drag-and-drop file uploader with upload progress bar | ✅ Included |
| **Date Picker** | Modern HTML5 date selector with min/max restrictions | ✅ Included |
| **Time Picker** | Pickup / dining slot time selector | ✅ Included |
| **Quantity Stepper (+/-)** | Circular plus/minus stepper for portion increments | ✅ Included |
| **Section Divider** | Clean separator with title and description for multi-step menus | ✅ Included |

---

## 💰 Dynamic Pricing Models

1. **Flat Fee (+/-)**: Adds or subtracts a fixed monetary amount (e.g., `+$1.50` for extra cheese).
2. **Quantity-Based**: Multiplied automatically by item quantity in the cart.
3. **Percentage Fee (%)**: Computes a percentage of the base product regular or sale price (e.g., `+10%` for gift packaging).
4. **Character Count Pricing**: Charges per character typed into a text or textarea field, supporting a free character threshold (e.g., first 5 characters free, `$0.10` per additional letter).
5. **Mathematical Formulas**: Evaluates custom arithmetic expressions with dynamic tag substitution:
   * `[price]` — Base product price
   * `[field_id]` — Numeric value or selection from any other form field
   * *Example*: `[price] * 0.10 + 2.50`

---

## 🛠️ Installation & Setup

### Method 1: Install via WordPress Admin (Recommended)
1. Download the latest `advanced-product-fields-by-aslam.zip` release.
2. Log into your WordPress Dashboard (`wp-admin`).
3. Navigate to **Plugins > Add New Plugin > Upload Plugin**.
4. Choose `advanced-product-fields-by-aslam.zip` and click **Install Now**.
5. Click **Activate Plugin**.

### Method 2: Manual / Git Installation
1. Clone this repository directly into your WordPress plugins folder:
   ```bash
   cd wp-content/plugins/
   git clone https://github.com/ASLAM-stack/Advanced-Product-Fields-Product-Options-for-WooCommerce---Pro-by-Aslam.git advanced-product-fields-by-aslam
   ```
2. In WordPress Admin, go to **Plugins > Installed Plugins** and activate **Advanced Product Fields (Product Options) for WooCommerce — Pro by Aslam**.

---

## 🍔 Quick Start Guide

1. **Create Global Field Groups**:
   * Navigate to **WooCommerce > Product Field Groups**.
   * Click **Add New Field Group**.
   * Click **Add New Field** to add options (steppers, swatches, checkboxes, etc.).
   * Under **Display & Assignment Rules**, choose where to display these fields:
     * *All Products*
     * *Specific Products* (search & multi-select products)
     * *Product Categories*
2. **Product-Level Overrides**:
   * Open any WooCommerce Product in edit mode.
   * Scroll down to the **Custom Product Fields (APF)** metabox to configure fields specifically for that single item.
3. **Style Customizer**:
   * Head over to **WooCommerce > Field Styles** to toggle preset themes or customize brand colors and border radii.

---

## 🎨 Barab Theme Style Customizer

Customize the look and feel to match any WooCommerce store:

* **Primary Accent**: Brand highlight (Default: `#EB1400` Barab Crimson)
* **Secondary Color**: Success and price badge highlight (Default: `#3F9065` Fresh Basil Green)
* **Background Pill Color**: Subtle card background (Default: `#F7F2E2` Warm Brioche Cream)
* **Corner Radius**: Adjust pill and input roundedness (Default: `50px` for smooth curves)
* **Receipt Card**: Live floating order receipt with real-time recalculation

---

## 🧪 Interactive Standalone Demo

A complete standalone demo HTML page is included in the plugin package for rapid frontend testing:
* Open `preview-demo.html` in any browser to test live radio pills, image swatches, conditional color swatches, multi-select checkboxes, quantity steppers, character count pricing, and formula calculations without needing a live WordPress installation.

---

## 👨‍💻 Developer API & Filters

### Custom Price Calculations
```php
add_filter( 'apf_calculated_field_price', function( $price_delta, $field, $value, $base_price ) {
    // Custom modifier logic
    return $price_delta;
}, 10, 4 );
```

### Alter Fields for a Product Programmatically
```php
add_filter( 'apf_fields_for_product', function( $fields, $product_id ) {
    // Inject or filter fields conditionally
    return $fields;
}, 10, 2 );
```

---

## 📄 License & Credits

* **Author**: Aslam ([@ASLAM-stack](https://github.com/ASLAM-stack))
* **License**: GNU General Public License v2 or later ([GPL-2.0-or-later](https://www.gnu.org/licenses/gpl-2.0.html))
* Designed and engineered for high-volume WooCommerce food ordering and customizable e-commerce stores.
