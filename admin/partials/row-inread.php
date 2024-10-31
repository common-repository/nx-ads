<tr>
	<td>
		<select <?php echo isset($key) ? 'name' : 'data-field' ?>="nx_ads[container][inread][seek][<?php echo isset($key) ? $key : '%fieldname%' ?>][tag]">
			<?php foreach($this->inread_tags as $tag): ?>
				<option value="<?php echo $tag ?>" <?php echo isset($seek['tag']) && $seek['tag'] === $tag ? 'selected="selected"' : ''; ?>>
					<?php echo strtoupper($tag); ?>
				</option>
			<?php endforeach; ?>
		</select>
	</td>
	<td>
		<input type="number" <?php echo isset($key) ? 'name' : 'data-field' ?>="nx_ads[container][inread][seek][<?php echo isset($key) ? $key : '%fieldname%' ?>][position]" value="<?php echo isset($seek['position']) ? $seek['position']: '1' ?>" >
	</td>
	<td>
	<?php 
		$categories = get_terms( 'category', "orderby=id&hide_empty=0" );

		if (!empty( $categories ) && ! is_wp_error( $categories)):
	?>
			<div class="dropdown" data-dropdown>
				<div class="dropdown-title" data-dropdown-handle>
					<span class="entries" data-entries="true">
						<?php if(isset($seek['categories']) && count($seek['categories']) > 0 ): ?>
							<?php foreach ( $categories as $category ): ?>
								<?php if(isset($seek['categories'][$category->term_id])): ?>
									<span class="entry"><?php echo $category->name ?></span>     
								<?php endif; ?>   
							<?php endforeach ?>
						<?php endif; ?>
					</span>
					
					<span data-dropdown-choose class="<?php echo isset($seek['categories']) && count($seek['categories']) > 0  ? "hidden":""  ?>"><?php _e('All categories', 'nx-ads' ) ?></span>

					<span class="dashicons dashicons-edit"></span>
				</div>
				<div class="dropdown-content">
					<?php foreach ( $categories as $category ): ?>
						<label>
							<input type="checkbox" <?php echo isset($key) ? 'name' : 'data-field' ?>="nx_ads[container][inread][seek][<?php echo isset($key) ? $key : '%fieldname%' ?>][categories][<?php echo $category->term_id ?>]" value="1"  <?php echo checked(1, isset($seek['categories'][$category->term_id]), false) ?>   />
							<?php echo $category->name ?>
						</label>        
					<?php endforeach ?>
				</div>
			</div>
	<?php
		endif;
	?>
	</td>
	<td>
		<span  class="dashicons btn-remove dashicons-no-alt"></span>
	</td>
</tr>