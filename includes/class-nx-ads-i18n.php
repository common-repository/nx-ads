<?php

class NX_Ads_i18n {
	public function load_plugin_textdomain() {
		load_plugin_textdomain(
			'nx-ads',
			false,
			dirname( dirname( plugin_basename( __FILE__ ) ) ) . '/languages/'
		);
	}
}