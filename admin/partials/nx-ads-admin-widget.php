<p>
	<label for="<?php echo $this->get_field_id('container_name'); ?>">
		<?php _e( 'Container:', 'nx-ads') ?>
	</label>
	<select  required name="<?php echo $this->get_field_name('container_name'); ?>" id="<?php echo $this->get_field_id('container_name'); ?>">
		<option value=""><?php _e('Please choose', 'nx-ads') ?></option>

		<?php foreach($container as $name => $data): 
			$selected = $container_name === $name ? 'selected' : '' ;
		?>
			<option value="<?php echo $name ?>" <?php echo $selected ?>><?php echo $name ?></option>
		<?php endforeach ?>
	</select>
</p>