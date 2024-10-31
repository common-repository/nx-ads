<?php

if ( ! defined( 'WP_UNINSTALL_PLUGIN' ) ) {
	exit;
}

require_once plugin_dir_path( __FILE__ ) . 'nx-ads.php';
require_once plugin_dir_path( __FILE__ ) . 'includes/class-nx-ads-uninstall.php';
NX_Ads_Uninstaller::uninstall();