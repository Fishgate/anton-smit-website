<?php
/**
 * Plugin Name: owwwlab KenBurned Slideshow - For TORANJ
 * Plugin URI:  
 * Description: This is the kenburned effect slideshow plugin for TORANJ theme. 
 * Author:      owwwlab Web Design Agency
 * Author URI:  http://owwwlab.com
 * Version:     1.1.0
 * Text Domain: owlabkbs
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
 * @package owwwlab-kenburn
 * @author  owwwlab
 */

 class Owlabkbs {

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
    public $plugin_name = 'owwwlab KenBurned Slideshow';


    /**
     * Unique plugin slug identifier.
     *
     * @since 1.0.0
     *
     * @var string
     */
    public $plugin_slug = 'owwwlab-kenburn';

    /**
     * Plugin file.
     *
     * @since 1.0.0
     *
     * @var string
     */
    public $file = __FILE__;


     /**
     * Primary class constructor.
     *
     * @since 1.0.0
     */
    public function __construct() {

        // Fire a hook before the class is setup.
        do_action( 'owlabkb_pre_init' );

        // Load the plugin textdomain.
        add_action( 'plugins_loaded', array( $this, 'load_plugin_textdomain' ) );

        // Load the plugin.
        add_action( 'init', array( $this, 'init' ), 0 );

    }

    /**
     * Loads the plugin textdomain for translation.
     *
     * @since 1.0.0
     */
    public function load_plugin_textdomain() {

        
        $domain = 'owlabkbs';
        $locale = apply_filters( 'plugin_locale', get_locale(), $domain );

        load_textdomain( $domain, WP_LANG_DIR . '/' . $domain . '/' . $domain . '-' . $locale . '.mo' );
        load_plugin_textdomain( $domain, false, dirname( plugin_basename( __FILE__ ) ) . '/languages/' );

    }

    /**
     * Loads the plugin into WordPress.
     *
     * @since 1.0.0
     */
    public function init() {

        
        // Run hook once owlabkbs has been initialized.
        do_action( 'owlabkbs_init' );

        // Load admin only components.
        if ( is_admin() ) {
            $this->require_admin();
        }

        // Load global components.
        $this->require_global();

    }

    /**
     * Loads all admin related files into scope.
     *
     * @since 1.0.0
     */
    public function require_admin() {

        require plugin_dir_path( __FILE__ ) . 'includes/admin/ajax.php';
        require plugin_dir_path( __FILE__ ) . 'includes/admin/common.php';
        require plugin_dir_path( __FILE__ ) . 'includes/admin/editor.php';
        require plugin_dir_path( __FILE__ ) . 'includes/admin/media.php';
        require plugin_dir_path( __FILE__ ) . 'includes/admin/metaboxes.php';
        require plugin_dir_path( __FILE__ ) . 'includes/admin/posttype.php';

    }

    /**
     * Loads all global files into scope.
     *
     * @since 1.0.0
     */
    public function require_global() {

        require plugin_dir_path( __FILE__ ) . 'includes/global/common.php';
        require plugin_dir_path( __FILE__ ) . 'includes/global/posttype.php';
        require plugin_dir_path( __FILE__ ) . 'includes/global/shortcode.php';

    }

    /**
     * Returns the instance of the class.
     *
     * @since 1.0.0
     *
     * @return object The Owwwlab_kenburn object.
     */
    public static function get_instance() {

        if ( ! isset( self::$instance ) && ! ( self::$instance instanceof Owlabkbs ) ) {
            self::$instance = new Owlabkbs();
        }

        return self::$instance;

    }

    /**
     * Returns a slider based on ID.
     *
     * @since 1.0.0
     *
     * @param int $id     The slider ID used to retrieve a slider.
     * @return array|bool Array of slider data or false if none found.
     */
    public function get_slider( $id ) {

        // Attempt to return the transient first, otherwise generate the new query to retrieve the data.
        // if ( false === ( $slider = get_transient( '_owlabkbs_cache_' . $id ) ) ) {
        //     $slider = $this->_get_slider( $id );
        //     if ( $slider ) {
        //         set_transient( '_owlabkbs_cache_' . $id, $slider, DAY_IN_SECONDS );
        //     }
        // }
        $slider = $this->_get_slider( $id );
        // Return the slider data.
        return $slider;

    }

    /**
     * Internal method that returns a slider based on ID.
     *
     * @since 1.0.0
     *
     * @param int $id     The slider ID used to retrieve a slider.
     * @return array|bool Array of slider data or false if none found.
     */
    public function _get_slider( $id ) {

        return get_post_meta( $id, '_owlabkbs_slider_data', true );

    }

    /**
     * Returns a slider based on slug.
     *
     * @since 1.0.0
     *
     * @param string $slug The slider slug used to retrieve a slider.
     * @return array|bool  Array of slider data or false if none found.
     */
    public function get_slider_by_slug( $slug ) {

        // Attempt to return the transient first, otherwise generate the new query to retrieve the data.
        // if ( false === ( $slider = get_transient( '_owlabkbs_cache_' . $slug ) ) ) {
        //     $slider = $this->_get_slider_by_slug( $slug );
        //     if ( $slider ) {
        //         set_transient( '_owlabkbs_cache_' . $slug, $slider, DAY_IN_SECONDS );
        //     }
        // }
        $slider = $this->_get_slider_by_slug( $slug );
        // Return the slider data.
        return $slider;

    }

    /**
     * Internal method that returns a slider based on slug.
     *
     * @since 1.0.0
     *
     * @param string $slug The slider slug used to retrieve a slider.
     * @return array|bool  Array of slider data or false if none found.
     */
    public function _get_slider_by_slug( $slug ) {

        $sliders = get_posts(
            array(
                'post_type'     => 'any',
                'no_found_rows' => true,
                'cache_results' => false,
                'nopaging'      => true,
                'fields'        => 'ids',
                'meta_query'    => array(
                    array(
                        'key'     => '_owlabkbs_slider_data',
                        'value'   => maybe_serialize( strval( $slug ) ),
                        'compare' => 'LIKE'
                    )
                )
            )
        );
        if ( empty( $sliders ) ) {
            return false;
        } else {
            return get_post_meta( $sliders[0], '_owlabkbs_slider_data', true );
        }

    }

    /**
     * Returns all sliders created on the site.
     *
     * @since 1.0.0
     *
     * @return array|bool Array of slider data or false if none found.
     */
    public function get_sliders() {

        // Attempt to return the transient first, otherwise generate the new query to retrieve the data.
        // if ( false === ( $sliders = get_transient( '_owlabkbs_cache_all' ) ) ) {
        //     $sliders = $this->_get_sliders();
        //     if ( $sliders ) {
        //         set_transient( '_owlabkbs_cache_all', $sliders, DAY_IN_SECONDS );
        //     }
        // }
        $sliders = $this->_get_sliders();
        // Return the slider data.
        return $sliders;

    }

    /**
     * Internal method that returns all sliders created on the site.
     *
     * @since 1.0.0
     *
     * @return array|bool Array of slider data or false if none found.
     */
    public function _get_sliders() {

        $sliders = get_posts(
            array(
                'post_type'     => 'any',
                'no_found_rows' => true,
                'cache_results' => false,
                'nopaging'      => true,
                'fields'        => 'ids',
                'meta_query'    => array(
                    array(
                        'key' => '_owlabkbs_slider_data'
                    )
                )
            )
        );
        if ( empty( $sliders ) ) {
            return false;
        }

        // Now loop through all the sliders found and only use sliders that have images in them.
        $ret = array();
        foreach ( $sliders as $id ) {
            $data = get_post_meta( $id, '_owlabkbs_slider_data', true );
            if ( empty( $data['slider'] ) && 'default' == Owlabkbs_Shortcode::get_instance()->get_config( 'type', $data ) || 'dynamic' == Owlabkbs_Shortcode::get_instance()->get_config( 'type', $data ) ) {
                continue;
            }

            $ret[] = $data;
        }

        // Return the slider data.
        return $ret;

    }

    /**
     * Getter method for retrieving the main plugin filepath.
     *
     * @since 1.0.0
     */
    public static function get_file() {

        return self::$file;

    }

    /**
     * Helper flag method for any owlabkbs screen.
     *
     * @since 1.2.0
     *
     * @return bool True if on a owlabkbs screen, false if not.
     */
    public static function is_owlabkbs_screen() {

        $current_screen = get_current_screen();

        if ( ! $current_screen ) {
            return false;
        }

        if ( 'owlabkbs' == $current_screen->post_type ) {
            return true;
        }

        return false;

    }

    /**
     * Helper flag method for the Add/Edit owlabkbs screens.
     *
     * @since 1.2.0
     *
     * @return bool True if on a owlabkbs Add/Edit screen, false if not.
     */
    public static function is_owlabkbs_add_edit_screen() {

        $current_screen = get_current_screen();

        if ( ! $current_screen ) {
            return false;
        }

        if ( 'owlabkbs' == $current_screen->post_type && 'post' == $current_screen->base ) {
            return true;
        }

        return false;

    }


 }


 // Load the main plugin class.
$owlabkbs = Owlabkbs::get_instance();



register_activation_hook( __FILE__, 'owlabkbs_activation_hook' );
/**
 * Fired when the plugin is activated.
 *
 * @since 1.0.0
 *
 * @global int $wp_version      The version of WordPress for this install.
 * @global object $wpdb         The WordPress database object.
 */
function owlabkbs_activation_hook() {

    global $wp_version;
    if ( version_compare( $wp_version, '3.5.1', '<' ) ) {
        deactivate_plugins( plugin_basename( __FILE__ ) );
        wp_die( sprintf( __( 'Sorry, but your version of WordPress does not meet owwwlab Kenburn Slider\'s required version of <strong>3.5.1</strong> to run properly. The plugin has been deactivated. <a href="%s">Click here to return to the Dashboard</a>.', 'owlabkbs' ), get_admin_url() ) );
    }

}