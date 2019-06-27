# WooCommerce Product Bundles - Conditional Images

### What's This?

Mini-extension for [WooCommerce Composite Products](https://woocommerce.com/products/composite-products/) that allows you to conditionally modify the main Composite Product image in response to configuration changes.

### How Does It Work?

The mini-extension introduces a new **Scenario Action** called **Overlay Image**. This Scenario Action allows you to select an image to overlay on top of the main Composite Product image when the specified Scenario matching conditions are satified.

When multiple Scenarios with an **Overlay Image** Action exist, each Scenario may contribute its own image to the product image stack. The images are stacked according to the relative order of Scenarios: The image at the top of the stack belongs to the active **Overlay Image** Scenario closest to the top of the **Product Data > Scenarios** list.

