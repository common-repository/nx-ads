<?php

class NX_Ads_Public {

	private $nx_ads;
	private $nx_ads_var;
	private $settings;
	private $is_visible;

	public function __construct($settings) {
		$this->nx_ads = NX_ADS_DOMAIN;
		$this->nx_ads_var = NX_ADS_VAR;
		$this->settings = $settings;
		$this->is_visible = true;
	}

	public function enqueue_scripts() {
		wp_enqueue_script($this->nx_ads, '//tag.md-nx.com/nx/'.$this->settings['site_id'].'/loader.js', array() , NX_ADS_VERSION, false);
	}

	public function enqueue_styles() {
		if ($this->is_visible) {
			wp_enqueue_style($this->nx_ads, plugin_dir_url( __FILE__ ) . 'css/nx-ads-public.css', array(), NX_ADS_VERSION, 'all');
			
			if(isset($this->settings['css'])) {
				wp_add_inline_style($this->nx_ads, $this->settings['css']);
			}
		}
	}

	public function wp_head() {
		?>
			<link rel="dns-prefetch" href="//tag.md-nx.com">
		<?php
	}

	private function shortcode($html, $inject, $name) {
		return preg_replace('/%'.$name.'%/i',  $inject, $html);
	}

	private function selector($html, $inject, $selector) {

		if (strlen($selector) == 0) {
			return $html;
		}

		$type = substr($selector, 0, 1) === '#' ? 'id' : 'class';
		$sel  = substr($selector, 1);
		
		if ($type === 'id') {
			return preg_replace('/(<.+id=("|\')'.$sel.'("|\')[^>].*?>)/is',  "$1" . $inject, $html);
		} else {
			$html = preg_replace('/(<.+class=("|"([^"]*)\s)'.$sel.'("|\s([^"]*)").*?>)/is', "$1".$inject, $html);
			return preg_replace('/(<.+class=(\'|\'([^\']*)\s)'.$sel.'(\'|\s([^\']*)\').*?>)/is' , "$1".$inject, $html);
		}

		
	}

	private function get_container($name) {
		return '<div data-nx-container="'.$name.'"></div>';
	}

	public function the_content($content) {
		// Check if we're inside the main loop in a single post page.
		if (
			is_single() && 
			in_the_loop() && 
			is_main_query() && 
			$this->is_visible && 
			isset($this->settings['container']
		)) {

			foreach($this->settings['container'] as $name => $container) {
				if (isset($container['seek'])) {
					$div_container = $this->get_container($name);
					
					foreach($container['seek'] as $key => $seek) {
						if(
							(isset($seek['categories']) && 
							count($seek['categories']) > 0 && 
							in_category($seek['categories'])) ||
							empty($seek['categories']) // all categories
						) {
							$positions = array();
							$pos = 0;

							do {
								$matches = array();
								preg_match('/<\/\s*'.$seek['tag'].'\s*>/i', $content, $matches, PREG_OFFSET_CAPTURE, $pos);

								if (count($matches)) {
									$pos = $matches[0][1];
									$positions[] = $pos;
									$pos++;
								}

							} while (count($matches));
							
							if (!empty($positions)) {
								if (count( $positions) >=  $seek['position']) {
									$content = substr_replace($content, $div_container, $positions[$seek['position'] - 1] + 3 + strlen($seek['tag']), 0);
								} 
							}
						}
					}
				}
			}

		}

		return $content;
	}


	public function set_output($buffer) {
		// modify buffer here, and then return the updated code
		
		if ($this->is_visible && isset($this->settings['container'])) {
			
			foreach($this->settings['container'] as $name => $container) {
				if (isset($container['placement'])) {

					if (isset($container['visibility'])) {
						$is_visible = $this->get_visibility($container['visibility']);

						if ($is_visible === false) {
							$buffer = $this->shortcode($buffer, '', $name);
							continue;
						}
					}

					$div_container = $this->get_container($name);
					$buffer = $this->shortcode($buffer, $div_container, $name);

					foreach($container['placement'] as $placement => $data) {
						if ($placement === "own") {
							$data = preg_replace('/\s+/', '', $data);
							
							if (!empty($data)) {
								$selcetions = explode(',', $data) ;

								foreach($selcetions as $selection) {
									$buffer = $this->selector($buffer, $div_container, $selection);
								}
							}
						}
					}
				}
			}
		}
		
		return $buffer;
	}

	public function buffer_start() {
		if (!is_feed() && !is_admin()) {
			if (isset($this->settings['visibility'])) {
				$this->is_visible = $this->get_visibility($this->settings['visibility']);
			}

			ob_start(array($this, 'set_output')); 
		}
	}

	public function script_loader_tag($tag, $handle, $src ) {
		
		if ($handle === $this->nx_ads) {
			
			$zone = $this->get_zone();

			if (!$this->is_visible) {
				$zone = 'noad';
			}

			$zoneAttr =  " data-nx-zone=\"$zone\"";
			$tag =  str_replace( '<script', '<script async ', $tag );
			return str_replace( '></script', $zoneAttr.'></script', $tag );
		} else {
			return $tag;
		}
	}

	private function get_visibility($visibility) {
			$post_type = get_query_var('post_type');

			foreach($visibility as $type => $val) {
				// custom handles
				if ($type === '_homepage' && is_front_page()) return false;
				if ($type === '_subpages' && !is_front_page()) return false;
				if ($type === '_search' && is_search()) return false;

				// types
				if ($type === 'attachment' && is_attachment()) return false;
				if ($type === 'post' && is_singular('post')) return false;
				if ($type === 'page' && (!is_singular('post')  && empty($post_type))) return false;
				if ($post_type === $type) return false;
			}
	
		return true;
	}

	private function get_zone() {
		if (is_front_page()) {
			return 'home';
		}

		$id = get_the_ID();
		$zones = isset($this->settings['zone']) ? $this->settings['zone'] : array();
		$zoneID = get_post_meta($id, $this->nx_ads_var.'_zone', true);
		$noad   = get_post_meta($id, $this->nx_ads_var.'_noad', true);

		if ($noad == true) {
			return 'noad';
		}

		if (!empty($zones)) {
			foreach($zones as $name => $zone) {
				if (intval($zone['id']) === intval($zoneID)) {
					return $name;
				}
			}
		}

		return 'subsite';
	}
}