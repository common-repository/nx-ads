<div class="wrap" id="plugin-<?php echo $this->nx_ads ?>">
	<form method="post" action="options.php">
		<header>

			<a href="https://www.mairdumont-netletix.com/" target="_blank" id="logo"></a>
			<div class="title"><?php echo sprintf(__( '%s Ad configuration', 'nx-ads'), NX_ADS_FULLBRAND) ?></div>
		</header>

		<div class="mdnx-tabs" data-mdnx-tabs>
			<div class="mdnx-tabs-title">
				<a href data-mdnx-tab-target="general" class="active"><?php _e('General', 'nx-ads'); ?></a>
				<a href data-mdnx-tab-target="ads"><?php _e('Banners', 'nx-ads') ?></a>
				<a href data-mdnx-tab-target="zones"><?php _e('Zones', 'nx-ads') ?></a>
				<a href data-mdnx-tab-target="misc"><?php _e('Misc', 'nx-ads') ?></a>
			</div>

			<div class="notification">
				<h1></h1>
			</div>
			<input type="hidden" name="nx_ads_hash" value="<?php echo $this->current_tab   ?>" data-current-tab />

			<div class="mdnx-tabs-content">
				<div data-mdnx-tab="general" class="active">
					<?php settings_fields( $this->nx_ads ); ?>
					<?php do_settings_sections( $this->nx_ads); ?>
				</div>

				<div data-mdnx-tab="ads">
					<!--h2><span class="dashicons dashicons-layout"></span> <?php _e('Banners', 'nx-ads') ?></h2-->
					<div id="nx_ads_section_ads">
						<?php do_settings_sections($this->nx_ads. '-ads'); ?>
					</div>
					<div class="table-wrapper">
						<table id="containerTable" class="widefat  striped posts">
							<colgroup>
								<col width="10%">
								<col width="10%">
								<col width="60%">
								<col width="15%">
								<col width="5%">
							</colgroup>

							<thead>
								<tr>
									<th><?php _e('Container-Name', 'nx-ads') ?></th>
									<th><?php _e('Hide', 'nx-ads') ?></th>
									<th><?php _e('Placement', 'nx-ads') ?></th>
									<th><?php _e('Shortcode', 'nx-ads') ?></th>
									<th></th>
								</tr>
							</thead>
							<tbody>
								<?php 
									if (isset($this->settings['container'])) :
										foreach($this->settings['container'] as $name => $container): 
											$data = $this->get_setting($name, 'container');
											include('row-container.php');
										endforeach;
									endif;
								?>
							</tbody>
						</table>
					</div>
					<p>
						<select id="containerType">
							<?php foreach($this->container as $containerType): 
								$hidden = "";
								// ignore exisiting container names:
								if (!empty($containerType['name'] ) &&
									isset($this->settings['container']) &&  
									array_key_exists($containerType['name'], $this->settings['container']) ) {
									$hidden = "hidden";
								}    
							?>
								<option value="<?php echo $containerType['name'] ?>" <?php echo $hidden ?>><?php echo empty($containerType['name']) ? __('Custom', 'nx-ads') : $containerType['name'] ?></option>
							<?php endforeach ?>
						</select>

						<button name="add-container" data-add-container class="button">
							<?php _e('Add', 'nx-ads') ?>
						</button>
					</p>
				</div>

				<div data-mdnx-tab="zones">
					<div id="nxAdsZones">
						<?php do_settings_sections($this->nx_ads. '-append'); ?>
						<input id="zoneCount" type="hidden" value="<?php echo  isset($this->settings['zone_count']) ? $this->settings['zone_count'] : 0 ?>" name="nx_ads[zone_count]" />
			
						<div class="table-wrapper">
							<table id="zoneTable" class="widefat striped posts">
								<colgroup>
									<col width="5%">
									<col width="20%">
									<col width="70%">
									<col width="5%">
								</colgroup>

								<thead>
									<tr>
										<th><?php _e('ID', 'nx-ads') ?></th>
										<th><?php _e('Zone-Name', 'nx-ads') ?></th>
										<th><?php _e('Description', 'nx-ads') ?></th>
										<th></th>
									</tr>
								</thead>
								<tbody>
									<?php 
										if (isset($this->settings['zone'])) :
											foreach($this->settings['zone'] as $name => $zone): 
												$data = $this->get_setting($name, 'zone');
												include('row-zone.php');
											endforeach;
										endif;
									?>
								</tbody>
							</table>
						</div>
						<p>
							<button name="add-zone" data-add-zone class="button">
								<?php _e('Add new zone', 'nx-ads') ?>
							</button>
						</p>
					</div>
				</div>
				<div data-mdnx-tab="misc">
					<?php do_settings_sections($this->nx_ads. '-misc'); ?>
					<?php do_settings_fields( $this->nx_ads, $this->nx_ads. '-misc' );?>
				</div>
			</div>
		</div>
	 
		<?php submit_button(); ?>
	 
	</form>
</div>

<script id="containerTemplate" type="text/template">
	<?php
		$name = "%fieldname%";
		$data = null;
		include('row-container.php'); 
	?>
</script>

<script id="containerTemplateSpecial" type="text/template">
	<?php
		$inject = true;
		$name = "sales";
		$data = null;
		include('row-container.php'); 
	?>
</script>

<script id="containerTemplateInread" type="text/template">
	<?php
		$inject = true;
		$name = "inread";
		$data = null;
		include('row-container.php'); 
	?>
</script>


<script id="zoneTemplate" type="text/template">
	<?php
		$name = "";
		$data = null;
		include('row-zone.php'); 
	?>
</script>

<script id="inreadSetTemplate" type="text/template">
	<?php
		$seek = null;
		$key = null;
		include('row-inread.php'); 
	?>
</script>

<script id="nxAdsData" type="application/json">
{ 
	"name_placeholder": "<?php _e('Name', 'nx-ads') ?>",
	"remove_area" : "<?php _e('Should the zone %name% be removed?', 'nx-ads') ?>",
	"remove_container": "<?php _e('Should the container %name% be removed?', 'nx-ads') ?>"
}
</script>