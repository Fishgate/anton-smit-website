<?php
/**
 * Shortcode class.
 *
 * @since 1.0.0
 *
 * @package owlabkbs
 * @author  owlab
 */
class Owlabkbs_Shortcode {

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
     * Holds the slider data.
     *
     * @since 1.0.0
     *
     * @var array
     */
    public $data;

    /**
     * Holds slider IDs for init firing checks.
     *
     * @since 1.0.0
     *
     * @var array
     */
    public $done = array();

    /**
     * Iterator for sliders on the page.
     *
     * @since 1.0.0
     *
     * @var int
     */
    public $counter = 1;

    /**
     * Flag for YouTube videos.
     *
     * @since 1.0.0
     *
     * @var bool
     */
    public $youtube = false;

    /**
     * Flag for Vimeo videos.
     *
     * @since 1.0.0
     *
     * @var bool
     */
    public $vimeo = false;

    /**
     * Flag for Wistia videos.
     *
     * @since 1.0.0
     *
     * @var bool
     */
    public $wistia = false;

    /**
     * Flag for HTML slides.
     *
     * @since 1.0.0
     *
     * @var bool
     */
    public $html = false;

    public $inline = '';

    /**
     * Primary class constructor.
     *
     * @since 1.0.0
     */
    public function __construct() {

        // Load the base class object.
        $this->base = Owlabkbs::get_instance();

        // Register main slider style.
        wp_register_style( $this->base->plugin_slug . '-style', plugins_url( 'assets/css/owlabkbs.css', $this->base->file ), array(), $this->base->version );

        // Register main slider script.
        wp_register_script( $this->base->plugin_slug . '-script', plugins_url( 'assets/js/owlabkbs.js', $this->base->file ), array( 'jquery' ), $this->base->version, true );

        // Load hooks and filters.
        add_shortcode( 'owlabkbs', array( $this, 'shortcode' ) );
        //add_filter( 'widget_text', 'do_shortcode' );

    }

    /**
     * Creates the shortcode for the plugin.
     *
     * @since 1.0.0
     *
     * @global object $post The current post object.
     *
     * @param array $atts Array of shortcode attributes.
     * @return string     The slider output.
     */
    public function shortcode( $atts ) {

        global $post;

        // If no attributes have been passed, the slider should be pulled from the current post.
        $slider_id = false;
        if ( empty( $atts ) ) 
        {
            $slider_id = $post->ID;
            $data      = is_preview() ? $this->base->_get_slider( $slider_id ) : $this->base->get_slider( $slider_id );
        } 
        else if ( isset( $atts['id'] ) ) 
        {
            $slider_id = (int) $atts['id'];
            $data      = is_preview() ? $this->base->_get_slider( $slider_id ) : $this->base->get_slider( $slider_id );
        } 
        else if ( isset( $atts['slug'] ) ) 
        {
            $slider_id = $atts['slug'];
            $data      = is_preview() ? $this->base->_get_slider_by_slug( $slider_id ) : $this->base->get_slider_by_slug( $slider_id );
        } else {
            // A custom attribute must have been passed. Allow it to be filtered to grab data from a custom source.
            $data = apply_filters( 'owlabkbs_custom_slider_data', false, $atts, $post );
        }

        // If there is no data and the attribute used is an ID, try slug as well.
        if ( ! $data && isset( $atts['id'] ) ) {
            $slider_id = $atts['id'];
            $data      = is_preview() ? $this->base->_get_slider_by_slug( $slider_id ) : $this->base->get_slider_by_slug( $slider_id );
        }

         

        // If there is no data to output or the slider is inactive, do nothing.
        if ( ! $data || empty( $data['slider'] ) || isset( $data['status'] ) && 'inactive' == $data['status'] && ! is_preview() ) {
            return false;
        }


        // Prepare variables.
        $this->data[$data['id']] = $data;
        $slider                  = '';
        $i                       = 1;

        // If this is a feed view, customize the output and return early.
        if ( is_feed() ) {
            return $this->do_feed_output( $data );
        }

        // Load scripts and styles.
        wp_enqueue_style( $this->base->plugin_slug . '-style' );
        wp_enqueue_script( $this->base->plugin_slug . '-script' );


        // Load slider init code in the footer.
        add_action( 'wp_footer', array( $this, 'slider_init' ), 1000 );

        // Build out the slider HTML.
        $slider .= '<div class="kb-slider" id="kb-slider-'.sanitize_html_class( $data['id'] ).'">';
            foreach ( (array) $data['slider'] as $id => $item ) {
                
                // Skip over images that are pending (ignore if in Preview mode).
                if ( isset( $item['status'] ) && 'pending' == $item['status'] && ! is_preview() ) {
                    continue;
                }

                $out = '<div class="item">';
                    $out .= $this->get_image_slide( $id, $item, $data, $i );
                $out .= '</div>';

                $slider .= $out;

                // Increment the iterator.
                $i++;

            }
        $slider .= '</div><!-- end kb-slider-'.sanitize_html_class( $data['id'] ).' -->';
        // Increment the counter.
        $this->counter++;

        // Add no JS fallback support.
        $no_js   = apply_filters( 'owlabkbs_output_no_js', '<noscript><style type="text/css">#owlabkbs-container-' . sanitize_html_class( $data['id'] ) . '{opacity:1}</style></noscript>', $data );
        $slider .= $no_js;

        // Return the slider HTML.
        return $slider;

    }


