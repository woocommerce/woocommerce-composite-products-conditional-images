<?php
/**
* Plugin Name: WooCommerce Composite Products - Conditional Images
* Plugin URI: http://www.woothemes.com/products/composite-products/
* Description: Composite Products mini-extension that allows you to conditionally overlay additional images over the main Composite Product image.
* Version: 1.0.0
* Author: SomewhereWarm
* Author URI: https://somewherewarm.gr/
*
* Text Domain: woocommerce-composite-products-conditional-images
* Domain Path: /languages/
*
* Requires at least: 4.4
* Tested up to: 5.2
*
* WC requires at least: 3.1
* WC tested up to: 3.6
*
* Copyright: © 2017-2019 SomewhereWarm SMPC.
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
 * @version  1.0.0
 */
class WC_CP_Conditional_Images {

	/**
	 * Plugin version.
	 *
	 * @var string
	 */
	public static $version = '1.0.0';

	/**
	 * Min required CP version.
	 *
	 * @var string
	 */
	public static $req_cp_version = '4.0.0';

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
		add_filter( 'woocommerce_composite_script_dependencies', array( __CLASS__, 'add_to_cart_script' ) );

		// Admin script.
		add_action( 'admin_enqueue_scripts', array( __CLASS__, 'admin_scripts' ) );

		// Add 'Overlay Image' action in Scenarios.
		add_action( 'woocommerce_composite_scenario_admin_actions_html', array( __CLASS__, 'scenario_admin_actions_html' ), 20, 4 );

		// Save 'Overlay Image' action settings.
		add_filter( 'woocommerce_composite_process_scenario_data', array( __CLASS__, 'process_scenario_data' ), 10, 5 );

		// Add qty data in scenarios.
		add_filter( 'woocommerce_composite_current_scenario_data', array( __CLASS__, 'scenario_data' ), 10, 4 );
	}

	/**
	 * CP version check notice.
	 */
	public static function cp_version_check_notice() {
	    echo '<div class="error"><p>' . sprintf( __( '<strong>WooCommerce Composite Products &ndash; Conditional Images</strong> requires Composite Products <strong>%s</strong> or higher.', 'woocommerce-composite-products-conditional-images' ), self::$req_cp_version ) . '</p></div>';
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
	public static function add_to_cart_script( $dependencies ) {

		$suffix = defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ? '' : '.min';

		wp_register_script( 'wc-add-to-cart-composite-ci', self::plugin_url() . '/assets/js/single-product' . $suffix . '.js', array(), self::$version );

		$dependencies[] = 'wc-add-to-cart-composite-ci';

		return $dependencies;
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
						'class' => 'wp-post-image',
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
				<?php echo wc_help_tip( __( 'Choose an image to overlay over the main Composite Product image.', 'woocommerce-composite-products-conditional-images' ) ); ?>
				<a href="#" class="remove_conditional_image_button <?php echo $image_id ? 'has_image': ''; ?>"><?php echo __( 'Remove image', 'woocommerce-composite-products' ); ?></a>
			</div>
		</div>
		<?php
	}
}

WC_CP_Conditional_Images::init();
