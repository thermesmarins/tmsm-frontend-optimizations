TMSM Frontend Optimizations
=================

Frontend Optimizations for Thermes Marins de Saint-Malo

Features
-----------

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
    * Javascript variables
* Google Tag Manager for WordPress:
    * Inject Google Tag Manager after body in OceanWP Theme
    * Exclude orders with status failed (only paid statuses)
* Gravity Forms: Anonymize user IPs
* WooCommerce: everyday, sales start, cache should be emptied (WP Rocket), dynamic product meta (free shipping, local pickup)
* User Request: Recipient of the data request confirmation notification