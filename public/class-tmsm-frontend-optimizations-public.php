<?php

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
class Tmsm_Frontend_Optimizations_Public
{

	/**
	 * The ID of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string $plugin_name The ID of this plugin.
	 */
	private string $plugin_name;

	/**
	 * The version of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string $version The current version of this plugin.
	 */
	private string $version;

	/**
	 * Unauthorized domains
	 *
	 * @access private
	 * @var array $unauthorised_domains
	 */
	private array $unauthorised_domains = array(
		'gmail.fr',
		'gnail.com',
		'glail.com',
		'gamil.com',
		'hotmal.fr',
		'orage.fr',
		'freee.fr',
		'wanado.fr',
		'wandoo.fr'
	);

	/**
	 * Initialize the class and set its properties.
	 *
	 * @param string $plugin_name The name of the plugin.
	 * @param string $version     The version of this plugin.
	 *
	 * @since    1.0.0
	 */
	public function __construct(string $plugin_name, string $version)
	{

		$this->plugin_name = $plugin_name;
		$this->version = $version;
		$shortcode = new Tmsm_Frontend_Optimizations_Shortcode;
		// add_shortcode('rss-with-test', array($this, 'tmsm_example_shortcode'));
		// add_shortcode( 'rss-with-image', array($this, 'custom_wp_widget_rss_output') );
	}

	/**
	 * Register the stylesheets for the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_styles()
	{

		wp_enqueue_style($this->plugin_name, plugin_dir_url(__FILE__) . 'css/tmsm-frontend-optimizations-public.css', array(), $this->version, 'all');
	}

	/**
	 * Register the JavaScript for the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_scripts()
	{

		//        wp_enqueue_script('jquery-mask', plugin_dir_url(__FILE__) . 'js/jquery.mask.min.js', array('jquery'), null, false);
		wp_enqueue_script('jquery-mask', plugin_dir_url(__FILE__) . 'js/jquery.mask.min.js', array('jquery'), null, true);

		//        wp_enqueue_script($this->plugin_name, plugin_dir_url(__FILE__) . 'js/tmsm-frontend-optimizations-public.js', array('jquery'), $this->version, false);
		wp_enqueue_script($this->plugin_name, plugin_dir_url(__FILE__) . 'js/tmsm-frontend-optimizations-public.js', array('jquery'), $this->version, true);

		// Don't load JS if current product type is bundle to prevent the page from not working
		if (function_exists('wc_get_product') && !(wc_get_product() && wc_get_product()->is_type('bundle'))) {
			wp_deregister_script('wc-add-to-cart-variation');
			wp_register_script(
				'wc-add-to-cart-variation',
				plugin_dir_url(__FILE__) . 'js/radioattribute.js',
				array('jquery', 'wp-util'),
				$this->version,
				true
			);
		}
	}

	/**
	 * Checks if a user has a role.
	 *
	 * @param int|WP_User $user The user.
	 * @param string      $role The role.
	 *
	 * @return bool
	 */
	function user_has_role($user, string $role): bool
	{
		if (!is_object($user)) {
			$user = get_userdata($user);
		}

		if (!$user || !$user->exists()) {
			return false;
		}

		return in_array($role, $user->roles, true);
	}

	/**
	 * Staging: noindex,nofollow,noarchive,nosnippet
	 */
	public function wp_staging_noindex()
	{
		if (get_option('blog_public') === '0') {
			echo '<meta name="robots" content="noindex,nofollow,noarchive,nosnippet">', "\n";
		}
	}

