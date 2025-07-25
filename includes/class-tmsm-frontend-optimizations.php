<?php

/** @noinspection SpellCheckingInspection */
/** @noinspection PhpIncludeInspection */

/**
 * The core plugin class.
 *
 * This is used to define internationalization, admin-specific hooks, and
 * public-facing site hooks.
 *
 * Also maintains the unique identifier of this plugin as well as the current
 * version of the plugin.
 *
 * @since      1.0.0
 * @package    Tmsm_Frontend_Optimizations
 * @subpackage Tmsm_Frontend_Optimizations/includes
 * @author     Nicolas Mollet <nico.mollet@gmail.com>
 */
class Tmsm_Frontend_Optimizations
{

	/**
	 * The loader that's responsible for maintaining and registering all hooks that power
	 * the plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      Tmsm_Frontend_Optimizations_Loader    $loader    Maintains and registers all hooks for the plugin.
	 */
	protected Tmsm_Frontend_Optimizations_Loader $loader;

	/**
	 * The unique identifier of this plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      string    $plugin_name    The string used to uniquely identify this plugin.
	 */
	protected string $plugin_name;

	/**
	 * The current version of the plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      string    $version    The current version of the plugin.
	 */
	protected string $version;

	/**
	 * Define the core functionality of the plugin.
	 *
	 * Set the plugin name and the plugin version that can be used throughout the plugin.
	 * Load the dependencies, define the locale, and set the hooks for the admin area and
	 * the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function __construct()
	{
		if (defined('TMSM_FRONTEND_OPTIMIZATIONS_VERSION')) {
			$this->version = TMSM_FRONTEND_OPTIMIZATIONS_VERSION;
		} else {
			$this->version = '1.0.0';
		}
		$this->plugin_name = 'tmsm-frontend-optimizations';

		$this->load_dependencies();
		$this->set_locale();
		$this->define_public_hooks();
	}

	/**
	 * Load the required dependencies for this plugin.
	 *
	 * Include the following files that make up the plugin:
	 *
	 * - Tmsm_Frontend_Optimizations_Loader. Orchestrates the hooks of the plugin.
	 * - Tmsm_Frontend_Optimizations_i18n. Defines internationalization functionality.
	 * - Tmsm_Frontend_Optimizations_Admin. Defines all hooks for the admin area.
	 * - Tmsm_Frontend_Optimizations_Public. Defines all hooks for the public side of the site.
	 *
	 * Create an instance of the loader which will be used to register the hooks
	 * with WordPress.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function load_dependencies()
	{

		/**
		 * Asset Optimizer Class
		 */
		require_once plugin_dir_path(dirname(__FILE__)) . 'includes/class-wp-simple-asset-optimizer.php';

		/**
		 * The class responsible for orchestrating the actions and filters of the
		 * core plugin.
		 */
		require_once plugin_dir_path(dirname(__FILE__)) . 'includes/class-tmsm-frontend-optimizations-loader.php';

		/**
		 * The class responsible for defining internationalization functionality
		 * of the plugin.
		 */
		require_once plugin_dir_path(dirname(__FILE__)) . 'includes/class-tmsm-frontend-optimizations-i18n.php';

		/**
		 * The class responsible for defining all actions that occur in the public-facing
		 * side of the site.
		 */
		require_once plugin_dir_path(dirname(__FILE__)) . 'public/class-tmsm-frontend-optimizations-public.php';
		/**
		 * The class responsible to handle shortcodes
		 */
		require_once plugin_dir_path(dirname(__FILE__)) . 'includes/class-tmsm-frontend-optimizations-shortcode.php';

