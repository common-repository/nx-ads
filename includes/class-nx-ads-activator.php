<?php

class NX_Ads_Activator {

	public static function activate() {
		$init_data = array (
			'site_id' => '',
			'css' => '',
			'container' => array (
				'top' => array (
					'placement' => array (
						'own' => ''
					)
				),
				'right' => array (
					'placement' => array (
						'own' => '',
					)
				),
				'content' => array (
					'placement' => array (
						'own' => '',
					)
				),
				'billboard' => array (
					'placement' => array (
						'own' => '',
					)
				),
				'privacy' => array (
					'placement' => array (
						'own' => '',
					)
				),
				'sales' => array (
					'placement' => array (
						'own' => '',
					)
				)
			)
		);

		self::check_outdated();

		add_option('nx_ads', $init_data);
		set_transient('nx_ads_activated', true, 5);
		self::add_cap();

	}

	public static function check_outdated() {

		if ( is_plugin_active( 'wp-nabanner/wp-nabanner.php' ) ) {
			$apikey_option = get_option('nabanner_apikey');
			if (!empty($apikey_option)) {
				set_transient('nx_ads_outdated_data', true, 0);
			}
		}
	}

	public static function add_cap() {
		$roles = get_editable_roles();

		foreach ($GLOBALS['wp_roles']->role_objects as $key => $role) {
			if (isset($roles[$key]) && $role->has_cap('manage_options')) {
				$role->add_cap(NX_ADS_CAP_CONFIG);
			}
			
			if (isset($roles[$key]) && $role->has_cap('edit_posts')) {
				$role->add_cap(NX_ADS_CAP_EDIT);
			}
		}
	}
}
