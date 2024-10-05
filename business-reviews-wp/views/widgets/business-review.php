<p>
	<label for="<?php echo esc_attr( $this->get_field_id( 'title' ) ); ?>"><?php esc_html_e( 'Title:', 'business-reviews-wp' ); ?></label>
	<input class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'title' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'title' ) ); ?>" type="text" value="<?php echo esc_attr( $instance['title'] ); ?>">
</p> 

<p>
	<label for="<?php echo esc_attr( $this->get_field_id( 'shortcode_id' ) ); ?>"><?php esc_html_e( 'Select Shortcode:', 'business-reviews-wp' ); ?></label>
	<select class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'shortcode_id' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'shortcode_id' ) ); ?>">
	<option value=""><?php esc_html_e( 'Select', 'business-reviews-wp' ); ?></option>
    <?php 
		$shortcode_args = new WP_Query(array(
			'post_type' => rtbr()->getPostType(),
		));

		while( $shortcode_args->have_posts() ): $shortcode_args->the_post(); 
			$selected = ( $instance['shortcode_id'] == get_the_ID() ) ? 'selected' : '';
			echo '<option '. $selected .' value="'. get_the_ID() .'">'. get_the_title() .'</option>';
		endwhile; 
		wp_reset_postdata();
	?>
	</select>
</p>