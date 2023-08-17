<?php
/**
* Plugin Name: Composite Products - Conditional Images
* Plugin URI: https://docs.woocommerce.com/document/composite-products/composite-products-extensions/#cp-ci
* Description: Free mini-extension for WooCommerce Composite Products that allows you to create dynamic, multi-layer Composite Product images that respond to option changes.
* Version: 1.2.6
* Author: SomewhereWarm
* Author URI: https://somewherewarm.com/
*
* Text Domain: woocommerce-composite-products-conditional-images
* Domain Path: /languages/
*
* Requires at least: 4.4
* Tested up to: 5.6
*
* WC requires at least: 3.1
* WC tested up to: 5.1
*
* Copyright: © 2017-2021 SomewhereWarm SMPC.
* License: GNU General Public License v3.0
* License URI: http://www.gnu.org/licenses/gpl-3.0.html
*/

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Main plugin class.
 *
 * @class    WC_CP_Conditional_Images
 * @version  1.2.6
 */
class WC_CP_Conditional_Images {

	/**
	 * Plugin version.
	 *
	 * @var string
	 */
	public static $version = '1.2.6';

	/**
	 * Min required CP version.
	 *
	 * @var string
	 */
	public static $req_cp_version = '5.1';

	/**
	 * CP URL.
	 *
	 * @var string
	 */
	private static $cp_url = 'https://woocommerce.com/products/composite-products/';

	/**
	 * Plugin URL.
	 *
	 * @return string
	 */
	public static function plugin_url() {
		return plugins_url( basename( plugin_dir_path(__FILE__) ), basename( __FILE__ ) );
	}

	/**
	 * Plugin path.
	 *
	 * @return string
	 */
	public static function plugin_path() {
		return untrailingslashit( plugin_dir_path( __FILE__ ) );
	}

	/**
	 * Fire in the hole!
	 */
	public static function init() {
		add_action( 'plugins_loaded', array( __CLASS__, 'load_plugin' ) );
	}

	/**
	 * Hooks.
	 */
	public static function load_plugin() {

		if ( ! function_exists( 'WC_CP' ) || version_compare( WC_CP()->version, self::$req_cp_version ) < 0 ) {
			add_action( 'admin_notices', array( __CLASS__, 'cp_version_check_notice' ) );
			return false;
		}

		// Localization.
		add_action( 'init', array( __CLASS__, 'localize_plugin' ) );

		// Front-end script (where the magic happens).
		add_filter( 'woocommerce_composite_script_dependencies', array( __CLASS__, 'frontend_script' ) );

		// If overlays exist, dequeue zoom script.
		add_action( 'woocommerce_composite_add_to_cart', array( __CLASS__, 'dequeue_zoom_script' ), 10 );

		// Admin script.
		add_action( 'admin_enqueue_scripts', array( __CLASS__, 'admin_scripts' ) );

		// Add 'Overlay Image' action in Scenarios.
		add_action( 'woocommerce_composite_scenario_admin_actions_html', array( __CLASS__, 'scenario_admin_actions_html' ), 20, 4 );

		// Save 'Overlay Image' action settings.
		add_filter( 'woocommerce_composite_process_scenario_data', array( __CLASS__, 'process_scenario_data' ), 10, 5 );

		// Add qty data in scenarios.
		add_filter( 'woocommerce_composite_current_scenario_data', array( __CLASS__, 'scenario_data' ), 10, 4 );

		if ( version_compare( WC_CP()->version, '8.0' ) < 0 ) {
			// Allow 'overlay_image' scenario actions to be created via the REST API.
			add_filter( 'woocommerce_rest_api_extended_composite_scenarios_field_args', array( __CLASS__, 'add_rest_api_scenario_action' ) );
		}
	}

	/**
	 * CP version check notice.
	 */
	public static function cp_version_check_notice() {
	    echo '<div class="error"><p>' . sprintf( __( '<strong>Composite Products &ndash; Conditional Images</strong> requires <a href="%1$s" target="_blank">WooCommerce Composite Products</a> version <strong>%2$s</strong> or higher.', 'woocommerce-composite-products-conditional-images' ), self::$cp_url, self::$req_cp_version ) . '</p></div>';
	}

