<?php

class nx_ads {

	protected $loader;
	protected $settings;

	public function __construct() {
		$this->load_data();
		$this->load_dependencies();

		$this->set_locale();

		$this->define_admin_hooks();
		$this->define_public_hooks();
	}

	private function load_data() {
		$this->settings = get_option(NX_ADS_VAR);
	}

	private function load_dependencies() {
		require_once plugin_dir_path(dirname( __FILE__ )) . 'includes/class-nx-ads-loader.php';
		require_once plugin_dir_path(dirname( __FILE__ )) . 'includes/class-nx-ads-i18n.php';
		require_once plugin_dir_path(dirname( __FILE__ )) . 'admin/class-nx-ads-admin.php';
		$this->loader = new NX_Ads_Loader();
	}

	private function set_locale() {
		$plugin_i18n = new NX_Ads_i18n();
		$this->loader->add_action('plugins_loaded', $plugin_i18n, 'load_plugin_textdomain');
	}

	private function define_admin_hooks() {
		$plugin_admin = new NX_Ads_Admin($this->settings);

		$this->loader->add_action('admin_enqueue_scripts', $plugin_admin, 'enqueue_scripts');
		$this->loader->add_action('admin_menu', $plugin_admin, 'menu_pages');
		$this->loader->add_action('widgets_init', $plugin_admin, 'register_widget');
		$this->loader->add_action('admin_init', $plugin_admin, 'admin_init');
		$this->loader->add_action('after_wp_tiny_mce', $plugin_admin, 'after_wp_tiny_mce');
		$this->loader->add_action('admin_notices', $plugin_admin, 'admin_notices');
		$this->loader->add_action('edit_form_before_permalink', $plugin_admin, 'edit_form_before_permalink');
		$this->loader->add_action('page_row_actions', $plugin_admin, 'row_actions', 10, 2 );
		$this->loader->add_action('post_row_actions', $plugin_admin, 'row_actions', 10, 2 );
		$this->loader->add_action('add_meta_boxes', $plugin_admin, 'add_meta_boxes');
		$this->loader->add_action('save_post', $plugin_admin, 'save_post');
	
		$plugin_folder = basename(plugin_dir_path(dirname( __FILE__ )));
		$filter_name = $plugin_folder . '/nx-ads.php';

		$this->loader->add_filter("plugin_action_links_".$filter_name, $plugin_admin, 'plugin_add_settings_link');
		$this->loader->add_filter('mce_external_plugins',  $plugin_admin, 'mce_external_plugins');
		$this->loader->add_filter('mce_buttons',  $plugin_admin, 'mce_buttons');
	}

	private function define_public_hooks() {
		$settings = $this->settings;

		if (isset($this->settings['site_id'])) {
			require_once plugin_dir_path(dirname( __FILE__ )) . 'public/class-nx-ads-public.php';
			
			$plugin_public = new NX_Ads_Public($settings);
			$this->loader->add_action('wp_enqueue_scripts', $plugin_public, 'enqueue_scripts', 0);
			$this->loader->add_action('wp_enqueue_scripts', $plugin_public, 'enqueue_styles', 100);
			$this->loader->add_action('wp_head', $plugin_public, 'wp_head', 1);
			$this->loader->add_action('wp_head', $plugin_public, 'buffer_start', 0);
			$this->loader->add_action('the_content', $plugin_public, 'the_content', 0);
			$this->loader->add_filter('script_loader_tag',  $plugin_public, 'script_loader_tag', 10, 3);
		}
	}

	public function run() {
		$this->loader->run();
	}
}