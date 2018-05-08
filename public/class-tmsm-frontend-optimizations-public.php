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
				'visualizer-render'
			);
		} );


		$wp_scripts = wp_scripts();
		$wp_styles = wp_styles();
		new WP_Simple_Asset_Optimizer( $wp_scripts, $wp_styles );
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
	}

	/**
	 * Gravity Forms: Do not collect IP Address from user
	 *
	 * @return bool
	 */
	public function gravityforms_donotcollect_ipaddress() {
		return false;
	}

	/**
	 * OceanWP / Google Tag Manager: inject tag after body
	 */
	public function googletagmanager_after_body(){
		if ( function_exists( 'gtm4wp_the_gtm_tag' ) ) { gtm4wp_the_gtm_tag(); }
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

}
