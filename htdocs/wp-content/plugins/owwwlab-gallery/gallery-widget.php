<?php
/**
 *  widget to show gallery images
 * 
 * @package Toranj
 * @author owwwlab (Alireza Jahandideh & Ehsan Dalvand @owwwlab)
 */

class owlab_gallery_widget extends WP_Widget {

	

	function owlab_gallery_widget() {
		$widget_ops = array( 'classname' => 'owlab_gallery_widget', 'description' => __( 'Gallery widget for your sidebar', 'owlabgal' ) );
		
		$control_ops = array( 'id_base' => 'owlab_gallery_widget' );
		
		$this->WP_Widget( 'owlab_gallery_widget', __('Latest shots from gallery by Toranj', 'owlabgal'), $widget_ops, $control_ops );
	}


	/**
	 * ----------------------------------------------------------------------------------------
	 * Creating widget front-end
	 * ----------------------------------------------------------------------------------------
	 */
	public function widget( $args, $instance ) {
		
		extract( $args );

		//Our variables from the widget settings.
		$title = apply_filters('widget_title', $instance['title'] );
		$limit = $instance['limit'];
		$album = $instance['album'];


		echo $before_widget;

		// Display the widget title 
		if ( $title )
			echo $before_title . $title . $after_title;

		//Display the gallery 
		if ( !$limit )
			$limit =8;



		// WP_Query arguments
		$args = array (
			'post_type'              => 'owlabgal',
			'post_status'            => 'publish',
			'pagination'             => false,
			'posts_per_page'         => $limit,
			'ignore_sticky_posts'    => false,
			'order'                  => 'DESC',
			'orderby'                => 'date',
			'cache_results'          => true,
			'update_post_meta_cache' => true,
			'update_post_term_cache' => true,
		);

		// add taxonomy to query if there is one
        if ( !empty ($album) ){

            $args['owlabgal_album'] = trim($album);
        }

		// The Query
		$query = new WP_Query( $args );

		// The Loop
		if ( $query->have_posts() ) {
			
			echo '<div class="tj-lightbox-gallery gallery-widget">';

			while ( $query->have_posts() ) {
				$query->the_post();
				
				$owlabgal_meta = get_post_meta( $query->post->ID ); 
            	$thumb_url = wp_get_attachment_image_src( get_post_thumbnail_id($query->post->ID), 'thumbnail' );
            	$img_url = wp_get_attachment_url( get_post_thumbnail_id($query->post->ID) );
				?>
					<!--Gallery item-->
					<a href="<?php echo $img_url; ?>" class="lightbox-gallery-item gallery-item tj-hover-4" title="<?php the_title(); ?>">
						<img src="<?php echo $thumb_url[0] ?>" class="img-fit" alt="<?php the_title(); ?>">
						<div class="tj-overlay">
						</div>
					</a>
					<!--/Gallery item-->
				
				<?php



			}
			echo '</div> <!-- /.tj-lightbox-gallery --> <div class="clearfix"></div>';

		} else {
			// no posts found

		}

		// Restore original Post Data
		wp_reset_postdata();

		//after widget markup
		echo $after_widget;

	}
		
	/**
	 * ----------------------------------------------------------------------------------------
	 * Widget Backend 
	 * ----------------------------------------------------------------------------------------
	 */
	public function form( $instance ) {
		
		//Set up some default widget settings.
		$defaults = array( 'title' => __('Latest shopts', 'owlabpfl'), 'limit' => 8, 'album' => '' );
		$instance = wp_parse_args( (array) $instance, $defaults );

		// Widget admin form
		?>
		<p>
		<label for="<?php echo $this->get_field_id( 'title' ); ?>"><?php _e( 'Title:' ); ?></label> 
		<input class="widefat" id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" type="text" value="<?php echo $instance['title'];  ?>" />
		</p>
		<p>
		<label for="<?php echo $this->get_field_id( 'limit' ); ?>"><?php _e( 'Number of photos:' ); ?></label> 
		<input class="widefat" id="<?php echo $this->get_field_id( 'limit' ); ?>" name="<?php echo $this->get_field_name( 'limit' ); ?>" type="text" value="<?php echo $instance['limit']; ?>" />
		</p>
		<p>
		<label for="<?php echo $this->get_field_id( 'album' ); ?>"><?php _e( 'Album slug:' ); ?></label> 
		<input class="widefat" id="<?php echo $this->get_field_id( 'album' ); ?>" name="<?php echo $this->get_field_name( 'album' ); ?>" type="text" value="<?php echo $instance['album']; ?>" />
		<br/>Get the slug from <a href="<?php echo site_url(); ?>/wp-admin/edit-tags.php?taxonomy=owlabgal_album&post_type=owlabgal"> here.</a>
		<br/>leave blank to use all photos
		</p>
		<?php 
	}
	
	/**
	 * ----------------------------------------------------------------------------------------
	 *  Updating widget replacing old instances with new
	 * ----------------------------------------------------------------------------------------
	 */
	public function update( $new_instance, $old_instance ) {
		$instance = $old_instance;

		//Strip tags from title and post_limits to remove HTML 
		$instance['title'] = strip_tags( $new_instance['title'] );
		$instance['limit'] = strip_tags( $new_instance['limit'] );
		$instance['album'] = strip_tags( $new_instance['album'] );

		return $instance;
	}

} // Class owlab_gallery_widget ends here

/**
 * ----------------------------------------------------------------------------------------
 * Register and load the widget
 * ----------------------------------------------------------------------------------------
 */
function owlab_gallery_load_widget() {
	register_widget( 'owlab_gallery_widget' );
}
add_action( 'widgets_init', 'owlab_gallery_load_widget' );