	/**
	 * Filters the HTML script tag of an enqueued script.
	 *
	 * @param string $tag    The `<script>` tag for the enqueued script.
	 * @param string $handle The script's registered handle.
	 * @param string $src    The script's source URL.
	 *
	 * @return string
	 * @since 4.1.0
	 *
	 */
	function wp_script_loader_tag(string $tag, string $handle, string $src): string
	{

		if (is_admin()) {
			return $tag;
		}

		if (!class_exists('\Elementor\Plugin') || (class_exists('\Elementor\Plugin') && (Plugin::$instance->preview && ! Plugin::$instance->preview->is_preview_mode() && Plugin::$instance->editor && ! Plugin::$instance->editor->is_edit_mode()))) {
			//$tag = '<script id="js-'.$handle.'" data-name="'.$handle.'" src="' . esc_url( $src ) . '"></script>';
			$tag = str_replace('<script type=\'text/javascript\' src', '<script id="js-' . $handle . '" data-name="' . $handle . '" type=\'text/javascript\' src', $tag);
		}

		return $tag;
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
	 * @param string          $email        The email address of the notification recipient.
	 * @param WP_User_Request $request_data The request that is initiating the notification.
	 *
	 * @return string
	 * @since 1.0.6
	 *
	 */
	function wp_user_request_confirmed_email_to_dpo(string $email, WP_User_Request $request_data): string
	{
		$email = 'dpo@thalasso-saintmalo.com';

		return $email;
	}

	/**
	 * Embed wrap
	 *
	 * @param string|false $cache
	 * @param string       $url
	 * @param array        $attr
	 * @param int|null     $post_ID
	 *
	 * @return string
	 */
	function wp_embed_wrap($cache, string $url, array $attr, int|null $post_ID): string
	{
		return '<div class="embed" data-nosnippet="true">' . $cache . '</div>';
	}

	/**
	 * Remove block styles
	 *
	 * @return void
	 */
	function wp_enqueue_block_assets()
	{
		wp_dequeue_style('wc-blocks-style');
		wp_dequeue_style('wc-blocks-vendors-style');
	}

	/**
	 * Remove YouTube related content and have modestbranding always on
	 *
	 * @param string $html
	 * @param string $url
	 * @param        $attr
	 * @param        $post_ID
	 *
	 * @return string
	 */
	function wp_oembed_result_modest(string $html, string $url, $attr, $post_ID): string
	{
		return str_replace('feature=oembed', 'feature=oembed&modestbranding=1&showinfo=0&rel=0', $html);
	}

	/**
	 * Remove "Category:" in titles
	 *
	 * @param $title
	 *
	 * @return string
	 */
	public function wp_get_the_archive_title_category($title)
	{
		if (is_category()) {
			$title = single_cat_title('', false);
		} elseif (is_tag()) {
			$title = single_tag_title('', false);
		} elseif (is_author()) {
			$title = '<span class="vcard">' . get_the_author() . '</span>';
		}
		return $title;
	}

	/**
	 * Disable default gallery style
	 */
	function wp_use_default_gallery_style(): bool
	{
		return false;
	}

	public function jetpack_remove_share()
	{

		remove_filter('the_content', 'sharing_display', 19);
		remove_filter('the_excerpt', 'sharing_display', 19);

		if (class_exists('Jetpack_Likes')) {
			remove_filter('the_content', array(Jetpack_Likes::init(), 'post_likes'), 30);
		}
	}

	/**
	 * Google Tag Manager for WordPress: Scripts in footer
	 */
	function gtm4wp_scriptsfooter()
	{
		if (!is_admin()) {
			add_filter('gtm4wp_event-outbound', '__return_true');
			add_filter('gtm4wp_event-form-move', '__return_true');
			add_filter('gtm4wp_event-social', '__return_true');
			add_filter('gtm4wp_event-email-clicks', '__return_true');
			add_filter('gtm4wp_event-downloads', '__return_true');
			add_filter('gtm4wp_scroller-enabled', '__return_true');
			add_filter('gtm4wp_integrate-woocommerce-track-classic-ecommerce', '__return_true');
			add_filter('gtm4wp_integrate-woocommerce-track-enhanced-ecommerce', '__return_true');

			if (function_exists('gtm4wp_wp_footer')) {
				remove_action('wp_footer', 'gtm4wp_wp_footer');
				add_action('wp_footer', 'gtm4wp_wp_footer', 999);
			}

			if (function_exists('gtm4wp_woocommerce_wp_footer')) {
				remove_action('wp_footer', 'gtm4wp_woocommerce_wp_footer');
				add_action('wp_footer', 'gtm4wp_woocommerce_wp_footer', 500);
			}
		}
	}

	// Jetpack: Remove Sharing Filters

	/**
	 * Google Tag Manager: inject tag after body in OceanWP theme
	 */
	public function googletagmanager_after_body()
	{
		if (function_exists('gtm4wp_the_gtm_tag')) {
			gtm4wp_the_gtm_tag();
		}
	}

	/**
	 * Google Tag Manager: Exclude orders with status failed (only paid statuses)
	 *
	 * @param $tag
	 *
	 * @return string
	 */
	public function googletagmanager_getthetag($tag)
	{
		global $wp;

		if (function_exists('is_order_received_page') && is_order_received_page()) {
			if (!empty($wp->query_vars['order-received'])) {

				$order = wc_get_order(absint($wp->query_vars['order-received']));

				// Check if paid date is in the past 24 hours
				if (!empty($order) && !empty($order->get_date_paid())) {
					$paid_time = $order->get_date_paid()->getTimestamp();
					$time = time();
					$difference = $time - $paid_time;
					if ($difference > 24 * 3600) { // order paid for more than 24 hours, remove the tag
						$tag = '<!-- order is 24 hours old -->';
					}
				}

				// Order is not paid, remove the tag
				if (!empty($order) && !$order->is_paid()) {
					$tag = '<!-- order not paid -->';
				}
			}
		}

		return $tag;
	}

	/**
	 * Asset Optimizer
	 */
	public function assetoptimizer()
	{

		add_filter('wpsao_move', function () {
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
		});


		$wp_scripts = wp_scripts();
		$wp_styles = wp_styles();
		new WP_Simple_Asset_Optimizer($wp_scripts, $wp_styles);
	}

	/**
	 * Jetpack: remove scripts
	 */
	function jetpack_dequeue_scripts()
	{
		wp_dequeue_script('devicepx');
		wp_dequeue_script('wp_rp_edit_related_posts_js');

		wp_deregister_script('jquery.spin');
		wp_deregister_script('spin');
		// Jetpack Jquery spin if carousel ou infinitescroll
		if (class_exists('Jetpack_Carousel') || class_exists('The_Neverending_Home_Page')) :
			wp_register_script('spin', plugins_url('jetpack/_inc/spin.js', 'jetpack'), false, null, true);
			wp_register_script('jquery.spin', plugins_url('jetpack/_inc/jquery.spin.js', 'jetpack'), array('jquery', 'spin'), null, true);
		endif;

		// Jetpack Tiled Gallery
		wp_dequeue_script('tiled-gallery');
		if (class_exists('Jetpack_Tiled_Gallery') && get_option('tiled_galleries')) :
			wp_enqueue_script('tiled-gallery', plugins_url('jetpack/modules/tiled-gallery/tiled-gallery/tiled-gallery.js', 'jetpack'), array('jquery'), null, true);
		endif;

		// Jetpack Carousel
		wp_dequeue_script('jetpack-carousel');
		if (class_exists('Jetpack_Carousel')) :
			wp_enqueue_script('jetpack-carousel', plugins_url('jetpack/modules/carousel/jetpack-carousel.js', 'jetpack'), array('jquery.spin'), null, true);
		endif;
	}

	/**
	 * Polylang: Body Class Lang
	 *
	 * @param array $classes An array of body classes.
	 *
	 * @return array
	 */
	function polylang_body_class(array $classes): array
	{
		if (function_exists('PLL') && $language = PLL()->model->get_language(get_locale())) {
			$classes[] = 'pll-' . str_replace('_', '-', sanitize_title_with_dashes($language->get_locale('raw')));
			$classes[] = 'lang-' . pll_current_language();
		}
		return $classes;
	}

	/**
	 * Polylang: Language Switcher Function
	 *
	 * @return string
	 */
	function polylang_language_switcher()
	{
		$output = '';
		if (function_exists('pll_the_languages')) {
			$args = [
				'show_flags' => 0,
				'show_names' => 1,
				'echo' => 0,
			];
			$ul_classes = '';
			if (self::has_parent_theme('OceanWP')) {
				$ul_classes = 'dropdown-menu';
			}
			if (self::has_parent_theme('StormBringer')) {
				$ul_classes = 'list-unstyled list-inline';
			}

			$output = '<ul class="language-switcher language-switcher-polylang ' . $ul_classes . '">' . pll_the_languages($args) . '</ul>';
		}

		return $output;
	}

	/**
	 * Theme has parent theme
	 *
	 * @param $theme_name
	 *
	 * @return bool
	 */
	function has_parent_theme($theme_name)
	{
		$current_theme = wp_get_theme();
		$parent_theme = $current_theme->parent();

		return !empty($parent_theme) && !empty($parent_theme->name) && $parent_theme->name === $theme_name;
	}

	/**
	 * Polylang: Config As Javascript
	 *
	 */
	function polylang_configjavascript()
	{
		wp_localize_script('jquery', 'polylang_params', array(
			'current_language' => pll_current_language(),
			'home_url' => pll_home_url(),
			'the_languages' => pll_the_languages(['raw' => 1]),
		));
	}

	/**
	 * Gravity Forms: customize form tag
	 *
	 * @param $form_tag
	 * @param $form
	 *
	 * @return mixed
	 */
	public function gravityforms_form_tag($form_tag, $form)
	{

		if (! empty($form['action']) && wp_http_validate_url($form['action'])) {
			$form_tag = preg_replace("|action='(.*?)'|", "action='" . esc_url($form['action']) . "' ", $form_tag);
			$form_tag = preg_replace("|method='(.*?)'|", "method='get' ", $form_tag);
		}

		return $form_tag;
	}

	/**
	 * Gravity Forms: form settings fields
	 */
	public function gravityforms_form_settings_fields(array $fields, array $form): array
	{

		$fields['form_options']['fields'][] = array(
			'type'    => 'text',
			'name'    => 'action',
			'label'   => __('Action', 'tmsm-frontend-optimizations'),
			'tooltip' => __(
				'Replaces the form action attribute, preventing confirmations and notifications to be processed.',
				'tmsm-frontend-optimizations'
			),
		);

		return $fields;
	}

	/**
	 * Gravity Forms: Add phone format to phone field type.
	 *
	 * @param string $content The field content.
	 * @param $field
	 * @param $value
	 * @param int    $lead_id The entry ID.
	 * @param int    $form_id The form ID.
	 *
	 * @return string
	 */
	public function gravityforms_field_content_name(string $content, $field, $value, $lead_id, int $form_id)
	{

		$form = GFAPI::get_form($form_id);

		// Replace input name with inputName var only if the form has a different action
		if (! empty($field['inputName']) && ! empty($form['action']) && wp_http_validate_url($form['action'])) {

			$content = preg_replace("|name='(.*?)'|", "name='" . esc_attr($field['inputName']) . "' ", $content);
		}

		return $content;
	}

	/**
	 * Gravity Forms: Non Blocking Render
	 *
	 * @return bool
	 */
	function gravityforms_footer_noblockrender()
	{
		return true;
	}

	/**
	 * Gravity Forms: CDATA Open
	 *
	 * @param string $content
	 *
	 * @return string
	 */
	function gravityforms_wrap_gform_cdata_open(string $content = ''): string
	{
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
	public function gravityforms_wrap_gform_cdata_close($content = '')
	{
		$content = ' }, false );';

		return $content;
	}

	/**
	 * Gravity Forms: Dequeue browsers specific CSS
	 */
	public function gravityforms_dequeue_stylesheets()
	{
		wp_dequeue_style('gforms_browsers_css');
		wp_deregister_style('gforms_browsers_css');
	}

	/**
	 * Gravity Forms: Do not collect IP Address from user
	 *
	 * @return bool
	 */
	public function gravityforms_donotcollect_ipaddress()
	{
		return '';
	}

	/**
	 * Gravity Forms: Phone formats
	 *
	 * @param $phone_formats
	 *
	 * @return mixed
	 */
	function gravityforms_phone_formats($phone_formats)
	{
		$phone_formats['fr'] = array(
			'label' => __('France', 'tmsm-frontend-optimizations'),
			'mask' => false,
			'regex' => '/^((\+)33|0)[1-9](\d{2}){4}$/',
			'instruction' => __('Invalid phone number format', 'tmsm-frontend-optimizations'),
		);

		$phone_formats['internationalvalidation'] = array(
			'label' => __('International with Validation', 'tmsm-frontend-optimizations'),
			'mask' => false,
			'regex' => '/^((\+)?)[0-9]{6,14}$/',
			'instruction' => __('Invalid phone number format', 'tmsm-frontend-optimizations'),
		);

		return $phone_formats;
	}

	/**
	 * Gravity Forms: Execute Personal Data Requests
	 *
	 * @param array $form The Form object
	 */
	public function gravityforms_personal_data($form)
	{

		$radio_fields = GFAPI::get_fields_by_type($form, array('radio'), true);

		$personal_data_type = null;
		$personal_data_email = null;

		// Look for a personal data type field
		foreach ($radio_fields as $field) {
			if ($field['cssClass'] === 'personal_data') { // CSS class has to be "personal_data"
				$personal_data_type = RGFormsModel::get_field_value($field);
			}
		}

		// Found personal data type field
		if (!empty($personal_data_type)) {

			if (in_array($personal_data_type, array('export_personal_data', 'remove_personal_data'), true)) {

				// Look for an email field
				$email_fields = GFAPI::get_fields_by_type($form, array('email'));
				foreach ($email_fields as $field) {
					$personal_data_email = RGFormsModel::get_field_value($field);
				}

				// Found personal data email field
				if (!empty($personal_data_email)) {
					$personal_data_email = is_array($personal_data_email) ? $personal_data_email[0] : $personal_data_email;

					if (is_email($personal_data_email)) {

						// Create personal data request
						$request_id = wp_create_user_request($personal_data_email, $personal_data_type);
						if (is_wp_error($request_id)) {
							error_log('request_id #' . $request_id . ': ' . $request_id->get_error_message());
						} else {
							// Send personal data request confirmation
							wp_send_user_request($request_id);
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
	public function gravityforms_mergetags($text, $form, $entry, $url_encode, $esc_html, $nl2br, $format): string
	{

		$current_user = wp_get_current_user();

		$custom_merge_tag_firstname = '{user_firstname}';
		if (strpos($text, $custom_merge_tag_firstname) !== false) {
			$text = str_replace($custom_merge_tag_firstname, $this->user_has_role($current_user, 'customer') ? $current_user->user_firstname : '', $text);
		}

		$custom_merge_tag_lastname = '{user_lastname}';
		if (strpos($text, $custom_merge_tag_lastname) !== false) {
			$text = str_replace($custom_merge_tag_lastname, $this->user_has_role($current_user, 'customer') ? $current_user->user_lastname : '', $text);
		}

		$custom_merge_tag_email = '{user_email}';
		if (strpos($text, $custom_merge_tag_email) !== false) {
			$text = str_replace($custom_merge_tag_email, $this->user_has_role($current_user, 'customer') ? $current_user->user_email : '', $text);
		}

		$custom_merge_tag_billingphone = '{user_billingphone}';
		if (strpos($text, $custom_merge_tag_billingphone) !== false) {
			$text = str_replace($custom_merge_tag_billingphone, $this->user_has_role($current_user, 'customer') ? get_user_meta($current_user->ID, 'billing_phone', true) : '', $text);
		}

		return $text;
	}



	/**
	 * Gravity Forms: Add autocomplete off to number field type.
	 *
	 * @param string $content The field content.
	 * @param array $field The Field Object.
	 * @param string $value The field value.
	 * @param int $lead_id The entry ID.
	 * @param int $form_id The form ID.
	 */
	public function gravityforms_field_content_autocompleteoff($content, $field, $value, $lead_id, $form_id)
	{

		if ($field['type'] == 'number') {
			$content = str_replace('<input', '<input autocomplete="off"', $content);
		}

		return $content;
	}

	/**
	 * Gravity Forms: Add phone format to phone field type.
	 *
	 * @param string $content The field content.
	 * @param array $field The Field Object.
	 * @param string $value The field value.
	 * @param int $lead_id The entry ID.
	 * @param int $form_id The form ID.
	 */
	public function gravityforms_field_content_phoneformat($content, $field, $value, $lead_id, $form_id)
	{

		if ($field['type'] == 'phone') {
			$content = str_replace('<input', '<input data-phoneformat="' . ($field['phoneFormat'] ?? '') . '"', $content);
		}

		return $content;
	}

	/**
	 * Gravity Forms: address field, change order of city and postal code fields.
	 *
	 * @param string           $format
	 * @param GF_Field_Address $field
	 *
	 * @return string
	 */
	public function gravityforms_address_zipbeforecity(string $format, GF_Field_Address $field): string
	{
		return 'zip_before_city';
	}

	/**
	 * Gravity Forms: Prevent users from submitting an email domain from the "not authorized domains" list.
	 *
	 * @param array        $result
	 * @param string|array $value
	 * @param mixed        $form  (contains all properties of a particular form)
	 * @param mixed        $field (The Field object contains all settings for a particular field)
	 *
	 * @return array $result
	 */
	function gravityforms_check_email_domain(array $result, $value, $form, $field)
	{
		if ($field->get_input_type() === 'email' && $result['is_valid'] && ! empty($value)) {
			$email             = (is_array($value) ? $value[0] : $value);
			$email_parts       = explode('@', $email);
			$email_domain = $email_parts[1];
			if (in_array($email_domain, $this->unauthorised_domains)) {
				$result['is_valid'] = false;
				$result['message']  = __('The domain of the email is invalid', 'tmsm-frontend-optimizations');
			}
		}

		return $result;
	}

	/**
	 * WPSEO Breadcrumb wrapper
	 *
	 * @return string
	 */
	public function wpseo_breadcrumb_output_wrapper()
	{
		return 'p';
	}

	/**
	 * WPSEO Breadcrumb wrapper class
	 *
	 * @return string
	 */
	public function wpseo_breadcrumb_output_class()
	{
		return 'breadcrumb';
	}

	/**
	 * WPSEO Breadcrumb next rel link, disable on home
	 *
	 * @return string
	 */
	public function wpseo_disable_rel_next_home($link)
	{
		if (is_home()) {
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
	function rocket_lazyload_excluded_attributes_elementor($attributes)
	{
		$attributes[] = 'class="slick-slide-image"';

		return $attributes;
	}

	/**
	 * WooCommerce: prevent users from registering with an email domain from the "not authorized" list.
	 *
	 * @param WP_Error $errors
	 * @param string   $username
	 * @param string   $email
	 *
	 * @return WP_Error
	 */
	function woocommerce_check_email_domain(WP_Error $errors, string $username, string $email): WP_Error
	{

		if (! empty($email)) {
			$email        = (is_array($email) ? $email[0] : $email);
			$email_parts  = explode('@', $email);
			$email_domain = $email_parts[1];
			if (in_array(
				strtolower($email_domain),
				$this->unauthorised_domains
			)) {
				$errors->add('billing_email', __('<strong>Error</strong>: The domain of the email is invalid', 'tmsm-frontend-optimizations'));
			}
		}

		return $errors;
	}

	/**
	 * WooCommerce: customize the "order received" page title when payment failed
	 *
	 * @param string $title
	 *
	 * @return string
	 * @since 1.2.2
	 */
	function woocommerce_endpoint_order_received_title(string $title)
	{
		global $wp;
		$order_id = isset($wp->query_vars['order-received']) ? absint($wp->query_vars['order-received']) : 0;
		$order    = wc_get_order($order_id);

		if ($order && $order->has_status('failed')) {
			return __('Payment Failed', 'tmsm-frontend-optimizations');
		}

		return $title;
	}

	/**
	 * WooCommerce Scheduled Sales: everyday, sales start, cache should be emptied (WP Rocket)
	 *
	 * @since 1.0.9
	 */
	function woocommerce_scheduled_sales_empty_cache()
	{
		// Clear WP Rocket Cache (whole site)
		if (function_exists('rocket_clean_domain')) {
			rocket_clean_domain();
		}

		// Reset woocommerce_scheduled_sales cron event in case it was set to 23PM
		$ve = get_option('gmt_offset') > 0 ? '-' : '+';
		wp_schedule_event(strtotime('00:00 tomorrow ' . $ve . absint(get_option('gmt_offset')) . ' HOURS'), 'daily', 'woocommerce_scheduled_sales');
	}

	/**
	 * Filters the text describing the site's password complexity policy.
	 *
	 * @param string $hint The password hint text.
	 *
	 * @return string
	 * @since 4.1.0
	 *
	 */
	function woocommerce_password_hint($hint)
	{

		$hint = __('The password must be at least twelve characters long. Use at least one upper case letter, one lower case letter, one number, and one symbol like ! ? $ % ^ &amp; ).', 'tmsm-frontend-optimizations');
		return $hint;
	}

	/**
	 * WooCommerce Lower Password Strength: 0 - Anything, 1 - Weakest, 2 - Weak, 3 - Medium (Default), 4 - Strong
	 *
	 * @return int
	 * @since 1.1.8
	 *
	 */
	function woocommerce_min_password_strength()
	{
		$strength = 2;

		return intval($strength);
	}

	/**
	 * WooCommerce Gateway Icon for COD
	 *
	 * @param string $icon The icon html markup
	 *
	 * @return string
	 * @since 1.2.3
	 *
	 */
	public function woocommerce_cod_icon_travel($icon)
	{

		$icon = '<img src="' . WC_HTTPS::force_https_url(TMSM_FRONTEND_OPTIMIZATIONS_BASE_URL . '/public/img/cod-payment-icon.png') .
			'" alt="' . esc_attr(__('Cash on Delivery', 'tmsm-frontend-optimizations')) . '" />';

		$icon = WC_HTTPS::force_https_url(TMSM_FRONTEND_OPTIMIZATIONS_BASE_URL . '/public/img/cod-payment-icon.png');

		return $icon;
	}

	/**
	 * WooCommerce: hide shipping when products marked as "local pickup only" are in the cart
	 *
	 * The shipping class "local-pickup-only" needs to be created first.
	 * Then assign the products that are have "local pickup only" to this class
	 *
	 * @param array $rates
	 * @param array $package
	 *
	 * @return array
	 * @since 1.2.3
	 *
	 */
	public function woocommerce_package_rates_hide_shipping_on_local_pickup_required($rates, $package)
	{
		$shipping_class_local_pickup_only = 'local_pickup_only';

		$local = [];

		foreach ($package['contents'] as $item) {
			$product        = $item['data'];
			$shipping_class = $product->get_shipping_class();

			if ($shipping_class == $shipping_class_local_pickup_only) {
				foreach ($rates as $rate_id => $rate) {
					if (in_array($rate->method_id, array('local_pickup', 'legacy_local_pickup'))) {
						//echo '*** il y a le local pickup';
						$local[$rate_id] = $rate;
						break;
					}
				}
			}
		}

		return ! empty($local) ? $local : $rates;
	}

	/**
	 * Display shipping options in product meta
	 *
	 * @since 1.2.5
	 */
	public function woocommerce_product_meta_end_freeshippingpocalpickup()
	{
		global $product;

		$gift_icon = null;
		$shipping_icon = null;
		$payment_icon = null;
		$localpickup_icon = null;

		if (self::has_parent_theme('StormBringer')) {
			$gift_icon = 'glyphicon glyphicon-gift';
			$shipping_icon = '';
			$payment_icon = 'glyphicon glyphicon-credit-card';
			$localpickup_icon = 'glyphicon glyphicon-map-marker';
		}
		if (self::has_parent_theme('OceanWP') || wp_style_is('font-awesome', 'registered')) {
			$gift_icon = 'fa fa-gift';
			$shipping_icon = 'fa fa-truck';
			$payment_icon = 'fa fa-credit-card';
			$localpickup_icon = 'fa fa-map-marker';
		}


		// Check if product or variation has a physical option
		$has_physical_option = false;
		if (!empty($product)) {
			if ($product->get_type() === 'simple') {
				$has_physical_option = ! $product->is_virtual();
			}
			if ($product->get_type() === 'variable') {
				foreach ($product->get_available_variations('objects') as $variation) {
					//$variation_id     = $available_variations[ $i ]['variation_id'];
					//$variable_product = new WC_Product_Variation( $variation_id );
					if (! $variation->is_virtual()) {
						$has_physical_option = true;
					}
				}
			}
		}

		if (!empty($product)) {
			if (!empty(WC()) && !empty(WC()->shipping()) && $has_physical_option) {

				$package = array();
				$package['destination']['country'] = get_option('woocommerce_default_country');
				$package['destination']['state'] = '';
				$package['destination']['postcode'] = '';
				$shipping_methods = WC()->shipping()->load_shipping_methods($package);

				foreach ($shipping_methods as $shipping_method) {

					if ($shipping_method->is_enabled() && !empty($shipping_method->get_title())) {

						// Free Shipping: displayed for all shipping options except if "Local Pickup Only" has been selected
						if (!empty($product) && $product->get_type() !== 'external' && (empty($product->get_shipping_class_id()) || $product->get_shipping_class() !== 'local_pickup_only')) {

							// Free shipping
							if (!empty($shipping_method->id) && $shipping_method->id === 'free_shipping') {

								// Requires min amount
								if ($shipping_method->get_option('requires') === 'min_amount' && !empty($shipping_method->get_option('min_amount')) && method_exists($shipping_method, 'get_title')) {
									echo '<p class="product_meta_freeshipping">
									<span class="' . $shipping_icon . '"></span> ' . sprintf(__('%1$s from %2$s', 'tmsm-frontend-optimizations'), (function_exists('pll__')
										? pll__($shipping_method->get_title()) : $shipping_method->get_title()), strip_tags(wc_price($shipping_method->get_option('min_amount'), ['decimals' => false]))) . '</p>';
								}

								// Requires nothing
								if ($shipping_method->get_option('requires') === '' && method_exists($shipping_method, 'get_title')) {
									echo '<p class="product_meta_freeshipping">
									<span class="' . $shipping_icon . '"></span> ' . (function_exists('pll__')
										? pll__($shipping_method->get_title()) : $shipping_method->get_title()) . '</p>';
								}
							}

							// Flat rate
							if (!empty($shipping_method->id) && $shipping_method->id === 'flat_rate') {

								$cost = null;
								$settings = $shipping_method->instance_settings;
								if (!empty($settings)) {
									$cost = $settings['cost'] ?? 0;
									if (!empty($product->get_shipping_class_id()) && isset($settings['class_cost_' . $product->get_shipping_class_id()])) {
										$cost = $settings['class_cost_' . $product->get_shipping_class_id()];
									}
								}
								if ($cost !== '' && get_site_url()  != "https://pro.thermes-marins.com" ) {
									echo '<p class="product_meta_flatrateshipping">
									<span class="' . $shipping_icon . '"></span> ' . (function_exists('pll__')
										? pll__($shipping_method->get_title()) : $shipping_method->get_title()) . ' ' . ($cost !== null ? ($cost == 0 ? __('(free)', 'tmsm-frontend-optimizations') : sprintf(__('(%s)', 'tmsm-frontend-optimizations'), strip_tags(wc_price($cost, ['decimals' => false])))) : '') . '</p>';
								}
							}
						}

						// Local Pickup: displayed for all shipping options except if "No Local Pickup" has been selected
						if (!empty($product) && $product->get_type() !== 'external' && (empty($product->get_shipping_class_id()) || $product->get_shipping_class() !== 'no_local_pickup')) {
							if (!empty($shipping_method->id) && $shipping_method->id === 'local_pickup') {
								if (method_exists($shipping_method, 'get_title')) {
									// Just show aquatonic pickup for aquatonic tickets and products
									if ($product->get_shipping_class() === 'aquatonic_pickup' && $shipping_method->get_instance_id() === 8) {
										echo '<p class="product_meta_localpickup">
									<span class="' . $localpickup_icon . '"></span> ' . (function_exists('pll__')
											? pll__($shipping_method->get_title()) : $shipping_method->get_title()) . '</p>';
									} else if ($product->get_shipping_class() != 'aquatonic_pickup' && $shipping_method->get_instance_id() === 2) {
										echo '<p class="product_meta_localpickup">
									<span class="' . $localpickup_icon . '"></span> ' . (function_exists('pll__')
											? pll__($shipping_method->get_title()) : $shipping_method->get_title()) . '</p>';
									}
								}
							}
						}
					}
				}
			}

			// Secure payment
			echo '<p class="product_meta_securepayment"><span class="' . $payment_icon . '"></span> ' . __('Secure payments', 'tmsm-frontend-optimizations') . '</p>';
		}
	}

	/**
	 * WooCommerce attributes <select> dropdown as <input> type radio
	 *
	 * @param $html
	 * @param $args
	 */
	public function woocommerce_dropdown_variation_attribute_options_html_radio($html, $args)
	{
		$old_html = $html;

		// je récupère l'url du site pour la comparaison plus tard.
		// $url = get_site_url(); 

		$args = wp_parse_args(apply_filters('woocommerce_dropdown_variation_attribute_options_html_radio_args', $args), array(
			'options'          => false,
			'attribute'        => false,
			'product'          => false,
			'selected'         => false,
			'name'             => '',
			'id'               => '',
			'class'            => '',
			'show_option_none' => __('Choose an option', 'tmsm-frontend-optimizations'),
		));

		$options               = $args['options'];
		$product               = $args['product'];
		$attribute             = $args['attribute'];
		$name                  = $args['name'] ? $args['name'] : sanitize_title($attribute);
		$sanitized_name        = sanitize_title($name);
		$id                    = $args['id'] ? $args['id'] : sanitize_title($attribute);
		$class                 = $args['class'];
		$show_option_none      = $args['show_option_none'] ? true : false;
		$show_option_none_text = $args['show_option_none']
			? $args['show_option_none']
			: __(
				'Choose an option',
				'tmsm-frontend-optimizations'
			); // We'll do our best to hide the placeholder, but we'll need to show something when resetting options.

		if (empty($options) && ! empty($product) && ! empty($attribute)) {
			$attributes          = $product->get_variation_attributes();
			$options             = $attributes[$attribute];
		} else {
			$selected_attributes = [];
		}
		$selected_attributes = $product->get_default_attributes();

		if (isset($_REQUEST['attribute_' . $sanitized_name])) {
			$checked_value = $_REQUEST['attribute_' . $sanitized_name];
		} elseif (isset($selected_attributes[$sanitized_name])) {
			$checked_value = $selected_attributes[$sanitized_name];
		} else {
			$checked_value = '';
		}

		$html = '';

		if (! empty($options)) {
			if ($product && taxonomy_exists($attribute)) {
				// Get terms if this is a taxonomy - ordered. We need the names too.
				$terms = wc_get_product_terms($product->get_id(), $attribute, array('fields' => 'all'));

				foreach ($terms as $term) {
					if (in_array($term->slug, $options)) {

						$value          = $term->slug;
						$label          = $term->name;
						$sanitized_name = $name;
						$description    = $term->description;

						$checked = sanitize_title($checked_value) === $checked_value ? checked($checked_value, sanitize_title($value), false)
							: checked($checked_value, $value, false);

						if (! empty($description)) {
							$description = ' (' . $description . ')';
						}
						$input_name     = 'attribute_' . esc_attr($name);
						$esc_value      = esc_attr($value);
						$id             = esc_attr($name . '_v_' . $value);
						$filtered_label = apply_filters('woocommerce_variation_option_name', $label);

						// les lignes à ajouter !! Remplacer l'url de comparaison par l'url de l'Aquatonic Rennes
						// if ($url == 'https://www.aquatonic.fr/rennes' || $url == 'https://stg-aquatonic-staging.kinsta.cloud/rennes') {
						// 	if($term->slug != 'bon-cadeau') { 
						// printf( '<div class="radio"><input type="radio" name="%1$s" value="%2$s" id="%3$s" %4$s><label for="%3$s">%5$s%6$s</label></div>',
						// 	$input_name, $esc_value, $id, $checked, $filtered_label, $description );
						// }
						// } else {
						// 	printf( '<div class="radio"><input type="radio" name="%1$s" value="%2$s" id="%3$s" %4$s><label for="%3$s">%5$s%6$s</label></div>',
						// 	$input_name, $esc_value, $id, $checked, $filtered_label, $description );
						// }

						printf(
							'<div class="radio"><input type="radio" name="%1$s" value="%2$s" id="%3$s" %4$s><label for="%3$s">%5$s%6$s</label></div>',
							$input_name,
							$esc_value,
							$id,
							$checked,
							$filtered_label,
							$description
						);
					}
				}
			} else {
				foreach ($options as $option) {
					// This handles < 2.4.0 bw compatibility where text attributes were not sanitized.
					$selected       = sanitize_title($args['selected']) === $args['selected'] ? checked(
						$args['selected'],
						sanitize_title($option),
						false
					) : checked($args['selected'], $option, false);
					$input_name     = 'attribute_' . esc_attr($name);
					$esc_value      = esc_attr($option);
					$id             = esc_attr($name . '_v_' . $option
						. $product->get_id()); //added product ID at the end of the name to target single products
					$checked        = checked($args['selected'], $option, false);
					$filtered_label = esc_html(apply_filters('woocommerce_variation_option_name', $option));
					$html           .= sprintf(
						'<div class="radio"><input type="radio" name="%1$s" value="%2$s" id="%3$s" %4$s><label for="%3$s">%5$s</label></div>',
						$input_name,
						$esc_value,
						$id,
						$checked,
						$filtered_label
					);
				}
			}
		}

		echo $html; // WPCS: XSS ok.
	}

	/**
	 * WooCommerce: Hides local_pickup shipping method if no_local_pickup shipping class is found in cart
	 *
	 * @param $available_shipping_methods
	 * @param $package
	 *
	 * @return mixed
	 * @since 1.2.3
	 *
	 */
	function woocommerce_package_rates_hide_local_pickup($available_shipping_methods, $package)
	{

		$shipping_class_to_exclude  = 'no_local_pickup';
		$shipping_method_to_exclude = 'local_pickup';

		$shipping_class_to_exclude_exists = false;
		foreach (WC()->cart->cart_contents as $key => $values) {
			if ($values['data']->get_shipping_class() == $shipping_class_to_exclude) {
				$shipping_class_to_exclude_exists = true;
				break;
			}
		}

		if ($shipping_class_to_exclude_exists) {
			foreach ($available_shipping_methods as $rate_id => $rate) {
				if ($rate->method_id == $shipping_method_to_exclude) {
					unset($available_shipping_methods[$rate_id]);
				}
			}
		}

		return $available_shipping_methods;
	}

	/**
	 * Paypal Checkout: Make Billing Address not Required
	 *
	 * @param bool $address_not_required
	 *
	 * @return bool
	 */
	function woocommerce_paypal_checkout_address_not_required($address_not_required)
	{
		$address_not_required = false;
		return $address_not_required;
	}
	/**
	 * Trim zeros in price decimals
	 *
	 * @return void
	 */
	function woocommerce_trim_zero()
	{
		add_filter('woocommerce_price_trim_zeros', '__return_true');
	}
	/**
	 * Empty cache when TAO Schedule Update is fired
	 *
	 * @since 1.1.3
	 */
	function tao_publish_post_emptycache()
	{

		// Clear WP Rocket Cache (whole site)
		if (function_exists('rocket_clean_domain')) {
			rocket_clean_domain();
		}
	}

	/**
	 * WooCommerce Advanced Messages: Get locations.
	 *
	 * Get all the location groups, names containing hook, priority, type and name.
	 * Used (but not only) for the 'location' setting.
	 *
	 * @return array List of location groups containing location_name + data.
	 * @since 1.1.8
	 *
	 */
	function wcam_locations($locations)
	{

		$locations['Product']['woocommerce_single_product_summary_excerpt_ocean'] = array(
			'action_hook' => 'ocean_after_single_product_excerpt',
			'priority'    => 15,
			'name'        => 'After product summary (with Ocean theme)',
		);
		$locations['Product']['woocommerce_single_product_summary_excerpt']       = array(
			'action_hook' => 'woocommerce_single_product_summary',
			'priority'    => 30,
			'name'        => 'After product summary (with standard theme)',
		);

		return $locations;
	}

	/**
	 * Display attribute name and value in WooCommerce
	 *
	 * Show the attribute name beside the attribute value in WooCommerce (in Cart, Checkout and order emails).
	 *
	 * @param bool       $should_include_attributes
	 * @param WC_Product $product Product object.
	 *
	 * @return false
	 *
	 */
	function woocommerce_product_variation_title_include_attributes(bool $should_include_attributes, WC_Product $product)
	{
		// Returning false messes up My Account/Downloads page - thanks for Leandro for reporting.
		if (is_account_page()) {
			return $should_include_attributes;
		}

		return false;
	}


	/**
	 * Elementor Search Form After Input
	 *
	 * @param mixed $form
	 */
	function elementor_search_form_after_input($form)
	{

		$settings = $form->get_data('settings');

		// If search form has "woocommerce-search" in CSS class, then search only in WooCommerce products
		if (isset($settings['_css_classes']) && strpos('woocommerce-searchform', $settings['_css_classes']) !== false) {
			echo '<input type="hidden" name="post_type" value="product" />';
		}
	}

	/**
	 * Post-Expirator: clear WP-Rocket cache after expiration
	 */
	function postexpirator_expireclearcache()
	{
		// Clear the cache.
		if (function_exists('rocket_clean_domain')) {
			rocket_clean_domain();
		}
	}
	// add_action('login_init', 'no_weak_password_header');
	// add_action('admin_head', 'no_weak_password_header');
	function no_weak_password_header()
	{
		echo "<style>.pw-weak{display:none!important}</style>";
		echo '<script>document.getElementById(\'pw-weak\').disabled=true;</script>';
	}
	// add_action('validate_password_reset', 'esp_validate_password_reset', 10, 2);
	/**
	 * Sanitise the input parameters and then check the password strength.
	 */
	function esp_validate_password_reset($errors, $user_data)
	{
		$is_password_ok = false;
		$user_name = null;
		if (isset($_POST['user_login'])) {
			$user_name = sanitize_text_field($_POST['user_login']);
		} elseif (isset($user_data->user_login)) {
			echo print_r($user_data->roles, true);
			$user_name = $user_data->user_login;
		} else {
			// No user specified.
		}
		$roles = $user_data->roles;
		$need_strong_password = false;
		foreach ($roles as $role) :
			$need_strong_password = match ($role) {
				'administrator' => true,
				'author' => true,
				'editor' => true,
				'shop_manager' => true,
				'shop_order_manager' => true,
				'golf_weather' => true,
				'golf_manager' => true,
				'golf_association' => true,
				'wpseo_editor' => true,
				'wpseo_manager' => true,
				'super' => true,
				'backwpup_helper' => true,
				'backwpup_check' => true,
				'backwpup_admin' => true,
				'wholesale_tax_free' => true,
				'wholesale_buyer' => true,
				'matomo_superuser_role' => true,
				'matomo_admin_role' => true,
				'matomo_write_role' => true,
				'matomo_view_role' => true,
				default => false,
			};
			if ($need_strong_password === true) {
				echo $role;
				break;
			}
		endforeach;
		echo $need_strong_password;
		$password = null;
		if ($need_strong_password) :
			if (isset($_POST['pass1']) && !empty(trim($_POST['pass1']))) {
				$password = sanitize_text_field(trim($_POST['pass1']));
			}
			$error_message = null;
			if (is_null($password)) {
				// Don't do anything if there isn't a password to check.
			} elseif (is_wp_error($errors) && $errors->get_error_data('pass')) {
				// We've already got a password-related error.
			} elseif (empty($user_name)) {
				$error_message = __('User name cannot be empty.');
			} elseif (!($is_password_ok = self::esp_is_password_ok($password, $user_name))) {
				$error_message = __('Password is not strong enough.');
			} else {
				// Password is strong enough. All OK.
			}
		endif;
		if (!empty($error_message)) {
			$error_message = '<strong>ERROR</strong>: ' . $error_message;
			if (!is_a($errors, 'WP_Error')) {
				$errors = new WP_Error('pass', $error_message);
			} else {
				$errors->add('pass', $error_message);
			}
		}
		return $errors;
	}
	/**
	 * Given a password, return true if it's OK, otherwise return false.
	 */
	private function esp_is_password_ok($password, $user_name)
	{
		// Default to the password not being valid - fail safe.
		$is_ok = false;
		$password = sanitize_text_field($password);
		$user_name = sanitize_text_field($user_name);
		$is_number_found = preg_match('/[0-9]/', $password);
		$is_lowercase_found = preg_match('/[a-z]/', $password);
		$is_uppercase_found = preg_match('/[A-Z]/', $password);
		$is_symbol_found = preg_match('/[^a-zA-Z0-9]/', $password);
		if (strlen($password) < 12) {
			// Too short
		} elseif (strtolower($user_name) == strtolower($password)) {
			// User name and password can't be the same.
		} elseif (!$is_number_found) {
			// ...
		} elseif (!$is_lowercase_found) {
			// ...
		} elseif (!$is_uppercase_found) {
			// ...
		} elseif (!$is_symbol_found) {
			// ...
		} else {
			// Password is OK.
			$is_ok = true;
		}
		return $is_ok;
	}

}
