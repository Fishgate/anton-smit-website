<?php
/**
 * Posttype admin class.
 *
 * @since 1.0.0
 *
 * @package owwwlab-kenburn
 * @author  owwwlab
 */
class Owlabkbs_Posttype_Admin {

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

        // Remove quick editing from the post type row actions.
        add_filter( 'post_row_actions', array( $this, 'row_actions' ), 10, 2 );

        // Manage post type columns.
        add_filter( 'manage_edit-owlabkbs_columns', array( $this, 'owlabkbs_columns' ) );
        add_filter( 'manage_owlabkbs_posts_custom_column', array( $this, 'owlabkbs_custom_columns' ), 10, 2 );

        // Update post type messages.
        add_filter( 'post_updated_messages', array( $this, 'messages' ) );

        // Force the menu icon to be scaled to proper size (for Retina displays).
        add_action( 'admin_head', array( $this, 'menu_icon' ) );

    }


    /**
     * Filter out unnecessary row actions from the post table.
     *
     * @since 1.0.0
     *
     * @param array $actions  Default row actions.
     * @param object $post    The current post object.
     * @return array $actions filter for row actions.
     */
    public function row_actions( $actions, $post ) {

        if ( isset( get_current_screen()->post_type ) && 'owlabkbs' == get_current_screen()->post_type ) {
            unset( $actions['inline hide-if-no-js'] );
        }

        return apply_filters( 'owlabkbs_row_actions', $actions, $post );

    }

    /**
     * Customize the post columns for the owlabkbs post type.
     *
     * @since 1.0.0
     *
     * @param array $columns  The default columns.
     * @return array $columns Amended columns.
     */
    public function owlabkbs_columns( $columns ) {

        $columns = $columns + array(
            'shortcode' => __( 'Shortcode', 'owlabkbs' ),
            'images'    => __( 'Number of Images', 'owlabkbs' ),
            'modified'  => __( 'Last Modified', 'owlabkbs' ),
        );

        return $columns;

    }

    /**
     * Add data to the custom columns added to the owlabkbs post type.
     *
     * @since 1.0.0
     *
     * @global object $post  The current post object.
     * @param string $column The name of the custom column.
     * @param int $post_id   The current post ID.
     */
    public function owlabkbs_custom_columns( $column, $post_id ) {

        global $post;
        $post_id = absint( $post_id );

        switch ( $column ) {
            case 'shortcode' :
                echo '<code>[owlabkbs id="' . $post_id . '"]</code>';
                break;

            case 'images' :
                $slider_data = get_post_meta( $post_id, '_owlabkbs_slider_data', true );
                echo ( ! empty( $slider_data['slider'] ) ? count( $slider_data['slider'] ) : 0 );
                break;

            case 'modified' :
                the_modified_date();
                break;
        }

    }

    

    /**
     * Contextualizes the post updated messages.
     *
     * @since 1.0.0
     *
     * @global object $post    The current post object.
     * @param array $messages  Array of default post updated messages.
     * @return array $messages Amended array of post updated messages.
     */
    public function messages( $messages ) {

        global $post;

        // Contextualize the messages.
        $messages['owlabkbs'] = apply_filters( 'owlabkbs_messages',
            array(
                0  => '',
                1  => __( 'Slider updated.', 'owlabkbs' ),
                2  => __( 'Slider custom field updated.', 'owlabkbs' ),
                3  => __( 'Slider custom field deleted.', 'owlabkbs' ),
                4  => __( 'Slider updated.', 'owlabkbs' ),
                5  => isset( $_GET['revision'] ) ? sprintf( __( 'Slider restored to revision from %s.', 'owlabkbs' ), wp_post_revision_title( (int) $_GET['revision'], false ) ) : false,
                6  => __( 'Slider published.', 'owlabkbs' ),
                7  => __( 'Slider saved.', 'owlabkbs' ),
                8  => __( 'Slider submitted.', 'owlabkbs' ),
                9  => sprintf( __( 'Slider scheduled for: <strong>%1$s</strong>.', 'owlabkbs' ), date_i18n( __( 'M j, Y @ G:i' ), strtotime( $post->post_date ) ) ),
                10 => __( 'Slider draft updated.', 'owlabkbs' )
            )
        );

        return $messages;

    }

    /**
     * Forces the Soliloquy menu icon width/height for Retina devices.
     *
     * @since 1.0.0
     */
    public function menu_icon() {

        ?>
        <style type="text/css">#menu-posts-owlabkbs .wp-menu-image img { width: 16px; height: 16px; }</style>
        <?php

    }

    /**
     * Returns the singleton instance of the class.
     *
     * @since 1.0.0
     *
     * @return object The Owlabkbs_Posttype_Admin object.
     */
    public static function get_instance() {

        if ( ! isset( self::$instance ) && ! ( self::$instance instanceof Owlabkbs_Posttype_Admin ) ) {
            self::$instance = new Owlabkbs_Posttype_Admin();
        }

        return self::$instance;

    }

}

// Load the posttype admin class.
$owlabkbs_Posttype_Admin = Owlabkbs_Posttype_Admin::get_instance();