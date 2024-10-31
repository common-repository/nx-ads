=== MAIRDUMONT NETLETIX Ads ===
Contributors: mdnx, mrfischer
Tags: banner, ads, banner-ads, advertising, netletix, mairdumont, netzathleten, nx, nxlib
Requires at least: 4.0
Tested up to: 5.4.2
Stable tag: 1.0.1
Requires PHP: 5.2.4
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

MAIRDUMONT NETLETIX ads integration.
This plugin is only for publishers who have a marketing contract with MAIRDUMONT NETLETIX.
With the Ads plugin you can easily place and configure the specific ad containers dynamically as well as shortcodes.

== Description ==

This plugin is only for publishers who have a marketing contract with MAIRDUMONT NETLETIX.
With the Ads plugin, you can easily place and configure specific ad containers dynamically and use shortcodes.
 
You can automatically integrate the `NX library` to display your already configured banner ads. On the settings page, you can enter your `Site-ID` which is linked with your configuration. After that, you can define the required ad containers and the placement of them.
 
All defined container names are available as a shortcode, for example `%top%`. You can use the shortcodes everywhere, especially in your child-theme templates.

If you have no child-theme, you can use the placement on the settings page. For each container, you can enter multiple comma separated selectors (`id` or `class`). For each found html element, the plugin will add the container dynamically at the beginning of the element as the first child. (Note: the container list is processed in the order of definition)
 
Alternatively, there is also a widget. With it, you can place your defined containers on all available widget areas.

For your comfort, we added an optional migration of the settings of our old/deprecated plugin `Netzathleten Banner` (not available in WordPress plugin directory). For this, there will be a call to our service at https://s.adadapter.netzathleten-media.de to map old settings to corresponding new settings.


== Installation ==

1. Upload the plugin
2. Activate the plugin through the 'Plugins' menu in WordPress
3. Go to wp-admin/options-general.php?page=nx-ads and define your settings like the site-id (required) and some container to output the ads

1. Install the plugin via the plugin installer, either by searching for it or uploading a .zip file
2. Activate the plugin through the 'Plugins' menu in WordPress
3. Go to wp-admin/options-general.php?page=nx-ads and define your settings like the site-id (required) and some container to output the ads


== Screenshots ==

1. The configuration page
2. A custom button to integrate banner ads into the content
3. An example page with the preview of all ads
4. For further placement you can use the widget