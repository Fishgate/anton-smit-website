<?php
/**
 * Include and setup custom metaboxes and fields.
 *
 */

/**
 * Class for post type metaboxes
 *
 * @since 1.0.0
 *
 * @package owwwlab-gallery
 * @author  owwwlab
 */

 class Owlabgal_metaboxes {

   	/**
   	 * Holds the class object.
   	 *
   	 * @since 1.0.0
   	 *
   	 * @var object
   	 */
   	public static $instance;

 	/**
   	 * Start with an underscore to hide fields from custom fields list
   	 *
   	 * @since 1.0.0
   	 *
   	 * @var object
   	 */
   	public $prefix;


   	/**
   	 * Holds the class object.
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

    	$this->base = Owlabgal::get_instance();
    	$this->prefix = 'owlabgal_';

    	//add meta boxes
        add_filter( 'cmb_meta_boxes', array( $this, 'add_metaboxes') );

        //initialize the metabox class
        add_action( 'init', array( $this, 'cmb_initialize_cmb_meta_boxes'), 9999 );
    }

    /**
     * adds metaboxes array
     *
     * @since 1.0.0
     * @param      
     * @return 
     */
    public function add_metaboxes( array $meta_boxes ) {
    
        $prefix = $this->prefix;

        $meta_boxes['gallery_metabox'] = array(
      			'id'         => 'gallery_metabox',
      			'title'      => __( 'gallery Metabox', 'owlabgal' ),
      			'pages'      => array( 'owlabgal' ), // Post type
      			'context'    => 'normal',
      			'priority'   => 'high',
      			'show_names' => true, // Show field names on the left
      			// 'cmb_styles' => true, // Enqueue the CMB stylesheet on the frontend
      			'fields'     => array(
      				
      				//Image
      				// array(
      				//     'name' => __( 'Cover Image', 'owlabgal' ),
      				//     'id' => $prefix . 'cover',
      				//     'type' => 'file',
      				//     'allow' => array( 'url', 'attachment' ) // limit to just attachments with array( 'attachment' )
      				// ),
      				// short description
      				array(
      					'name' => __( 'Short Description', 'owlabgal' ),
      					'desc' => __( 'Input a very abstract description for the Photo, do not exceed 200 characters for the best visual result', 'owlabgal' ),
      					'id'   => $prefix . 'short_des',
      					'type' => 'textarea_small'
      				),
      				// long description at sidebar
      				array(
      					'name' => __( 'Description', 'owlabgal' ),
      					'id'   => $prefix . 'des',
      					'type' => 'wysiwyg',
      					'options' => array(
      				        'wpautop' => true, // use wpautop?
      				        'media_buttons' => true, // show insert/upload button(s)
      				        'textarea_name' => 'editor', // set the textarea name to something different, square brackets [] can be used here
      				        'textarea_rows' => 5, // rows="..."
      				        'tabindex' => '',
      				        'editor_css' => '', // intended for extra styles for both visual and HTML editors buttons, needs to include the `<style>` tags, can use "scoped".
      				        'editor_class' => '', // add extra class(es) to the editor textarea
      				        'teeny' => false, // output the minimal editor config used in Press This
      				        'dfw' => false, // replace the default fullscreen with DFW (needs specific css)
      				        'tinymce' => true, // load TinyMCE, can be used to pass settings directly to TinyMCE using an array()
      				        'quicktags' => true // load Quicktags, can be used to pass settings directly to Quicktags using an array()  
      				    ),
      				),
      				

      				//grid ratio
      				// customer
      				array(
      					'name' => __( 'Grid ratio', 'owlabgal' ),
      					'desc' => __( 'ex. 2 or 1, leave blank to not set<br>Ratio of this item thumbnail against others, only will apply if you use the grid layout for gallery list.', 'owlabgal' ),
      					'id'   => $prefix . 'grid_ratio',
      					'type' => 'text_medium'
      				),

              array(
                'name' => __( 'Grid sizer', 'owlabgal' ),
                'desc' => __( 'Use this image ratio as the base for the grid sizes? please check one image in each album, normally your smallest image.', 'owlabgal' ),
                'id'   => $prefix . 'grid_sizer',
                'type' => 'checkbox'
              )
      			),
      		);

        return $meta_boxes;
    
    }



    /**
     * initializez the cbm class
     *
     * @since 1.0.0
     * @param      
     * @return 
     */
    public function cmb_initialize_cmb_meta_boxes() {
    	
    	if ( ! class_exists( 'cmb_Meta_Box' ) )
		require_once plugin_dir_path( $this->base->file ) . '3rd-party/cmb/init.php';
    
    }


    /**
     * Returns the instance of the class.
     *
     * @since 1.0.0
     *
     * @return object The Owlabgal_metaboxes object.
     */
    public static function get_instance() {

        if ( ! isset( self::$instance ) && ! ( self::$instance instanceof Owlabgal_metaboxes ) ) {
            self::$instance = new Owlabgal_metaboxes();
        }

        return self::$instance;

    }

}

// Load the main plugin class.
$owlabgal_metaboxes = Owlabgal_metaboxes::get_instance();



