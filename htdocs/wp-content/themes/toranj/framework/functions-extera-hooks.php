<?php
/**
 *  Extera hooks for filters and actions for the theme
 * 
 * @package toranj
 * @author owwwlab
 */

/**
 * ----------------------------------------------------------------------------------------
 * disable and enable wordpress featurs
 * ----------------------------------------------------------------------------------------
 */

// Hide admin bar
//add_filter('show_admin_bar', '__return_false');



/**
 * ----------------------------------------------------------------------------------------
 * Process Page titles and make them double lined based on vertical line
 * ----------------------------------------------------------------------------------------
 */

function owlab_split_title($title, $id) {

	if ( is_admin() )
		return $title;
	
    $separator = htmlentities('|');
    $lines = explode( $separator , $title);
    if (count($lines)>1){

    	$first_part = $lines[0];
	    $the_rest="";
	    foreach($lines as $key=>$value){
	    	if ($key != 0){
	    		$the_rest.=$value;
	    	}
	    }
	    
    	if (ot_get_option('blog_index_layout')=='minimal' && is_home()){
    		return "$first_part<span>$the_rest</span>";
    	}else{
			return "<span class='second-part'>$first_part</span>$the_rest";
    	}
	    
	}
	return $title;
}
add_filter('the_title', 'owlab_split_title', 10, 2);



/**
 * ----------------------------------------------------------------------------------------
 * Add classes to prev and next posts links
 * ----------------------------------------------------------------------------------------
 */

add_filter('next_posts_link_attributes', 'owlab_posts_link_attributes_prev');
add_filter('previous_posts_link_attributes', 'owlab_posts_link_attributes_next');

function owlab_posts_link_attributes_prev() {
    return 'class="prev-post btn btn-lg btn-simple pull-right"';
}
function owlab_posts_link_attributes_next() {
    return 'class="prev-post btn btn-lg btn-simple"';
}




/**
 * ----------------------------------------------------------------------------------------
 * Change post thumbnail image marckup
 * ----------------------------------------------------------------------------------------
 */
if ( ! function_exists( 'owlab_remove_width_attribute' ) ) {
	function owlab_remove_width_attribute( $html ){
		$html = preg_replace( '/(width|height)="\d*"\s/', "", $html );
   		return $html;
	}
	add_filter( 'post_thumbnail_html', 'owlab_remove_width_attribute', 10 );
	add_filter( 'image_send_to_editor', 'owlab_remove_width_attribute', 10 );
}


/**
 * ----------------------------------------------------------------------------------------
 * add body classes for fixed and dark sidebar
 * ----------------------------------------------------------------------------------------
 */
if ( ! function_exists('owlab_body_class_add') ){
	function owlab_body_class_add($classes){
		
		if ( !function_exists('ot_get_option'))
			return $classes;

		if ( ot_get_option('dark_sidebar') == 'on' ){
			$classes[] = 'dark-sidebar';
		}

		if ( ot_get_option('fixed_sidebar') == 'on' ){
			$classes[] = 'show-sidebar';
		}
		
		return $classes;
	}
	add_action( 'body_class', 'owlab_body_class_add'); 

}



/**
 * ----------------------------------------------------------------------------------------
 * we want to get all the posts
 * ----------------------------------------------------------------------------------------
 */

if ( ! function_exists('owlab_custom_get_posts') ){

	add_filter( 'pre_get_posts', 'owlab_custom_get_posts' );

	function owlab_custom_get_posts( $query ) {

		$post_types = array('owlabgal','owlabpfl');
		
		if ( ! is_admin() ){
			if( is_tax( 'owlabgal_album' ) || is_post_type_archive( $post_types ) || is_tax("owlabpfl_group") ) { 

				$query->query_vars['posts_per_page'] = -1;
				//$query->query_vars['orderby'] = 'menu_order';
				//$query->query_vars['order'] = 'ASC';

			}
		}

	    return $query;
	}
}

/**
 * ----------------------------------------------------------------------------------------
 * Add logout link to woocommerce menu
 * ----------------------------------------------------------------------------------------
 */
function owlab_add_login_logout_link( $items, $args  ) {
	if( $args->theme_location == 'woocommerce-menu-logged-in' ) {
	        $loginoutlink = wp_loginout('index.php', false);
	        $items .= '<li>'. $loginoutlink .'</li>';
			return $items;
	    }
	    return $items;
}
add_filter( 'wp_nav_menu_items', 'owlab_add_login_logout_link', 10, 2 );

/**
 * ----------------------------------------------------------------------------------------
 * add <span> to categories widget
 * ----------------------------------------------------------------------------------------
 */

function owlab_add_span_cat_count($links) {
	$links = str_replace('</a> (', '</a> <span>(', $links);
	$links = str_replace(')', ')</span>', $links);
	return $links;
}
add_filter('wp_list_categories', 'owlab_add_span_cat_count');


function owlab_archive_count_no_brackets($links) {
$links = str_replace('</a>&nbsp;(', '</a><span>(', $links);
$links = str_replace(')', ')</span>', $links);
return $links;
}
add_filter('get_archives_link', 'owlab_archive_count_no_brackets');

