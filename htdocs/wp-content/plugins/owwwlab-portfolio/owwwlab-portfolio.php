<?php
/**
 * Plugin Name: owwwlab Portfolio Plugin - For TORANJ
 * Plugin URI:  
 * Description: This is the Portfolio plugin TORANJ theme. 
 * Author:      owwwlab Web Design Agency
 * Author URI:  http://owwwlab.com
 * Version:     1.1.0
 * Text Domain: owlabpfl
 * Domain Path: /languages/
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
 * @package owwwlab-portfolio
 * @author  owwwlab
 */

 class Owlabpfl {

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
    public $plugin_name = 'owwwlab Portfolio Plugin - For TORANJ';


    /**
     * Unique plugin slug identifier.
     *
     * @since 1.0.0
     *
     * @var string
     */
    public $plugin_slug = 'owwwlab-portfolio';

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
    public $post_type_name = 'owlabpfl';

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
    public $custom_taxonomy_name = 'owlabpfl_group';

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
        $this->post_type_slug = __('portfolio','owlabpfl');

        //taxonomy slug
        $this->custom_taxonomy_slug = __('portfoliogroup','owlabpfl');

        //upon activation plugin do this
        register_activation_hook( $this->file, array( $this,'my_plugin_activation') );

        //upon deactivation plugin do this
        register_deactivation_hook( $this->file, array ( $this, 'my_plugin_deactivation' ) );

        

        // Load the plugin.
        add_action( 'init', array( $this, 'init' ), 0 );

        // Load the plugin textdomain.
        add_action( 'init', array( $this, 'load_plugin_textdomain' ) );
    }

    /**
     * plugin activation
     *
     * @since 1.0.0
     * @param      
     * @return 
     */
    public function my_plugin_activation() {

        // register portfolio post type
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

        
        $domain = 'owlabpfl';
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

        //include metaboxes, should this be only at admin??
        require plugin_dir_path( __FILE__ ) . 'portfolio-metaboxes.php';

        // register portfolio post type
        $this->register_post_type();
        
        // custom taxonomies
        $this->add_custom_taxonomies();

        // Adding Custom Meta Fields to Taxonomies
        $this->add_custom_meta_to_groups();       

    }

    /**
     * Registers the portfolio post type
     *
     * @since 1.0.0
     * @param  null    
     * @return void
     */
    public function register_post_type() {
    
        // Build the labels for the post type.
        $labels = apply_filters( 'owlabpfl_post_type_labels',
            array(
                'name'               => __( 'owwwlab portfolios', 'owlabpfl' ),
                'singular_name'      => __( 'Portfolio', 'owlabpfl' ),
                'add_new'            => __( 'Add New', 'owlabpfl' ),
                'add_new_item'       => __( 'Add New Portfolio', 'owlabpfl' ),
                'edit_item'          => __( 'Edit Portfolio', 'owlabpfl' ),
                'new_item'           => __( 'New portfolio', 'owlabpfl' ),
                'view_item'          => __( 'View portfolio', 'owlabpfl' ),
                'search_items'       => __( 'Search portfolios', 'owlabpfl' ),
                'not_found'          => __( 'No portfolios found.', 'owlabpfl' ),
                'not_found_in_trash' => __( 'No portfolios found in trash.', 'owlabpfl' ),
                'parent_item_colon'  => '',
                'menu_name'          => __( 'Portfolio', 'owlabpfl' )
            )
        );

        // Build out the post type arguments.
        $args = apply_filters( 'owlabpfl_post_type_args',
            array(
                'labels'              => $labels,
                'public'              => true,
                'exclude_from_search' => false,
                'show_ui'             => true,
                'show_in_admin_bar'   => true,
                'rewrite'             => array(
                                            'slug' => $this->post_type_slug,
                                            'with_front' => true,
                                            'feeds' => true,
                                            'pages' => true
                                        ),
                'query_var'           => $this->post_type_name,
                'menu_position'       => 20,
                'menu_icon'           => plugins_url( 'assets/css/images/menu-icon.png', $this->file ),
                'supports'            => array( 'title', 'editor', 'revisions', 'thumbnail' ),
                'has_archive'         => true,
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
            case 'owlabpfl-group' : 
                echo get_the_term_list( $post->ID, $this->custom_taxonomy_name, '', ', ',''); 
                break;

            case 'owlabpfl-thumb' :
                //get postmeta
                echo get_the_post_thumbnail( $post->ID, array(100,100) );
                break;
            case 'menu_order':
                $order = $post->menu_order;
                echo $order;
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
    
        $columns = $columns + array(
            'cb'                => '<input type="checkbox">',
            'owlabpfl-thumb'    => __('Thumbnail', 'owlabpfl'),
            'title'             => __('Title'),
            'owlabpfl-group'    => __('Group', 'pwlabpfl'),
            //'menu_order'        => __('Order'),
            'date'              => __('Date') 
        );
        return $columns;
    
    }

    public function sortable_columns() {

      return array(

        'title'             => 'title',
        'owlabpfl-group'    => 'owlabgal-album',
        'date'              => 'date',
        'comments'          => 'comments',
        //'menu_order'        => 'menu_order'

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

        if ($pagenow == 'edit.php' && isset($q_vars['post_type']) && $q_vars['post_type'] == $this->post_type_name && isset($q_vars['label']) && is_numeric($q_vars['label']) && $q_vars['label'] != 0) {
            $term = get_term_by('id', $q_vars['label'], 'label');
            $q_vars['label'] = $term->slug;
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
    
        add_action( 'init', array( $this, 'add_group_taxonomy') );
        add_action( 'init', array( $this, 'add_tag_taxonomy') );
    }



    /**
     * add group taxonomy for plugin post type
     * see additional help here: http://codex.wordpress.org/Function_Reference/register_taxonomy
     *
     * @since 1.0.0
     * @param      
     * @return 
     */
    public function add_group_taxonomy() {
    	
        $labels = array(
            'name'                      => __('Groups','owlabpfl'), //general name for the taxonomy, usually plural.
            'singular_name'             => __('group','owlabpfl'), //name for one object of this taxonomy
            'all_items'                 => __('All Groups','owlabpfl'),
            'edit_item'                 => __('Edit Group','owlabpfl'),
            'view_item'                 => __('View Group','owlabpfl'),
            'update_item'               => __('Update Group','owlabpfl'),
            'add_new_item'              => __('Add New Group','owlabpfl'),
            'new_item_name'             => __('New Group Name','owlabpfl'),
            'parent_item'               => __('Parent Group','owlabpfl'),
            'parent_item_colon'         => __('Parent Group:','owlabpfl'),
            'search_items'              => __('Search Groups','owlabpfl'),
            'popular_items'             => __('Popular Groups','owlabpfl'),
            'separate_items_with_commas' => __('Separate groups with commas','owlabpfl'),
            'add_or_remove_items'       => __('Add or Remove Groups','owlabpfl'),
            'not_found'                 => __('No gtoups found','owlabpfl')
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
     * add group taxonomy for plugin post type
     * see additional help here: http://codex.wordpress.org/Function_Reference/register_taxonomy
     *
     * @since 1.0.0
     * @param      
     * @return 
     */
    public function add_tag_taxonomy() {
        
        $labels = array(
            'name'                      => _x('Labels','taxonomy general name', 'owlabpfl'), //general name for the taxonomy, usually plural.
            'singular_name'             => _x('label', 'taxonomy singular name', 'owlabpfl'), //name for one object of this taxonomy
            'search_items'              =>  __( 'Search Labels' ),
            'popular_items'             => __( 'Popular Labels' ),
            'all_items'                 => __( 'All Labels' ),
            'parent_item'               => null,
            'parent_item_colon'         => null,
            'edit_item'                 => __( 'Edit Label' ), 
            'update_item'               => __( 'Update Label' ),
            'add_new_item'              => __( 'Add New Label' ),
            'new_item_name'             => __( 'New Label Name' ),
            'separate_items_with_commas'=> __( 'Separate Labels with commas' ),
            'add_or_remove_items'       => __( 'Add or remove Labels' ),
            'choose_from_most_used'     => __( 'Choose from the most used Labels' ),
            'menu_name'                 => __( 'Labels' ),
        );
        register_taxonomy(
            'label',
            $this->post_type_name, //post type
            array(
                'hierarchical' => false,
                'labels' => $labels,
                'show_ui' => true,
                'update_count_callback' => '_update_post_term_count',
                'query_var' => true,
                'rewrite' => array( 'slug' => 'label' ),
            )
        ); 
    
    }



    /**
     * adds custom meta fields for groups taxonomy
     * will need 3 functions
     * 1- adds a field to new page
     * 2- adds a field to edit page
     * 3- save the values of the custom field from both pages
     *
     * @since 1.0.0
     * @param      
     * @return 
     */
    public function add_custom_meta_to_groups() {
        
        //styles and scripts
        add_action( 'admin_enqueue_scripts', array( $this, '_add_styles_and_scripts'), 11 );
        

        //add field to new page
        add_action( $this->custom_taxonomy_name.'_add_form_fields', array( $this, '_owlabpflgroup_add_meta_field'), 10, 2 );
        
        // add field to edit page
        add_action( $this->custom_taxonomy_name.'_edit_form_fields', array( $this, '_owlabpflgroup_edit_meta_field'), 10, 2 );

        //save
        add_action( 'edited_'.$this->custom_taxonomy_name, array( $this, '_save_owlabpflgroup_custom_meta'), 10, 2 );  
        add_action( 'create_'.$this->custom_taxonomy_name, array( $this, '_save_owlabpflgroup_custom_meta'), 10, 2 );
    
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

            wp_enqueue_script( 'owlabpfl-group-upload-admin-js', plugins_url( 'assets/js/group-meta.js', $this->file ), array( 'jquery'), $this->version );
            wp_enqueue_style( 'owlabpfl-group-upload-admin-css', plugins_url( 'assets/css/group-meta.css', $this->file ), array(), $this->version );

        }
        
        // If on an owlabkbs post type, add custom CSS for hiding specific things.
        if ( isset( get_current_screen()->post_type ) && 'owlabpfl' == get_current_screen()->post_type ) {
            add_action( 'admin_head', array( $this, 'meta_box_css' ) );
        } 
    
    }

    /**
     * Hides unnecessary meta box items on owlabkbs post type screens.
     *
     * @since 1.0.0
     */
    public function meta_box_css() {

        ?>
        <style type="text/css">.misc-pub-section:not(.misc-pub-post-status) { display: none; }</style>
        <?php

        // Fire action for CSS on owlabkbs post type screens.
        do_action( 'owlabpfl_admin_css' );

    }

    /**
     * add new fields for group taxonomy
     *
     * @since 1.0.0
     * @param      
     * @return 
     */
    public function _owlabpflgroup_add_meta_field() {
        
        
        $out = '<div class="form-field">';
        // this will add the custom meta field to the add new term page
        wp_nonce_field( plugin_basename( __FILE__ ), 'owlabpfl_media_nonce' );
        
        
        $out .= '<div class="drop_meta_item_group gallery">
            <label for="owlabpfl_group_image">'.__('Choose Cover Image','owlabpfl').'</label>
            <div class="inner_meta">
            <!-- image container -->
            <div class="image-container"></div>
            <!-- end images container -->

            <input type="text" class="meta_field media_field_input" id="owlabpfl_group_image" name="term_meta[owlabpfl_group_image]" value="" />
            <input type="button" name="uploader" id="owlabpfl_group_image_btn" class="group_media_uploader_button button button-primary" value="'.__('Select Image' , 'owlabpfl').'">
            <div class="meta_description"><p>'.__('Choose one image as the cover of this group.','owlabpfl').'</p></div>
            </div><!-- end inner -->
            </div><!-- end single meta -->';
        
        
        $out .= '<div class="drop_meta_item_group">
            <label for="owlabpfl_layout_type">'.__('Choose Layout type','owlabpfl').'</label>
            <select name="term_meta[owlabpfl_layout_type]" id="owlabpfl_layout_type">';
                        
            $out .= $this->_get_layout_types_html();        
        
        $out .='</select>
            <br /><span class="description">'.__('If you want to show portfolio items in this group in a separate page, please select a fron-end layout for it.','owlabpfl').'</span></div>';

        $out .= '<div class="drop_meta_item_group">
            <label for="owlabpfl_same_ratio_grid">'.__('Same ratio in grid','owlabpfl').'</label>
            <input type="checkbox" name="term_meta[owlabpfl_same_ratio_grid]" id="owlabpfl_same_ratio_grid">';      
        

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
    public function _owlabpflgroup_edit_meta_field($term) {

        // put the term ID into a variable
        $t_id = $term->term_id;

        // retrieve the existing value(s) for this meta field. This returns an array
        $term_meta = get_option( "owlab_group_$t_id" );
        if ( ! $term_meta ){
            $term_meta = array();
        }
        ?>
        <tr class="form-field">
            <th scope="row" valign="top">
                <label for="owlabpfl_group_image"><?php  _e('Choose Cover Image','owlabpfl')?> </label>
            </th>
            <td>
                <div class="drop_meta_item_group gallery">
                    <div class="inner_meta">
                        <!-- image container -->
                        <div class="image-container"></div>
                        <!-- end images container -->
                        <?php $image=''; ?>
                        <?php if (array_key_exists('owlabpfl_group_image', $term_meta) ):  ?>
                            <?php $image = $term_meta['owlabpfl_group_image']; ?>
                        <?php endif; ?>
                        <input type="text" class="meta_field media_field_input" id="owlabpfl_group_image" name="term_meta[owlabpfl_group_image]" value=<?php echo $image; ?> />
                        <input type="button" name="uploader" id="owlabpfl_group_image_btn" class="group_media_uploader_button button button-primary" value="<?php _e('Select Image' , 'owlabpfl'); ?>">
                        <div class="meta_description"><p><?php _e('Choose one image as the cover of this group.','owlabpfl'); ?></p></div>
                    </div><!-- end inner -->
                </div>
            </td>
        </tr>

        <tr class="form-field">
            <th scope="row" valign="top">
                <label for="owlabpfl_layout_type"><?php  _e('Choose Layout type','owlabpfl')?> </label>
            </th>
            <td>
                <select name="term_meta[owlabpfl_layout_type]" id="owlabpfl_layout_type">
                    <?php if (array_key_exists('owlabpfl_layout_type', $term_meta) ):  ?>
                    <?php $selected = esc_attr( $term_meta['owlabpfl_layout_type'] ) ? esc_attr( $term_meta['owlabpfl_layout_type'] ) : null; ?>
                    <?php endif; ?>
                    <?php echo $this->_get_layout_types_html($selected); ?>
                </select>
                <br/>
                <span class="description">'<?php _e('If you want to show portfolio items in this group in a separate page, please select a fron-end layout for it.','owlabpfl');?></span>
            </td>
        </tr>

        <tr class="form-field">
            <th scope="row" valign="top">
                <label for="owlabpfl_same_ratio_grid"><?php  _e('Same ratio grid','owlabpfl')?> </label>
            </th>
            <td>
                <?php $selectedbox=""; ?>
                <?php if (array_key_exists('owlabpfl_same_ratio_grid', $term_meta) ):  ?>
                    <?php $selectedbox = $term_meta['owlabpfl_same_ratio_grid'] =="on" ? 'checked' : ''; ?>
                <?php endif; ?>
                <input type="checkbox" name="term_meta[owlabpfl_same_ratio_grid]" id="owlabpfl_same_ratio_grid" <?php echo $selectedbox; ?>>
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
            'vertical'   => __('Vertical Images - Horizontal Scroll','owlabpfl'),
            'horizontal' => __('Horizontal Images - Vertical Scroll','owlabpfl'),
            'grid'       => __('Grid','owlabpfl')
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
    public function _save_owlabpflgroup_custom_meta($term_id) {
        
        //owlabpfl_layout_type
            

        if ( isset( $_POST['term_meta'] ) ) {

            if (! array_key_exists('owlabpfl_same_ratio_grid', $_POST['term_meta']))
                $_POST['term_meta']['owlabpfl_same_ratio_grid'] = 'off';
            
            $t_id = $term_id;
            $term_meta = get_option( "owlab_group_$t_id" );
            $cat_keys = array_keys( $_POST['term_meta'] );
            foreach ( $cat_keys as $key ) {
                if ( isset ( $_POST['term_meta'][$key] ) ) {
                    $term_meta[$key] = $_POST['term_meta'][$key];
                }
            }
            // Save the option array.
            update_option( "owlab_group_$t_id", $term_meta );
        }
    
    }


    

    /**
     * Helper flag method for any owlabpfl screen.
     *
     * @since 1.2.0
     *
     * @return bool True if on a owlabpfl screen, false if not.
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
     * Helper flag method for the Add/Edit owlabpfl screens.
     *
     * @since 1.2.0
     *
     * @return bool True if on a owlabpfl Add/Edit screen, false if not.
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
     * @return object The Owlabpfl object.
     */
    public static function get_instance() {

        if ( ! isset( self::$instance ) && ! ( self::$instance instanceof Owlabpfl ) ) {
            self::$instance = new Owlabpfl();
        }

        return self::$instance;

    }

}

// Load the main plugin class.
$owlabpfl = Owlabpfl::get_instance();