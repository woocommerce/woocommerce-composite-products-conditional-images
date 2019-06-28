# WooCommerce Product Bundles - Conditional Images

### What's This?

Mini-extension for [WooCommerce Composite Products](https://woocommerce.com/products/composite-products/) that allows you to conditionally modify the main Composite Product image in response to configuration changes.

### How Does It Work?

The mini-extension introduces a new **Scenario Action** called **Overlay Image**, which requires you to choose an image from your Media library. When the Scenario matching conditions are satified, this image will be overlaid on top of the main Composite Product image.

When multiple **Overlay Image** Scenarios exist, each may contribute its own image to the main product image stack. The order of images in the stack follows the order of Scenarios in **Product Data > Scenarios**.

This approach makes it possible to utilize either **swapping** or **compositing** techniques to conditionally modify the main product image, depending on your needs:

#### Image Swapping

If you have worked with Variable Products and Variation Images, chances are you are already familiar with this concept. Here's the idea behind image swapping: 

The main product image is **swapped** with an image that depicts the chosen options as soon as the configuration of the Composite Product is complete.

#### Image Compositing

**Compositing** is the combination of multiple layered images into a single image. The final composition creates the illusion that all stacked images are parts of the same scene. For this technique to work, each layered image must include:

* some **transparent** areas that allow lower layers of the stack to be seen;
* some **opaque** areas that introduce new elements in the scene by covering the layers below; and
* possibly, some **semi-trasparent** areas that introduce new elements while partially allowing the layers below to be seen.

#### Swapping vs Compositing

The **Swapping** technique is a viable choice if the number of Component Options is relatively low, as it requires you to **capture one picture** and **create one Scenario** for every possible configuration. Its main advantage over the **Compositing** technique is that it allows you to obtain a natural-looking result, even if you don't have advanced image processing skills.

The **Compositing** technique is often a better choice if the number of possible configurations is high, as it requires you to create one "compositable" image for each Component Option. However, specialized knowledge and tools are required to produce a natural-looking result. For this reason, the Compositing technique is often preferred when 3D product models are available, as 3D modelling software can speed up the production of "compositable" images.

