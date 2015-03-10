<?php
/**
 *  Single template page for portfolio - regular light
 * 
 * @package Toranj
 * @author owwwlab
 */
?>

<div id="main-content"> 
	<div class="page-wrapper regular-page">
		<div class="container">

			<div class="mb-large"></div>
			<!-- page title -->
			<h2 class="section-title double-title">
				<?php the_title(); ?>
			</h2>
			<!--/ page title -->


			<div class="row mb-large">
				
				<div class="col-md-9">
					<?php $img_url = wp_get_attachment_url( get_post_thumbnail_id($post->ID) , 'large' ); ?>
					<img src="<?php echo $img_url ?>" alt="<?php the_title(); ?>" class="img-fit" >
				</div>
				<div class="col-md-3">
					<h3 class="bordered">Details</h3>
					<ul class="list-items">
						<?php owlab_portfolio_meta($owlabpfl_meta); ?>
					</ul>
					<div>
						<?php echo array_key_exists('owlabpfl_side_des', $owlabpfl_meta) ? $owlabpfl_meta["owlabpfl_side_des"][0] : ''; ?>
					</div>
				</div>

			</div>

			<div class="row mb-large">

				<div class="col-md-12">
					<?php the_content(); ?>
				</div>

				
			</div>

			<?php owlab_portfolio_regular_nav(); ?>
			<hr>
			<a class="back-to-top" href="#"></a>
		</div>
	</div>
</div>