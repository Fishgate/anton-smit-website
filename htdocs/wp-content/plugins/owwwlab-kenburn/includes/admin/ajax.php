<?php
/**
 * Handles all admin ajax interactions for the owlabkbs plugin.
 *
 * @since 1.0.0
 *
 * @package owwwlab-kenburn
 * @author  owwwlab
 */





add_action( 'wp_ajax_owlabkbs_load_image', 'owlabkbs_ajax_load_image' );
/**
 * Loads an image into a slider.
 *
 * @since 1.0.0
 */
function owlabkbs_ajax_load_image() {

    // Run a security check first.
    check_ajax_referer( 'owlabkbs-load-image', 'nonce' );

    // Prepare variables.
    $id      = absint( $_POST['id'] );
    $post_id = absint( $_POST['post_id'] );

    // Set post meta to show that this image is attached to one or more sliders.
    $has_slider = get_post_meta( $id, '_owlabkbs_has_slider', true );
    if ( empty( $has_slider ) ) {
        $has_slider = array();
    }

    $has_slider[] = $post_id;
    update_post_meta( $id, '_owlabkbs_has_slider', $has_slider );

    // Set post meta to show that this image is attached to a slider on this page.
    $in_slider = get_post_meta( $post_id, '_owlabkbs_in_slider', true );
    if ( empty( $in_slider ) ) {
        $in_slider = array();
    }

    $in_slider[] = $id;
    update_post_meta( $post_id, '_owlabkbs_in_slider', $in_slider );

    // Set data and order of image in slider.
    $slider_data = get_post_meta( $post_id, '_owlabkbs_slider_data', true );
    if ( empty( $slider_data ) ) {
        $slider_data = array();
    }

    // If no slider ID has been set, set it now.
    if ( empty( $slider_data['id'] ) ) {
        $slider_data['id'] = $post_id;
    }

    // Set data and update the meta information.
    $slider_data = owlabkbs_ajax_prepare_slider_data( $slider_data, $id );
    update_post_meta( $post_id, '_owlabkbs_slider_data', $slider_data );

    // Run hook before building out the item.
    do_action( 'owlabkbs_ajax_load_image', $id, $post_id );

    // Build out the individual HTML output for the slider image that has just been uploaded.
    $html = Owlabkbs_Metaboxes::get_instance()->get_slider_item( $id, $slider_data['slider'][$id], 'image', $post_id );

    // Flush the slider cache.
    Owlabkbs_Common::get_instance()->flush_slider_caches( $post_id );

    echo json_encode( $html );
    die;

}






add_action( 'wp_ajax_owlabkbs_load_library', 'owlabkbs_ajax_load_library' );
/**
 * Loads the Media Library images into the media modal window for selection.
 *
 * @since 1.0.0
 */
function owlabkbs_ajax_load_library() {

    // Run a security check first.
    check_ajax_referer( 'owlabkbs-load-slider', 'nonce' );

    // Prepare variables.
    $offset  = (int) $_POST['offset'];
    $post_id = absint( $_POST['post_id'] );
    $html    = '';

    // Grab the library contents with the included offset parameter.
    $library = get_posts( array( 'post_type' => 'attachment', 'post_mime_type' => 'image', 'post_status' => 'any', 'posts_per_page' => 20, 'offset' => $offset ) );
    if ( $library ) {
        foreach ( (array) $library as $image ) {
            $has_slider = get_post_meta( $image->ID, '_owlabkbs_has_slider', true );
            $class       = $has_slider && in_array( $post_id, (array) $has_slider ) ? ' selected owlabkbs-in-slider' : '';

            $html .= '<li class="attachment' . $class . '" data-attachment-id="' . absint( $image->ID ) . '">';
                $html .= '<div class="attachment-preview landscape">';
                    $html .= '<div class="thumbnail">';
                        $html .= '<div class="centered">';
                            $src = wp_get_attachment_image_src( $image->ID, 'thumbnail' );
                            $html .= '<img src="' . esc_url( $src[0] ) . '" />';
                        $html .= '</div>';
                    $html .= '</div>';
                    $html .= '<a class="check" href="#"><div class="media-modal-icon"></div></a>';
                $html .= '</div>';
            $html .= '</li>';
        }
    }

    echo json_encode( array( 'html' => stripslashes( $html ) ) );
    die;

}






