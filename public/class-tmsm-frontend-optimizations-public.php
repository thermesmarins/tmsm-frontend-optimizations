<?php

/**
 * The public-facing functionality of the plugin.
 *
 * @link       https://github.com/nicomollet
 * @since      1.0.0
 *
 * @package    Tmsm_Frontend_Optimizations
 * @subpackage Tmsm_Frontend_Optimizations/public
 */

use Elementor\Plugin;

/**
 * The public-facing functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the public-facing stylesheet and JavaScript.
 *
 * @package    Tmsm_Frontend_Optimizations
 * @subpackage Tmsm_Frontend_Optimizations/public
 * @author     Nicolas Mollet <nico.mollet@gmail.com>
 */
class Tmsm_Frontend_Optimizations_Public {

	/**
	 * The ID of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $plugin_name    The ID of this plugin.
	 */
	private $plugin_name;

	/**
	 * The version of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $version    The current version of this plugin.
	 */
	private $version;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.0
	 * @param      string    $plugin_name       The name of the plugin.
	 * @param      string    $version    The version of this plugin.
	 */
	public function __construct( $plugin_name, $version ) {

		$this->plugin_name = $plugin_name;
		$this->version = $version;

	}

	/**
	 * Register the stylesheets for the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_styles() {

		wp_enqueue_style( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'css/tmsm-frontend-optimizations-public.css', array(), $this->version, 'all' );

	}

	/**
	 * Register the JavaScript for the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_scripts() {

		wp_enqueue_script( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'js/tmsm-frontend-optimizations-public.js', array( 'jquery' ), $this->version, true );

		// Don't load JS if current product type is bundle to prevent the page from not working
		if ( function_exists( 'wc_get_product' ) && ! ( wc_get_product() && wc_get_product()->is_type( 'bundle' ) ) ) {
			wp_deregister_script( 'wc-add-to-cart-variation' );
			wp_register_script( 'wc-add-to-cart-variation', plugin_dir_url( __FILE__ ) . 'js/radioattribute.js', array( 'jquery', 'wp-util' ),
				$this->version, true );
		}
	}

	/**
	 * Staging: noindex,nofollow,noarchive,nosnippet
	 */
	public function staging_noindex() {
		if ( get_option( 'blog_public' ) === '0' ) {
			echo '<meta name="robots" content="noindex,nofollow,noarchive,nosnippet">', "\n";
		}
	}


	/**
	 * Embed wrap
	 *
	 * @param        $cache
	 * @param        $url
	 * @param string $attr
	 * @param string $post_ID
	 *
	 * @return string
	 */
	function embed_wrap( $cache, $url, $attr = '', $post_ID = '' ) {
		return '<div class="embed">' . $cache . '</div>';
	}

	/**
	 * Remove "Category:" in titles
	 *
	 * @param $title
	 *
	 * @return string
	 */
	public function get_the_archive_title_category($title){
		if ( is_category() ) {
			$title = single_cat_title( '', false );
		} elseif ( is_tag() ) {
			$title = single_tag_title( '', false );
		} elseif ( is_author() ) {
			$title = '<span class="vcard">' . get_the_author() . '</span>' ;
		}
		return $title;
	}

	// Jetpack: Remove Sharing Filters
	public function jetpack_remove_share() {

		remove_filter( 'the_content', 'sharing_display',19 );
		remove_filter( 'the_excerpt', 'sharing_display',19 );

		if ( class_exists( 'Jetpack_Likes' ) ) {
			remove_filter( 'the_content', array( Jetpack_Likes::init(), 'post_likes' ), 30 );
		}
	}

	/**
	 * Google Tag Manager for WordPress: Scripts in footer
	 */
	function gtm4wp_scriptsfooter() {
		if(!is_admin()){
			add_filter('gtm4wp_event-outbound', '__return_true');
			add_filter('gtm4wp_event-form-move', '__return_true');
			add_filter('gtm4wp_event-social', '__return_true');
			add_filter('gtm4wp_event-email-clicks', '__return_true');
			add_filter('gtm4wp_event-downloads', '__return_true');
			add_filter('gtm4wp_scroller-enabled', '__return_true');
			add_filter('gtm4wp_integrate-woocommerce-track-classic-ecommerce', '__return_true');
			add_filter('gtm4wp_integrate-woocommerce-track-enhanced-ecommerce', '__return_true');

			if(function_exists('gtm4wp_wp_footer')){
				remove_action( 'wp_footer', 'gtm4wp_wp_footer' );
				add_action( 'wp_footer', 'gtm4wp_wp_footer', 999 );
			}

			if(function_exists('gtm4wp_woocommerce_wp_footer')){
				remove_action( 'wp_footer', 'gtm4wp_woocommerce_wp_footer' );
				add_action( 'wp_footer', 'gtm4wp_woocommerce_wp_footer',500 );
			}
		}

	}