	/**
	 * Load textdomain.
	 *
	 * @return void
	 */
	public static function localize_plugin() {
		load_plugin_textdomain( 'woocommerce-composite-products-conditional-images', false, dirname( plugin_basename( __FILE__ ) ) . '/languages/' );
	}

	/**
	 * Front-end script.
	 *
	 * @param array $dependencies
	 */
	public static function frontend_script( $dependencies ) {

		if ( ! current_theme_supports( 'wc-product-gallery-slider' ) ) {
			return false;
		}

		$suffix = defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ? '' : '.min';
		wp_register_script( 'wc-add-to-cart-composite-ci', self::plugin_url() . '/assets/js/single-product' . $suffix . '.js', array( 'jquery', 'underscore', 'backbone' ), self::$version, true );
		$dependencies[] = 'wc-add-to-cart-composite-ci';

		return $dependencies;
	}

	/**
	 * Dequeue zoom script.
	 */
	public static function dequeue_zoom_script( $dependencies ) {

		global $product;

		if ( ! current_theme_supports( 'wc-product-gallery-slider' ) ) {
			return false;
		}

		$has_overlay_image_scenarios = false;
		$scenario_metadata           = $product->get_scenario_data();

		if ( ! empty( $scenario_metadata ) ) {
			foreach ( $scenario_metadata as $scenario_id => $metadata ) {
				if ( isset( $metadata[ 'scenario_actions' ][ 'overlay_image' ][ 'is_active' ] ) && 'yes' === $metadata[ 'scenario_actions' ][ 'overlay_image' ][ 'is_active' ] ) {
					$has_overlay_image_scenarios = true;
					break;
				}
			}
		}

		if ( $has_overlay_image_scenarios ) {
			wp_dequeue_script( 'zoom' );
		}
	}

	/**
	 * Admin scripts and styles.
	 *
	 * @return void
	 */
	public static function admin_scripts() {

		$suffix = defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ? '' : '.min';

		$script_dependency = 'wc-composite-admin-product-panel';
		wp_register_script( 'wc-cp-ci-admin-product-metaboxes', self::plugin_url() . '/assets/js/meta-boxes-product' . $suffix . '.js', array( 'jquery', 'jquery-ui-datepicker', 'wp-util', 'wc-admin-product-meta-boxes', $script_dependency ), self::$version );

		$style_dependency = 'wc-composite-writepanel-css';
		wp_register_style( 'wc-cp-ci-admin-product-metaboxes-css', self::plugin_url() . '/assets/css/meta-boxes-product.css', array( 'woocommerce_admin_styles', $style_dependency ), self::$version );
		wp_style_add_data( 'wc-cp-ci-admin-product-metaboxes-css', 'rtl', 'replace' );

		// Get admin screen id.
		$screen    = get_current_screen();
		$screen_id = $screen ? $screen->id : '';

		if ( 'product' === $screen_id ) {
			wp_enqueue_script( 'wc-cp-ci-admin-product-metaboxes' );

			$params = array(
				'i18n_choose_component_image' => __( 'Choose an Image', 'woocommerce-composite-products-conditional-images' ),
				'i18n_set_component_image'    => __( 'Save Image', 'woocommerce-composite-products-conditional-images' )
			);

			wp_localize_script( 'wc-cp-ci-admin-product-metaboxes', 'wc_cp_ci_admin_params', $params );
		}

		if ( in_array( $screen_id, array( 'edit-product', 'product' ) ) ) {
			wp_enqueue_style( 'wc-cp-ci-admin-product-metaboxes-css' );
		}
	}

