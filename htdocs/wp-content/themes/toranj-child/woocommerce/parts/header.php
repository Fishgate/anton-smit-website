
<div class="container">
	<div class="custom-grid-row shop-menus">
		<!-- shop breadcrumb -->
		<div class="shop-breadcrumb">
			<!-- breadcrumbs -->
			<ol class="breadcrumb">
				<?php if(function_exists('woocommerce_breadcrumb')) woocommerce_breadcrumb(); ?>
			</ol>
			<!--/ breadcrumbs -->
		</div>	
		<!-- /shop breadcrumb -->

	</div>
	
	<div class="custom-grid-row">
		
		<div class="left-side">
			<?php if ( apply_filters( 'woocommerce_show_page_title', true ) ) : ?>

				<!-- page title -->	
				<h2 class="page-title section-title double-title">
					<?php woocommerce_page_title(); ?>
				</h2>
				<!--/ page title -->

			<?php endif; ?>
			<div calss="archive-description">
				<?php do_action( 'woocommerce_archive_description' ); ?>
			</div>
		</div>
		
		
		<div id="shop-cart">
			<h4 class="shop-cart-title"><span><?php _e('Shopping cart', 'toranj'); ?></span></h4>
			<a class="cart-contents" href="<?php echo $woocommerce->cart->get_cart_url(); ?>" title="<?php _e('View your shopping cart', 'toranj'); ?>">  
				<?php echo sprintf(_n('%d item', '%d items', $woocommerce->cart->cart_contents_count, 'toranj'), $woocommerce->cart->cart_contents_count);?> - <?php echo $woocommerce->cart->get_cart_total(); ?>
			</a>

		</div>
	</div>
</div>