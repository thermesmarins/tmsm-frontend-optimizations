TMSM Frontend Optimizations
=================

Frontend Optimizations for Thermes Marins de Saint-Malo

Features
-----------

* Gravity Forms:
    * Action setting
    * Action depending on radio input
    * Phone format for France
    * Phone format for international numbers
    * New merge tags: {user_firstname}, {user_lastname}, {user_email} and {user_billingphone}
    * Autocomplete off for numbers
    * Block unauthorized domains for email fields
* Google Tag Manager for WordPress:
    * Inject Google Tag Manager after body in OceanWP Theme
    * Exclude orders with status failed (only paid statuses)
    * Exclude orders of tracking if they are 1 day old
    * Javascripts in the footer
* WooCommerce: everyday, sales start, cache should be emptied (WP Rocket), dynamic product meta (free shipping, local pickup)
  * Block unauthorized domains for billing email
  * Order received page have title "payment failed" if it has failed
  * Empty cache when sale starts or ends
  * Change password strength
  * Hide shipping on local pickup required
  * Variations selection are a radio button instead of select
  * Product variation never include variation attributes in title except on account page
  * PayPal Express Checkout address required
  * Product meta:
    * Cash on delivery custom icon
    * "Free shipping" meta
    * "Local Pickup" meta
    * Flat Rate value meta
* Polylang:
    * Body Class Lang
    * Shortcode for language switcher
    * Javascript variables
* Elementor:
  * Search form only search for products if class name has "woocommerce-searchform" 
* Post Expirator (PublishPress):
  * Empty cache when a post expires 
* WooCommerce Advanced Messages:
  * Custom locations
* User Requests: 
  * Recipient of the data request confirmation notification
* Robots:
  * noindex,nofollow,noarchive,nosnippet when site not public
  * Embed have "no-snippet" attribute
* YouTube embeds:
  * Have modestbranding=1 / showinfo=0 / rel=0
* JetPack
  * Remove automatic Share feature 
  * Remove automatic Likes feature
  * Dequeue various scripts
* Scripts to footer:
    * GTM4WP
    * Gravity Forms: Force scripts to footer
    * Use of Asset Optimizer to move scripts to footer
* Shortcode for rss feed:
    Add to page shortcode [rss-with-image]
    Attributes variables $atts by default to modify 
    * 'rss'          => '', Feed URL
		* 'show_author'  => 0,
		* 'show_summary' => 0,
		* 'show_date'    => 0,
		* 'show_media'   => 0,
		* 'show_price'   => 0,
		* 'items'        => 0,
		* 'columns'      => 3,
		* 'button_show'  => true,
		* 'description_length' => 0, Add a truncate description. 
		* 'button_text'  => 'Voir la prestation',
  * Add thumbnail for actuality events
  * Need to add this code into the functions.php where the feed come's from
  ```php
    add_action('rss2_item', function() {
    global $post;
    if (has_post_thumbnail($post->ID)) {
        $thumbnail_id = get_post_thumbnail_id($post->ID);
        $thumbnail = wp_get_attachment_image_src($thumbnail_id, 'thumbnail');
        if ($thumbnail) {
            echo '<enclosure url="' . esc_url($thumbnail[0]) . '" length="0" type="image/jpeg" />' . PHP_EOL;
        }
    }
    });
  ```
Gravity Forms Action Replacement How-To
---
If you want to change the action attribute of a GF form, fill the form setting "Action" with a valid URL.
Next, for every input that needs a custom name, enable "Allow field to be populated dinamically" and give a "Parameter name"

If the form needs an action depending on a radio button, add a value to each radio option. 
And add a CSS class "form-action-replacement" to the field.
Each value needs to be a valid URL.
With javascript the action will be changed depending on the radio checked.

Gravity Forms User Requests custom form
---
Create a Gravity Forms for User Requests.
For example create a radio field with CSS name "personal_data":
1. Contact the DPO
2. Edit Personal Data
3. Export Personal Data (with value "export_personal_data" )
4. Delete Personal Data (with value "remove_personal_data" )

1 and 2 have regular GF confirmations/notifications.
3 and 4 are hooked with "user requests" feature from WordPress
The Gravity Forms pre_submission will check the value of this field and hook if necessary.
