<?php

class NX_Ads_Admin {

	private $nx_ads;
	private $nx_ads_var;
	private $settings;

	public function __construct($settings) {
		$this->nx_ads = NX_ADS_DOMAIN;
		$this->nx_ads_var = NX_ADS_VAR;
		$this->settings = $settings;
		$this->inread_tags = array('p', 'h1', 'h2', 'h3', 'h4', 'h5', 'h6');
		//print_r($this->settings);
	}

	public function enqueue_scripts() {
		if (isset($_GET['page']) && ($_GET['page'] == 'nx-ads')) { 
			wp_enqueue_style ($this->nx_ads, plugin_dir_url( __FILE__ ) . 'css/nx-ads-admin.css', array(), NX_ADS_VERSION, 'all');
			wp_enqueue_script($this->nx_ads, plugin_dir_url( __FILE__ ) . 'js/nx-ads-app.js', array('jquery'), NX_ADS_VERSION, false);
			wp_enqueue_script($this->nx_ads . '-admin', plugin_dir_url( __FILE__ ) . 'js/nx-ads-admin.js', array( 'jquery', $this->nx_ads), NX_ADS_VERSION, false);
		
			// Enqueue code editor (codemirror - since version 4.9).
			if (function_exists('wp_enqueue_code_editor')) {
				wp_enqueue_code_editor(array());
			}
		}
	}

	public function after_wp_tiny_mce() {

		$data = $this->get_setting('container');
		
		if (!is_array($data)) {
			return false;
		}

		$container = array_keys($data);
		?>
			<script id="nxAdsData" type="application/json">
			{ 
				"container": <?php echo json_encode($container); ?>,
				"button_title": "<?php _e('Insert MD-NX ad container', 'nx-ads') ?>",
				"window_title": "<?php echo NX_ADS_TITLE ?>"
			}
			</script>
		<?php
	}

	public function plugin_add_settings_link($links) {
		if ($this->user_can_config()) {
			$settings_link = '<a href="options-general.php?page=nx-ads">' . __( 'Settings', 'nx-ads') . '</a>';
			array_push($links, $settings_link);
		}

		return $links;
	}

	public function mce_external_plugins($plugin_array) {
		//$container = array_keys($this->get_setting('container'));
		//$containerQuery = urlencode(join(',', $container));
		
		$plugin_array['nx_ads'] = plugin_dir_url( __FILE__ ) . 'js/nx-ads-editor-plugin.js';
		return $plugin_array;
	}

	public function mce_buttons($buttons) {
		array_push( $buttons, 'nx_ads');
		return $buttons;
	}

	public function admin_notices(){

		/* Check transient, if available display notice */
		if( get_transient('nx_ads_activated') ){
			?>
			<div class="updated notice is-dismissible">
				<p>
					<?php echo  sprintf(__('<strong>%s</strong> successfully activated. The Settings are available <a href="%s">on this page</a>.', 'nx-ads' ), NX_ADS_FULLTITLE, 'options-general.php?page=nx-ads'); ?>
				</p>
			</div>
			<?php
			/* Delete transient, only display this notice once. */
			delete_transient('nx_ads_activated');
		}


		/* Check transient, if available display notice */
		if( get_transient('nx_ads_migrated') ){
			?>
			<div class="updated notice is-dismissible">
				<p>
					<?php echo  sprintf(__('<strong>%s</strong> successfully migrated the data from "Netzathleten Banner"', 'nx-ads' ), NX_ADS_FULLTITLE); ?>
				</p>
				<p>
					<?php echo sprintf(__('Please note, it is recommended to disable the old "Netzathleten Banner" plugin, after adding all settings correctly to the new one.', 'nx-ads'), NX_ADS_FULLTITLE); ?>
				</p>
			</div>
			<?php
			/* Delete transient, only display this notice once. */
			delete_transient('nx_ads_migrated');
		}



		if( get_transient('nx_ads_outdated_data') ){
			if (self::is_migration_available()) {
			?>
				<div class=" notice-info  notice">
					<p>
						<?php echo sprintf(__('<strong>%s</strong> has found the active plugin "Netzathleten Banner".<br/>Should the data mirgrated to the new plugin?', 'nx-ads' ), NX_ADS_FULLTITLE); ?>
					</p>

					<p class="submit">
						<?php
							$current_rel_uri = add_query_arg( NULL, NULL );

						?>
						<a  class=" button" href="<?php echo esc_url(add_query_arg( 'nx-ads-migration', 'false', $current_rel_uri)) ?>"><?php _e('Ignore', 'nx-ads') ?></a>
						<a  class=" button button-primary" href="<?php echo esc_url(add_query_arg( 'nx-ads-migration', 'true', $current_rel_uri)) ?>"><?php _e('Migrate data', 'nx-ads') ?></a>
					</p>
				</div>
			<?php
			} else {
				delete_transient('nx_ads_outdated_data');
			}
			
		}
	}

