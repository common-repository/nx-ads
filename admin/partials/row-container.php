<tr>
	<?php if ($name !== "sales" && $name !== "privacy" && $name !== "inread"): ?>
		<td>
			<span data-input data-name><?php echo $name ?></span>
		</td>
		<td>
			<div class="dropdown" data-dropdown>
				<div class="dropdown-title" data-dropdown-handle>
					<?php echo sprintf(__('Types (<span data-types>%d</span> &sol; %d)', 'nx-ads' ),
						isset($data['visibility']) ? count($data['visibility']) : 0,
						count($this->post_types) 
					);?>
					
					<span class="dashicons dashicons-edit"></span>
				</div>
				<div class="dropdown-content">
					<?php foreach($this->post_types as $post_type): ?>
						<label>
							<input type="checkbox" name="nx_ads[container][<?php echo $name ?>][visibility][<?php echo $post_type->name ?>]" value="1"  <?php echo checked(1, isset($data['visibility'][$post_type->name]), false) ?>   />
							<?php echo $post_type->label ?>
						</label>        
					<?php endforeach ?>
				</div>
			</div>
		</td>
		<td>
			<label>
				<input class="regular-text" placeholder="#container, .container" type="text" name="nx_ads[container][<?php echo $name ?>][placement][own]" value="<?php echo isset($data['placement']['own']) ? esc_html($data['placement']['own']) : '' ?>" />
				<p class="description">
					<?php _e('ID and or class selectors', 'nx-ads') ?>
				</p>
			</label>
		</td>
	<?php elseif ($name != "inread"): ?>
	<?
		if (isset($inject) && $inject == true)  {
			$name = "%fieldname%";
		}
	?>
		<td>
			<span data-input data-name><?php echo $name ?></span>
		</td>
		<td  colspan="2" class="text-container text-hidden" data-container="<?php echo $name?>">
			<div class="wrap">
				<a href data-show-text><span class="dashicons dashicons-editor-help"></span></a>

				<span data-info-toggle data-text="sales" class="text text-sales">
					<?php echo sprintf(__('This tag serves to deliver the contact details of your marketer %s GmbH & Co. KG. Please insert the shortcode <strong>%%sales%%</strong> in the place of your imprint, where the contact details should be displayed. You can deposit the shortcode directly in the desired page or contribution.', 'nx-ads' ), NX_ADS_FULLBRAND );?>
				</span>
				
				<span data-info-toggle data-text="privacy" class="text text-privacy">
					<?php echo sprintf(__('This tag serves to deliver the privacy policy of your marketer %s GmbH & Co. KG. Please add the shortcode <strong>%%privacy%%</strong> to your privacy policy, where our privacy policy should be displayed. You can deposit the shortcode directly in the desired page or contribution.', 'nx-ads' ), NX_ADS_FULLBRAND );?>
				</span>
				
				<input type="hidden" name="nx_ads[container][<?php echo $name ?>][placement][own]" value="" />
			</div>
		</td>
	<?php else: ?>

	<?php
		if (isset($inject) && $inject == true)  {
			$name = "%fieldname%";
		}
	?>
		<td class="inread">
			<span data-input data-name>inread</span>
		</td>
		<td  colspan="2" class="text-container text-hidden" data-container="inread">
			<div class="wrap">
				<a href data-show-text><span class="dashicons dashicons-editor-help"></span></a>

				<span data-info-toggle data-text="inread" class="text text-inread">
					<?php _e('The inread container can be globally set for certain categories, so as to control the delivery of the ad individually in the article pages. Please select the desired category (Default: All Categories), select the desired selector from the dropdown and enter the number of how many selector the ad should be included.', 'nx-ads' );?>
				</span>
				
				<input type="hidden" name="nx_ads[container][inread][placement][own]" value="" />
			</div>

			<div class="addon addon-inread">
				<h4 data-toggle="true" class=""><?php _e('Entries', 'nx-ads' ) ?></h4>

				<table style="display:none;" id="inreadAddon">
					<colgroup>
						<col width="12%">
						<col width="12%">
						<col width="76%">
					</colgroup>

					<thead>
						<tr>
							<th><?php _e('HTML-Tag', 'nx-ads' ) ?></th>
							<th><?php _e('Position', 'nx-ads' ) ?></th>
							<th><?php _e('Categories', 'nx-ads' ) ?></th>
						</tr>
					</thead>
					<tbody>
						<?php if( isset($data['seek'])):?>
							<?php foreach($data['seek'] as $key => $seek): ?>
								<?php 

									include('row-inread.php');
								?>
							<?php endforeach; ?>

						<?php endif; ?>

					</tbody>
				</table>

				<button style="display:none;" data-add-inread-set class="button">
					<span class="dashicons dashicons-plus dashicons-no-alt"></span> <?php _e('New entry', 'nx-ads' ) ?>
				</button>
			</div>
		</td>
	<?php endif; ?>

	<td class="<?php echo $name == 'inread' ? 'inread':  ''; ?>" data-placeholder>%<?php echo $name ?>%</td>
	<td class="<?php echo $name == 'inread' ? 'inread':  ''; ?>">
		<span  class="dashicons btn-remove dashicons-no-alt"></span>
	</td>
</tr>