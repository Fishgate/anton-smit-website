<?php
/**
 *  Archive Horizontal template page for portfolio
 * 
 * @package Toranj
 * @author owwwlab
 */
 
?>

<!-- Page main wrapper -->
<div id="main-content" class="dark-template">
	<div class="page-wrapper">
		
		<!-- Sidebar -->
		<div class="page-side ajax-element">
			<div class="inner-wrapper vcenter-wrapper">
				<div class="side-content vcenter">
					<div class="title">
						<span class="second-part"><?php echo ot_get_option('portfolio_group_upper_title',__('Browse Group','toranj')); ?></span>
						<span><?php echo $the_group->name; ?></span>
					</div>
					
					<p><?php echo wpautop($the_group->description); ?></p>

					<?php if (count($the_group_childs) >0 ): ?>
					
						<h5 class="lined"><?php _e('Sub Groups','toranj'); ?></h5>
						
						<ul class="list list-unstyled">
						<?php foreach ($the_group_childs as $child) :?>
							<li><a href="<?php echo get_term_link( $child->term_id, $child->taxonomy ); ?>"><?php echo $child->name ?></a></li>
						<?php endforeach; ?>
						</ul>	
					
					<?php endif; ?>
					
				</div>
			</div>
		</div>
		<!-- /Sidebar -->

		<!-- Main Content -->
		<div class="page-main ajax-element">
			<!-- Portflio wrapper -->	
			<div class="ajax-folio vertical-folio">
				
				
				
				<?php if ( have_posts() ) : while( have_posts() ) : the_post(); ?>

				<?php 
				$owlabpfl_meta = get_post_meta( $id ); 
				$img_url = wp_get_attachment_image_src( get_post_thumbnail_id($id) , 'large');
				$owlab_load = "i";
	            if(!empty($owlabpfl_meta["owlabpfl_use_video"]) && $owlabpfl_meta["owlabpfl_use_video"][0]=='on'){
	            	$owlab_load = "v";
	            }
				?>

				<!-- Portflio Item -->		
				<div class="vf-item<?php if($owlab_load == "i"){echo " set-bg";}?> tj-hover-3 <?php if (ot_get_option('portfolio_horizontal_animate') == "on"): ?>inview-animate inview-fadeleft<?php endif; ?>">
					<a href="<?php the_permalink(); ?>" class="ajax-portfolio">
						<?php if( $owlab_load =="v" ): ?>
							<?php owlab_video_background($owlabpfl_meta, $img_url) ?>
								
						<?php else: ?>
							<?php owlab_lazy_image($img_url,get_the_title()); ?>
						<?php endif; ?>
						

						<!-- Item Overlay -->	
						<div class="tj-overlay vcenter-wrapper">
							<div class="overlay-texts vcenter">
								<h3 class="title"><?php the_title(); ?></h3>
								<h4 class="subtitle"><?php echo array_key_exists('owlabpfl_short_des', $owlabpfl_meta) ? $owlabpfl_meta['owlabpfl_short_des'][0] : ''; ?></h4>
							</div>
						</div>
						<!-- /Item Overlay -->	
					</a>
				</div>
				<!-- /Portflio Item -->	
				<?php endwhile; else: ?>
					<?php _e('No items found.','toranj'); ?>
				<?php endif; ?>
				
			</div>
			<!-- /Portflio wrapper -->	
		</div>
		<!-- /Main Content -->

		
		<!--Ajax folio-->
		<div id="ajax-folio-loader">
			<!-- loading css3 -->
			<div id="followingBallsG">
				<div id="followingBallsG_1" class="followingBallsG">
				</div>
				<div id="followingBallsG_2" class="followingBallsG">
				</div>
				<div id="followingBallsG_3" class="followingBallsG">
				</div>
				<div id="followingBallsG_4" class="followingBallsG">
				</div>
			</div>
		</div>
		<div id="ajax-folio-item"></div>
		<!--Ajax folio-->
	</div>
</div>
<!-- /Page main wrapper -->


