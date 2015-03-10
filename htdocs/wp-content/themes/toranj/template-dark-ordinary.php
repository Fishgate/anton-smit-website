<?php 
/**
 * Template Name: Dark Normal Page
 *
 * The template to display Dark page template.
 *
 * @author owwwlab
 */
?>

<?php get_header(); ?>

<!--Page main wrapper-->
<div id="main-content" class="dark-template"> 
	<div class="page-wrapper padding-top">
		

		<!-- Page main content -->	
		<div class="page-main no-side">
			<div class="container">
			<?php while( have_posts() ) : the_post(); ?>
				<?php the_content(); ?>
			<?php endwhile; ?>
			</div>
		</div>


	</div>
</div>
<!--/Page main wrapper-->

<?php get_footer(); ?>