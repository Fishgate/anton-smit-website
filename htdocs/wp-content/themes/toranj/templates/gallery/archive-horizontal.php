<?php
/**
 *  Archive Horizontal template page for gallery
 * 
 * @package Toranj
 * @author owwwlab
 */
 
?>


<!-- Page main wrapper -->
		<div id="main-content" class="abs dark-template">
			<div class="page-wrapper">
				
				<!-- Page sidebar -->
				<div class="page-side">
					<div class="inner-wrapper vcenter-wrapper">
						<div class="side-content vcenter">

							<!-- Page title -->
							<h1 class="title">
								<span class="second-part"><?php echo ot_get_option('gallery_title_1'); ?></span>
								<span><?php echo ot_get_option('gallery_title_2'); ?></span>
							</h1>
							<!-- /Page title -->

							<div class="hidden-sm hidden-xs">
								<?php echo ot_get_option('gallery_side_content'); ?>
							</div>
							
						</div>
					</div>
				</div>
				<!-- /Page sidebar -->

				<!-- Page main content -->
				<div class="page-main horizontal-folio-wrapper set-height-mobile tj-lightbox-gallery">

					<!-- Portfolio wrapper -->	
					<div class="horizontal-folio">
						
						<?php if ( have_posts() ) : while( have_posts() ) : the_post(); ?>

							<?php $owlabgal_meta = get_post_meta( $id ); ?>
							<?php $item_overlay = owlab_get_gallery_overlay(ot_get_option("gallery_index_overlay_type"));

							$thumb_url = wp_get_attachment_image_src( get_post_thumbnail_id($post->ID), 'blog-thumb' );
						 	// [0] => url
							// [1] => width
							// [2] => height
							// [3] => boolean: true if $url is a resized image, false if it is the original.

						 	$img_url = wp_get_attachment_url( get_post_thumbnail_id($post->ID) );
						 	?>
							<!-- Portfolio Item -->		
							<div class="gp-item <?php echo $item_overlay['parent_class']; ?>">
								<a href="<?php echo $img_url; ?>" class="lightbox-gallery-item set-bg" title="<?php the_title(); ?><?php echo (array_key_exists('owlabgal_short_des', $owlabgal_meta) ) ? " | ".$owlabgal_meta['owlabgal_short_des'][0] : ''; ?>">
									<img src="<?php echo $thumb_url[0]; ?>" alt="<?php the_title(); ?>" class="img-responsive">

									<?php echo $item_overlay['markup']; ?>	
								</a>
							</div>
							<!-- /Portfolio Item -->

						<?php endwhile; else: ?>
							<?php _e('No items found.','toranj'); ?>
						<?php endif; ?>						
							
	
					</div>
					<!-- /Portfolio wrapper -->	
				</div>
				<!-- Page main content -->
			</div>
		</div>
		<!-- /Page main wrapper -->


