<tr <?php echo empty($name) ? 'data-init' : ''; ?>>
	<td>
		<span data-zone-id><?php echo isset($data['id']) ? $data['id']: '' ?></span>
	</td>
	<td>
		<label>
			<input data-zone-name data-field="nx_ads[zone][%fieldname%]" placeholder="<?php _e('Zone Name', 'nx-ads')  ?>" type="text"  value="<?php echo $name ?>" required />
		</label>
	</td>
	<td>
		<label>
			<input data-zone-desc data-field="nx_ads[zone][%fieldname%][description]" class="regular-text" placeholder="<?php _e('A short description', 'nx-ads')  ?>" type="text" name="nx_ads[zone][<?php echo $name ?>][description]" value="<?php echo isset($data['description']) ? esc_html($data['description']) : '' ?>"  />
		</label>
	</td>
	<td>
		<span  class="dashicons btn-remove dashicons-no-alt"></span>
		<input data-zone-id data-field="nx_ads[zone][%fieldname%][id]" name="nx_ads[zone][<?php echo $name ?>][id]" value="<?php echo isset($data['id']) ? $data['id']: '' ?>"  type="hidden" />
	</td>
</tr>