add_action( 'wp_ajax_owlabkbs_library_search', 'owlabkbs_ajax_library_search' );
/**
 * Searches the Media Library for images matching the term specified in the search.
 *
 * @since 1.0.0
 */
function owlabkbs_ajax_library_search() {

    // Run a security check first.
    check_ajax_referer( 'owlabkbs-library-search', 'nonce' );

    // Prepare variables.
    $search  = stripslashes( $_POST['search'] );
    $post_id = absint( $_POST['post_id'] );
    $html    = '';

    // Grab the library contents with the included offset parameter.
    $library = get_posts( array( 'post_type' => 'attachment', 'post_mime_type' => 'image', 'post_status' => 'any', 'posts_per_page' => -1, 's' => $search ) );
    if ( $library ) {
        foreach ( (array) $library as $image ) {
            $has_slider = get_post_meta( $image->ID, '_owlabkbs_has_slider', true );
            $class       = $has_slider && in_array( $post_id, (array) $has_slider ) ? ' selected owlabkbs-in-slider' : '';

            $html .= '<li class="attachment' . $class . '" data-attachment-id="' . absint( $image->ID ) . '">';
                $html .= '<div class="attachment-preview landscape">';
                    $html .= '<div class="thumbnail">';
                        $html .= '<div class="centered">';
                            $src = wp_get_attachment_image_src( $image->ID, 'thumbnail' );
                            $html .= '<img src="' . esc_url( $src[0] ) . '" />';
                        $html .= '</div>';
                    $html .= '</div>';
                    $html .= '<a class="check" href="#"><div class="media-modal-icon"></div></a>';
                $html .= '</div>';
            $html .= '</li>';
        }
    }

    echo json_encode( array( 'html' => stripslashes( $html ) ) );
    die;

}




add_action( 'wp_ajax_owlabkbs_insert_slides', 'owlabkbs_ajax_insert_slides' );
/**
 * Inserts one or more slides into a slider.
 *
 * @since 1.0.0
 */
function owlabkbs_ajax_insert_slides() {

    // Run a security check first.
    check_ajax_referer( 'owlabkbs-insert-images', 'nonce' );

    // Prepare variables.
    $images  = ! empty( $_POST['images'] ) ? stripslashes_deep( (array) $_POST['images'] ) : array();
    $post_id = absint( $_POST['post_id'] );

    // Grab and update any slider data if necessary.
    $in_slider = get_post_meta( $post_id, '_owlabkbs_in_slider', true );
    if ( empty( $in_slider ) ) {
        $in_slider = array();
    }

    // Set data and order of image in slider.
    $slider_data = get_post_meta( $post_id, '_owlabkbs_slider_data', true );
    if ( empty( $slider_data ) ) {
        $slider_data = array();
    }

    // If no slider ID has been set, set it now.
    if ( empty( $slider_data['id'] ) ) {
        $slider_data['id'] = $post_id;
    }

    // Loop through the images and add them to the slider.
    foreach ( (array) $images as $i => $id ) {
        // Update the attachment image post meta first.
        $has_slider = get_post_meta( $id, '_owlabkbs_has_slider', true );
        if ( empty( $has_slider ) ) {
            $has_slider = array();
        }

        $has_slider[] = $post_id;
        update_post_meta( $id, '_owlabkbs_has_slider', $has_slider );

        // Now add the image to the slider for this particular post.
        $in_slider[] = $id;
        $slider_data = owlabkbs_ajax_prepare_slider_data( $slider_data, $id );
    }


    // Update the slider data.
    update_post_meta( $post_id, '_owlabkbs_in_slider', $in_slider );
    update_post_meta( $post_id, '_owlabkbs_slider_data', $slider_data );

    // Run hook before finishing.
    do_action( 'owlabkbs_ajax_insert_slides', $images, $videos, $html, $post_id );

    // Flush the slider cache.
    Owlabkbs_Common::get_instance()->flush_slider_caches( $post_id );

    echo json_encode( true );
    die;

}