	public function add_meta_boxes() {
		if (!$this->user_can_edit_page()) {
			return;
		}
	
		$custom_types = array_keys(get_post_types(array(
			'public' => true, 
			'_builtin' => false
		), 'objects', 'and')); 

		$types = array_merge(array("page", "post"), $custom_types);
		
		foreach($types as $type) {
			add_meta_box(
				$this->nx_ads,
				NX_ADS_TITLE,
				array( $this, 'render_meta_box' ), 
				$type, 
				'side', 
				'default'
			);
		}

	}

	public function render_meta_box($post, $metabox) {
		// Use nonce for verification
		wp_nonce_field(plugin_basename( __FILE__ ), $this->nx_ads);
		
		$zone = get_post_meta($post->ID, $this->nx_ads_var.'_zone', true);
		$noad = get_post_meta($post->ID, $this->nx_ads_var.'_noad', true);

		$settings = get_option($this->nx_ads_var);
		$zones = isset($settings['zone']) ? $settings['zone'] : array();
		$isFrontPage = $post->ID === get_option( 'page_on_front' );
		
		?>
			<?php if (!empty($zones) && !$isFrontPage) : ?>
				<p>
					<label class="post-attributes-label" for="<?php echo $this->nx_ads.'-zone' ?>">
						<?php _e('Zone', 'nx-ads') ?>
					</label>
				</p>
				<select  id="<?php echo $this->nx_ads.'-zone' ?>" name="<?php echo $this->nx_ads_var.'_zone' ?>">
					<option value=""><?php _e('Default', 'nx-ads') ?></option>

					<?php foreach($zones as $name => $data): 
						$selected = intval($zone) === intval($data['id']) ? 'selected' : '' ;
					
					?>
						<option value="<?php echo $data['id'] ?>" <?php echo $selected ?>><?php echo $name ?></option>
					<?php endforeach ?>
				</select>
			<?php endif; ?>
			<p>
				<input id="<?php echo $this->nx_ads.'-noad' ?>"  <?php echo checked(1, $noad, false) ?> type="checkbox"  name="<?php echo $this->nx_ads_var.'_noad' ?>" value="1" />
				<label for="<?php echo $this->nx_ads.'-noad' ?>"><?php _e('Hide ads', 'nx-ads') ?></label>
			</p>
		<?php
	}

	public function save_post( $post_id ) {
		if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) return;

		if (isset($_POST[$this->nx_ads])) {
			if ( !wp_verify_nonce( $_POST[$this->nx_ads], plugin_basename( __FILE__ ) ) ) return;
		} else return;

		if (!$this->user_can_edit_page()) {
			return;
		} else if ('page' == $_POST['post_type'] ) {
			if (!current_user_can( 'edit_page', $post_id ) ) return;
		} else if ('post' == $_POST['post_type']) {
			if (!current_user_can( 'edit_post', $post_id ) ) return;
		}

		$zone = filter_input(INPUT_POST, $this->nx_ads_var.'_zone', FILTER_SANITIZE_NUMBER_INT);
		$noad = filter_input(INPUT_POST, $this->nx_ads_var.'_noad', FILTER_SANITIZE_NUMBER_INT);
	