	/**
	 * Asset Optimizer
	 */
	public function assetoptimizer() {

		add_filter( 'wpsao_move', function () {
			return array(
				'jquery',
				'jquery-migrate',
				'jquery-core',
				'jquery_json',
				'gform_json',
				'bootstrap',
				'gform_placeholder',
				'gform_gravityforms',
				'gform_conditional_logic',
				'flatpickr',
				'flatpickr-fr',
				'optin-monster-api-script',
				'wp-mediaelement',
				'visualizer-google-jsapi',
				'visualizer-render',
				'optinmonster-api-script',
			);
		} );


		$wp_scripts = wp_scripts();
		$wp_styles = wp_styles();
		new WP_Simple_Asset_Optimizer( $wp_scripts, $wp_styles );
	}

	/**
	 * Disable default gallery style
	 */
	function use_default_gallery_style(){
		return false;
	}

	/**
	 * Jetpack: remove scripts
	 */
	function jetpack_dequeue_scripts() {
		wp_dequeue_script( 'devicepx' );
		wp_dequeue_script( 'wp_rp_edit_related_posts_js' );

		wp_deregister_script( 'jquery.spin' );
		wp_deregister_script( 'spin' );
		// Jetpack Jquery spin if carousel ou infinitescroll
		if ( class_exists('Jetpack_Carousel') || class_exists('The_Neverending_Home_Page')) :
			wp_register_script( 'spin', plugins_url( 'jetpack/_inc/spin.js', 'jetpack' ), false, null, true );
			wp_register_script( 'jquery.spin', plugins_url( 'jetpack/_inc/jquery.spin.js', 'jetpack' ) , array( 'jquery', 'spin' ), null, true );
		endif;

		// Jetpack Tiled Gallery
		wp_dequeue_script( 'tiled-gallery' );
		if ( class_exists('Jetpack_Tiled_Gallery') && get_option( 'tiled_galleries' )) :
			wp_enqueue_script( 'tiled-gallery', plugins_url( 'jetpack/modules/tiled-gallery/tiled-gallery/tiled-gallery.js', 'jetpack' ), array( 'jquery' ), null, true );
		endif;

		// Jetpack Carousel
		wp_dequeue_script( 'jetpack-carousel' );
		if ( class_exists('Jetpack_Carousel')) :
			wp_enqueue_script( 'jetpack-carousel', plugins_url( 'jetpack/modules/carousel/jetpack-carousel.js', 'jetpack' ), array( 'jquery.spin' ), null, true );
		endif;

	}

	/**
	 * WPML: Body Class Language
	 *
	 * @param  array $classes An array of body classes.
	 * @return array
	 */
	public function wpml_body_class($classes = []) {
		if (defined('ICL_LANGUAGE_CODE')) $classes[] = "lang-" . ICL_LANGUAGE_CODE;
		return $classes;
	}

	/**
	 * WPML: Language Switcher Function
	 *
	 * @return string
	 */
	private function wpml_language_switcher(){
		$languages = icl_get_languages('skip_missing=0&orderby=code');
		$inactives='';
		$actives='';
		$output = '';
		if(!empty($languages)){
			$output.= '<div class="language-switcher language-switcher-wpml"><div class="btn-group">';
			foreach($languages as $l) :
				if($l['active'])
					$actives='
              <button data-toggle="dropdown" class="btn dropdown-toggle">
              '.$l['language_code'].' <span class="caret"></span>
              </button>
              <ul class="dropdown-menu">
            ';
				else
					$inactives.= '
              <li>
                <a href="'.$l['url'].'" data-lang="'.$l['language_code'].'">'.$l['language_code'].'</a>
              </li>
            ';
			endforeach;

			$output.= $actives.$inactives;
			$output.= '</ul></div></div>';
		}
		return $output;
	}

	/**
	 * Polylang: Body Class Lang
	 *
	 * @param  array $classes An array of body classes.
	 * @return array
	 */
	function polylang_body_class( $classes )
	{
		if ( function_exists( 'PLL' ) && $language = PLL()->model->get_language( get_locale() ) )
		{
			$classes[] = 'pll-' . str_replace( '_', '-', sanitize_title_with_dashes( $language->get_locale( 'raw' ) ) );
			$classes[] = 'lang-' . pll_current_language();
		}
		return $classes;
	}

