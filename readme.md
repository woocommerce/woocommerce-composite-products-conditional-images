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

### Image Swapping Example

The two techniques can be better understood by looking at a practical example. The Build Your Outfit composite product. The product consists of a **T-Shirt**, a pair of **Shoes** and a free **Accessory**.

[Swap Image Scenario]

Consider the following configuration of Component Options:

1. White T-Shirt
2. Brown Shoes
3. Red Tie

Our objective is to swap the main product image once these options have been selected. To achieve this:

1. Create a new Scenario, titled "Swap image: White T-Shirt / Brown Shoes / Red Tie".
2. Locate the **Configuration** section.
3. Tick the **T-Shirt** Component checkbox.
4. Choose **is**.
5. Search for and select the **White T-Shirt**.
6. Tick the **Shoes** Component checkbox.
7. Choose **is**.
8. Search for and select the **Brown Shoes**.
9. Tick the **Accessory** Component checkbox.
10. Choose **is**.
11. Search for and select the **Red Tie**. 
12. Locate the **Scenario Actions** section.
13. Check the **Overlay Image** option.
14. Click **Select Image** and choose an image that depicts the White T-Shirt / Brown Shoes / Red Tie configuration.
15. Make sure that the **Activate Options** and **Hide Components** options are disabled.
16. **Update** the product to save your changes.

Now, after choosing the White T-Shirt, Brown Shoes and Red Tie, the main product image will be replaced by the image you selected in Step 14. To achieve the same result when any combination of options is selected, repeat Steps 1-15 for all possible **Build Your Outfit** configurations.

[Result]

Note that the image will be updated after **all 3 Component Options have been selected**.

#### Image Compositing Example

This time, instead of creating an image (and Scenario) for every possible configuration, we will layer multiple images on top of each other to obtain the same end-result. An advantage of this method is that the main product image will be updated **while** the **T-Shirt**, **Shoes** and **Accessory** Components are configured.

Let's start by adding an image for the **White T-Shirt** option:

1. Create a new Scenario, titled "White T-Shirt Image".
2. Locate the **Configuration** section.
3. Tick the **T-Shirt** Component checkbox.
4. Choose **is**.
5. Search for and select the **White T-Shirt**.
6. Leave the **Shoes** and **Accessory** Component boxes unchecked.
7. Locate the **Scenario Actions** section.
8. Check the **Overlay Image** option.
9. Click **Select Image** and choose an image that depicts the **White T-Shirt**.
10. Make sure that the **Activate Options** and **Hide Components** options are disabled.
11. **Update** the product to save your changes.

[White Tee Scenario]

Our objective is to replace the featured image of the product with a new "scene", and add a new element every time a Component Option is selected. To achieve this, the image that we added in Step 9 **completely covers** the original featured image:

[White Tee Image]

Next, we will add a "compositable" image for the **Brown Shoes** option:

1. Create a new Scenario, titled "Brown Shoes Image".
2. Locate the **Configuration** section.
3. Tick the **T-Shirt** Component checkbox.
4. Choose **is**.
5. Select the **Any Product or Variation** option.
6. Tick the **Shoes** Component checkbox.
7. Choose **is**.
8. Search for and select the **Brown Shoes**.
9. Leave the **Accessory** Component box unchecked.
10. Locate the **Scenario Actions** section.
11. Check the **Overlay Image** option.
12. Click **Select Image** and choose an image that depicts the **Brown Shoes**.
13. Make sure that the **Activate Options** and **Hide Components** options are disabled.
14. **Update** the product to save your changes.

[Brown Shoes Scenario]

Note that:

* The Brown Shoes image we selected in Step 12 contains a transparent area that allows the White T-Shirt image to remain visible when the Brown Shoes option is selected.
* The Brown Shoes image should be layered over the chosen T-Shirt image only when a T-Shirt is selected. This is achieved in Steps 3-5.

[Brown Shoes Image]

Finally, let's add a "compositable" image for the **Red Tie** option by repeating steps 1-14 above:

1. Create a new Scenario, titled "Red Tie Image".
2. Locate the **Configuration** section.
3. Tick the **T-Shirt** Component checkbox.
4. Choose **is**.
5. Select the **Any Product or Variation** option.
6. Tick the **Accessory** Component checkbox.
7. Choose **is**.
8. Search for and select the **Brown Shoes**.
9. Leave the **Accessory** Component box unchecked.
10. Locate the **Scenario Actions** section.
11. Check the **Overlay Image** option.
12. Click **Select Image** and choose an image that depicts the **Brown Shoes**.
13. Make sure that the **Activate Options** and **Hide Components** options are disabled.
14. **Update** the product to save your changes.

[Red Tie Scenario]

[Result]