		update_post_meta($post_id, $this->nx_ads_var.'_zone', $zone);
		update_post_meta($post_id, $this->nx_ads_var.'_noad', $noad);
	}

	private function get_preview_link($postID, $style = "") {
		$link = get_permalink($postID);
		$link = add_query_arg('nx-preview', 'all', $link);
		$label = __( 'MDNX Banner preview', 'nx-ads');
		$link = "<a style='$style' href='$link' title='$label' rel='permalink'>$label</a>";
		return $link;
	}

	public function edit_form_before_permalink() {
		if (isset($this->settings['site_id'])) {
			$style = 'float: right; margin: 8px 2px 0 0;';
			if (isset($_GET['post'])) {
				$link = $this->get_preview_link($_GET['post'], $style);
				echo $link;
			} 
		}
	}

	public function row_actions($actions, $post) {
		if (isset($this->settings['site_id'])) {
			$actions['banner_preview'] = $this->get_preview_link($post->ID);
		}

		return $actions;
	}
	
	private function user_can_config() {
		return current_user_can(NX_ADS_CAP_CONFIG) && current_user_can('manage_options');
	}

	private function user_can_edit_page() {
		return current_user_can(NX_ADS_CAP_EDIT);
	}

	public function menu_pages() {
		add_submenu_page(
			'options-general.php' ,
			sprintf(__( 'Configuration | %s', 'nx-ads'), NX_ADS_FULLTITLE), 
			NX_ADS_TITLE,
			$this->user_can_config(), 
			$this->nx_ads,
			array( $this, 'menu_pages_callback' )
		);
	}

	public function register_widget() {
		register_widget('NX_Ads_Widget');
	}

	public function renderfield_site_id($args) {
		?>
			<input class="regular-text" maxlength="36" pattern="^[0-9a-f]{8}-[0-9a-f]{4}-4[0-9a-f]{3}-[89ab][0-9a-f]{3}-[0-9a-f]{12}$"  placeholder="XXXXXXXX-XXXX-XXXX-XXXX-XXXXXXXXXXXX" type="text" id="<?php echo $this->nx_ads_var . '-site-id' ?>" name="nx_ads[site_id]" value="<?php echo esc_html($this->settings['site_id']) ?>" required/>
			<div></div>
			<p class="description" id="tagline-description">
				<?php _e('Please enter your personal site ID here', 'nx-ads'); ?>
			</p>
		<?php
	}

	public function renderfield_css($args) {
		?>
			<p class="info" data-info-toggle>
				<?php _e('Below you can customize the positioning of the banner ad.<br/>The individual div containers are automatically given the .nx-container-<strong>container-name</strong> class (for example .nx-container-top for container top), through which the styles can be addressed.', 'nx-ads'); ?>
			</p>

			<textarea name="nx_ads[css]" id="nxAdsStyle" cols="60"><?php echo esc_html($this->settings['css']) ?></textarea>
		<?php
	}
	
	public function renderfield_uninstall($args) {
		?>
			<table class="form-table">
				<tbody>
					<tr class="small_height">
						<th scope="row"><?php echo sprintf(__('Uninstall %s', 'nx-ads'), NX_ADS_TITLE); ?></th>
						<td>
										
							<label for="checkbox_uninstall">
								<input type="checkbox" id="checkbox_uninstall" name="nx_ads[uninstall]" value="1" <?php echo checked(1, isset($this->settings['uninstall']) ? $this->settings['uninstall']: false, false) ?> />
								<?php echo sprintf(__('Check this if you would like to remove ALL %s data upon plugin deletion. All settings will be unrecoverable.', 'nx-ads'), NX_ADS_TITLE); ?>
							</label>
						</td>
					</tr>
				</tbody>
			</table>
		<?php
	}

	public function renderfield_checkbox($args) {
		?>
		<label for="<?php echo $args['id'] ?>">
			<input type="checkbox" id="<?php echo $args['id'] ?>" name="nx_ads[visibility][<?php echo $args['name'] ?>]" value="1" <?php echo checked(1, $this->get_setting($args['name'], 'visibility'), false) ?> />
			<?php echo sprintf(__('Hide banner at %s', 'nx-ads' ),  $args['label']); ?>
		</label>			
		<?php
	}

	public function get_setting($name, $area = null) {
		if (isset($area)) {
			if (isset($this->settings[$area])) {
				if (isset($this->settings[$area][$name])) {
					return $this->settings[$area][$name];
				}
			}
		} else {
			if (isset($this->settings[$name])) {
				return $this->settings[$name];
			}
		}

		return null;
	}

	public function settings_container() {
		global $wp_version;
		?>
			<div data-info-toggle>
				<p class="info">
					<?php _e('Subsequently, you can control the placement within the page. There are four ways to integrate an ad container within the page. First you have to add the container via the dropdown below the overview and apply the changes.', 'nx-ads'); ?>
				</p>

				<ol>
					<li><?php _e('In the following overview, you specify the ID or class of the parent page element under which the ad container should appear.', 'nx-ads'); ?></li>
					<li><?php echo sprintf(__('You use the widget "%s" and select an activated ad container there.', 'nx-ads'), NX_ADS_FULLTITLE); ?></li>
					<?php if(version_compare($wp_version, '5.0', '>=')) : ?>
						<li><?php _e('Within the WordPress Block Editor please use the function "Add block" - "Widgets" - Shortcode" and insert the required shortcode of the container, for example %%privacy%%.', 'nx-ads'); ?></li>
					<?php else : ?>
						<li><?php _e('You use the button "Insert MDNX ad container" within the editor of a page or post.', 'nx-ads'); ?></li>
					<?php endif; ?>
					<li><?php _e('You use the shortcodes to place within your (child) theme.', 'nx-ads'); ?></li>
				</ol>
			</div>
		<?php
	}

	public function validate($data)
	{

		if (!preg_match('/^[0-9A-F]{8}-[0-9A-F]{4}-4[0-9A-F]{3}-[89AB][0-9A-F]{3}-[0-9A-F]{12}$/i', $data['site_id'])) {
			$data['site_id'] = '';
		}

		$hash = filter_input(INPUT_POST, $this->nx_ads_var.'_hash', FILTER_SANITIZE_STRING);
		setcookie($this->nx_ads_var.'_hash', $hash, time() + (60 * 3), '/'); 

		// clean css code
		$data['css'] = wp_strip_all_tags($data['css']);
		
	
		// clean zone data
		if (isset($data['zone']) && is_array($data['zone'])) {
			foreach($data['zone'] as $name => $entry) {
				$data['zone'][$name] = array(
					'id' => (int) $entry['id'],
					'description' => sanitize_text_field($entry['description'])
				);
			}
		}

		
		return $data;
	}

	public function rendersection_visibility() {
		?>
			<p class="info" data-info-toggle>
				<?php echo sprintf(__('In the following, you can completely prevent the global delivery of advertising material for certain page areas. You can make individual settings in the page or contribution settings in the "%s" section or you define in the overview below which containers should not be used in certain areas.', 'nx-ads'), NX_ADS_TITLE); ?>
			</p>
		<?php
	}

	public function rendersection_zones () {
		?>
			<p class="info" data-info-toggle>
				<?php echo sprintf(__('For marketing, certain pages can be considered differently. These areas are marked by zones. Please define here first the desired zone (only in agreement with %s) and then assign them in the settings of the respective page or a contribution.', 'nx-ads'), NX_ADS_FULLBRAND); ?>
			</p>
		<?php
	}

	private function get_new_site_id($deprecated_key) {
		$api_path = str_replace('%%key%%', $deprecated_key, NX_ADS_MIGRATION_API);
		$content = file_get_contents($api_path);
		
		if (empty($content)) {
			return "";
		}

		$data = json_decode($content, true); 
		return isset($data['siteId']) ? $data['siteId'] : '';
	}


	private function check_migration() {
		if( get_transient('nx_ads_outdated_data') ){
			
			
			if (!empty($_GET['nx-ads-migration'])) {
				$migration = $_GET['nx-ads-migration'];

				if ($migration == 'false') {
					delete_transient('nx_ads_outdated_data');
				}

				if ($migration == 'true') {
					if (self::is_migration_available()) {
						
						$ad_matrix = array(
							"top" => "top_mobile",
							"mid" => "mid_mobile",
							"bottom" => "bottom_mobile",
							"superbanner" => "top",
							"wide_skyscraper" => "right",
							"medium_rectangle" => "content",
							"content_rollover" => "inread",
							'billboard' => null,
							'layer'  => null,
							'wallpaper'  => null,
							'big_billboard'  => null,
							'sitebar'  => null,
							'skinning'  => null,
							'halfpage'  => null,
							'super_wide_skyscraper'  => null
						);

						$new_nx_ads_data = array (
							'site_id' => $this->get_new_site_id(get_option('nabanner_apikey')),
							'css' => get_option('nabanner_css'),
							'container' => array ()
						);

						foreach($ad_matrix as $container => $name) {

							$name = ($name === null) ? $container : $name;
							$container = strtoupper($container);

							$is_home = get_option('nabanner_enable_'.$container.'_home');
							$is_sub = get_option('nabanner_enable_'.$container); 

							if ( $is_home == "1" || $is_sub == "1") {
		
								if ($name == "inread" && $is_sub !== "1") {

									continue; // ignore inread container with no visibility on subpages
								}


								$placement = get_option('nabanner_placement_'.$container); 
								$selector = "";

								if (!empty($placement) && !is_array($placement)) {
									$selector = '#'.$placement;
								}

								$new_nx_ads_data['container'][$name] = array(
									'placement' => array (
										'own' =>  $selector
									)	
								);

								if ( $is_home !== "1" || $is_sub !== "1") {
									$new_nx_ads_data['container'][$name]['visibility'] = array();

									if ($is_home !== "1") {
										$new_nx_ads_data['container'][$name]['visibility']['_homepage'] = 1;	
									}

									if ($is_sub !== "1") {
										$new_nx_ads_data['container'][$name]['visibility']['_subpages'] = 1;	
									}
								}

								if ($name === 'inread') {
									$html_tag = get_option('nabanner_needle_'.$container);
									$pos = get_option('nabanner_pos_'.$container);

									if (!is_numeric($pos)) {
										$pos = "1";
									}

									$new_nx_ads_data['container'][$name]['seek'] = array();
									$new_nx_ads_data['container'][$name]['seek'][uniqid()] = array(
										'tag' => $html_tag,
										'position' => $pos,
										'categories' => is_array($placement) ? array_fill_keys($placement, '1') : array()
									);
								}
							}
						}

						update_option('nx_ads', $new_nx_ads_data);
						$this->settings = $new_nx_ads_data;
						delete_transient('nx_ads_outdated_data');
						set_transient('nx_ads_migrated', true, 5);
					}
				}
			}
		}
	}

	public static function is_migration_available() {
		if ( is_plugin_active( 'wp-nabanner/wp-nabanner.php' ) ) {
			$apikey_option = get_option('nabanner_apikey');
			if (!empty($apikey_option)) {
				return true;
			}
		}

		return false;
	}

	public function admin_init() {		
		$helper = '<a href data-show-help ><span class="dashicons dashicons-editor-help"></span></a>';

		$this->container = array(
			array("name" => ""),
			array("name" => "top"),
			array("name" => "billboard"),
			array("name" => "right"),
			array("name" => "content"),
			array("name" => "content_bottom"),
			array("name" => "layer"),
			array("name" => "inread"),
			array("name" => "native"),
			array("name" => "privacy"),
			array("name" => "sales")
		);

		$this->current_tab = "";

		if (isset($_COOKIE[$this->nx_ads_var.'_hash'])) {
			$this->current_tab = $_COOKIE[$this->nx_ads_var.'_hash'];

			// Remove cookie:
			unset($_COOKIE[$this->current_tab]);
			setcookie($this->nx_ads_var.'_hash', '', time() - 3600, '/'); 
		}

		add_settings_section(
			$this->nx_ads . '-settings-general',
			sprintf(__('%s Settings', 'nx-ads'), '<span class="dashicons dashicons-admin-generic"></span>'),
			null,
			$this->nx_ads
		);

		add_settings_section(
			$this->nx_ads . '-settings-visibility',
			sprintf(__('%s Restrict visibility %s', 'nx-ads'), '<span class="dashicons dashicons-visibility"></span>', $helper),
			array($this, 'rendersection_visibility'),
			$this->nx_ads
		);

		add_settings_section(
			$this->nx_ads . '-settings-container',
			sprintf(__('%s Containers %s', 'nx-ads'), '<span class="dashicons dashicons-layout"></span>', $helper),
			array($this, 'settings_container'),
			$this->nx_ads. '-ads'
		);

		add_settings_section(
			$this->nx_ads . '-settings-misc',
			sprintf(__('%s Misc', 'nx-ads'), '<span class="dashicons dashicons-admin-tools"></span>'),
			null,
			$this->nx_ads. '-misc'
		);
		

		add_settings_field( 
			$this->nx_ads_var . '-site-id',
			__('Site-ID', 'nx-ads'),
			array($this, 'renderfield_site_id'),
			$this->nx_ads,
			$this->nx_ads . '-settings-general'
		);

		add_settings_field( 
			$this->nx_ads_var . '-css', 
			sprintf(__('Custom CSS %s', 'nx-ads'), $helper),        
			array($this, 'renderfield_css'),  
		 	$this->nx_ads,
			$this->nx_ads . '-settings-general'
		);

		add_settings_field( 
			$this->nx_ads_var . '-uninstall', 
			null,        
			array($this, 'renderfield_uninstall'),  
		 	$this->nx_ads,
			$this->nx_ads .  '-misc'
		);


		$this->post_types = get_post_types(array(
			'public'   => true
		), 'objects', 'and'); 
		
		$type_start = new stdClass();
		$type_start->label = __( 'Homepage', 'nx-ads');
		$type_start->name = '_homepage';

		$type_sub = new stdClass();
		$type_sub->label = __( 'Subpages', 'nx-ads');
		$type_sub->name = '_subpages';

		array_unshift($this->post_types, $type_start, $type_sub);


		foreach ( $this->post_types  as $post_type ) {
			$fieldID = $this->nx_ads_var . '-posttype-' . $post_type->name;

			add_settings_field( 
				$fieldID,
				$post_type->label,
				array($this, 'renderfield_checkbox'),  
				$this->nx_ads,
				$this->nx_ads . '-settings-visibility',
				array('name' => $post_type->name, 'id' => $fieldID, 'label' => $post_type->label, 'class' => 'small_height')
			);
		}

		add_settings_section(
			$this->nx_ads . '-settings-zones',
			sprintf(__('%s Zone List %s', 'nx-ads'), '<span class="dashicons dashicons-admin-multisite"></span>', $helper),
			array($this, 'rendersection_zones'),
			$this->nx_ads. '-append'
		);

		register_setting($this->nx_ads, $this->nx_ads_var, array($this, 'validate'));

	//	$this->settings = get_option($this->nx_ads_var);
		$this->check_migration();
	}

	public function update_options()
	{
		if( isset($_POST['show_in']) ) {
			update_option($this->nx_ads_var.'_visibility', $_POST['show_in']);
		} else {
			update_option($this->nx_ads_var.'_visibility', array());
		}
	}

	/**
	 * The callback for creating a new submenu page under the "Tools" menu.
	 * @access public
	 */
	public function menu_pages_callback() {
		require_once plugin_dir_path( __FILE__ ) . 'partials/nx-ads-admin-display.php';
	}
}

class NX_Ads_Widget extends WP_Widget {

	function __construct() {
		parent::__construct(
			'nx_ad_widget',
			NX_ADS_FULLTITLE,
			array('description' => sprintf(__( 'Place banner from %s', 'nx-ads'), NX_ADS_FULLBRAND))
		);
	}

	public function widget( $args, $instance ) {
		if (isset($instance['container_name'])) {
			echo $this->get_container($instance['container_name']);
		}
	}
	
	private function get_container($name) {
		return '<div data-nx-container="'.$name.'"></div>';
	}

	public function form ($instance) {
		$settings = get_option('nx_ads');
		$container = isset($settings['container']) ? $settings['container'] : array();
		$container_name = isset($instance['container_name']) ? $instance['container_name'] : '';
	
		require plugin_dir_path( __FILE__ ) . 'partials/nx-ads-admin-widget.php';
	}

	public function update( $new_instance, $old_instance ) {
		$instance = array();
		$instance['container_name'] = (!empty($new_instance['container_name'])) ? strip_tags( $new_instance['container_name']) : '';
		return $instance;
	}
}