add_action( 'wp_ajax_owlabkbs_sort_images', 'owlabkbs_ajax_sort_images' );
/**
 * Sorts images based on user-dragged position in the slider.
 *
 * @since 1.0.0
 */
function owlabkbs_ajax_sort_images() {

    // Run a security check first.
    check_ajax_referer( 'owlabkbs-sort', 'nonce' );

    // Prepare variables.
    $order       = explode( ',', $_POST['order'] );
    $post_id     = absint( $_POST['post_id'] );
    $slider_data = get_post_meta( $post_id, '_owlabkbs_slider_data', true );
    $new_order   = array();

    // Loop through the order and generate a new array based on order received.
    foreach ( $order as $id ) {
        $new_order['slider'][$id] = $slider_data['slider'][$id];
    }

    // Update the slider data.
    update_post_meta( $post_id, '_owlabkbs_slider_data', $new_order );

    // Flush the slider cache.
    Owlabkbs_Common::get_instance()->flush_slider_caches( $post_id );

    echo json_encode( true );
    die;

}




add_action( 'wp_ajax_owlabkbs_remove_slide', 'owlabkbs_ajax_remove_slide' );
/**
 * Removes an image from a slider.
 *
 * @since 1.0.0
 */
function owlabkbs_ajax_remove_slide() {

    // Run a security check first.
    check_ajax_referer( 'owlabkbs-remove-slide', 'nonce' );

    // Prepare variables.
    $post_id     = absint( $_POST['post_id'] );
    $attach_id   = trim( $_POST['attachment_id'] );
    $slider_data = get_post_meta( $post_id, '_owlabkbs_slider_data', true );
    $in_slider   = get_post_meta( $post_id, '_owlabkbs_in_slider', true );
    $has_slider  = get_post_meta( $attach_id, '_owlabkbs_has_slider', true );

    // Unset the image from the slider, in_slider and has_slider checkers.
    unset( $slider_data['slider'][$attach_id] );

    if ( ( $key = array_search( $attach_id, (array) $in_slider ) ) !== false ) {
        unset( $in_slider[$key] );
    }

    if ( ( $key = array_search( $post_id, (array) $has_slider ) ) !== false ) {
        unset( $has_slider[$key] );
    }

    // Update the slider data.
    update_post_meta( $post_id, '_owlabkbs_slider_data', $slider_data );
    update_post_meta( $post_id, '_owlabkbs_in_slider', $in_slider );
    update_post_meta( $attach_id, '_owlabkbs_has_slider', $has_slider );

    // Run hook before finishing the reponse.
    do_action( 'owlabkbs_ajax_remove_slide', $attach_id, $post_id );

    // Flush the slider cache.
    Owlabkbs_Common::get_instance()->flush_slider_caches( $post_id );

    echo json_encode( true );
    die;

}

add_action( 'wp_ajax_owlabkbs_save_meta', 'owlabkbs_ajax_save_meta' );
/**
 * Saves the metadata for an image in a slider.
 *
 * @since 1.0.0
 */