		$this->loader = new Tmsm_Frontend_Optimizations_Loader();
	}

	/**
	 * Define the locale for this plugin for internationalization.
	 *
	 * Uses the Tmsm_Frontend_Optimizations_i18n class in order to set the domain and to register the hook
	 * with WordPress.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function set_locale()
	{

		$plugin_i18n = new Tmsm_Frontend_Optimizations_i18n();

		$this->loader->add_action('plugins_loaded', $plugin_i18n, 'load_plugin_textdomain');
	}

	/**
	 * Register all of the hooks related to the public-facing functionality
	 * of the plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function define_public_hooks()
	{

		$plugin_public = new Tmsm_Frontend_Optimizations_Public($this->get_plugin_name(), $this->get_version());

		$this->loader->add_action('wp_enqueue_scripts', $plugin_public, 'enqueue_styles');
		$this->loader->add_action('wp_enqueue_scripts', $plugin_public, 'enqueue_scripts');

		$this->loader->add_action('get_the_archive_title', $plugin_public, 'wp_get_the_archive_title_category', 10); // Remove "Category:" in titles
		$this->loader->add_filter('script_loader_tag', $plugin_public, 'wp_script_loader_tag', 10, 3);

		$this->loader->add_action('init', $plugin_public, 'assetoptimizer'); // Asset Optimizer
		$this->loader->add_filter('use_default_gallery_style', $plugin_public, 'wp_use_default_gallery_style', 10); //Default gallery
		remove_action('wp_print_styles', 'print_emoji_styles'); // Emoji
		$this->loader->add_action('wp_head', $plugin_public, 'wp_staging_noindex', 10);
		remove_action('wp_head', 'print_emoji_detection_script', 7); //Emoji
		$this->loader->add_filter('user_request_confirmed_email_to', $plugin_public, 'wp_user_request_confirmed_email_to_dpo', 10, 2);
		$this->loader->add_filter('embed_oembed_html', $plugin_public, 'wp_oembed_result_modest', 100, 4);
		$this->loader->add_filter('embed_oembed_html', $plugin_public, 'wp_embed_wrap', 100, 4);
		$this->loader->add_filter('embed_googlevideo', $plugin_public, 'wp_embed_wrap', 10, 4);
		$this->loader->add_action('enqueue_block_assets', $plugin_public, 'wp_enqueue_block_assets', 10);
		$this->loader->add_action('login_init', $plugin_public, 'no_weak_password_header');
		$this->loader->add_action('admin_head', $plugin_public, 'no_weak_password_header');
		$this->loader->add_action('validate_password_reset', $plugin_public, 'esp_validate_password_reset', 10, 2);
		remove_action('wp_head', 'wlwmanifest_link');
		remove_action('wp_head', 'rsd_link');
		remove_action('wp_head', 'wp_generator');
		remove_action('wp_head', 'wp_shortlink_wp_head');
		remove_action('set_comment_cookies', 'wp_set_comment_cookies');

		// Google Tag Manager
		$this->loader->add_filter('init', $plugin_public, 'gtm4wp_scriptsfooter', 10); // Google Tag Manager for WordPress
		$this->loader->add_action('ocean_before_outer_wrap', $plugin_public, 'googletagmanager_after_body', 10); // For OceanWP theme
		$this->loader->add_filter('gtm4wp_get_the_gtm_tag', $plugin_public, 'googletagmanager_getthetag', 10, 1);

		// Storefront Theme
		remove_action('storefront_footer', 'storefront_credit', 20);

		// Polylang
		if (function_exists('is_plugin_active') && (is_plugin_active('polylang/polylang.php') || is_plugin_active('polylang-pro/polylang.php'))) {
			$this->loader->add_filter('body_class', $plugin_public, 'polylang_body_class', 10);
			$this->loader->add_action('wp_enqueue_scripts', $plugin_public, 'polylang_configjavascript'); // Localize
		}
		add_shortcode('language_switcher', array($plugin_public, 'polylang_language_switcher'));

		// Gravity Forms
		if (class_exists('GFForms') && property_exists('GFForms', 'version') && version_compare(GFForms::$version, '2.5', '<')) {
			$this->loader->add_filter('gform_cdata_close', $plugin_public, 'gravityforms_wrap_gform_cdata_close', 10);
			$this->loader->add_filter('gform_init_scripts_footer', $plugin_public, 'gravityforms_footer_noblockrender', 10);
			$this->loader->add_filter('gform_cdata_open', $plugin_public, 'gravityforms_wrap_gform_cdata_open', 10);
		}
		if (class_exists('GFForms')) {
			$this->loader->add_filter('gform_form_tag', $plugin_public, 'gravityforms_form_tag', 10, 2);
			$this->loader->add_filter('gform_field_content', $plugin_public, 'gravityforms_field_content_name', 10, 5);
			$this->loader->add_filter('gform_form_settings_fields', $plugin_public, 'gravityforms_form_settings_fields', 10, 2);
			$this->loader->add_filter('gform_phone_formats', $plugin_public, 'gravityforms_phone_formats', 10, 1);
			$this->loader->add_filter('gform_replace_merge_tags', $plugin_public, 'gravityforms_mergetags', 20, 7);
			$this->loader->add_action('gform_enqueue_scripts', $plugin_public, 'gravityforms_dequeue_stylesheets', 10);
			$this->loader->add_action('gform_ip_address', $plugin_public, 'gravityforms_donotcollect_ipaddress', 10);
			$this->loader->add_action('gform_pre_submission', $plugin_public, 'gravityforms_personal_data', 10, 1);
			$this->loader->add_filter('gform_field_content', $plugin_public, 'gravityforms_field_content_autocompleteoff', 10, 5);
			$this->loader->add_filter('gform_field_content', $plugin_public, 'gravityforms_field_content_phoneformat', 10, 5);
			$this->loader->add_filter('gform_address_display_format', $plugin_public, 'gravityforms_address_zipbeforecity', 10, 2);
			$this->loader->add_filter('gform_field_validation', $plugin_public, 'gravityforms_check_email_domain', 10, 4);
			add_filter('gform_required_legend', '__return_empty_string');
		}

		// Jetpack
		$this->loader->add_action('loop_start', $plugin_public, 'jetpack_remove_share', 10);
		$this->loader->add_action('wp_enqueue_scripts', $plugin_public, 'jetpack_dequeue_scripts', 10); //Jetpack
		add_filter('jetpack_tools_to_include', function ($tools) {
			return array_diff($tools, array('geo-location.php'));
		});

		// Yoast SEO
		$this->loader->add_filter('wpseo_breadcrumb_output_wrapper', $plugin_public, 'wpseo_breadcrumb_output_wrapper', 10);
		$this->loader->add_filter('wpseo_breadcrumb_output_class', $plugin_public, 'wpseo_breadcrumb_output_class', 10);
		$this->loader->add_filter('wpseo_next_rel_link', $plugin_public, 'wpseo_disable_rel_next_home', 10);

		// WP Rocket
		$this->loader->add_filter('rocket_lazyload_excluded_attributes', $plugin_public, 'rocket_lazyload_excluded_attributes_elementor', 10);

		// WooCommerce
		$this->loader->add_filter('woocommerce_registration_errors', $plugin_public, 'woocommerce_check_email_domain', 10, 3);
		$this->loader->add_action('woocommerce_endpoint_order-received_title', $plugin_public, 'woocommerce_endpoint_order_received_title', 200);
		$this->loader->add_action('woocommerce_scheduled_sales', $plugin_public, 'woocommerce_scheduled_sales_empty_cache', 200);
		$this->loader->add_filter('password_hint', $plugin_public, 'woocommerce_password_hint', 10, 1);
		$this->loader->add_filter('woocommerce_min_password_strength', $plugin_public, 'woocommerce_min_password_strength', 10, 0);
		$this->loader->add_filter('woocommerce_cod_icon', $plugin_public, 'woocommerce_cod_icon_travel', 10);
		$this->loader->add_filter('woocommerce_package_rates', $plugin_public, 'woocommerce_package_rates_hide_shipping_on_local_pickup_required', 10, 2);
		$this->loader->add_action('woocommerce_product_meta_end', $plugin_public, 'woocommerce_product_meta_end_freeshippingpocalpickup', 50);
		$this->loader->add_action('woocommerce_dropdown_variation_attribute_options_html', $plugin_public, 'woocommerce_dropdown_variation_attribute_options_html_radio', 50, 2);
		$this->loader->add_filter('woocommerce_product_variation_title_include_attributes', $plugin_public, 'woocommerce_product_variation_title_include_attributes', 10, 2);
		$this->loader->add_filter('woocommerce_price_trim_zeros', $plugin_public, 'woocommerce_trim_zero', 10);

		// Paypal Checkout
		$this->loader->add_filter('woocommerce_paypal_express_checkout_address_not_required', $plugin_public, 'woocommerce_paypal_checkout_address_not_required', 10, 1);

		// TAO Schedule Update
		$this->loader->add_action('tao_publish_post', $plugin_public, 'tao_publish_post_emptycache', 200);

		// WooCommerce Advanced messages
		$this->loader->add_filter('wcam_locations', $plugin_public, 'wcam_locations', 10, 1);

		// Elementor
		$this->loader->add_filter('elementor_pro/search_form/after_input', $plugin_public, 'elementor_search_form_after_input', 100, 1);

		// PublishPress Future
		$this->loader->add_action('postExpiratorExpire', $plugin_public, 'postexpirator_expireclearcache', 10);
	}

	/**
	 * Run the loader to execute all of the hooks with WordPress.
	 *
	 * @since    1.0.0
	 */
	public function run()
	{
		$this->loader->run();
	}

	/**
	 * The name of the plugin used to uniquely identify it within the context of
	 * WordPress and to define internationalization functionality.
	 *
	 * @since     1.0.0
	 * @return    string    The name of the plugin.
	 */
	public function get_plugin_name()
	{
		return $this->plugin_name;
	}

	/**
	 * The reference to the class that orchestrates the hooks with the plugin.
	 *
	 * @since     1.0.0
	 * @return    Tmsm_Frontend_Optimizations_Loader    Orchestrates the hooks of the plugin.
	 */
	public function get_loader()
	{
		return $this->loader;
	}

	/**
	 * Retrieve the version number of the plugin.
	 *
	 * @since     1.0.0
	 * @return    string    The version number of the plugin.
	 */
	public function get_version()
	{
		return $this->version;
	}
}
