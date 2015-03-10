<?php
/**
 * Plugin Name: owwwlab gallery Plugin - For TORANJ
 * Plugin URI:  
 * Description: This is the gallery plugin TORANJ theme. 
 * Author:      owwwlab Web Design Agency
 * Author URI:  http://owwwlab.com
 * Version:     1.1.0
 * Text Domain: owlabgal
 * Domain Path: languages
 *
 * This is not a free software and you can only use it with TORANJ theme.
 *
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * Main plugin class.
 *
 * @since 1.0.0
 *
 * @package owwwlab-gallery
 * @author  owwwlab
 */

 class Owlabgal {

    /**
     * Holds the class object.
     *
     * @since 1.0.0
     *
     * @var object
     */
    public static $instance;

    /**
     * Plugin version used for caching of styles and scripts
     *
     * @since 1.0.0
     *
     * @var string
     */
    public $version = "1.0.0";

    /**
     * The name of the plugin.
     *
     * @since 1.0.0
     *
     * @var string
     */
    public $plugin_name = 'owwwlab Pallery Plugin - For TORANJ';


    /**
     * Unique plugin slug identifier.
     *
     * @since 1.0.0
     *
     * @var string
     */
    public $plugin_slug = 'owwwlab-gallery';

    /**
     * Plugin file.
     *
     * @since 1.0.0
     *
     * @var string
     */
    public $file = __FILE__;

    /**
     * Custom post type name
     *
     * @since 1.0.0
     *
     * @var string
     */
    public $post_type_name = 'owlabgal';

    /**
     * Custom post type slug for permalinks
     *
     * @since 1.0.0
     *
     * @var string
     */
    public $post_type_slug;


    /**
     * Custom taxonomy name
     *
     * @since 1.0.0
     *
     * @var string
     */
    public $custom_taxonomy_name = 'owlabgal_album';

    /**
     * Custom taxonomy slug for permalinks
     *
     * @since 1.0.0
     *
     * @var string
     */
    public $custom_taxonomy_slug;


    
    


    /**
     * Primary class constructor.
     *
     * @since 1.0.0
     */
    public function __construct() {

        //post type slug
        $this->post_type_slug = __('gallery','owlabgal');

        //taxonomy slug
        $this->custom_taxonomy_slug = __('album','owlabgal');

        //upon activation plugin do this
        register_activation_hook( $this->file, array( $this,'my_plugin_activation') );

        //upon deactivation plugin do this
        register_deactivation_hook( $this->file, array ( $this, 'my_plugin_deactivation' ) );

        // Load the plugin textdomain.
        add_action( 'plugins_loaded', array( $this, 'load_plugin_textdomain' ) );

        // Load the plugin.
        add_action( 'init', array( $this, 'init' ), 0 );
    }

    /**
     * plugin activation
     *
     * @since 1.0.0
     * @param      
     * @return 
     */
    public function my_plugin_activation() {
    
        
        
        // register gallery post type
        $this->register_post_type();

        // custom taxonomies
        $this->add_custom_taxonomies();

        // Then flush rewrite rules
        flush_rewrite_rules();
    }

    /**
     * plugin deactivation
     *
     * @since 1.0.0
     * @param      
     * @return 
     */
    public function my_plugin_deactivation() {
        

        flush_rewrite_rules();
    
    }

    /**
     * Loads the plugin textdomain for translation.
     *
     * @since 1.0.0
     */
    public function load_plugin_textdomain() {

        
        $domain = 'owlabgal';
        $locale = apply_filters( 'plugin_locale', get_locale(), $domain );

        load_textdomain( $domain, WP_LANG_DIR . '/' . $domain . '/' . $domain . '-' . $locale . '.mo' );
        load_plugin_textdomain( $domain, false, dirname( plugin_basename( __FILE__ ) ) . '/languages/' );

    }


    /**
     * Loads the plugin into WordPress
     *
     * @since 1.0.0
     * @param      
     * @return 
     */
    public function init() {
    
        // Load admin only components.
        if ( is_admin() ) {

            
        }
        //widget
        require plugin_dir_path( __FILE__ ) . 'gallery-widget.php';

        //include metaboxes, should this be only at admin??
        require plugin_dir_path( __FILE__ ) . 'gallery-metaboxes.php';

        // register gallery post type
        $this->register_post_type();
        
        // custom taxonomies
        $this->add_custom_taxonomies();

        // Adding Custom Meta Fields to Taxonomies
        $this->add_custom_meta_to_album();     


    }

    /**
     * Registers the gallery post type
     *
     * @since 1.0.0
     * @param  null    
     * @return void
     */
    public function register_post_type() {
    
        // Build the labels for the post type.
        $labels = apply_filters( 'owlabgal_post_type_labels',
            array(
                'name'               => __( 'owwwlab Gallery', 'owlabgal' ),
                'singular_name'      => __( 'gallery', 'owlabgal' ),
                'add_new'            => __( 'Add New', 'owlabgal' ),
                'add_new_item'       => __( 'Add New Photo', 'owlabgal' ),
                'edit_item'          => __( 'Edit photo', 'owlabgal' ),
                'new_item'           => __( 'New photo', 'owlabgal' ),
                'view_item'          => __( 'View photo', 'owlabgal' ),
                'search_items'       => __( 'Search Photos', 'owlabgal' ),
                'not_found'          => __( 'No Photo found.', 'owlabgal' ),
                'not_found_in_trash' => __( 'No photos found in trash.', 'owlabgal' ),
                'parent_item_colon'  => '',
                'menu_name'          => __( 'Gallery', 'owlabgal' )
            )
        );

        // Build out the post type arguments.
        $args = apply_filters( 'owlabgal_post_type_args',
            array(
                'labels'              => $labels,
                'public'              => true,
                'publicly_queryable'  => true,
                'exclude_from_search' => false,
                'show_ui'             => true,
                'show_in_nav_menus'   => false,
                'show_in_menu'        => true,
                'show_in_admin_bar'   => true,
                'menu_position'       => 20,
                'menu_icon'           => plugins_url( 'assets/css/images/menu-icon.png', $this->file ),
                'can_export'          => true,
                'delete_with_user'    => false,
                'capability_type'     => 'page',
                'hierarchical'        => false,
                'has_archive'         => 'gallery',
                'query_var'           => $this->post_type_name,
                'rewrite'             => true,
                'supports'            => array( 'title', 'comments','thumbnail'),
                'taxonomies'          => array($this->custom_taxonomy_name)
            )
        );

        // Register the post type with WordPress.
        register_post_type( $this->post_type_name, $args );
        
        $post_type = $this->post_type_name;
        //custom column content
        add_action('manage_posts_custom_column', array($this,'add_custom_columns') );
        //add custom headers
        add_filter('manage_edit-'.$post_type.'_columns', array( $this,'add_new_columns') );

        //make them sortable
        add_filter( 'manage_edit-'.$post_type.'_sortable_columns', array( $this,"sortable_columns") );

        //add filters to custom post type
        add_action('restrict_manage_posts', array($this, 'taxonomy_filter_restrict_manage_posts') );

        //convert id to term
        add_filter('parse_query', array($this,'convert_id_to_term_in_query'));
    }

    /**
     * adds custom columns to browse page at admin
     *
     * @since 1.0.0
     * @param      
     * @return 
     */
    public function add_custom_columns($column) {
    
        global $post;
    
        switch ($column) {
            case 'owlabgal-album' : 
                echo get_the_term_list( $post->ID, $this->custom_taxonomy_name, '', ', ',''); 
                break;

            case 'owlabgal-thumb' :
                //get postmeta
                echo get_the_post_thumbnail( $post->ID, array(100,100) );
                
                break;
            case 'owlabgal-id':
                echo "ID:<code>".$post->ID."</code>";
                $att = wp_get_attachment_image_src( get_post_thumbnail_id($post->ID),'full' );
                echo "<br>".__("Size","toranj").":<code>".$att[1].'x'.$att[2].'px</code>';
                break;

        }
    
    }

    /**
     * Description
     *
     * @since 1.0.0
     * @param      
     * @return 
     */
    public function add_new_columns($columns) {
    
        $columns["owlabgal-thumb"]=__('Thumbnail', 'owlabgal');
        $columns['owlabgal-id']="ID";
        $columns["owlabgal-album"]=__('Album', 'owlabgal');
        $columns["comments"] =__('Comments', 'owlabgal');
        return $columns;
    
    }


    public function sortable_columns() {

      return array(

        'title'             => 'title',
        'owlabgal-album'    => 'owlabgal-album',
        'date'              => 'date',
        'comments'          => 'comments'

      );

    }

    /**
     * adds filter to browse page at admin
     *
     * @since 1.0.0
     * @param      
     * @return 
     */
    public function taxonomy_filter_restrict_manage_posts() {
    
        global $typenow;

        if ($typenow != $this->post_type_name)
            return;
        // If you only want this to work for your specific post type,
        // check for that $type here and then return.
        // This function, if unmodified, will add the dropdown for each
        // post type / taxonomy combination.

        $post_types = get_post_types( array( '_builtin' => false ) );

        if ( in_array( $typenow, $post_types ) ) {
            $filters = get_object_taxonomies( $typenow );

            foreach ( $filters as $tax_slug ) {
                $tax_obj = get_taxonomy( $tax_slug );
                wp_dropdown_categories( array(
                    'show_option_all'   => __('Show All '.$tax_obj->label ),
                    'taxonomy'          => $tax_slug,
                    'name'              => $tax_obj->name,
                    'orderby'           => 'name',
                    'selected'          => isset($_GET[$tax_slug]) ? $_GET[$tax_slug] : '',
                    'hierarchical'      => $tax_obj->hierarchical,
                    'show_count'        => true,
                    'hide_empty'        => true
                ) );
            }
        }
    
    }


    /**
     * Description
     *
     * @since 1.0.0
     * @param      
     * @return 
     */
    public function convert_id_to_term_in_query($query) {
    
        global $pagenow;
        $q_vars = &$query->query_vars;
        if ($pagenow == 'edit.php' && isset($q_vars['post_type']) && $q_vars['post_type'] == $this->post_type_name && isset($q_vars[$this->custom_taxonomy_name]) && is_numeric($q_vars[$this->custom_taxonomy_name]) && $q_vars[$this->custom_taxonomy_name] != 0) {
            $term = get_term_by('id', $q_vars[$this->custom_taxonomy_name], $this->custom_taxonomy_name);
            $q_vars[$this->custom_taxonomy_name] = $term->slug;
        }
    
    }
    /**
     * Adds custom taxonomies for this post types
     *
     * @since 1.0.0
     * @param      
     * @return 
     */
    public function add_custom_taxonomies() {
    
        add_action( 'init', array( $this, 'add_album_taxonomy') );
        

    }



    /**
     * add album taxonomy for plugin post type
     * see additional help here: http://codex.wordpress.org/Function_Reference/register_taxonomy
     *
     * @since 1.0.0
     * @param      
     * @return 
     */
    public function add_album_taxonomy() {
        
        $labels = array(
            'name'                      => __('Albums','owlabgal'), //general name for the taxonomy, usually plural.
            'singular_name'             => __('album','owlabgal'), //name for one object of this taxonomy
            'all_items'                 => __('All albums','owlabgal'),
            'edit_item'                 => __('Edit album','owlabgal'),
            'view_item'                 => __('View album','owlabgal'),
            'update_item'               => __('Update album','owlabgal'),
            'add_new_item'              => __('Add New album','owlabgal'),
            'new_item_name'             => __('New album Name','owlabgal'),
            'parent_item'               => __('Parent album','owlabgal'),
            'parent_item_colon'         => __('Parent album:','owlabgal'),
            'search_items'              => __('Search albums','owlabgal'),
            'popular_items'             => __('Popular albums','owlabgal'),
            'separate_items_with_commas' => __('Separate albums with commas','owlabgal'),
            'add_or_remove_items'       => __('Add or Remove albums','owlabgal'),
            'not_found'                 => __('No albums found','owlabgal')
        );
        register_taxonomy(  
            $this->custom_taxonomy_name,  //The name of the taxonomy. Name should be in slug form 
            $this->post_type_name,        //post type name
            array(  
                'hierarchical' => true,  
                'labels'       => $labels,
                'query_var'    => $this->custom_taxonomy_name,
                'rewrite'      => array(
                                   'slug'       => $this->custom_taxonomy_slug, 
                                   // 'with_front' => true,
                                   // 'hierarchical' => true
                )
            )  
        ); 
    
    }

    
    /**
     * adds custom meta fields for Album taxonomy
     * will need 3 functions
     * 1- adds a field to new page
     * 2- adds a field to edit page
     * 3- save the values of the custom field from both pages
     *
     * @since 1.0.0
     * @param      
     * @return 
     */
    public function add_custom_meta_to_album() {
        
        //styles and scripts
        add_action( 'admin_enqueue_scripts', array( $this, '_add_styles_and_scripts'), 11 );
        

        //add field to new page
        add_action( $this->custom_taxonomy_name.'_add_form_fields', array( $this, '_owlab_add_meta_field_to_taxonimy'), 10, 2 );
        
        // add field to edit page
        add_action( $this->custom_taxonomy_name.'_edit_form_fields', array( $this, '_owlab_edit_meta_field_of_taxonomy'), 10, 2 );

        //save
        add_action( 'edited_'.$this->custom_taxonomy_name, array( $this, '_save_owlab_custom_meta_of_taxonomy'), 10, 2 );  
        add_action( 'create_'.$this->custom_taxonomy_name, array( $this, '_save_owlab_custom_meta_of_taxonomy'), 10, 2 );
    
    }


    /**
     * adds custom styles and scripts
     *
     * @since 1.0.0
     * @param      
     * @return 
     */
    public function _add_styles_and_scripts() {
       
        global $post_type;

        
        if( $this->post_type_name == $post_type ){

            wp_enqueue_media();

            wp_enqueue_script( 'owlabgal-album-upload-admin-js', plugins_url( 'assets/js/album-meta.js', $this->file ), array( 'jquery'), $this->version );
            wp_enqueue_style( 'owlabgal-album-upload-admin-css', plugins_url( 'assets/css/album-meta.css', $this->file ), array(), $this->version );

        }
        
        // If on an owlabkbs post type, add custom CSS for hiding specific things.
        if ( isset( get_current_screen()->post_type ) && 'owlabgal' == get_current_screen()->post_type ) {
            add_action( 'admin_head', array( $this, 'meta_box_css' ) );
        }  
    
    }

    /**
     * Hides unnecessary meta box items on  post type screens.
     *
     * @since 1.0.0
     */
    public function meta_box_css() {

        ?>
        <style type="text/css">.misc-pub-section:not(.misc-pub-post-status) { display: none; }</style>
        <?php

        // Fire action for CSS on owlabkbs post type screens.
        do_action( 'owlabgal_admin_css' );

    }

    /**
     * add new fields for group taxonomy
     *
     * @since 1.0.0
     * @param      
     * @return 
     */
    public function _owlab_add_meta_field_to_taxonimy() {
        
        
        $out = '<div class="form-field">';
        // this will add the custom meta field to the add new term page
        wp_nonce_field( plugin_basename( __FILE__ ), 'owlabgal_media_nonce' );
        
        
        $out .= '<div class="drop_meta_item_group gallery">
            <label for="owlabpfl_group_image">'.__('Choose Cover Image',"owlabgal").'</label>
            <div class="inner_meta">
            <!-- image container -->
            <div class="image-container"></div>
            <!-- end images container -->

            <input type="text" class="meta_field media_field_input" id="owlabpfl_group_image" name="term_meta[owlabgal_album_image]" value="" />
            <input type="button" name="uploader" id="owlabpfl_group_image_btn" class="group_media_uploader_button button button-primary" value="'.__('Select Image' , "owlabgal").'">
            <div class="meta_description"><p>'.__('Choose one image as the cover of this Album.',"owlabgal").'</p></div>
            </div><!-- end inner -->
            </div><!-- end single meta -->';
        
        
        $out .= '<div class="drop_meta_item_group">
            <label for="owlabpfl_layout_type">'.__('Choose Layout type',"owlabgal").'</label>
            <select name="term_meta[owlabgal_layout_type]" id="owlabpfl_layout_type">';
                        
            $out .= $this->_get_layout_types_html();        
        
        $out .='</select>
            <br /><span class="description">'.__('If you want to show Photos items in this Album in a separate page, please select a fron-end layout for it.',"owlabgal").'</span></div>';

        $out .= '<div class="drop_meta_item_group">
            <label for="owlabgal_same_ratio_grid">'.__('Same ratio in grid','owlabgal').'</label>
            <input type="checkbox" name="term_meta[owlabgal_same_ratio_grid]" id="owlabfal_same_ratio_grid">';

        $out .='</div><!-- end form-field -->';       
        echo $out;
        
    
    }


    /**
     * edit fields for group taxonomy
     *
     * @since 1.0.0
     * @param      
     * @return 
     */
    public function _owlab_edit_meta_field_of_taxonomy($term) {

        // put the term ID into a variable
        $t_id = $term->term_id;

        // retrieve the existing value(s) for this meta field. This returns an array
        $term_meta = get_option( "owlab_album_$t_id" );
        
        ?>
        <tr class="form-field">
            <th scope="row" valign="top">
                <label for="owlabpfl_group_image"><?php  _e('Choose Cover Image','owlabgal')?> </label>
            </th>
            <td>
                <div class="drop_meta_item_group gallery">
                    <div class="inner_meta">
                        <!-- image container -->
                        <div class="image-container"></div>
                        <!-- end images container -->

                        <input type="text" class="meta_field media_field_input" id="owlabpfl_group_image" name="term_meta[owlabgal_album_image]" value=<?php echo esc_attr( $term_meta['owlabgal_album_image'] ) ? esc_attr( $term_meta['owlabgal_album_image'] ) : ''; ?> />
                        <input type="button" name="uploader" id="owlabpfl_group_image_btn" class="group_media_uploader_button button button-primary" value="<?php _e('Select Image' , 'owlabgal'); ?>">
                        <div class="meta_description"><p><?php _e('Choose one image as the cover of this Album.','owlabgal'); ?></p></div>
                    </div><!-- end inner -->
                </div>
            </td>
        </tr>

        <tr class="form-field">
            <th scope="row" valign="top">
                <label for="owlabpfl_layout_type"><?php  _e('Choose Layout type','owlabgal')?> </label>
            </th>
            <td>
                <select name="term_meta[owlabgal_layout_type]" id="owlabpfl_layout_type">
                    <?php $selected = esc_attr( $term_meta['owlabgal_layout_type'] ) ? esc_attr( $term_meta['owlabgal_layout_type'] ) : null; ?>
                    <?php echo $this->_get_layout_types_html($selected); ?>
                </select>
                <br/>
                <span class="description">'<?php _e('If you want to show Photo items in this album in a separate page, please select a fron-end layout for it.','owlabgal');?></span>
            </td>
        </tr>
        <tr class="form-field">
            <th scope="row" valign="top">
                <label for="owlabgal_same_ratio_grid"><?php  _e('Same ratio grid','owlabgal')?> </label>
            </th>
            <td>
                <?php $selectedbox=""; ?>
                <?php if (array_key_exists('owlabgal_same_ratio_grid', $term_meta) ):  ?>
                    <?php $selectedbox = $term_meta['owlabgal_same_ratio_grid'] =="on" ? 'checked' : ''; ?>
                <?php endif; ?>
                <input type="checkbox" name="term_meta[owlabgal_same_ratio_grid]" id="owlabgal_same_ratio_grid" <?php echo $selectedbox; ?>>
            </td>
        </tr>
        <?php 
    }


    /**
     * layout types
     *
     * @since 1.0.0
     * @param      
     * @return 
     */
    public function _get_layout_types() {
    
        return array(
            'grid'       => __('Grid','owlabgal'),
            'vertical' => __('Vertical images - Horizaontal scrolling','owlabgal')
        );
    
    }


    /**
     * Print layout types
     *
     * @since 1.0.0
     * @param      
     * @return 
     */
    public function _get_layout_types_html($selected=null) {
    
        $types = $this->_get_layout_types();

        $out = '';
        $i = 0;
        foreach ( $types as $id=>$value){
            $out .= '<option value="'.$id.'" ';
            if ( isset($selected) ){
                if( $selected == $id )
                    $out .= 'selected';
            }else{
                if ($i == 0)
                   $out .= 'selected'; 
            }
            $out .='>'.$value.'</option>';
            $i++;
        }

        return $out;
    
    }

    /**
     * save fields for group taxonomy 
     *
     * @since 1.0.0
     * @param      
     * @return 
     */
    public function _save_owlab_custom_meta_of_taxonomy($term_id) {
        
        //owlabgal_layout_type
        

        if ( isset( $_POST['term_meta'] ) ) {

            if (! array_key_exists('owlabgal_same_ratio_grid', $_POST['term_meta']))
                $_POST['term_meta']['owlabgal_same_ratio_grid'] = 'off';

            $t_id = $term_id;
            $term_meta = get_option( "owlab_album_$t_id" );
            $cat_keys = array_keys( $_POST['term_meta'] );
            foreach ( $cat_keys as $key ) {
                if ( isset ( $_POST['term_meta'][$key] ) ) {
                    $term_meta[$key] = $_POST['term_meta'][$key];
                }
            }
            // Save the option array.
            update_option( "owlab_album_$t_id", $term_meta );
        }
    
    }


    /**
     * Helper flag method for any owlabgal screen.
     *
     * @since 1.2.0
     *
     * @return bool True if on a owlabgal screen, false if not.
     */
    public static function is_plugin_screen() {

        $current_screen = get_current_screen();

        if ( ! $current_screen ) {
            return false;
        }

        if ( $this->post_type_name == $current_screen->post_type ) {
            return true;
        }

        return false;

    }

    /**
     * Helper flag method for the Add/Edit owlabgal screens.
     *
     * @since 1.2.0
     *
     * @return bool True if on a owlabgal Add/Edit screen, false if not.
     */
    public static function is_plugin_add_edit_screen() {

        $current_screen = get_current_screen();

        if ( ! $current_screen ) {
            return false;
        }

        if ( $this->post_type_name == $current_screen->post_type && 'post' == $current_screen->base ) {
            return true;
        }

        return false;

    }





    /**
     * Returns the instance of the class.
     *
     * @since 1.0.0
     *
     * @return object The Owlabgal object.
     */
    public static function get_instance() {

        if ( ! isset( self::$instance ) && ! ( self::$instance instanceof Owlabgal ) ) {
            self::$instance = new Owlabgal();
        }

        return self::$instance;

    }

}

// Load the main plugin class.
$owlabgal = Owlabgal::get_instance();