	/**
	 * Polylang: Language Switcher Function
	 *
	 * @return string
	 */
	function polylang_language_switcher() {
		$output = '';
		if ( function_exists( 'pll_the_languages' ) ) {
			$args   = [
				'show_flags' => 0,
				'show_names' => 1,
				'echo'       => 0,
			];
			$ul_classes = '';
			$theme = wp_get_theme();
			if(!empty($theme)){
				$parent_theme = $theme->parent();
				$theme_name = $theme->name;
				if(!empty($parent_theme)){
					$theme_name = $parent_theme->name;
				}
				switch($theme_name){
					case 'OceanWP':
						$ul_classes= 'dropdown-menu';
						break;
					case 'StormBringer':
						$ul_classes= 'list-unstyled list-inline';
						break;
					default:
						$ul_classes= '';
						break;
				}
			}
			$output = '<ul class="language-switcher language-switcher-polylang '.$ul_classes.'">'.pll_the_languages( $args ). '</ul>';
		}

		return $output;
	}

	/**
	 * Polylang: Config As Javascript
	 *
	 */
	function polylang_configjavascript() {
		wp_localize_script('jquery', 'polylang_params', array(
			'current_language' => pll_current_language(),
			'home_url' => pll_home_url(),
			'the_languages' => pll_the_languages(['raw' => 1]),
		));
	}

	/**
	 * Gravity Forms: Non Blocking Render
	 *
	 * @return bool
	 */
	function gravityforms_footer_noblockrender() {
		return true;
	}

	/**
	 * Gravity Forms: CDATA Open
	 *
	 * @param string $content
	 *
	 * @return string
	 */
	function gravityforms_wrap_gform_cdata_open( $content = '' ) {
		$content = 'document.addEventListener( "DOMContentLoaded", function() { ';

		return $content;
	}

	/**
	 * Gravity Forms: CDATA Close
	 *
	 * @param string $content
	 *
	 * @return string
	 */
	public function gravityforms_wrap_gform_cdata_close( $content = '' ) {
		$content = ' }, false );';

		return $content;
	}

	/**
	 * Gravity Forms: Dequeue browsers specific CSS
	 */
	public function gravityforms_dequeue_stylesheets() {
		wp_dequeue_style( 'gforms_browsers_css' );
		wp_deregister_style( 'gforms_browsers_css' );
	}

	/**
	 * Gravity Forms: Do not collect IP Address from user
	 *
	 * @return bool
	 */
	public function gravityforms_donotcollect_ipaddress() {
		return '';
	}

	/**
	 * Gravity Forms: Phone formats
	 *
	 * @param $phone_formats
	 *
	 * @return mixed
	 */
	function gravityforms_phone_formats( $phone_formats ) {
		$phone_formats['fr'] = array(
			'label'       => __( 'France', 'tmsm-frontend-optimizations' ),
			'mask'        => false,
			'regex'       => '/^((\+)33|0)[1-9](\d{2}){4}$/',
			'instruction' => __( 'Invalid phone number format', 'tmsm-frontend-optimizations' ),
		);

		return $phone_formats;
	}

	/**
	 * Gravity Forms: Execute Personal Data Requests
	 *
	 * @param array $form The Form object
	 */
	public function gravityforms_personal_data( $form ) {

		$radio_fields = GFAPI::get_fields_by_type( $form, array( 'radio' ), true );

		$personal_data_type  = null;
		$personal_data_email = null;

		// Look for a personal data type field
		foreach ( $radio_fields as $field ) {
			if ( $field['cssClass'] === 'personal_data' ) { // CSS class has to be "personal_data"
				$personal_data_type = RGFormsModel::get_field_value( $field );
			}
		}

		// Found personal data type field
		if ( ! empty( $personal_data_type ) ) {

			if ( in_array( $personal_data_type, array( 'export_personal_data', 'remove_personal_data' ), true ) ) {

				// Look for an email field
				$email_fields = GFAPI::get_fields_by_type( $form, array( 'email' ) );
				foreach ( $email_fields as $field ) {
					$personal_data_email = RGFormsModel::get_field_value( $field );
				}

				// Found personal data email field
				if ( ! empty( $personal_data_email ) ) {
					$personal_data_email = is_array($personal_data_email) ? $personal_data_email[0] : $personal_data_email;

					if(is_email($personal_data_email)){

						// Create personal data request
						$request_id = wp_create_user_request( $personal_data_email, $personal_data_type );
						if ( is_wp_error( $request_id ) ) {
							error_log('request_id #'.$request_id. ': '.$request_id->get_error_message());
						}
						else{
							// Send personal data request confirmation
							wp_send_user_request( $request_id );
						}
					}
				}
			}
		}
	}

