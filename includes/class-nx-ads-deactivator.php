<?php

class NX_Ads_Deactivator {

	public static function deactivate() {
		delete_transient('nx_ads_outdated_data');
	}

}
