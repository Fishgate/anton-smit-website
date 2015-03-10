<?php
/**
 * Posttype class.
 *
 * @since 1.0.0
 *
 * @package owwwlab-kenburn
 * @author  owwwlab
 */
class Owlabkbs_Posttype {

    /**
     * Holds the class object.
     *
     * @since 1.0.0
     *
     * @var object
     */
    public static $instance;

    /**
     * Path to the file.
     *
     * @since 1.0.0
     *
     * @var string
     */
    public $file = __FILE__;

    /**
     * Holds the base class object.
     *
     * @since 1.0.0
     *
     * @var object
     */
    public $base;

    /**
     * Primary class constructor.
     *
     * @since 1.0.0
     */
    public function __construct() {

        // Load the base class object.
        $this->base = Owlabkbs::get_instance();

        // Build the labels for the post type.
        $labels = apply_filters( 'owlabkbs_post_type_labels',
            array(
                'name'               => __( 'owwwlab Kenburn Sliders', 'owlabkbs' ),
                'singular_name'      => __( 'Slider', 'owlabkbs' ),
                'add_new'            => __( 'Add New', 'owlabkbs' ),
                'add_new_item'       => __( 'Add New Slider', 'owlabkbs' ),
                'edit_item'          => __( 'Edit Slider', 'owlabkbs' ),
                'new_item'           => __( 'New Slider', 'owlabkbs' ),
                'view_item'          => __( 'View Slider', 'owlabkbs' ),
                'search_items'       => __( 'Search Sliders', 'owlabkbs' ),
                'not_found'          => __( 'No sliders found.', 'owlabkbs' ),
                'not_found_in_trash' => __( 'No sliders found in trash.', 'owlabkbs' ),
                'parent_item_colon'  => '',
                'menu_name'          => __( 'Kenburn Slider', 'owlabkbs' )
            )
        );

        // Build out the post type arguments.
        $args = apply_filters( 'owlabkbs_post_type_args',
            array(
                'labels'              => $labels,
                'public'              => false,
                'exclude_from_search' => false,
                'show_ui'             => true,
                'show_in_admin_bar'   => false,
                'rewrite'             => false,
                'query_var'           => false,
                'menu_position'       => 20,
                'menu_icon'           => plugins_url( 'assets/css/images/menu-icon@2x.png', $this->base->file ),
                'supports'            => array( 'title' )
            )
        );

        // Register the post type with WordPress.
        register_post_type( 'owlabkbs', $args );

    }

    /**
     * Returns the singleton instance of the class.
     *
     * @since 1.0.0
     *
     * @return object The Owlabkbs_Posttype object.
     */
    public static function get_instance() {

        if ( ! isset( self::$instance ) && ! ( self::$instance instanceof Owlabkbs_Posttype ) ) {
            self::$instance = new Owlabkbs_Posttype();
        }

        return self::$instance;

    }

}

// Load the posttype class.
$owlabkbs_Posttype = Owlabkbs_Posttype::get_instance();