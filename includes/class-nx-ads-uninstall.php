<?php

class NX_Ads_Uninstaller {

	public static function uninstall_hook() {
	}
	
	public static function uninstall() {
		$settings = get_option(NX_ADS_VAR);

		if (isset( $settings['uninstall']) &&  $settings['uninstall'] == true) {
			delete_option(NX_ADS_VAR);
		}

		self::remove_cap();
	}

	private static function remove_cap() {
		$roles = get_editable_roles();

		foreach ($GLOBALS['wp_roles']->role_objects as $key => $role) {
			if (isset($roles[$key]) && $role->has_cap('manage_options')) {
				$role->remove_cap(NX_ADS_CAP_CONFIG);
			}

			if (isset($roles[$key]) && $role->has_cap('edit_posts')) {
				$role->remove_cap(NX_ADS_CAP_EDIT);
			}
		}
	}
}