    /**
     * Outputs the slider init script in the footer.
     *
     * @since 1.0.0
     */
    public function slider_init() {

        foreach ( $this->data as $id => $data ) {
            // Prevent multiple init scripts for the same slider ID.
            if ( in_array( $data['id'], $this->done ) ) {
                continue;
            }
            $this->done[] = $data['id'];

            $zoom = $this->get_config( "zoom", $data );
            $duration = $this->get_config( "duration", $data );

            $inline ='
                (function($){
                    
                        var $ = jQuery;
                        var owlabkbs_slider'.$data["id"].' = $("#kb-slider-'.$data["id"].'");

                        owlabkbs_slider'.$data["id"].'.kenburnIt({
                            zoom:'. $zoom .',
                            duration:'.$duration.'
                        });
                    
                })(jQuery);
            ';
            $this->inline .= $this->minify($inline);
            
        }

        add_action('wp_footer', array( $this,'append_js_to_footer'),100000,1);

    }

    /**
     * Echo exera js 
     *
     * @since 1.0.0
     *
     * @param string $inline the string of js that will be echo
     */
    public function append_js_to_footer() {
        $out = '<script type="text/javascript">';
        $out .= $this->inline;
        $out .='</script>';
        echo $out;
    }

    /**
     * Retrieves an individual image slide for the slider.
     *
     * @since 1.0.0
     *
     * @param int|string $id The ID for the slide.
     * @param array $item    Array of data for the slide.
     * @param array $data    Array of data for the slider.
     * @param int $i         The number of the slide in the slider.
     * @return string        HTML markup for the image slide.
     */
    public function get_image_slide( $id, $item, $data, $i ) {

        
        
        // Grab our image src and prepare our output.
        $imagesrc = $this->get_image_src( $id, $item, $data );
        $output   = '<img src="'.$imagesrc.'" alt="'.$item["alt"].'" />';


        //if we have caption add it
        if ( !empty($item['caption']) ){

            $output .= '<div class="caption"';
                if ( !empty($item['cdir']) ){ 
                    $output .= ' data-pos="'.$item['cdir'].'"';
                }
                $output .='>';

                if( !empty($item['link']) ){
                    $output .= '<a href="'.$item['link'].'">';
                }

                $output .= $item['caption'];

                if( !empty($item['link']) ){
                    $output .= '</a>';
                }


            $output .= '</div> <!-- /caption -->';

        }


        // Return our inner image slide HTML.
        return $output;

    }






    /**
     * Helper method to retrieve the proper image src attribute based on slider settings.
     *
     * @since 1.0.0
     *
     * @param int $id      The image attachment ID to use.
     * @param array $item  Slider item data.
     * @param array $data  The slider data to use for retrieval.
     * @param string $type The type of cropped image to retrieve.
     * @return string      The proper image src attribute for the image.
     */
    public function get_image_src( $id, $item, $data) {

        // Get the full image src. If it does not return the data we need, return the image link instead.
        $src   = wp_get_attachment_image_src( $id, 'full' );
        $image = ! empty( $src[0] ) ? $src[0] : false;

                

        // If no image, return with the base link.
        if ( ! $image ) {
            $image = ! empty( $item['src'] ) ? $item['src'] : false;
            if ( ! $image ) {
                return $item['link'];
            }
        }

        return $image;

        

    }


    /**
     * Helper method for retrieving config values.
     *
     * @since 1.0.0
     *
     * @param string $key The config key to retrieve.
     * @param array $data The slider data to use for retrieval.
     * @return string     Key value on success, default if not set.
     */
    public function get_config( $key, $data ) {

        $instance = Owlabkbs_Common::get_instance();
        return isset( $data['config'][$key] ) ? $data['config'][$key] : $instance->get_config_default( $key );

    }


    /**
     * Helper method to minify a string of data.
     *
     * @since 1.0.4
     *
     * @param string $string  String of data to minify.
     * @return string $string Minified string of data.
     */
    public function minify( $string ) {

        $clean = preg_replace( '/((?:\/\*(?:[^*]|(?:\*+[^*\/]))*\*+\/)|(?:\/\/.*))/', '', $string );
        $clean = str_replace( array( "\r\n", "\r", "\t", "\n", '  ', '    ', '     ' ), '', $clean );
        return apply_filters( 'owlabkbs_minified_string', $clean, $string );

    }



    /**
     * Outputs only the first image of the slider inside a regular <div> tag
     * to avoid styling issues with feeds.
     *
     * @since 1.0.0
     *
     * @param array $data     Array of slider data.
     * @return string $slider Custom slider output for feeds.
     */
    public function do_feed_output( $data ) {

        $slider = '<div class="owlabkbs-feed-output">';
            foreach ( $data['slider'] as $id => $item ) {
                // Skip over images that are pending (ignore if in Preview mode).
                if ( isset( $item['status'] ) && 'pending' == $item['status'] && ! is_preview() ) {
                    continue;
                }

                $imagesrc = $this->get_image_src( $id, $item, $data );
                $slider  .= '<img class="owlabkbs-feed-image" src="' . esc_url( $imagesrc ) . '" title="' . esc_attr( $item['title'] ) . '" alt="' . esc_attr( $item['alt'] ) . '" />';
                break;
             }
        $slider .= '</div>';

        return apply_filters( 'owlabkbs_feed_output', $slider, $data );

    }


	/**
     * Returns the singleton instance of the class.
     *
     * @since 1.0.0
     *
     * @return object The Owlabkbs_Shortcode object.
     */
    public static function get_instance() {

        if ( ! isset( self::$instance ) && ! ( self::$instance instanceof Owlabkbs_Shortcode ) ) {
            self::$instance = new Owlabkbs_Shortcode();
        }

        return self::$instance;

    }
}

// Load the shortcode class.
$owlabkbs_shortcode = Owlabkbs_Shortcode::get_instance();