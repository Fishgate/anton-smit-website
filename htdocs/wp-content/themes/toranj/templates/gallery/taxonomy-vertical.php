<?php
/**
 *  Album term vertical scroll view
 * 
 * @package TORANJ
 * @author owwwlab ( Alireza Jahandideh & Ehsan Dalvand @owwwlab )
 */

?>


<!-- Page main wrapper -->
<div id="main-content" class="abs dark-template">
	<div class="page-wrapper">
		
		<!-- Sidebar -->
		<div class="page-side">
			<div class="inner-wrapper vcenter-wrapper">
				<div class="side-content vcenter">
					<div class="title">
						<span class="second-part"><?php _e('Browse Album','toranj'); ?></span>
						<span><?php echo $the_album->name; ?></span>
					</div>
					
					<p><?php echo $the_album->description; ?></p>
					
					<?php if (count($the_album_childs) >0 ): ?>
					
						<h5 class="lined"><?php _e('Sub Albums','toranj'); ?></h5>
						
						<ul class="list list-unstyled">
						<?php foreach ($the_album_childs as $child) :?>
							<li><a href="<?php echo get_term_link( $child->term_id, $child->taxonomy ); ?>"><?php echo $child->name ?></a></li>
						<?php endforeach; ?>
						</ul>	
					
					<?php endif; ?>

				</div>
			</div>
		</div>
		<!-- /Sidebar -->

		<!-- Main Content -->
		<div class="page-main horizontal-folio-wrapper set-height-mobile tj-lightbox-gallery">
			<!-- Portflio wrapper -->	
			<div class="horizontal-folio ">
				
				<?php if ( have_posts() ) : while( have_posts() ) : the_post(); ?>

				<?php 
					$owlabgal_meta = get_post_meta( $post->ID );


				 	$the_terms = wp_get_post_terms( $post->ID, 'owlabgal_album', array('fileds' => 'all') ); 
				 	//d($the_terms);
				 	$this_terms =array();
				 	if (is_array($the_terms)){
					 	foreach($the_terms as $term){
					 		$this_terms[]= $term->slug;
					 	}
				 	}
				 	$album_terms = implode(' ', $this_terms);

				 	$item_overlay = owlab_get_gallery_overlay("circle");
				 	
				 	$thumb_url = wp_get_attachment_image_src( get_post_thumbnail_id($post->ID), 'large' );
				 	// [0] => url
					// [1] => width
					// [2] => height
					// [3] => boolean: true if $url is a resized image, false if it is the original.

				 	$img_url = wp_get_attachment_url( get_post_thumbnail_id($post->ID) );

				?>

				<div class="gp-item <?php echo $item_overlay['parent_class']; ?>">
                    <a href="<?php echo $img_url; ?>" class="lightbox-gallery-item set-bg" title="<?php the_title(); ?>">
                        <img src="<?php echo $thumb_url[0]; ?>" alt="<?php the_title(); ?>'" class="img-responsive">
						<?php echo $item_overlay['markup']; ?>  
                    </a>
                </div>
				
				<?php endwhile; else: ?>
					<?php _e('No items found.','toranj'); ?>
				<?php endif; ?>

					
			</div>
			<!-- /Portflio wrapper -->	
		</div>
		<!-- /Main Content -->

	</div>
</div>
<!-- /Page main wrapper -->