	/**
	 * Allow the text to be filtered so custom merge tags can be replaced.
	 *
	 * @param $text
	 * @param $form
	 * @param $entry
	 * @param $url_encode
	 * @param $esc_html
	 * @param $nl2br
	 * @param $format
	 *
	 * @return string
	 */
	public function gravityforms_mergetags( $text, $form, $entry, $url_encode, $esc_html, $nl2br, $format ){

		$current_user = wp_get_current_user();

		$custom_merge_tag_firstname = '{user_firstname}';
		if ( strpos( $text, $custom_merge_tag_firstname ) !== false ) {
			$text = str_replace( $custom_merge_tag_firstname, $this->user_has_role( $current_user, 'customer') ? $current_user->user_firstname : '', $text );
		}

		$custom_merge_tag_lastname = '{user_lastname}';
		if ( strpos( $text, $custom_merge_tag_lastname ) !== false ) {
			$text = str_replace( $custom_merge_tag_lastname, $this->user_has_role( $current_user, 'customer') ? $current_user->user_lastname : '', $text );
		}

		$custom_merge_tag_email = '{user_email}';
		if ( strpos( $text, $custom_merge_tag_email ) !== false ) {
			$text = str_replace( $custom_merge_tag_email, $this->user_has_role( $current_user, 'customer') ? $current_user->user_email : '', $text );
		}

		$custom_merge_tag_billingphone = '{user_billingphone}';
		if ( strpos( $text, $custom_merge_tag_billingphone ) !== false ) {
			$text = str_replace( $custom_merge_tag_billingphone, $this->user_has_role( $current_user, 'customer') ? get_user_meta( $current_user->ID, 'billing_phone', true ) : '', $text );
		}

		return $text;
	}

	/**
	 * Checks if a user has a role.
	 *
	 * @param int|WP_User $user The user.
	 * @param string      $role The role.
	 * @return bool
	 */
	function user_has_role( $user, $role ) {
		if ( ! is_object( $user ) ) {
			$user = get_userdata( $user );
		}

		if ( ! $user || ! $user->exists() ) {
			return false;
		}

		return in_array( $role, $user->roles, true );
	}

	/**
	 * Google Tag Manager: inject tag after body in OceanWP theme
	 */
	public function googletagmanager_after_body(){
		if ( function_exists( 'gtm4wp_the_gtm_tag' ) ) { gtm4wp_the_gtm_tag(); }
	}

	/**
	 * Google Tag Manager: Exclude orders with status failed (only paid statuses)
	 *
	 * @param $tag
	 *
	 * @return string
	 */
	public function googletagmanager_getthetag( $tag ) {
		global $wp;

		if ( function_exists('is_order_received_page') && is_order_received_page() ) {
			if ( ! empty( $wp->query_vars['order-received'] ) ) {

				$order = wc_get_order( absint( $wp->query_vars['order-received'] ) );

				// Check if paid date is in the past 24 hours
				if ( ! empty( $order ) && ! empty( $order->get_date_paid() ) ) {
					$paid_time = $order->get_date_paid()->getTimestamp();
					$time = time();
					$difference = $time - $paid_time;
					if($difference > 24 * 3600){ // order paid for more than 24 hours, remove the tag
						$tag = '<!-- order is 24 hours old -->';
					}
				}

				// Order is not paid, remove the tag
				if ( ! empty( $order ) && ! $order->is_paid() ) {
					$tag = '<!-- order not paid -->';
				}

			}
		}

		return $tag;
	}

	/**
	 * WPSEO Breadcrumb wrapper
	 *
	 * @return string
	 */
	public function wpseo_breadcrumb_output_wrapper() {
		return 'p';
	}

	/**
	 * WPSEO Breadcrumb wrapper class
	 *
	 * @return string
	 */
	public function wpseo_breadcrumb_output_class() {
		return 'breadcrumb';
	}

	/**
	 * WPSEO Breadcrumb next rel link, disable on home
	 *
	 * @return string
	 */
	public function wpseo_disable_rel_next_home( $link ) {
		if ( is_home() ) {
			return false;
		}
		return $link;
	}

