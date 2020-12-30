* **New** - New Feature to replace WC Variations Radio Buttons plugin; All template overriding woocommerce/single-product/add-to-cart/variable.php should be removed
* **New** -  WooCommerce: Product meta includes flat rate if available (testing: display none for non admins)

### 1.2.6: December 2nd, 2020
* **Tweak** - Update FR translations

### 1.2.5: December 2nd, 2020
* **New** - WooCommerce: Product meta includes free shipping if available (testing: display none for non admins)
* **New** - WooCommerce: Product meta includes local pickup if available (testing: display none for non admins)

### 1.2.4: November 12th, 2020
* **Fix** - Missing image for "Cash on Delivery"

### 1.2.3: November 11th, 2020
* **New** - WooCommerce: Image for "Cash on Delivery" with payment means: cash, travel checks, gift checks
* **New** - WooCommerce: hide shipping when products marked as "local pickup only" are in the cart (needs a shipping class with value=local_pickup_only"
* **New** - WooCommerce: Hides local_pickup shipping method if no_local_pickup shipping class is found in cart

### 1.2.2: October 26th, 2020
* **New** - WooCommerce: customer the order received title page when payment failed

### 1.2.1: September 21st, 2020
* **New** - Oembed: YouTube videos always with rel=0 modestbranding=1 showinfo=0

### 1.2.0: January 16th, 2020
* **New** - WooCommerce Style Password Strength Hint

### 1.1.9: January 16th, 2020
* **New** - WooCommerce Lower Password Strength to 2

### 1.1.8: January 7th, 2020
* **New** - WooCommerce Advanced Messages: add "after summary" location for Product 

### 1.1.7: December 19th, 2019
* **New** - Customize password hint text to explain better what WooCommerce customers must respect

### 1.1.6: November 08th, 2019
* **New** - GitHub Actions for code review
* **Tweak** - Update dpo email

### 1.1.5: August 08th, 2019
* **Fix** - Script Tag broken when inline script

### 1.1.4: August 07th, 2019
* **Tweak** - Remove debug notices
* **New** - Script Tag now with "data-name" attribute
* **Tweak** - Paypal Checkout: Make Billing Address not Required

### 1.1.3: June 25th, 2019
* **New** - Empty cache when TAO Schedule Update is fired

### 1.1.2: April 30th, 2019
* Remove debug

### 1.1.1: April 24th, 2019
* Prevent duplicate transactions by checking paid date: if paid date is older than 24 hours, do not fire Google Tag Manager

### 1.1.0: April 17th, 2019
* Gravity Forms function execute personal data requests
* Fix DPO recipient function fatal error

### 1.0.10: April 1st, 2019
* Remove Jetpack Geo Location module

### 1.0.9: December 7th, 2018
* WooCommerce Scheduled Sales: everyday, sales start, cache should be emptied (WP Rocket)

### 1.0.8: November 27th, 2018
* Hide WordPress logo on GDPR confirmation page
* OptinMonster script to footer

### 1.0.7: November 5th, 2018
* Default DPO email

### 1.0.6: November 5th, 2018
* Recipient of the data request confirmation notification
* WP Rocket: Lazyload, Exclude attributes from Elementor

### 1.0.5: August 28th, 2018
* Google Tag Manager: Exclude orders with status failed (only paid statuses)

### 1.0.4: May 18st, 2018
* Disable default gallery style
* Jetpack: Remove Sharing Filters

### 1.0.3: May 11st, 2018
* Gravity Forms: Do not collect IP Address (GDPR compliance)
* Fix language shortcode
* Embed responsive (max width)

### 1.0.2: April 17th, 2018
* Yoast SEO: breadcrumb class
* Remove "Category:" in titles

### 1.0.1: April 16th, 2018
* Polylang javascript variables

### 1.0.0: April 10th, 2018
* Remove CSS styles:
    * Default gallery
    * Emoji
* Remove JS scripts:
    * Emoji
* Scripts to footer:
    * GTM4WP
    * Gravity Forms: Force scripts to footer
    * Use of Asset Optimizer to move scripts to footer    
* Robots noindex,nofollow,noarchive,nosnippet when site not public
* Head: remove shortlink, generator, rsd, manifest
* Cookies: remove comments cookies
* Shortcodes:
    * [language_switcher] for language switcher (WPML or Polylang depending on which is loaded)
* WPML:
    * Body Class Lang
    * Shortcode for language switcher
* Polylang:
    * Body Class Lang
    * Shortcode for language switcher
* OceanWP:
    * Inject Google Tag Manager after body