	/**
	 * Save qty override settings in scenarios
	 *
	 * @param  array  $scenario_meta
	 * @param  array  $scenario_post_data
	 * @param  string $scenario_id
	 * @param  array  $composite_meta
	 * @param  string $composite_id
	 * @return array
	 */
	public static function process_scenario_data( $scenario_meta, $scenario_post_data, $scenario_id, $composite_meta, $composite_id ) {

		$is_active = ! empty( $scenario_post_data[ 'scenario_actions' ][ 'overlay_image' ][ 'is_active' ] );
		$image_id  = ! empty( $scenario_post_data[ 'scenario_actions' ][ 'overlay_image' ][ 'image_id' ] ) ? $scenario_post_data[ 'scenario_actions' ][ 'overlay_image' ][ 'image_id' ] : '';

		// Save defaults.
		$scenario_meta[ 'scenario_actions' ][ 'overlay_image' ][ 'is_active' ] = 'no';
		$scenario_meta[ 'scenario_actions' ][ 'overlay_image' ][ 'image_id' ] = '';

		// Save image src.
		if ( $image_id ) {
			$scenario_meta[ 'scenario_actions' ][ 'overlay_image' ][ 'image_id' ] = $image_id;
		}

		// Save active state.
		if ( $is_active && $image_id ) {
			$scenario_meta[ 'scenario_actions' ][ 'overlay_image' ][ 'is_active' ] = 'yes';
		}

		return $scenario_meta;
	}

	/**
	 * Add qty data in scenarios
	 *
	 * @param  array  $scenario_data
	 * @param  array  $component_options
	 * @param  string $composite
	 * @return array
	 */
	public static function scenario_data( $scenario_data, $component_options, $composite ) {

		if ( ! current_theme_supports( 'wc-product-gallery-slider' ) ) {
			return $scenario_data;
		}

		$scenario_metadata = $composite->get_scenario_data();

		if ( ! empty( $scenario_data[ 'scenarios' ] ) ) {

			foreach ( $scenario_data[ 'scenarios' ] as $scenario_id ) {

				if ( isset( $scenario_metadata[ $scenario_id ][ 'scenario_actions' ][ 'overlay_image' ][ 'is_active' ] ) && 'yes' === $scenario_metadata[ $scenario_id ][ 'scenario_actions' ][ 'overlay_image' ][ 'is_active' ] ) {

					$image_id = ! empty( $scenario_metadata[ $scenario_id ][ 'scenario_actions' ][ 'overlay_image' ][ 'image_id' ] ) ? $scenario_metadata[ $scenario_id ][ 'scenario_actions' ][ 'overlay_image' ][ 'image_id' ] : '';

					if ( ! $image_id ) {
						continue;
					}

					$image_size = apply_filters( 'woocommerce_gallery_image_size', 'woocommerce_single' );
					$image      = wp_get_attachment_image( $image_id, $image_size, false, apply_filters( 'woocommerce_gallery_image_html_attachment_image_params', array(
						'class'    => 'wp-post-image wc-cp-overlay-image'
					), $image_id, $image_size, true ) );

					$scenario_data[ 'scenario_settings' ][ 'overlay_image' ][ $scenario_id ] = $image;
				}
			}
		}

		return $scenario_data;
	}