	/**
	 * WP Rocket: Lazyload, Exclude attributes from Elementor
	 *
	 * @param $attributes
	 *
	 * @return array
	 */
	function rocket_lazyload_excluded_attributes_elementor( $attributes ) {
		$attributes[] = 'class="slick-slide-image"';

		return $attributes;
	}

	/**
	 * Recipient of the data request confirmation notification
	 *
	 * In a Multisite environment, this will default to the email address of the
	 * network admin because, by default, single site admins do not have the
	 * capabilities required to process requests. Some networks may wish to
	 * delegate those capabilities to a single-site admin, or a dedicated person
	 * responsible for managing privacy requests.
	 *
	 * @since 1.0.6
	 *
	 * @param string          $email        The email address of the notification recipient.
	 * @param WP_User_Request $request_data The request that is initiating the notification.
	 *
	 * @return string
	 */
	function user_request_confirmed_email_to_dpo( $email, $request_data ) {
		$email = 'dpo@thalasso-saintmalo.com';

		return $email;
	}

	/**
	 * WooCommerce customize the "order received" page title when payment failed
	 *
	 * @since 1.2.2
	 * @param string $title
	 *
	 * @return string
	 */
	function woocommerce_endpoint_order_received_title( string $title ){
		global $wp;
		$order_id  = isset( $wp->query_vars['order-received'] ) ? absint( $wp->query_vars['order-received'] ) : 0;
		$order     = wc_get_order( $order_id );

		if ( $order && $order->has_status( 'failed' ) ) {
			return __( 'Payment Failed', 'tmsm-frontend-optimizations' );
		}

		return $title;
	}

	/**
	 * WooCommerce Scheduled Sales: everyday, sales start, cache should be emptied (WP Rocket)
	 *
	 * @since 1.0.9
	 */
	function woocommerce_scheduled_sales_empty_cache(){
		// Clear WP Rocket Cache (whole site)
		if ( function_exists( 'rocket_clean_domain' ) ) {
			rocket_clean_domain();
		}
	}

	/**
	 * Filters the text describing the site's password complexity policy.
	 *
	 * @since 4.1.0
	 *
	 * @param string $hint The password hint text.
	 *
	 * @return string
	 */
	function password_hint( $hint ) {

		$hint = __( 'The password must be at least twelve characters long. Use at least one upper case letter, one lower case letter, one number, and one symbol like ! ? $ % ^ &amp; ).', 'tmsm-frontend-optimizations' );
		return $hint;
	}

	/**
	 * WooCommerce Lower Password Strength: 0 - Anything, 1 - Weakest, 2 - Weak, 3 - Medium (Default), 4 - Strong
	 *
	 * @since 1.1.8
	 *
	 * @return int
	 */
	function woocommerce_min_password_strength() {
		$strength = 2;

		return intval( $strength );
	}

	/**
	 * WooCommerce Gateway Icon for COD
	 *
	 * @since 1.2.3
	 *
	 * @param string $icon The icon html markup
	 *
	 * @return string
	 */
	public function woocommerce_cod_icon_travel( $icon ) {

		$icon = '<img src="' . WC_HTTPS::force_https_url( TMSM_FRONTEND_OPTIMIZATIONS_BASE_URL . '/public/img/cod-payment-icon.png' ) .
		'" alt="' 		. esc_attr( __( 'Cash on Delivery', 'tmsm-frontend-optimizations' ) ) . '" />';

		$icon = WC_HTTPS::force_https_url( TMSM_FRONTEND_OPTIMIZATIONS_BASE_URL . '/public/img/cod-payment-icon.png' ) ;
		return $icon;
	}

	/**
	 * WooCommerce: hide shipping when products marked as "local pickup only" are in the cart
	 *
	 * The shipping class "local-pickup-only" needs to be created first.
	 * Then assign the products that are have "local pickup only" to this class
	 *
	 * @since 1.2.3
	 *
	 * @param array $rates
	 * @param array $package
	 *
	 * @return array
	 */
	public function woocommerce_package_rates_hide_shipping_on_local_pickup_required( $rates, $package )
	{
		$shipping_class_local_pickup_only = 'local_pickup_only';

		$local = [];

		foreach( $package['contents'] as $item )
		{
			$product = $item['data'];
			$shipping_class = $product->get_shipping_class();

			if( $shipping_class == $shipping_class_local_pickup_only )
			{
				foreach( $rates as $rate_id => $rate )
				{
					if( in_array( $rate->method_id, array( 'local_pickup', 'legacy_local_pickup' ) ) )
					{
						//echo '*** il y a le local pickup';
						$local[ $rate_id ] = $rate;
						break;
					}
				}
			}
		}

		return !empty( $local ) ? $local : $rates;
	}


