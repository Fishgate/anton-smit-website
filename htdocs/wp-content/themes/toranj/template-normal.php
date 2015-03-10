<?php 
/**
 * Template Name: Normal Page
 *
 * The template for displaying all regular pages.
 */
?>

<?php get_header(); ?>

<!--Page main wrapper-->
<div id="main-content"> 
	<div class="page-wrapper regular-page">
		<div class="container">

			<?php while( have_posts() ) : the_post(); ?>

				<?php the_content(); ?>

				<?php wp_link_pages(); ?>

				<?php //comments_template(); ?>
			
			<?php endwhile; ?>
			<hr/>
			<a class="back-to-top" href="#"></a>
			<div class="clearfix"></div>
		</div>
	</div>
</div>
<!--/Page main wrapper-->

<?php get_footer(); ?>