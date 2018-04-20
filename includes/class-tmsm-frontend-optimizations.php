<?php

/**
 * The file that defines the core plugin class
 *
 * A class definition that includes attributes and functions used across both the
 * public-facing side of the site and the admin area.
 *
 * @link       https://github.com/nicomollet
 * @since      1.0.0
 *
 * @package    Tmsm_Frontend_Optimizations
 * @subpackage Tmsm_Frontend_Optimizations/includes
 */

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
class Tmsm_Frontend_Optimizations {

	/**
	 * The loader that's responsible for maintaining and registering all hooks that power
	 * the plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      Tmsm_Frontend_Optimizations_Loader    $loader    Maintains and registers all hooks for the plugin.
	 */
	protected $loader;

	/**
	 * The unique identifier of this plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      string    $plugin_name    The string used to uniquely identify this plugin.
	 */
	protected $plugin_name;

	/**
	 * The current version of the plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      string    $version    The current version of the plugin.
	 */
	protected $version;

	/**
	 * Define the core functionality of the plugin.
	 *
	 * Set the plugin name and the plugin version that can be used throughout the plugin.
	 * Load the dependencies, define the locale, and set the hooks for the admin area and
	 * the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function __construct() {
		if ( defined( 'TMSM_FRONTEND_OPTIMIZATIONS_VERSION' ) ) {
			$this->version = TMSM_FRONTEND_OPTIMIZATIONS_VERSION;
		} else {
			$this->version = '1.0.0';
		}
		$this->plugin_name = 'tmsm-frontend-optimizations';

		$this->load_dependencies();
		$this->set_locale();
		//$this->define_admin_hooks();
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
	private function load_dependencies() {

		/**
		 * Asset Optimizer Class
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-wp-simple-asset-optimizer.php';

		/**
		 * The class responsible for orchestrating the actions and filters of the
		 * core plugin.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-tmsm-frontend-optimizations-loader.php';

		/**
		 * The class responsible for defining internationalization functionality
		 * of the plugin.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-tmsm-frontend-optimizations-i18n.php';

		/**
		 * The class responsible for defining all actions that occur in the admin area.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/class-tmsm-frontend-optimizations-admin.php';

		/**
		 * The class responsible for defining all actions that occur in the public-facing
		 * side of the site.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'public/class-tmsm-frontend-optimizations-public.php';

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
	private function set_locale() {

		$plugin_i18n = new Tmsm_Frontend_Optimizations_i18n();

		$this->loader->add_action( 'plugins_loaded', $plugin_i18n, 'load_plugin_textdomain' );

	}

	/**
	 * Register all of the hooks related to the admin area functionality
	 * of the plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function define_admin_hooks() {

		$plugin_admin = new Tmsm_Frontend_Optimizations_Admin( $this->get_plugin_name(), $this->get_version() );

		$this->loader->add_action( 'admin_enqueue_scripts', $plugin_admin, 'enqueue_styles' );
		$this->loader->add_action( 'admin_enqueue_scripts', $plugin_admin, 'enqueue_scripts' );

	}

	/**
	 * Register all of the hooks related to the public-facing functionality
	 * of the plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function define_public_hooks() {

		$plugin_public = new Tmsm_Frontend_Optimizations_Public( $this->get_plugin_name(), $this->get_version() );

		//$this->loader->add_action( 'wp_enqueue_scripts', $plugin_public, 'enqueue_styles' );
		//$this->loader->add_action( 'wp_enqueue_scripts', $plugin_public, 'enqueue_scripts' );

		$this->loader->add_action( 'get_the_archive_title', $plugin_public, 'get_the_archive_title_category', 10 ); // Remove "Category:" in titles

		// Scripts to footer
		$this->loader->add_filter( 'init', $plugin_public, 'gtm4wp_scriptsfooter', 10 ); // Google Tag Manager for WordPress
		$this->loader->add_action( 'init', $plugin_public, 'assetoptimizer' ); // Asset Optimizer

		// Remove styles
		$this->loader->add_filter( 'use_default_gallery_style', $plugin_public, '__return_false', 10 ); //Default gallery
		remove_action( 'wp_print_styles', 'print_emoji_styles' ); // Emoji

		// Remove scripts
		remove_action( 'wp_head', 'print_emoji_detection_script', 7 ); //Emoji
		$this->loader->add_action( 'wp_enqueue_scripts', $plugin_public, 'jetpack_dequeue_scripts', 10 ); //Jetpack

		// Development
		$this->loader->add_action( 'wp_head', $plugin_public, 'staging_noindex', 10 );

		// Head
		remove_action('wp_head', 'wlwmanifest_link');
		remove_action('wp_head', 'rsd_link');
		remove_action('wp_head', 'wp_generator');
		remove_action('wp_head', 'wp_shortlink_wp_head');


		// Cookies
		remove_action('set_comment_cookies', 'wp_set_comment_cookies');

		// Storefront Theme
		remove_action( 'storefront_footer', 'storefront_credit', 20 );

		// WPML
		if ( function_exists( 'icl_get_languages' ) ) {
			add_shortcode( 'language_switcher', 'wpml_language_switcher' );
			define('ICL_DONT_LOAD_LANGUAGE_SELECTOR_CSS',true);
			define('ICL_DONT_LOAD_NAVIGATION_CSS',true);
			define('ICL_DONT_LOAD_LANGUAGES_JS',true);
			$this->loader->add_filter( 'body_class', $plugin_public, 'wpml_body_class', 10 );
		}

		// Polylang
		if ( is_plugin_active( 'polylang/polylang.php' ) || is_plugin_active( 'polylang-pro/polylang.php' )) {
			add_shortcode( 'language_switcher', 'polylang_language_switcher' );
			$this->loader->add_filter( 'body_class', $plugin_public, 'polylang_body_class', 10 );
			$this->loader->add_action( 'wp_enqueue_scripts', $plugin_public, 'polylang_configjavascript' ); // Localize
		}

		// Gravity Forms
		$this->loader->add_filter( 'gform_cdata_close', $plugin_public, 'gravityforms_wrap_gform_cdata_close', 10 );
		$this->loader->add_filter( 'gform_init_scripts_footer', $plugin_public, 'gravityforms_footer_noblockrender', 10 );
		$this->loader->add_filter( 'gform_cdata_open', $plugin_public, 'gravityforms_wrap_gform_cdata_open', 10 );
		$this->loader->add_action( 'gform_enqueue_scripts', $plugin_public, 'gravityforms_dequeue_stylesheets', 10 );
		$this->loader->add_action( 'gform_ip_address', $plugin_public, 'gravityforms_donotcollect_ipaddress', 10 );

		// Google Tag Manager / OceanWP
		$this->loader->add_action( 'ocean_before_outer_wrap', $plugin_public, 'googletagmanager_after_body', 10 ); // For OceanWP

		// Yoast SEO
		$this->loader->add_filter( 'wpseo_breadcrumb_output_wrapper', $plugin_public, 'wpseo_breadcrumb_output_wrapper', 10 );
		$this->loader->add_filter( 'wpseo_breadcrumb_output_class', $plugin_public, 'wpseo_breadcrumb_output_class', 10 );
		$this->loader->add_filter( 'wpseo_next_rel_link', $plugin_public, 'wpseo_disable_rel_next_home', 10 );

	}

	/**
	 * Run the loader to execute all of the hooks with WordPress.
	 *
	 * @since    1.0.0
	 */
	public function run() {
		$this->loader->run();
	}

	/**
	 * The name of the plugin used to uniquely identify it within the context of
	 * WordPress and to define internationalization functionality.
	 *
	 * @since     1.0.0
	 * @return    string    The name of the plugin.
	 */
	public function get_plugin_name() {
		return $this->plugin_name;
	}

	/**
	 * The reference to the class that orchestrates the hooks with the plugin.
	 *
	 * @since     1.0.0
	 * @return    Tmsm_Frontend_Optimizations_Loader    Orchestrates the hooks of the plugin.
	 */
	public function get_loader() {
		return $this->loader;
	}

	/**
	 * Retrieve the version number of the plugin.
	 *
	 * @since     1.0.0
	 * @return    string    The version number of the plugin.
	 */
	public function get_version() {
		return $this->version;
	}

}