function owlabkbs_ajax_save_meta() {

    // Run a security check first.
    check_ajax_referer( 'owlabkbs-save-meta', 'nonce' );

    // Prepare variables.
    $post_id     = absint( $_POST['post_id'] );
    $attach_id   = $_POST['attach_id'];
    $meta        = $_POST['meta'];
    $slider_data = get_post_meta( $post_id, '_owlabkbs_slider_data', true );

    // Save the different types of default meta fields for images.
    if ( isset( $meta['title'] ) ) {
        $slider_data['slider'][$attach_id]['title'] = trim( esc_html( $meta['title'] ) );
    }

    if ( isset( $meta['alt'] ) ) {
        $slider_data['slider'][$attach_id]['alt'] = trim( esc_html( $meta['alt'] ) );
    }

    if ( isset( $meta['link'] ) ) {
        $slider_data['slider'][$attach_id]['link'] = esc_url( $meta['link'] );
    }

    if ( isset( $meta['cdir'] ) ) {
        $slider_data['slider'][$attach_id]['cdir'] = trim( esc_html( $meta['cdir'] ));
    }

    if ( isset( $meta['caption'] ) ) {
        $slider_data['slider'][$attach_id]['caption'] = trim( $meta['caption'] );
    }

    // Allow filtering of meta before saving.
    $slider_data = apply_filters( 'owlabkbs_ajax_save_meta', $slider_data, $meta, $attach_id, $post_id );

    // Update the slider data.
    update_post_meta( $post_id, '_owlabkbs_slider_data', $slider_data );

    // Flush the slider cache.
    Owlabkbs_Common::get_instance()->flush_slider_caches( $post_id );

    echo json_encode( true );
    die;

}





add_action( 'wp_ajax_owlabkbs_refresh', 'owlabkbs_ajax_refresh' );
/**
 * Refreshes the DOM view for a slider.
 *
 * @since 1.0.0
 */
function owlabkbs_ajax_refresh() {

    // Run a security check first.
    check_ajax_referer( 'owlabkbs-refresh', 'nonce' );

    // Prepare variables.
    $post_id = absint( $_POST['post_id'] );
    $slider = '';

    // Grab all slider data.
    $slider_data = get_post_meta( $post_id, '_owlabkbs_slider_data', true );

    // If there are no slider items, don't do anything.
    if ( empty( $slider_data ) || empty( $slider_data['slider'] ) ) {
        echo json_encode( array( 'error' => true ) );
        die;
    }

    // Loop through the data and build out the slider view.
    foreach ( (array) $slider_data['slider'] as $id => $data ) {
        $slider .= Owlabkbs_Metaboxes::get_instance()->get_slider_item( $id, $data, $data['type'], $post_id );
    }

    echo json_encode( array( 'success' => $slider ) );
    die;

}




add_action( 'wp_ajax_owlabkbs_load_slider_data', 'owlabkbs_ajax_load_slider_data' );
/**
 * Retrieves and return slider data for the specified ID.
 *
 * @since 1.0.0
 */
function owlabkbs_ajax_load_slider_data() {

    // Prepare variables and grab the slider data.
    $slider_id   = absint( $_POST['id'] );
    $slider_data = get_post_meta( $slider_id, '_owlabkbs_slider_data', true );

    // Send back the slider data.
    echo json_encode( $slider_data );
    die;

}




/**
 * Helper function to prepare the metadata for an image in a slider.
 *
 * @since 1.0.0
 *
 * @param array $slider_data  Array of data for the slider.
 * @param int $id             The attachment ID to prepare data for.
 * @param string $type        The type of slide to prepare (defaults to image).
 * @param array $data         Data to be used for the slide.
 * @return array $slider_data Amended slider data with updated image metadata.
 */
function owlabkbs_ajax_prepare_slider_data( $slider_data, $id, $type = 'image', $data = array() ) {

    $attachment = get_post( $id );
    $url        = wp_get_attachment_image_src( $id, 'full' );
    $alt_text   = get_post_meta( $id, '_wp_attachment_image_alt', true );
    $slider_data['slider'][$id] = array(
        'status'  => 'pending',
        'src'     => isset( $url[0] ) ? esc_url( $url[0] ) : '',
        'title'   => get_the_title( $id ),
        'link'    => '',
        'alt'     => ! empty( $alt_text ) ? $alt_text : get_the_title( $id ),
        'cdir'    => 'bottom-left',
        'caption' => '<span class="sub-title">UPPER TITLE HERE</span><span class="title">TITLE HERE</span>',
        'type'    => $type
    );

    $slider_data = apply_filters( 'owlabkbs_ajax_item_data', $slider_data, $id, $type );

    return $slider_data;

}