	/**
	 * Add 'Overlay Image' action in Scenarios.
	 *
	 * @return void
	 */
	public static function scenario_admin_actions_html( $id, $scenario_data, $composite_data, $product_id ) {

		$overlay_image = isset( $scenario_data[ 'scenario_actions' ][ 'overlay_image' ][ 'is_active' ] ) && 'yes' === $scenario_data[ 'scenario_actions' ][ 'overlay_image' ][ 'is_active' ] ? 'yes' : 'no';
		$image_id      = ! empty( $scenario_data[ 'scenario_actions' ][ 'overlay_image' ][ 'image_id' ] ) ? $scenario_data[ 'scenario_actions' ][ 'overlay_image' ][ 'image_id' ] : '';
		$image         = $image_id ? wp_get_attachment_thumb_url( $image_id ) : '';
		$is_cp_gte_80  = version_compare( WC_CP()->version, '8.0' ) >= 0;

		if ( $is_cp_gte_80 ) {
			?>
			<div class="scenario_action_config_group scenario_action_overlay_image_group" >
				<div class="toggle_scenario_action_config toggle_overlay_image">
					<label for="scenario_action_overlay_image_<?php echo $id; ?>">
						<input id="scenario_action_overlay_image_<?php echo $id; ?>" type="checkbox" class="checkbox scenario_action_overlay_image" <?php echo ( $overlay_image === 'yes' ? ' checked="checked"' : '' ); ?> name="bto_scenario_data[<?php echo $id; ?>][scenario_actions][overlay_image][is_active]" <?php echo ( $overlay_image === 'yes' ? ' value="1"' : '' ); ?> />
						<?php
							echo __( 'Overlay Image', 'woocommerce-composite-products-conditional-images' );
							echo wc_help_tip( __( 'Enable this option to conditionally overlay an image over the main Composite Product image. When using this feature, product image zooming will be disabled.', 'woocommerce-composite-products-conditional-images' ) );
						?>
					</label>
				</div>
				<div class="action_config action_conditional_images" <?php echo ( $overlay_image === 'no' ? ' style="display:none;"' : '' ); ?> >
					<a href="#" class="upload_conditional_image_button <?php echo $image_id ? 'has_image': ''; ?>">
						<span class="prompt"><?php echo __( 'Select image', 'woocommerce-composite-products' ); ?></span>
						<img src="<?php if ( ! empty( $image ) ) echo esc_attr( $image ); else echo esc_attr( wc_placeholder_img_src() ); ?>" />
						<input type="hidden" name="bto_scenario_data[<?php echo $id; ?>][scenario_actions][overlay_image][image_id]" class="image" value="<?php echo $image_id; ?>" />
					</a>
					<a href="#" class="remove_conditional_image_button <?php echo $image_id ? 'has_image': ''; ?>"><?php echo __( 'Remove image', 'woocommerce-composite-products' ); ?></a>
				</div>
			</div>
			<?php

		} else {

			?>
			<div class="scenario_action_overlay_image_group" >
				<div class="form-field toggle_overlay_image">
					<label for="scenario_action_overlay_image_<?php echo $id; ?>">
						<?php echo __( 'Overlay Image', 'woocommerce-composite-products-conditional-images' ); ?>
					</label>
					<input id="scenario_action_overlay_image_<?php echo $id; ?>" type="checkbox" class="checkbox scenario_action_overlay_image" <?php echo ( $overlay_image === 'yes' ? ' checked="checked"' : '' ); ?> name="bto_scenario_data[<?php echo $id; ?>][scenario_actions][overlay_image][is_active]" <?php echo ( $overlay_image === 'yes' ? ' value="1"' : '' ); ?> /><?php
						echo wc_help_tip( __( 'Enable this option to conditionally overlay an image over the main Composite Product image. When using this feature, product image zooming will be disabled.', 'woocommerce-composite-products-conditional-images' ) );
					?>
				</div>
				<div class="form-field action_conditional_images" <?php echo ( $overlay_image === 'no' ? ' style="display:none;"' : '' ); ?> >
					<a href="#" class="upload_conditional_image_button <?php echo $image_id ? 'has_image': ''; ?>">
						<span class="prompt"><?php echo __( 'Select image', 'woocommerce-composite-products' ); ?></span>
						<img src="<?php if ( ! empty( $image ) ) echo esc_attr( $image ); else echo esc_attr( wc_placeholder_img_src() ); ?>" />
						<input type="hidden" name="bto_scenario_data[<?php echo $id; ?>][scenario_actions][overlay_image][image_id]" class="image" value="<?php echo $image_id; ?>" />
					</a>
					<a href="#" class="remove_conditional_image_button <?php echo $image_id ? 'has_image': ''; ?>"><?php echo __( 'Remove image', 'woocommerce-composite-products' ); ?></a>
				</div>
			</div>
			<?php
		}
	}

	/**
	 * Add support for creating 'overlay_image' scenario actions via the REST API.
	 *
	 * @since  1.1.1
	 * @param  array
	 */
	public static function add_rest_api_scenario_action( $args ) {
		$args[ 'schema' ][ 'items' ][ 'properties' ][ 'actions' ][ 'items' ][ 'properties' ][ 'action_id' ][ 'enum' ][] = 'overlay_image';
		return $args;
	}
}

WC_CP_Conditional_Images::init();
