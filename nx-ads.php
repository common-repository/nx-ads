<?php

/**
 * @author    MAIRDUMONT NETLETIX <info@mairdumont-netletix.com>
 * @link      https://www.mairdumont-netletix.com
 * @copyright 2018 MAIRDUMONT NETLETIX
 * @since     1.0.0
 *
 * @wordpress-plugin
 * Plugin Name:       MAIRDUMONT NETLETIX Ads
 * Description:       This plugin is only for publishers who have a marketing contract with MAIRDUMONT NETLETIX. With the Ads plugin you can easily place and configure the specific ad containers dynamically as well as shortcodes.
 * Version:           1.0.1
 * Author:            MAIRDUMONT NETLETIX
 * Author URI:        https://www.mairdumont-netletix.com/
 * Text Domain:       nx-ads
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Domain Path:       /languages/
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

/* Names */
define( 'NX_ADS_BRAND', 'MD-NX' );
define( 'NX_ADS_FULLBRAND', 'MAIRDUMONT NETLETIX' );
define( 'NX_ADS_FULLTITLE', sprintf(__( '%s Ads', 'nx-ads'), NX_ADS_FULLBRAND) );
define( 'NX_ADS_TITLE', sprintf(__( '%s Ads', 'nx-ads'), NX_ADS_BRAND) );

/* Version & Prefixes */
define( 'NX_ADS_VERSION', '1.0.1' );
define( 'NX_ADS_DOMAIN', 'nx-ads' );
define( 'NX_ADS_VAR', str_replace('-', '_', NX_ADS_DOMAIN) );

/*  API */
define( 'NX_ADS_MIGRATION_API', 'https://s.adadapter.netzathleten-media.de/API-1.0/%%key%%/migration.json' );

/* Capabilities */
define( 'NX_ADS_CAP_CONFIG', NX_ADS_VAR . "_config" );
define( 'NX_ADS_CAP_EDIT', NX_ADS_VAR ."_page_edit" );

function activate_nx_ads() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-nx-ads-activator.php';
	NX_Ads_Activator::activate();
}

function deactivate_nx_ads() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-nx-ads-deactivator.php';
	NX_Ads_Deactivator::deactivate();
}

function uninstall_nx_ads() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-nx-ads-uninstall.php';
	NX_Ads_Uninstaller::uninstall_hook();
}

register_activation_hook( __FILE__, 'activate_nx_ads');
register_deactivation_hook( __FILE__, 'deactivate_nx_ads');
register_uninstall_hook( __FILE__, 'uninstall_nx_ads');


require plugin_dir_path( __FILE__ ) . 'includes/class-nx-ads.php';

function run_nx_ads() {
	$plugin = new nx_ads();
	$plugin->run();
}

run_nx_ads();
