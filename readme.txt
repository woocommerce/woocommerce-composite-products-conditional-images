=== Composite Products - Conditional Images for WooCommerce ===

Contributors: franticpsyx, SomewhereWarm
Tags: woocommerce, composite, products, conditional, image, layers, overlay
Requires at least: 4.4
Tested up to: 5.4
Stable tag: 1.2.2
Requires PHP: 5.6
WC requires at least: 3.1
WC tested up to: 4.2
License: GNU General Public License v3.0
License URI: http://www.gnu.org/licenses/gpl-3.0.html

Free mini-extension for WooCommerce Composite Products that allows you to create dynamic, multi-layer Composite Product images that respond to option changes.


== Description ==

Free mini-extension for [WooCommerce Composite Products](https://woocommerce.com/products/composite-products/) that allows you to create dynamic, multi-layer Composite Product images that respond to option changes. The mini-extension leverages [Scenarios](https://docs.woocommerce.com/document/composite-products/composite-products-advanced-configuration/) to conditionally layer additional images over the main Composite Product image.


== Documentation ==

The plugin introduces a new "Overlay Image" Scenario Action that lets you:

* Choose an image from your Media library.
* Define **conditions** for layering this image over the main Composite Product image.

The layering order of these conditional images depends on the relative position of your "Overlay Image" Scenarios.

This approach makes it possible to utilize either **swapping** or **compositing** techniques to conditionally modify the main product image.

For more information and examples, check out the plugin's repository on [GitHub](https://github.com/somewherewarm/woocommerce-composite-products-conditional-images).


= Image Swapping =

If you have previously worked with Variable Products and Variation Images, chances are you are already familiar with this technique. Here's the idea behind image swapping:

The main product image is **swapped** with an image that depicts the chosen options as soon as the configuration of the Composite Product is complete.

Instead of replacing the product images, the same effect can be achieved by covering the main product image with a fully opaque (non-transparent) image.


= Image Compositing =

**Compositing** is the combination of multiple layers of images into a single image. The final composition creates the illusion that all stacked images are parts of the same scene. For this technique to work, each layered image must include:

* some **transparent** areas that allow lower layers of the stack to be seen;
* some **opaque** areas that introduce new elements in the scene by covering the layers below; and
* possibly, some **semi-trasparent** areas that introduce new elements while partially allowing the layers below to be seen.


= Swapping vs Compositing =

The **Swapping** technique is a viable choice if the number of Component Options is relatively low, as it requires you to **capture one picture** and **create one Scenario** for every possible configuration. Its main advantage over the **Compositing** technique is that it allows you to obtain a natural-looking result, even if you don't have advanced image processing skills.

The **Compositing** technique is often a better choice if the number of possible configurations is high, as it requires you to create one "compositable" image for each Component Option. However, specialized knowledge and tools are required to produce a natural-looking result. For this reason, the Compositing technique is often preferred when 3D product models are available, as 3D modelling software can speed up the production of "compositable" images.


= Limitations =

1. The product image cannot be opened in a lightbox once the mini-extension has stacked additional images over it.
2. The image zooming feature included with WooCommerce is not compatible with the swapping/compositing techniques introduced by the mini-extension. When a Composite Product includes Scenarios with the **Overlay Image** Scenario Action, the image zooming feature of the Composite Product is disabled.
3. The plugin works with themes that declare support for the `wc-product-gallery-slider` feature.


== Installation ==

Composite Products - Conditional Images:

1. Requires the official [WooCommerce Composite Products](https://woocommerce.com/products/composite-products/) extension. Before installing this plugin, please ensure that you are running the latest versions of both **WooCommerce** and **WooCommerce Composite Products**.
2. Only works with themes that declare support for the `wc-product-gallery-slider` feature.


== Screenshots ==

1. A Composite Product with a dynamic, multi-layer product image.
2. Using Scenarios to create conditional image layers.


== Changelog ==

= 1.2.2 =
* Fix - Declared WooCommerce 4.2 compatibility.

= 1.2.1 =
* Fix - Declared WooCommerce 4.0 compatibility.

= 1.2.0 =
* Tweak - Renamed plugin to comply with WordPress.org guidelines.

= 1.1.1 =
* Fix - Added support for creating 'overlay_image' scenarios via the REST API.
* Tweak - Declared support for WordPress 5.3 and WooCommerce 3.9.

= 1.1.0 =
* Fix - Compatibility with Composite Products 5.0+. Older versions of Composite Products are no longer supported.

= 1.0.2 =
* Fix - Resolve issue with Composite Products that do not have a product image set.

= 1.0.1 =
* Fix - Resize stacked images when the viewport width changes.
* Tweak - Adjust overlay image width based on main 'img' width.

= 1.0.0 =
* Initial Release.


== Upgrade Notice ==

= 1.2.2 =
Declared WooCommerce 4.2 compatibility.
