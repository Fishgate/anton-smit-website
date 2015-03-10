<?php 

add_filter( 'ot_child_theme_mode', '__return_true' );


function toranj_child_latest_enqueue(){
    //child theme stylesheet
    wp_register_style('fishgatej-style', get_stylesheet_directory_uri() . '/css/fishgatej-style.css', array(), '1.0', 'all');
    wp_enqueue_style('fishgatej-style');
	
    //child theme scripts
    wp_register_script('fishgatej-script', get_stylesheet_directory_uri() . '/js/fishgatej-scripts.min.js', '1.0', true);
    wp_enqueue_script('fishgatej-script');

}
add_action("wp_enqueue_scripts", "toranj_child_latest_enqueue", 10000);


/**
 * ----------------------------------------------------------------------------------------
 * Display modified portfolio meta
 * ----------------------------------------------------------------------------------------
 */
function owlab_portfolio_meta($owlabpfl_meta) { 


        //get fields from theme options
        if ( function_exists("ot_get_option")){
                if ( ot_get_option('incr_portfolio_fields') ){
                        $pp_fileds = ot_get_option('incr_portfolio_fields');

                    foreach ($pp_fileds as $f) {

                        if ( !empty($f['title']) and !empty($f["id"]) and array_key_exists("owlabpfl_".$f["id"] , $owlabpfl_meta) ){
                                echo '
                                <li>
                                                <div class="list-label">'.__($f['title'],'toranj').'</div>
                                                <div class="list-des">'.$owlabpfl_meta["owlabpfl_".$f["id"]][0].'</div>
                                        </li>
                                ';
                        }
                    }


                }
        }
        ?>
                <?php if (ot_get_option('portfolio_show_date') == 'on' && !empty($owlabpfl_meta["owlabpfl_date"]) ): ?>
                <li>
                        <!--<div class="list-label"><?php _e('Date','toranj'); ?></div>-->
                        <div class="list-des"><i><?php echo $owlabpfl_meta['owlabpfl_date'][0]; ?></i></div>
                </li>
                <?php endif; ?>
                
                <?php if (!empty($owlabpfl_meta['owlabpfl_dimensions'][0])): ?>
                <li>
                        <!--<div class="list-label"><?php _e('Dimensions','toranj'); ?></div>-->
                        <div class="list-des"><?php echo $owlabpfl_meta['owlabpfl_dimensions'][0]; ?></div>
                </li>
                <?php endif; ?>

                <?php $groups = custom_taxonomies_terms_links('owlabpfl_group'); ?>
                <?php if (ot_get_option('portfolio_show_groups') == 'on'  && !empty($groups) ): ?>
                <li>
                        <div class="list-label"><?php _e('Group','toranj'); ?></div>
                        <div class="list-des"><?php echo $groups; ?></div>
                </li>
                <?php endif; ?>

                <?php $tags = custom_taxonomies_terms_links('label'); ?>
                <?php if (ot_get_option('portfolio_show_tags') == 'on' && !empty($tags) ): ?>
                <li>
                        <div class="list-label"><?php _e('Label','toranj'); ?></div>
                        <div class="list-des"><?php echo $tags; ?></div>
                </li>
                <?php endif; ?>
        <?php

}


/*
 * Shortcode Empty Paragraph Fix
 * http://www.johannheyne.de/wordpress/shortcode-empty-paragraph-fix/
 * 
 */
function shortcode_empty_paragraph_fix( $content ) {
    $array = array (
        '<p>[' => '[',
        ']</p>' => ']',
        ']<br />' => ']'
    );

    $content = strtr( $content, $array );
    
    return $content;
}
add_filter( 'the_content', 'shortcode_empty_paragraph_fix' );

// some custom cropping sizes because eeee
add_image_size('tax-archive-vert-1080p', 350, 1051, array('center', 'center'));
//add_image_size('port-single-right-1080p', 1500, 1500, array('center', 'center'));