	/**
	 * Display shipping options in product meta
	 *
	 * @since 1.2.5
	 */
	public function woocommerce_product_meta_end_freeshippingpocalpickup(){
		global $product;
		if(!empty($product) ){
			if(!empty(WC()) && !empty(WC()->shipping())){

				$package = array();
				$package['destination']['country'] = get_option( 'woocommerce_default_country' );
				$package['destination']['state'] = '';
				$package['destination']['postcode'] = '';
				$shipping_methods = WC()->shipping()->load_shipping_methods($package);

				foreach($shipping_methods as $shipping_method){

					if($shipping_method->is_enabled() && !empty($shipping_method->get_title())){

						// Free Shipping: displayed for all shipping options except if "Local Pickup Only" has been selected
						if(!empty($product) && $product->get_type() !== 'external' && (empty($product->get_shipping_class_id()) || $product->get_shipping_class() !== 'local_pickup_only')){

							// Free shipping
							if(!empty($shipping_method->id) && $shipping_method->id === 'free_shipping'){

								// Requires min amount
								if($shipping_method->get_option('requires') === 'min_amount' && !empty($shipping_method->get_option('min_amount')) && method_exists($shipping_method, 'get_title')){
									echo '<p class="product_meta_freeshipping">
									<span class="glyphicon glyphicon-gift fa fa-truck"></span> '.sprintf(__('%1$s from %2$s','tmsm-frontend-optimizations'), ( function_exists( 'pll__' )
											? pll__( $shipping_method->get_title() ) : $shipping_method->get_title() ), strip_tags(wc_price($shipping_method->get_option('min_amount'), ['decimals'=> false]))).'</p>';
								}

								// Requires nothing
								if($shipping_method->get_option('requires') === '' && method_exists($shipping_method, 'get_title')){
									echo '<p class="product_meta_freeshipping">
									<span class="glyphicon glyphicon-gift fa fa-truck"></span> '.( function_exists( 'pll__' )
											? pll__( $shipping_method->get_title() ) : $shipping_method->get_title() ).'</p>';
								}

							}

							// Flat rate
							if(!empty($shipping_method->id) && $shipping_method->id === 'flat_rate'){

								$cost = null;
								$settings = $shipping_method->instance_settings;
								if(!empty($settings)){
									$cost = $settings['cost'] ?? 0;
									if(!empty($product->get_shipping_class_id()) && !empty($settings['class_cost_'.$product->get_shipping_class_id()])){
										$cost = $settings['class_cost_'.$product->get_shipping_class_id()];
									}
								}
								echo '<p class="product_meta_flatrateshipping">
									<span class="glyphicon glyphicon-gift fa fa-truck"></span> '.( function_exists( 'pll__' )
										? pll__( $shipping_method->get_title() ) : $shipping_method->get_title() ).' '.($cost !== null ? ($cost == 0 ? __('(free)','tmsm-frontend-optimizations') : sprintf(__('(%s)','tmsm-frontend-optimizations'), strip_tags(wc_price($cost, ['decimals'=> false])))) : '').'</p>';

							}
						}

						// Local Pickup: displayed for all shipping options except if "No Local Pickup" has been selected
						if(!empty($product) && $product->get_type() !== 'external' && (empty($product->get_shipping_class_id()) || $product->get_shipping_class() !== 'no_local_pickup')){
							if(!empty($shipping_method->id) && $shipping_method->id === 'local_pickup'){
								if( method_exists($shipping_method, 'get_title') ){
									echo '<p class="product_meta_localpickup">
									<span class="glyphicon glyphicon-forward fa fa-map-marker"></span> '.( function_exists( 'pll__' )
											? pll__( $shipping_method->get_title() ) : $shipping_method->get_title() ).'</p>';
								}
							}
						}
					}
				}
			}

			// Secure payment
			echo '<p class="product_meta_securepayment"><span class="glyphicon glyphicon-credit-card fa fa-credit-card"></span> '.__('Secure payments','tmsm-frontend-optimizations').'</p>';
		}
	}

	/**
	 * WooCommerce attributes <select> dropdown as <input> type radio
	 *
	 * @param $html
	 * @param $args
	 */
	public function woocommerce_dropdown_variation_attribute_options_html_radio( $html, $args ) {
		$old_html = $html;

		$args = wp_parse_args( apply_filters( 'woocommerce_dropdown_variation_attribute_options_html_radio_args', $args ), array(
			'options'          => false,
			'attribute'        => false,
			'product'          => false,
			'selected' 	       => false,
			'name'             => '',
			'id'               => '',
			'class'            => '',
			'show_option_none' => __( 'Choose an option', 'tmsm-frontend-optimizations' ),
		) );

		$options               = $args['options'];
		$product               = $args['product'];
		$attribute             = $args['attribute'];
		$name                  = $args['name'] ? $args['name'] : sanitize_title( $attribute );
		$sanitized_name = sanitize_title( $name );
		$id                    = $args['id'] ? $args['id'] : sanitize_title( $attribute );
		$class                 = $args['class'];
		$show_option_none      = $args['show_option_none'] ? true : false;
		$show_option_none_text = $args['show_option_none'] ? $args['show_option_none'] : __( 'Choose an option', 'tmsm-frontend-optimizations' ); // We'll do our best to hide the placeholder, but we'll need to show something when resetting options.

		if ( empty( $options ) && ! empty( $product ) && ! empty( $attribute ) ) {
			$selected_attributes = $product->get_default_attributes();
			$attributes = $product->get_variation_attributes();
			$options    = $attributes[ $attribute ];
		}
		else{
			$selected_attributes = [];
		}

		if ( isset( $_REQUEST[ 'attribute_' . $sanitized_name ] ) ) {
			$checked_value = $_REQUEST[ 'attribute_' . $sanitized_name ];
		} elseif ( isset( $selected_attributes[ $sanitized_name ] ) ) {
			$checked_value = $selected_attributes[ $sanitized_name ];
		} else {
			$checked_value = '';
		}

		$html = '';

		if ( ! empty( $options ) ) {
			if ( $product && taxonomy_exists( $attribute ) ) {
				// Get terms if this is a taxonomy - ordered. We need the names too.
				$terms = wc_get_product_terms( $product->get_id(), $attribute, array( 'fields' => 'all' ) );

				foreach ( $terms as $term ) {
					if ( in_array( $term->slug, $options ) ) {

						$value = $term->slug;
						$label = $term->name;
						$sanitized_name = $name;
						$description = $term->description;

						$checked = sanitize_title( $checked_value ) === $checked_value ? checked( $checked_value, sanitize_title( $value ), false ) : checked( $checked_value, $value, false );

						if(!empty($description)){
							$description = ' ('.$description.')';
						}
						$input_name = 'attribute_' . esc_attr( $name ) ;
						$esc_value = esc_attr( $value );
						$id = esc_attr( $name . '_v_' . $value );
						$filtered_label = apply_filters( 'woocommerce_variation_option_name', $label );
						printf( '<div class="radio"><input type="radio" name="%1$s" value="%2$s" id="%3$s" %4$s><label for="%3$s">%5$s%6$s</label></div>', $input_name, $esc_value, $id, $checked, $filtered_label, $description );
					}
				}
			} else {
				foreach ( $options as $option ) {
					// This handles < 2.4.0 bw compatibility where text attributes were not sanitized.
					$selected = sanitize_title( $args['selected'] ) === $args['selected'] ? checked( $args['selected'], sanitize_title( $option ), false ) : checked( $args['selected'], $option, false );
					$input_name = 'attribute_' . esc_attr( $name ) ;
					$esc_value = esc_attr( $option );
					$id = esc_attr( $name . '_v_' . $option . $product->get_id() ); //added product ID at the end of the name to target single products
					$checked = checked( $args['selected'], $option, false );
					$filtered_label = esc_html( apply_filters( 'woocommerce_variation_option_name', $option ) );
					$html .=  sprintf( '<div class="radio"><input type="radio" name="%1$s" value="%2$s" id="%3$s" %4$s><label for="%3$s">%5$s</label></div>', $input_name, $esc_value, $id, $checked, $filtered_label );

				}
			}
		}

		echo $html; // WPCS: XSS ok.
	}

	/**
	 * WooCommerce: Hides local_pickup shipping method if no_local_pickup shipping class is found in cart
	 *
	 * @since 1.2.3
	 *
	 * @param $available_shipping_methods
	 * @param $package
	 *
	 * @return mixed
	 */
	function woocommerce_package_rates_hide_local_pickup( $available_shipping_methods, $package ) {

		$shipping_class_to_exclude = 'no_local_pickup';
		$shipping_method_to_exclude = 'local_pickup';

		$shipping_class_to_exclude_exists = false;
		foreach(WC()->cart->cart_contents as $key => $values) {
			if ($values['data']->get_shipping_class() == $shipping_class_to_exclude) {
				$shipping_class_to_exclude_exists = true;
				break;
			}
		}

		if ($shipping_class_to_exclude_exists) {
			foreach($available_shipping_methods as $rate_id => $rate) {
				if($rate->method_id == $shipping_method_to_exclude){
					unset($available_shipping_methods[$rate_id]);
				}
			}
		}

		return $available_shipping_methods;
	}

	/**
	 * Empty cache when TAO Schedule Update is fired
	 *
	 * @since 1.1.3
	 */
	function tao_publish_post_emptycache(){

		// Clear WP Rocket Cache (whole site)
		if ( function_exists( 'rocket_clean_domain' ) ) {
			rocket_clean_domain();
		}
	}

	/**
	 * Filters the HTML script tag of an enqueued script.
	 *
	 * @since 4.1.0
	 *
	 * @param string $tag    The `<script>` tag for the enqueued script.
	 * @param string $handle The script's registered handle.
	 * @param string $src    The script's source URL.
	 *
	 * @return string
	 */
	function script_loader_tag( $tag, $handle, $src )
	{

		if ( is_admin()){
			return $tag;
		}

		if ( ! class_exists('\Elementor\Plugin') || ( class_exists('\Elementor\Plugin') && (! Plugin::$instance->preview->is_preview_mode() && ! Plugin::$instance->editor->is_edit_mode())) ) {
			//$tag = '<script id="js-'.$handle.'" data-name="'.$handle.'" src="' . esc_url( $src ) . '"></script>';
			$tag = str_replace( '<script type=\'text/javascript\' src', '<script id="js-'.$handle.'" data-name="'.$handle.'" type=\'text/javascript\' src', $tag );
		}

		return $tag;
	}


	/**
	 * Paypal Checkout: Make Billing Address not Required
	 *
	 * @param bool $address_not_required
	 *
	 * @return bool
	 */
	function woocommerce_paypal_checkout_address_not_required($address_not_required){
		$address_not_required = false;
		return $address_not_required;
	}


	/**
	 * WooCommerce Advanced Messages: Get locations.
	 *
	 * Get all the location groups, names containing hook, priority, type and name.
	 * Used (but not only) for the 'location' setting.
	 *
	 * @since 1.1.8
	 *
	 * @return array List of location groups containing location_name + data.
	 */
	function wcam_locations($locations){

		$locations['Product']['woocommerce_single_product_summary_excerpt_ocean'] = array(
			'action_hook' => 'ocean_after_single_product_excerpt',
			'priority'    => 15,
			'name'        => 'After product summary (with Ocean theme)',
		);
		$locations['Product']['woocommerce_single_product_summary_excerpt'] = array(
			'action_hook' => 'woocommerce_single_product_summary',
			'priority'    => 30,
			'name'        => 'After product summary (with standard theme)',
		);

		return $locations;
	}

	/**
	 * Remove YouTube related content and have modestbranding always on
	 *
	 * @param $html
	 * @param $url
	 * @param $attr
	 * @param $post_ID
	 *
	 * @return mixed
	 */
	function oembed_result_modest( $html, $url, $attr, $post_ID ) {
		return str_replace( 'feature=oembed', 'feature=oembed&modestbranding=1&showinfo=0&rel=0', $html );
	}

	/**
	 * Filters the HTML returned by the oEmbed provider.
	 *
	 * @param string|false $data The returned oEmbed HTML (false if unsafe).
	 * @param string       $url  URL of the content to be embedded.
	 * @param array        $args Optional arguments, usually passed from a shortcode.
	 *
	 * @return string
	 */
	function oembed_result_nosnippet( string $data, string $url, array $args ) {

		$data = '<div data-nosnippet="true">' . $data . '</div>';

		return $data;
	}

	/**
	 * Elementor Search Form After Input
	 *
	 * @param mixed $form
	 */
	function elementor_search_form_after_input( $form){

		$settings = $form->get_data( 'settings' );

		// If search form has "woocommerce-search" in CSS class, then search only in WooCommerce products
		if ( isset( $settings['_css_classes'] ) && strpos( 'woocommerce-searchform', $settings['_css_classes'] ) !== false  ) {
			echo '<input type="hidden" name="post_type" value="product" />';
		}

	}
}
