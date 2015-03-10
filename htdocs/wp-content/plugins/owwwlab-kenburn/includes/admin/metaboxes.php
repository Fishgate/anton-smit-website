<?php
/**
 * Metabox class.
 *
 * @since 1.0.0
 *
 * @package owwwlab-kenburn
 * @author  owwwlab
 */
class Owlabkbs_Metaboxes {

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

        // Load metabox assets.
        add_action( 'admin_enqueue_scripts', array( $this, 'meta_box_styles' ) );
        add_action( 'admin_enqueue_scripts', array( $this, 'meta_box_scripts' ) );

        // Load the metabox hooks and filters.
        add_action( 'add_meta_boxes', array( $this, 'add_meta_boxes' ), 100 );

        // Load all tabs.
        add_action( 'owlabkbs_tab_images', array( $this, 'images_tab' ) );
        add_action( 'owlabkbs_tab_config', array( $this, 'config_tab' ) );
        add_action( 'owlabkbs_tab_misc', array( $this, 'misc_tab' ) );

        // Add action to save metabox config options.
        add_action( 'save_post', array( $this, 'save_meta_boxes' ), 10, 2 );

    }

    /**
     * Loads styles for our metaboxes.
     *
     * @since 1.0.0
     *
     * @return null Return early if not on the proper screen.
     */
    public function meta_box_styles() {

        if ( 'post' !== get_current_screen()->base ) {
            return;
        }

        if ( isset( get_current_screen()->post_type ) && in_array( get_current_screen()->post_type, $this->get_skipped_posttypes() ) ) {
            return;
        }

        // Load necessary metabox styles.
        wp_register_style( $this->base->plugin_slug . '-metabox-style', plugins_url( 'assets/css/metabox.css', $this->base->file ), array(), $this->base->version );
        wp_enqueue_style( $this->base->plugin_slug . '-metabox-style' );

        // Fire a hook to load in custom metabox styles.
        do_action( 'owlabkbs_metabox_styles' );

    }

    /**
     * Loads scripts for our metaboxes.
     *
     * @since 1.0.0
     *
     * @global int $id      The current post ID.
     * @global object $post The current post object..
     * @return null         Return early if not on the proper screen.
     */
    public function meta_box_scripts( $hook ) {

        global $id, $post;

        if ( isset( get_current_screen()->base ) && 'post' !== get_current_screen()->base ) {
            return;
        }

        if ( isset( get_current_screen()->post_type ) && in_array( get_current_screen()->post_type, $this->get_skipped_posttypes() ) ) {
            return;
        }

        // Set the post_id for localization.
        $post_id = isset( $post->ID ) ? $post->ID : (int) $id;

        // Load necessary metabox scripts.
        wp_enqueue_script( 'jquery-ui-sortable' );
        wp_enqueue_media( array( 'post' => $post_id ) );

        // Load necessary metabox scripts.
        wp_enqueue_script( 'plupload-handlers' );
        wp_register_script( $this->base->plugin_slug . '-metabox-script', plugins_url( 'assets/js/metabox.js', $this->base->file ), array( 'jquery', 'plupload-handlers', 'quicktags', 'jquery-ui-sortable' ), $this->base->version, true );
        wp_enqueue_script( $this->base->plugin_slug . '-metabox-script' );
        wp_localize_script(
            $this->base->plugin_slug . '-metabox-script',
            'owlabkbs_metabox',
            array(
                'ajax'           => admin_url( 'admin-ajax.php' ),
                'change_nonce'   => wp_create_nonce( 'owlabkbs-change-type' ),
                'slider'         => esc_attr__( 'Click Here to Insert Slides from Other Sources', 'owlabkbs' ),
                'id'             => $post_id,
                'htmlcode'       => __( 'HTML Slide Code', 'owlabkbs' ),
                'htmlslide'      => __( 'HTML Slide Title', 'owlabkbs' ),
                'htmlplace'      => __( 'Enter HTML slide title here...', 'owlabkbs' ),
                'htmlstart'      => __( '<!-- Enter your HTML code here for this slide (you can delete this line). -->', 'owlabkbs' ),
                'htmlthumb'      => __( 'HTML Slide Thumbnail', 'owlabkbs' ),
                'htmlsrc'        => __( 'Enter your HTML thumbnail URL here...', 'owlabkbs' ),
                'htmlselect'     => __( 'Choose HTML Thumbnail', 'owlabkbs' ),
                'htmldelete'     => __( 'Remove HTML Thumbnail', 'owlabkbs' ),
                'htmlframe'      => __( 'Choose a HTML Thumbnail', 'owlabkbs' ),
                'htmluse'        => __( 'Select Thumbnail', 'owlabkbs' ),
                'import'         => __( 'You must select a file to import before continuing.', 'owlabkbs' ),
                'insert_nonce'   => wp_create_nonce( 'owlabkbs-insert-images' ),
                'inserting'      => __( 'Inserting...', 'owlabkbs' ),
                'library_search' => wp_create_nonce( 'owlabkbs-library-search' ),
                'load_image'     => wp_create_nonce( 'owlabkbs-load-image' ),
                'load_slider'    => wp_create_nonce( 'owlabkbs-load-slider' ),
                'path'           => plugin_dir_path( 'assets' ),
                'plupload'       => $this->get_plupload_init( $post_id ),
                'refresh_nonce'  => wp_create_nonce( 'owlabkbs-refresh' ),
                'remove'         => __( 'Are you sure you want to remove this slide from the slider?', 'owlabkbs' ),
                'remove_nonce'   => wp_create_nonce( 'owlabkbs-remove-slide' ),
                'removeslide'    => __( 'Remove', 'owlabkbs' ),
                'save_nonce'     => wp_create_nonce( 'owlabkbs-save-meta' ),
                'saving'         => __( 'Saving...', 'owlabkbs' ),
                'sort'           => wp_create_nonce( 'owlabkbs-sort' ),
                'upgrade_nonce'  => wp_create_nonce( 'owlabkbs-upgrade' ),
                'videocaption'   => __( 'Video Slide Caption', 'owlabkbs' ),
                'videoslide'     => __( 'Video Slide Title', 'owlabkbs' ),
                'videoplace'     => __( 'Enter video slide title here...', 'owlabkbs' ),
                'videotitle'     => __( 'Video Slide URL', 'owlabkbs' ),
                'videothumb'     => __( 'Video Slide Thumbnail', 'owlabkbs' ),
                'videosrc'       => __( 'Enter your video thumbnail URL here...', 'owlabkbs' ),
                'videoselect'    => __( 'Choose Video Thumbnail', 'owlabkbs' ),
                'videodelete'    => __( 'Remove Video Thumbnail', 'owlabkbs' ),
                'videooutput'    => __( 'Enter your video URL here...', 'owlabkbs' ),
                'videoframe'     => __( 'Choose a Video Thumbnail', 'owlabkbs' ),
                'videouse'       => __( 'Select Thumbnail', 'owlabkbs' )
            )
        );

        // Load necessary HTML slide scripts and styles.
        wp_register_script( $this->base->plugin_slug . '-codemirror', plugins_url( 'assets/js/codemirror.js', $this->base->file ), array(), $this->base->version, true );
        wp_register_style( $this->base->plugin_slug . '-codemirror', plugins_url( 'assets/css/codemirror.css', $this->base->file ), array(), $this->base->version );
        wp_enqueue_script( $this->base->plugin_slug . '-codemirror' );
        wp_enqueue_style( $this->base->plugin_slug . '-codemirror' );

        // If on an owlabkbs post type, add custom CSS for hiding specific things.
        if ( isset( get_current_screen()->post_type ) && 'owlabkbs' == get_current_screen()->post_type ) {
            add_action( 'admin_head', array( $this, 'meta_box_css' ) );
        }

        // Fire a hook to load custom metabox scripts.
        do_action( 'owlabkbs_metabox_scripts' );

    }

    /**
     * Returns custom plupload init properties for the media uploader.
     *
     * @since 1.0.0
     *
     * @param int $post_id The current post ID.
     * @return array       Array of plupload init data.
     */
    public function get_plupload_init( $post_id ) {

        // Prepare $_POST form variables and apply backwards compat filter.
    	$post_params = array(
    	    'post_id'  => $post_id,
    	    '_wpnonce' => wp_create_nonce( 'media-form' ),
    	    'type'     => '',
    	    'tab'      => '',
    	    'short'    => 3
    	);
    	$post_params = apply_filters( 'upload_post_params', $post_params );

    	// Prepare upload size parameters.
        $max_upload_size = wp_max_upload_size();

        // Prepare the plupload init array.
        $plupload_init = array(
        	'runtimes'            => 'html5,silverlight,flash,html4',
        	'browse_button'       => 'owlabkbs-plupload-browse-button',
        	'container'           => 'owlabkbs-plupload-upload-ui',
        	'drop_element'        => 'owlabkbs-drag-drop-area',
        	'file_data_name'      => 'async-upload',
        	'multiple_queues'     => true,
        	'max_file_size'       => $max_upload_size . 'b',
        	'url'                 => admin_url( 'async-upload.php' ),
        	'flash_swf_url'       => includes_url( 'js/plupload/plupload.flash.swf' ),
        	'silverlight_xap_url' => includes_url( 'js/plupload/plupload.silverlight.xap' ),
        	'filters'             => array(
        	    array(
        	        'title'       => __( 'Allowed Files', 'owlabkbs' ),
        	        'extensions'  => '*'
                ),
            ),
        	'multipart'           => true,
        	'urlstream_upload'    => true,
        	'multipart_params'    => $post_params,
        	'resize'              => false
        );

        // If we are on a mobile device, disable multi selection.
        if ( wp_is_mobile() ) {
            $plupload_init['multi_selection'] = false;
        }

        // Apply backwards compat filter.
        $plupload_init = apply_filters( 'plupload_init', $plupload_init );

        // Return and apply a custom filter to our init data.
        return apply_filters( 'owlabkbs_plupload_init', $plupload_init, $post_id );

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
        do_action( 'owlabkbs_admin_css' );

    }

    /**
     * Creates metaboxes for handling and managing sliders.
     *
     * @since 1.0.0
     */
    public function add_meta_boxes() {

        // Let's remove all of those dumb metaboxes from our post type screen to control the experience.
        $this->remove_all_the_metaboxes();

        // Get all public post types.
        $post_types = get_post_types( array( 'public' => true ) );

        // Splice the owlabkbs post type since it is not visible to the public by default.
        $post_types[] = 'owlabkbs';

        // Loops through the post types and add the metaboxes.
        foreach ( (array) $post_types as $post_type ) {
            // Don't output boxes on these post types.
            if ( in_array( $post_type, $this->get_skipped_posttypes() ) ) {
                continue;
            }

            add_meta_box( 'owlabkbs', __( 'Slider Settings', 'owlabkbs' ), array( $this, 'meta_box_callback' ), $post_type, 'normal', 'high' );
        }

    }

    /**
     * Removes all the metaboxes except the ones I want on MY POST TYPE.
     *
     * @since 1.0.0
     *
     * @global array $wp_meta_boxes Array of registered metaboxes.
     * @return void
     */
    public function remove_all_the_metaboxes() {

        global $wp_meta_boxes;

        // This is the post type you want to target. Adjust it to match yours.
        $post_type  = 'owlabkbs';

        // These are the metabox IDs you want to pass over. They don't have to match exactly. preg_match will be run on them.
        $pass_over  = array( 'submitdiv', 'owlabkbs' );

        // All the metabox contexts you want to check.
        $contexts   = array( 'normal', 'advanced', 'side' );

        // All the priorities you want to check.
        $priorities = array( 'high', 'core', 'default', 'low' );

        // Loop through and target each context.
        foreach ( $contexts as $context ) {
            // Now loop through each priority and start the purging process.
            foreach ( $priorities as $priority ) {
                if ( isset( $wp_meta_boxes[$post_type][$context][$priority] ) ) {
                    foreach ( (array) $wp_meta_boxes[$post_type][$context][$priority] as $id => $metabox_data ) {
                        // If the metabox ID to pass over matches the ID given, remove it from the array and continue.
                        if ( in_array( $id, $pass_over ) ) {
                            unset( $pass_over[$id] );
                            continue;
                        }

                        // Otherwise, loop through the pass_over IDs and if we have a match, continue.
                        foreach ( $pass_over as $to_pass ) {
                            if ( preg_match( '#^' . $id . '#i', $to_pass ) ) {
                                continue;
                            }
                        }

                        // If we reach this point, remove the metabox completely.
                        unset( $wp_meta_boxes[$post_type][$context][$priority][$id] );
                    }
                }
            }
        }

    }

    /**
     * Callback for displaying content in the registered metabox.
     *
     * @since 1.0.0
     *
     * @param object $post The current post object.
     */
    public function meta_box_callback( $post ) {

        // Keep security first.
        wp_nonce_field( 'owlabkbs', 'owlabkbs' );

        // Check for our meta overlay helper.
        $slider_data = get_post_meta( $post->ID, '_owlabkbs_slider_data', true );
        


        ?>
        <div id="owlabkbs-tabs" class="owlabkbs-clear <?php echo $class; ?>">
            
            
            <?php foreach ( (array) $this->get_owlabkbs_tab_nav() as $id => $title ) :  ?>
                
                <div id="owlabkbs-<?php echo $id; ?>" class="owlabkbs-clear">
                    <h2><?php echo $title; ?></h2><hr>
                    <?php do_action( 'owlabkbs_tab_' . $id, $post ); ?>
                </div>
            <?php endforeach; ?>

            <?php $this->meta_helper( $post, $slider_data ); ?>
        </div>
        <?php

    }

    /**
     * Callback for getting all of the tabs for owlabkbs sliders.
     *
     * @since 1.0.0
     *
     * @return array Array of tab information.
     */
    public function get_owlabkbs_tab_nav() {

        $tabs = array(
            'images'     => __( 'Images', 'owlabkbs' ),
            'config'     => __( 'Config', 'owlabkbs' ),
        );
        $tabs = apply_filters( 'owlabkbs_tab_nav', $tabs );

        return $tabs;

    }

    /**
     * Callback for displaying the UI for main images tab.
     *
     * @since 1.0.0
     *
     * @param object $post The current post object.
     */
    public function images_tab( $post ) {

        

        // Output the slider type selection items.
        ?>
        <ul id="owlabkbs-types-nav" class="owlabkbs-clear" style="display:none;">
            <li class="owlabkbs-type-label"><span><?php _e( 'Slider Type', 'owlabkbs' ); ?></span></li>
            <?php $i = 0; foreach ( (array) $this->get_owlabkbs_types( $post ) as $id => $title ) : ?>
                <li>
                    <label for="owlabkbs-type-<?php echo $id; ?>">
                    <input id="owlabkbs-type-<?php echo sanitize_html_class( $id ); ?>" type="radio" name="_owlabkbs[type]" value="<?php echo $id; ?>"
                    <?php checked( $this->get_config( 'type', $this->get_config_default( 'type' ) ), $id ); ?> /> <?php echo $title; ?></label></li>
            <?php $i++; endforeach; ?>
            <li class="owlabkbs-type-spinner"><span class="spinner owlabkbs-spinner"></span></li>
        </ul>
        <?php

        // Output the display based on the type of slider being created.
        echo '<div id="owlabkbs-slider-main" class="owlabkbs-clear">';
            $this->images_display( $this->get_config( 'type', $this->get_config_default( 'type' ) ), $post );
        echo '</div>';

    }

    /**
     * Returns the types of sliders available.
     *
     * @since 1.0.0
     *
     * @param object $post The current post object.
     * @return array       Array of slider types to choose.
     */
    public function get_owlabkbs_types( $post ) {

        $types = array(
            'default' => __( 'Default', 'owlabkbs' )
        );

        return apply_filters( 'owlabkbs_slider_types', $types, $post );

    }

    /**
     * Determines the Images tab display based on the type of slider selected.
     *
     * @since 1.0.0
     *
     * @param string $type The type of display to output.
     * @param object $post The current post object.
     */
    public function images_display( $type = 'default', $post ) {

        // Output a unique hidden field for settings save testing for each type of slider.
        echo '<input type="hidden" name="_owlabkbs[type_' . $type . ']" value="1" />';

        // Output the display based on the type of slider available.
        switch ( $type ) {
            case 'default' :
                $this->do_default_display( $post );
                break;
            default:
                do_action( 'owlabkbs_display_' . $type, $post );
                break;
        }

    }

    /**
     * Callback for displaying the default slider UI.
     *
     * @since 1.0.0
     *
     * @param object $post The current post object.
     */
    public function do_default_display( $post ) {

        

        // Output the custom media upload form.
        Owlabkbs_Media::get_instance()->media_upload_form();

        // Prepare output data.
        $slider_data = get_post_meta( $post->ID, '_owlabkbs_slider_data', true );

        ?>
        <ul id="owlabkbs-output" class="owlabkbs-clear">
            <?php if ( ! empty( $slider_data['slider'] ) ) : ?>
                <?php foreach ( $slider_data['slider'] as $id => $data ) : ?>
                    <?php echo $this->get_slider_item( $id, $data, ( ! empty( $data['type'] ) ? $data['type'] : 'image' ), $post->ID ); ?>
                <?php endforeach; ?>
            <?php endif; ?>
        </ul>
        <?php $this->media_library( $post );

    }

    /**
     * Inserts the meta icon for displaying useful slider meta like shortcode and template tag.
     *
     * @since 1.0.0
     *
     * @param object $post        The current post object.
     * @param array $slider_data Array of slider data for the current post.
     * @return null               Return early if this is an auto-draft.
     */
    public function meta_helper( $post, $slider_data ) {

        


        ?>
        <div>
            
            
                <h2><?php _e('Usage','owlabkbs'); ?></h2><hr/>
                <p><?php _e( 'You can place this slider anywhere into your posts, pages, custom post types or widgets by using <strong>one</strong> of the shortcode(s) below:', 'owlabkbs' ); ?></p>
                <code><?php echo '[owlabkbs id="' . $post->ID . '"]'; ?></code>
                <?php if ( ! empty( $slider_data['config']['slug'] ) ) : ?>
                    <br><code><?php echo '[owlabkbs slug="' . $slider_data['config']['slug'] . '"]'; ?></code>
                <?php endif; ?>
                <p>&nbsp;</p>
            
        </div>
        <?php

    }

    /**
     * Callback for displaying the UI for selecting images from the media library to insert.
     *
     * @since 1.0.0
     *
     * @param object $post The current post object.
     */
    public function media_library( $post ) {

        ?>
        <div id="owlabkbs-upload-ui-wrapper">
            <div id="owlabkbs-upload-ui" class="owlabkbs-image-meta" style="display: none;">
                <div class="media-modal wp-core-ui">
                    <a class="media-modal-close" href="#"><span class="media-modal-icon"></span></a>
                    <div class="media-modal-content">
                        <div class="media-frame owlabkbs-media-frame wp-core-ui hide-menu owlabkbs-meta-wrap">
                            <div class="media-frame-title">
                                <h1><?php _e( 'Insert Slides into Slider', 'owlabkbs' ); ?></h1>
                            </div>
                            <div class="media-frame-router">
                                <div class="media-router">
                                    <a href="#" class="media-menu-item active" data-owlabkbs-content="image-slides"><?php _e( 'Image Slides', 'owlabkbs' ); ?></a>
                                    <?php do_action( 'owlabkbs_modal_router', $post ); ?>
                                </div><!-- end .media-router -->
                            </div><!-- end .media-frame-router -->
                            <?php $this->image_slides_content( $post ); ?>
                            <?php do_action( 'owlabkbs_modal_content', $post ); ?>
                            <div class="media-frame-toolbar">
                                <div class="media-toolbar">
                                    <div class="media-toolbar-primary">
                                        <a href="#" class="owlabkbs-media-insert button media-button button-large button-primary media-button-insert" title="<?php esc_attr_e( 'Insert Slides into Slider', 'owlabkbs' ); ?>"><?php _e( 'Insert Slides into Slider', 'owlabkbs' ); ?></a>
                                    </div><!-- end .media-toolbar-primary -->
                                </div><!-- end .media-toolbar -->
                            </div><!-- end .media-frame-toolbar -->
                        </div><!-- end .media-frame -->
                    </div><!-- end .media-modal-content -->
                </div><!-- end .media-modal -->
                <div class="media-modal-backdrop"></div>
            </div><!-- end .owlabkbs-image-meta -->
        </div><!-- end #owlabkbs-upload-ui-wrapper-->
        <?php

    }

    /**
     * Outputs the image slides content in the modal selection window.
     *
     * @since 1.0.0
     *
     * @param object $post The current post object.
     */
    public function image_slides_content( $post ) {

        ?>
        <div id="owlabkbs-image-slides">
            <div class="media-frame-content">
                <div class="attachments-browser">
                    <div class="media-toolbar owlabkbs-library-toolbar">
                        <div class="media-toolbar-primary">
                            <span class="spinner owlabkbs-spinner"></span><input type="search" placeholder="<?php esc_attr_e( 'Search', 'owlabkbs' ); ?>" id="owlabkbs-slider-search" class="search" value="" />
                        </div>
                        <div class="media-toolbar-secondary">
                            <a class="button media-button button-large button-secodary owlabkbs-load-library" href="#" data-owlabkbs-offset="20"><?php _e( 'Load More Images from Library', 'owlabkbs' ); ?></a><span class="spinner owlabkbs-spinner"></span>
                        </div>
                    </div>
                    <?php $library = get_posts( array( 'post_type' => 'attachment', 'post_mime_type' => 'image', 'post_status' => 'inherit', 'posts_per_page' => 20 ) ); ?>
                    <?php if ( $library ) : ?>
                    <ul class="attachments owlabkbs-slider">
                    <?php foreach ( (array) $library as $image ) :
                        $has_slider = get_post_meta( $image->ID, '_owlabkbs_has_slider', true );
                        $class       = $has_slider && in_array( $post->ID, (array) $has_slider ) ? ' selected owlabkbs-in-slider' : ''; ?>
                        <li class="attachment<?php echo $class; ?>" data-attachment-id="<?php echo absint( $image->ID ); ?>">
                            <div class="attachment-preview landscape">
                                <div class="thumbnail">
                                    <div class="centered">
                                        <?php $src = wp_get_attachment_image_src( $image->ID, 'thumbnail' ); ?>
                                        <img src="<?php echo esc_url( $src[0] ); ?>" />
                                    </div>
                                </div>
                                <a class="check" href="#"><div class="media-modal-icon"></div></a>
                            </div>
                        </li>
                    <?php endforeach; ?>
                    </ul><!-- end .owlabkbs-meta -->
                    <?php endif; ?>
                    <div class="media-sidebar">
                        <div class="owlabkbs-meta-sidebar">
                            <h3><?php _e( 'Helpful Tips', 'owlabkbs' ); ?></h3>
                            <strong><?php _e( 'Selecting Images', 'owlabkbs' ); ?></strong>
                            <p><?php _e( 'You can insert any image from your Media Library into your slider. If the image you want to insert is not shown on the screen, you can either click on the "Load More Images from Library" button to load more images or use the search box to find the images you are looking for.', 'owlabkbs' ); ?></p>
                        </div><!-- end .owlabkbs-meta-sidebar -->
                    </div><!-- end .media-sidebar -->
                </div><!-- end .attachments-browser -->
            </div><!-- end .media-frame-content -->
        </div><!-- end #owlabkbs-image-slides -->
        <?php

    }

    /**
     * Callback for displaying the UI for setting slider config options.
     *
     * @since 1.0.0
     *
     * @param object $post The current post object.
     */
    public function config_tab( $post ) {

        ?>
        <div id="owlabkbs-config">
            <p class="owlabkbs-intro"><?php _e( 'The settings below adjust the basic configuration options for the slider display.', 'owlabkbs' ); ?></p>
            <table class="form-table">
                <tbody>
                    
                    
                    <tr id="owlabkbs-config-slider-duration-box">
                        <th scope="row">
                            <label for="owlabkbs-config-duration"><?php _e( 'Slider Transition Duration', 'owlabkbs' ); ?></label>
                        </th>
                        <td>
                            <input id="owlabkbs-config-duration" type="number" name="_owlabkbs[duration]" value="<?php echo $this->get_config( 'duration', $this->get_config_default( 'duration' ) ); ?>" />
                            <p class="description"><?php _e( 'Sets the amount of time between each slide transition <strong>(in seconds)</strong>.', 'owlabkbs' ); ?></p>
                        </td>
                    </tr>

                    <tr id="owlabkbs-config-slider-zoom-box">
                        <th scope="row">
                            <label for="owlabkbs-config-zoom"><?php _e( 'Slider Transition zoom', 'owlabkbs' ); ?></label>
                        </th>
                        <td>
                            <input id="owlabkbs-config-zoom" type="text" name="_owlabkbs[zoom]" value="<?php echo $this->get_config( 'zoom', $this->get_config_default( 'zoom' ) ); ?>" />
                            <p class="description"><?php _e( 'Sets the transition zoom when moving ( eg: 1.2) .', 'owlabkbs' ); ?></p>
                        </td>
                    </tr>
                    <?php do_action( 'owlabkbs_config_box', $post ); ?>
                </tbody>
            </table>
            
        </div>
        <?php

    }



    /**
     * Callback for saving values from owlabkbs metaboxes.
     *
     * @since 1.0.0
     *
     * @param int $post_id The current post ID.
     * @param object $post The current post object.
     */
    public function save_meta_boxes( $post_id, $post ) {

        // Bail out if we fail a security check.
        if ( ! isset( $_POST['owlabkbs'] ) || ! wp_verify_nonce( $_POST['owlabkbs'], 'owlabkbs' ) || ! isset( $_POST['_owlabkbs'] ) ) {
            return;
        }

        // Bail out if running an autosave, ajax, cron or revision.
        if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
            return;
        }

        if ( defined( 'DOING_AJAX' ) && DOING_AJAX ) {
            return;
        }

        if ( defined( 'DOING_CRON' ) && DOING_CRON ) {
            return;
        }

        if ( wp_is_post_revision( $post_id ) ) {
            return;
        }

        // Bail out if the user doesn't have the correct permissions to update the slider.
        if ( ! current_user_can( 'edit_post', $post_id ) ) {
            return;
        }

        

        // Sanitize all user inputs.
        $settings = get_post_meta( $post_id, '_owlabkbs_slider_data', true );
        if ( empty( $settings ) ) {
            $settings = array();
        }

        // If the ID of the slider is not set or is lost, replace it now.
        if ( empty( $settings['id'] ) || ! $settings['id'] ) {
            $settings['id'] = $post_id;
        }

        // Save the config settings.
        $settings['config']['type']          = isset( $_POST['_owlabkbs']['type'] ) ? $_POST['_owlabkbs']['type'] : $this->get_config_default( 'type' );
        $settings['config']['duration']      = absint( $_POST['_owlabkbs']['duration'] );
        $settings['config']['zoom']         = $_POST['_owlabkbs']['zoom'] ;

        // If on an owlabkbs post type, map the title and slug of the post object to the custom fields if no value exists yet.
        if ( isset( $post->post_type ) && 'owlabkbs' == $post->post_type ) {
            if ( empty( $settings['config']['title'] ) ) {
                $settings['config']['title'] = trim( strip_tags( $post->post_title ) );
            }

            if ( empty( $settings['config']['slug'] ) ) {
                $settings['config']['slug'] = sanitize_text_field( $post->post_name );
            }
        }

        // Provide a filter to override settings.
        $settings = apply_filters( 'owlabkbs_save_settings', $settings, $post_id, $post );

        // Update the post meta.
        update_post_meta( $post_id, '_owlabkbs_slider_data', $settings );

        // Change states of images in slider from pending to active.
        $this->change_slider_states( $post_id );


        // Fire a hook for addons that need to utilize the cropping feature.
        do_action( 'owlabkbs_saved_settings', $settings, $post_id, $post );

        // Finally, flush all slider caches to ensure everything is up to date.
        $this->flush_slider_caches( $post_id, $settings['config']['slug'] );

    }

    /**
     * Helper method for retrieving the slider layout for an item in the admin.
     *
     * @since 1.0.0
     *
     * @param int $id The  ID of the item to retrieve.
     * @param array $data  Array of data for the item.
     * @param string $type The type of slide to retrieve.
     * @param int $post_id The current post ID.
     * @return string The  HTML output for the slider item.
     */
    public function get_slider_item( $id, $data, $type, $post_id = 0 ) {


        $item = $this->get_slider_image( $id, $data, $post_id );


        return apply_filters( 'owlabkbs_slide_item', $item, $id, $data, $type, $post_id );

    }

    /**
     * Helper method for retrieving the slider image layout in the admin.
     *
     * @since 1.0.0
     *
     * @param int $id The  ID of the item to retrieve.
     * @param array $data  Array of data for the item.
     * @param int $post_id The current post ID.
     * @return string The  HTML output for the slider item.
     */
    public function get_slider_image( $id, $data, $post_id = 0 ) {

        $thumbnail = wp_get_attachment_image_src( $id, 'thumbnail' ); ob_start(); ?>
        <li id="<?php echo $id; ?>" class="owlabkbs-slide owlabkbs-image owlabkbs-status-<?php echo $data['status']; ?>" data-owlabkbs-slide="<?php echo $id; ?>">
            <img src="<?php echo esc_url( $thumbnail[0] ); ?>" alt="<?php esc_attr_e( $data['alt'] ); ?>" />
            <a href="#" class="owlabkbs-remove-slide" title="<?php esc_attr_e( 'Remove Image Slide from Slider?', 'owlabkbs' ); ?>"></a>
            <a href="#" class="owlabkbs-modify-slide" title="<?php esc_attr_e( 'Modify Image Slide', 'owlabkbs' ); ?>"></a>
            <?php echo $this->get_slider_image_meta( $id, $data, $post_id ); ?>
        </li>
        <?php
        return ob_get_clean();

    }

    /**
     * Helper method for retrieving the slider image metadata.
     *
     * @since 1.0.0
     *
     * @param int $id      The ID of the item to retrieve.
     * @param array $data  Array of data for the item.
     * @param int $post_id The current post ID.
     * @return string      The HTML output for the slider item.
     */
    public function get_slider_image_meta( $id, $data, $post_id ) {

        ob_start();
        ?>
        <div id="owlabkbs-meta-<?php echo $id; ?>" class="owlabkbs-meta-container" style="display:none;">
            <div class="media-modal wp-core-ui">
                <a class="media-modal-close" href="#"><span class="media-modal-icon"></span></a>
                <div class="media-modal-content">
                    <div class="media-frame owlabkbs-media-frame wp-core-ui hide-menu hide-router owlabkbs-meta-wrap">
                        <div class="media-frame-title">
                            <h1><?php _e( 'Edit Metadata', 'owlabkbs' ); ?></h1>
                        </div>
                        <div class="media-frame-content">
                            <div class="attachments-browser">
                                <div class="owlabkbs-meta attachments">
                                    <?php do_action( 'owlabkbs_before_image_meta_table', $id, $data, $post_id ); ?>
                                    <table id="owlabkbs-meta-table-<?php echo $id; ?>" class="form-table owlabkbs-meta-table" data-owlabkbs-meta-id="<?php echo $id; ?>">
                                        <tbody>

                                            <?php do_action( 'owlabkbs_before_image_meta_settings', $id, $data, $post_id ); ?>
                                            <tr id="owlabkbs-title-box-<?php echo $id; ?>" valign="middle">
                                                <th scope="row"><label for="owlabkbs-title-<?php echo $id; ?>"><?php _e( 'Image Title', 'owlabkbs' ); ?></label></th>
                                                <td>
                                                    <input id="owlabkbs-title-<?php echo $id; ?>" class="owlabkbs-title" type="text" name="_owlabkbs[meta_title]" value="<?php echo ( ! empty( $data['title'] ) ? esc_attr( $data['title'] ) : '' ); ?>" data-owlabkbs-meta="title" />
                                                    <p class="description"><?php _e( 'Sets the image title attribute for the image.', 'owlabkbs' ); ?></p>
                                                </td>
                                            </tr>


                                            <?php do_action( 'owlabkbs_before_image_meta_alt', $id, $data, $post_id ); ?>
                                            <tr id="owlabkbs-alt-box-<?php echo $id; ?>" valign="middle">
                                                <th scope="row"><label for="owlabkbs-alt-<?php echo $id; ?>"><?php _e( 'Image Alt Text', 'owlabkbs' ); ?></label></th>
                                                <td>
                                                    <input id="owlabkbs-alt-<?php echo $id; ?>" class="owlabkbs-alt" type="text" name="_owlabkbs[meta_alt]" value="<?php echo ( ! empty( $data['alt'] ) ? esc_attr( $data['alt'] ) : '' ); ?>" data-owlabkbs-meta="alt" />
                                                    <p class="description"><?php _e( 'The image alt text is used for SEO. You should probably fill this one out!', 'owlabkbs' ); ?></p>
                                                </td>
                                            </tr>


                                            <?php do_action( 'owlabkbs_before_image_meta_link', $id, $data, $post_id ); ?>
                                            <tr id="owlabkbs-link-box-<?php echo $id; ?>" class="owlabkbs-link-cell" valign="middle">
                                                <th scope="row"><label for="owlabkbs-link-<?php echo $id; ?>"><?php _e( 'Image Hyperlink', 'owlabkbs' ); ?></label></th>
                                                <td>
                                                    <input id="owlabkbs-link-<?php echo $id; ?>" class="owlabkbs-link" type="text" name="_owlabkbs[meta_link]" value="<?php echo ( ! empty( $data['link'] ) ? esc_url( $data['link'] ) : '' ); ?>" data-owlabkbs-meta="link" />
                                                    <p class="description"><?php _e( 'The image hyperlink determines what opens once the image is clicked. If left empty, no link will be added.', 'owlabkbs' ); ?></p>
                                                </td>
                                            </tr>

                                            

                                            <?php do_action( 'owlabkbs_before_image_meta_cdir', $id, $data, $post_id ); ?>
                                            <tr id="owlabkbs-cdir-box-<?php echo $id; ?>" class="owlabkbs-cdir-cell" valign="middle">
                                                <th scope="row"><label for="owlabkbs-cdir-<?php echo $id; ?>"><?php _e( 'Caption Start point', 'owlabkbs' ); ?></label></th>
                                                <td>
                                                    
                                                    <select id="owlabkbs-cdir-<?php echo $id; ?>" class="owlabkbs-cdir" name="_owlabkbs[meta_cdir]" data-owlabkbs-meta="cdir"> 
                                                      <option value="bottom-left"  <?php echo ( (! empty( $data['cdir'] ) && $data['cdir'] == 'bottom-left') ? 'selected' : '' ); ?>>Bottom-left</option>
                                                      <option value="bottom-right" <?php echo ( (! empty( $data['cdir'] ) && $data['cdir'] == 'bottom-right') ? 'selected' : '' ); ?> >Bottom-right</option>
                                                      <option value="top-left" <?php echo ( (! empty( $data['cdir'] ) && $data['cdir'] == 'top-left') ? 'selected' : '' ); ?>>Top-left</option>
                                                      <option value="top-right" <?php echo ( (! empty( $data['cdir'] ) && $data['cdir'] == 'top-right') ? 'selected' : '' ); ?>>Top-right</option>
                                                      <!--<option value="center-center" <?php echo ( (! empty( $data['cdir'] ) && $data['cdir'] == 'center-center') ? 'selected' : '' ); ?>>Center-center</option>-->
                                                    </select>
                                                    <p class="description"><?php _e( 'You can specify the start point of the caption can be 4 values only: 1)top-right 2)top-left 3)buttom-right 4)buttom-left', 'owlabkbs' ); ?></p>
                                                </td>
                                            </tr>



                                            <?php do_action( 'owlabkbs_before_image_meta_caption', $id, $data, $post_id ); ?>
                                            <tr id="owlabkbs-caption-box-<?php echo $id; ?>" valign="middle">
                                                <th scope="row"><label for="owlabkbs-caption-<?php echo $id; ?>"><?php _e( 'Image Caption', 'owlabkbs' ); ?></label></th>
                                                <td>
                                                    <?php wp_editor( ( ! empty( $data['caption'] ) ? $data['caption'] : '' ), 'owlabkbs-caption-' . $id, array( 'media_buttons' => false, 'wpautop' => false, 'tinymce' => false, 'textarea_name' => '_owlabkbs[meta_caption]', 'quicktags' => array( 'buttons' => 'strong,em,link,ul,ol,li,close' ) ) ); ?>
                                                    <p class="description"><?php _e( 'Image captions can take any type of HTML. <br><strong>remove the content of caption if you dont want to show caption on Image.</stong>', 'owlabkbs' ); ?></p>
                                                </td>
                                            </tr>
                                            <?php do_action( 'owlabkbs_after_image_meta_settings', $id, $data, $post_id ); ?>

                                            

                                        </tbody>
                                    </table>
                                    <?php do_action( 'owlabkbs_after_image_meta_table', $id, $data, $post_id ); ?>
                                </div><!-- end .owlabkbs-meta -->
                                
                            </div><!-- end .attachments-browser -->
                        </div><!-- end .media-frame-content -->
                        <div class="media-frame-toolbar">
                            <div class="media-toolbar">
                                <div class="media-toolbar-primary">
                                    <a href="#" class="owlabkbs-meta-submit button media-button button-large button-primary media-button-insert" title="<?php esc_attr_e( 'Save Metadata', 'owlabkbs' ); ?>" data-owlabkbs-item="<?php echo $id; ?>"><?php _e( 'Save Metadata', 'owlabkbs' ); ?></a>
                                </div><!-- end .media-toolbar-primary -->
                            </div><!-- end .media-toolbar -->
                        </div><!-- end .media-frame-toolbar -->
                    </div><!-- end .media-frame -->
                </div><!-- end .media-modal-content -->
            </div><!-- end .media-modal -->
            <div class="media-modal-backdrop"></div>
        </div>
        <?php
        return ob_get_clean();

    }

    /**
     * Helper method to change a slider state from pending to active. This is done
     * automatically on post save. For previewing sliders before publishing,
     * simply click the "Preview" button and owlabkbs will load all the images present
     * in the slider at that time.
     *
     * @since 1.0.0
     *
     * @param int $id The current post ID.
     */
    public function change_slider_states( $post_id ) {

        $slider_data = get_post_meta( $post_id, '_owlabkbs_slider_data', true );
        if ( ! empty( $slider_data['slider'] ) ) {
            foreach ( (array) $slider_data['slider'] as $id => $item ) {
                $slider_data['slider'][$id]['status'] = 'active';
            }
        }

        update_post_meta( $post_id, '_owlabkbs_slider_data', $slider_data );

    }

    

    /**
     * Helper method to flush slider caches once a slider is updated.
     *
     * @since 1.0.0
     *
     * @param int $post_id The current post ID.
     * @param string $slug The unique slider slug.
     */
    public function flush_slider_caches( $post_id, $slug ) {

        Owlabkbs_Common::get_instance()->flush_slider_caches( $post_id, $slug );

    }

    /**
     * Helper method for retrieving config values.
     *
     * @since 1.0.0
     *
     * @global int $id        The current post ID.
     * @global object $post   The current post object.
     * @param string $key     The config key to retrieve.
     * @param string $default A default value to use.
     * @return string         Key value on success, empty string on failure.
     */
    public function get_config( $key, $default = false ) {

        global $id, $post;

        // Get the current post ID. If ajax, grab it from the $_POST variable.
        if ( defined( 'DOING_AJAX' ) && DOING_AJAX ) {
            $post_id = absint( $_POST['post_id'] );
        } else {
            $post_id = isset( $post->ID ) ? $post->ID : (int) $id;
        }

        $settings = get_post_meta( $post_id, '_owlabkbs_slider_data', true );
        if ( isset( $settings['config'][$key] ) ) {
            return $settings['config'][$key];
        } else {
            return $default ? $default : '';
        }

    }

    /**
     * Helper method for setting default config values.
     *
     * @since 1.0.0
     *
     * @param string $key The default config key to retrieve.
     * @return string Key value on success, false on failure.
     */
    public function get_config_default( $key ) {

        $instance = Owlabkbs_Common::get_instance();
        return $instance->get_config_default( $key );

    }



    /**
     * Returns the post types to skip for loading owlabkbs metaboxes.
     *
     * @since 1.0.0
     *
     * @return array Array of skipped posttypes.
     */
    public function get_skipped_posttypes() {

        $post_types = get_post_types( array( 'public' => true ) );
        unset( $post_types['owlabkbs'] );
        return apply_filters( 'owlabkbs_skipped_posttypes', $post_types );

    }


    /**
     * Returns the singleton instance of the class.
     *
     * @since 1.0.0
     *
     * @return object The Owlabkbs_Metaboxes object.
     */
    public static function get_instance() {

        if ( ! isset( self::$instance ) && ! ( self::$instance instanceof Owlabkbs_Metaboxes ) ) {
            self::$instance = new Owlabkbs_Metaboxes();
        }

        return self::$instance;

    }

}

// Load the metabox class.
$owlabkbs_Metaboxes = Owlabkbs_Metaboxes::get_instance();