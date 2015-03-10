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
 * @package owwwlab-kenburn
 * @author  owwwlab
 */

 class Owlabpfl_metaboxes {

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

    	$this->base = Owlabpfl::get_instance();
    	$this->prefix = 'owlabpfl_';

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

        $meta_boxes['portfolio_metabox'] = array(
			'id'         => 'portfolio_metabox',
			'title'      => __( 'Portfolio Metabox', 'owlabpfl' ),
			'pages'      => array( 'owlabpfl' ), // Post type
			'context'    => 'normal',
			'priority'   => 'high',
			'show_names' => true, // Show field names on the left
			// 'cmb_styles' => true, // Enqueue the CMB stylesheet on the frontend
			'fields'     => array(
				
				

				// date of execution of the project
				array(
					'name'       => __( 'Date', 'owlabpfl' ),
					'desc'       => __( 'Date of execution', 'owlabpfl' ),
					'id'         => $prefix . 'date',
					'type'       => 'text_small',
				),
                            
                                // dimensions of the piece in mm, as seen in the book
				array(
					'name'       => __( 'Dimensions', 'owlabpfl' ),
					'desc'       => __( 'Dimensions of the piece', 'owlabpfl' ),
					'id'         => $prefix . 'dimensions',
					'type'       => 'text_medium',
				),


				// short description
				array(
					'name' => __( 'Short Description', 'owlabpfl' ),
					'desc' => __( 'Input a very abstract description for the project, do not exceed 200 characters for the best visual result', 'owlabpfl' ),
					'id'   => $prefix . 'short_des',
					'type' => 'textarea_small'
				),


				// long description at sidebar
				array(
					'name' => __( 'Sidebar Description', 'owlabpfl' ),
					'desc' => __( 'If you want to use right sidebar layout fill here.', 'owlabpfl' ),
					'id'   => $prefix . 'side_des',
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

				//video as cover
				array(
				    'name'    	=> __( 'Use video as cover', 'owlabpfl' ),
				    'desc'		=> __( 'Do you want to use video instead of featured image? <br> <strong>Note:</strong> You still need to set the cover image, it will be used as the cover image some portfolio lists and as the video poster until it loads.', 'owlabpfl' ),
				    'id'      	=> $prefix . 'use_video',
				    'type'    	=> 'checkbox'
				),

				//video files mp4
				array(
				    'name' => __( 'Video MP4 file', 'owlabpfl' ),
				    'id' => $prefix . 'video_mp4',
				    'type' => 'file',
				    'allow' => array( 'url', 'attachment' ) // limit to just attachments with array( 'attachment' )
				),

				//video files WEBM
				array(
				    'name' => __( 'Video WEBM file', 'owlabpfl' ),
				    'id' => $prefix . 'video_webm',
				    'type' => 'file',
				    'allow' => array( 'url', 'attachment' ) // limit to just attachments with array( 'attachment' )
				),

				//video files ogg
				array(
				    'name' => __( 'Video ogv file', 'owlabpfl' ),
				    'id' => $prefix . 'video_ogg',
				    'type' => 'file',
				    'allow' => array( 'url', 'attachment' ) // limit to just attachments with array( 'attachment' )
				),



				// layout
				array(
					'name'    => __( 'Choose Layout mode at front-end', 'owlabpfl' ),
					'desc'    => __( 'Which layout do you want to display your data in the single portfolio page?', 'owlabpfl' ),
					'id'      => $prefix . 'layout',
					'type'    => 'select',
					'options' => array(
						'rightside'  => __( 'Right Sidebar Parallax title', 'owlabpfl' ),
						'leftside'   => __( 'Left Sidebar Parallax cover', 'owlabpfl' ),
						'regular-light'   => __( 'Regular light', 'owlabpfl' ),
						'regular-dark'   => __( 'Regular dark', 'owlabpfl' ),
					)
				),

				//grid ratio
				array(
					'name' => __( 'Grid ratio', 'owlabpfl' ),
					'desc' => __( 'ex. 2 or 1, leave blank to not set<br>Ratio of this item thumbnail against others, only will apply if you use the grid layout for portfolio list.', 'owlabpfl' ),
					'id'   => $prefix . 'grid_ratio',
					'type' => 'text_medium'
				),
				//grid sizer
				array(
	                'name' => __( 'Grid sizer', 'owlabgal' ),
	                'desc' => __( 'Use this image ratio as the base for the grid sizes? please check one image in each album, normally your smallest image.', 'owlabgal' ),
	                'id'   => $prefix . 'grid_sizer',
	                'type' => 'checkbox'
	             )
			),
		);
		
		//lets see if we have any other things in the theme options
		if ( function_exists("ot_get_option")){
			if ( ot_get_option('incr_portfolio_fields') ){
				$pp_fileds = ot_get_option('incr_portfolio_fields');
			    $to_be_added = array();
			    $default_fields = $meta_boxes['portfolio_metabox']['fields'];
			    foreach ($pp_fileds as $f) {
			    	
			    	if ( !empty($f['title']) and !empty($f["id"]) ){
			    		$add= array(
							
								'name' => __( $f['title'], 'owlabpfl' ),
								'id'   => $prefix . $f["id"],
								'type' => 'text_medium'
							
						);

						array_unshift($meta_boxes['portfolio_metabox']['fields'], $add);
			    	} 
			    }

			    
			}
		}
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
     * @return object The Owlabpfl_metaboxes object.
     */
    public static function get_instance() {

        if ( ! isset( self::$instance ) && ! ( self::$instance instanceof Owlabpfl_metaboxes ) ) {
            self::$instance = new Owlabpfl_metaboxes();
        }

        return self::$instance;

    }

}

// Load the main plugin class.
$owlabpfl_metaboxes = Owlabpfl_metaboxes::get_instance();



