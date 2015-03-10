/* ==========================================================
 * metabox.js
 * http://owlabkbswp.com/
 * ==========================================================
 * Copyright 2014 Thomas Griffin.
 *
 * Licensed under the GPL License, Version 2.0 or later (the "License");
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at
 *
 * http://www.gnu.org/licenses/gpl-2.0.html
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 * ========================================================== */
;(function($){
    $(function(){
        // Initialize the slider tabs.
        var owlabkbs_tabs           = $('#owlabkbs-tabs'),
            owlabkbs_tabs_nav       = $('#owlabkbs-tabs-nav'),
            owlabkbs_tabs_hash      = window.location.hash,
            owlabkbs_tabs_hash_sani = window.location.hash.replace('!', '');

        // If we have a hash and it begins with "owlabkbs-tab", set the proper tab to be opened.
        if ( owlabkbs_tabs_hash && owlabkbs_tabs_hash.indexOf('owlabkbs-tab-') >= 0 ) {
            $('.owlabkbs-active').removeClass('owlabkbs-active');
            owlabkbs_tabs_nav.find('li a[href="' + owlabkbs_tabs_hash_sani + '"]').parent().addClass('owlabkbs-active');
            owlabkbs_tabs.find(owlabkbs_tabs_hash_sani).addClass('owlabkbs-active').show();

            // Update the post action to contain our hash so the proper tab can be loaded on save.
            var post_action = $('#post').attr('action');
            if ( post_action ) {
                post_action = post_action.split('#')[0];
                $('#post').attr('action', post_action + owlabkbs_tabs_hash);
            }
        }

        // Change tabs on click.
        $(document).on('click', '#owlabkbs-tabs-nav li a', function(e){
            e.preventDefault();
            var $this = $(this);
            if ( $this.parent().hasClass('owlabkbs-active') ) {
                return;
            } else {
                window.location.hash = owlabkbs_tabs_hash = this.hash.split('#').join('#!');
                var current = owlabkbs_tabs_nav.find('.owlabkbs-active').removeClass('owlabkbs-active').find('a').attr('href');
                $this.parent().addClass('owlabkbs-active');
                owlabkbs_tabs.find(current).removeClass('owlabkbs-active').hide();
                owlabkbs_tabs.find($this.attr('href')).addClass('owlabkbs-active').show();

                // Update the post action to contain our hash so the proper tab can be loaded on save.
                var post_action = $('#post').attr('action');
                if ( post_action ) {
                    post_action = post_action.split('#')[0];
                    $('#post').attr('action', post_action + owlabkbs_tabs_hash);
                }
            }
        });

        // Load plupload if necessary.
        var owlabkbs_uploader;
        if ( $('input[name="_owlabkbs[type]"]').length > 0 && 'default' == $('input[name="_owlabkbs[type]"]:checked').val() ) {
            owlabkbsPlupload();
        }

        // Conditionally show necessary fields.
        owlabkbsConditionals();

        // Handle the meta icon helper.
        if ( 0 !== $('.owlabkbs-helper-needed').length ) {
            $('<div class="owlabkbs-meta-helper-overlay" />').prependTo('#owlabkbs');
        }

        $(document).on('click', '.owlabkbs-meta-icon', function(e){
            e.preventDefault();
            var $this     = $(this),
                container = $this.parent(),
                helper    = $this.next();
            if ( helper.is(':visible') ) {
                $('.owlabkbs-meta-helper-overlay').remove();
                container.removeClass('owlabkbs-helper-active');
            } else {
                if ( 0 === $('.owlabkbs-meta-helper-overlay').length ) {
                    $('<div class="owlabkbs-meta-helper-overlay" />').prependTo('#owlabkbs');
                }
                container.addClass('owlabkbs-helper-active');
            }
        });

        // Handle switching between different slider types.
        $(document).on('change', 'input[name="_owlabkbs[type]"]:radio', function(e){
            var $this = $(this);
            $('.owlabkbs-type-spinner .owlabkbs-spinner').css({'display' : 'inline-block', 'margin-top' : '-1px'});

            // Prepare our data to be sent via Ajax.
            var change = {
                action:  'owlabkbs_change_type',
                post_id: owlabkbs_metabox.id,
                type:    $this.val(),
                nonce:   owlabkbs_metabox.change_nonce
            };

            // Process the Ajax response and output all the necessary data.
            $.post(
                owlabkbs_metabox.ajax,
                change,
                function(response) {
                    // Append the response data.
                    if ( 'default' == response.type ) {
                        $('#owlabkbs-slider-main').html(response.html);
                        owlabkbsPlupload();
                    } else {
                        $('#owlabkbs-slider-main').html(response.html);
                    }

                    // Fire an event to attach to.
                    $(document).trigger('owlabkbsSliderType', response);

                    // Remove the spinner.
                    $('.owlabkbs-type-spinner .owlabkbs-spinner').hide();
                },
                'json'
            );
        });

        // Open up the media manager modal.
        $(document).on('click', '.owlabkbs-media-library', function(e){
            e.preventDefault();

            // Show the modal.
            owlabkbs_main_frame = true;
            $('#owlabkbs-upload-ui').appendTo('body').show();
        });

        // Add the selected state to images when selected from the library view.
        $('.owlabkbs-slider').on('click', '.thumbnail, .check, .media-modal-icon', function(e){
            e.preventDefault();
            if ( $(this).parent().parent().hasClass('owlabkbs-in-slider') )
                return;
            if ( $(this).parent().parent().hasClass('selected') )
                $(this).parent().parent().removeClass('details selected');
            else
                $(this).parent().parent().addClass('details selected');
        });

        // Load more images into the library view.
        $(document).on('click', '.owlabkbs-load-library', function(e){
            e.preventDefault();
            var $this = $(this);
            $this.next().css({'display' : 'inline-block', 'margin-top' : '14px', 'margin-left' : '-5px'});

            // Prepare our data to be sent via Ajax.
            var load = {
                action:  'owlabkbs_load_library',
                offset:  parseInt($this.attr('data-owlabkbs-offset')),
                post_id: owlabkbs_metabox.id,
                nonce:   owlabkbs_metabox.load_slider
            };

            // Process the Ajax response and output all the necessary data.
            $.post(
                owlabkbs_metabox.ajax,
                load,
                function(response) {
                    $this.attr('data-owlabkbs-offset', parseInt($this.attr('data-owlabkbs-offset')) + 20);

                    // Append the response data.
                    if ( response && response.html && $this.hasClass('has-search') ) {
                        $('.owlabkbs-slider').html(response.html);
                        $this.removeClass('has-search');
                    } else {
                        $('.owlabkbs-slider').append(response.html);
                    }

                    // Remove the spinner.
                    $this.next().hide();
                },
                'json'
            );
        });

        // Load images related to the search term specified
        $(document).on('keyup keydown', '#owlabkbs-slider-search', function(){
            var $this = $(this);
            $this.prev().css({'display' : 'inline-block', 'margin-top' : '1px', 'vertical-align' : 'middle', 'margin-right' : '4px'});

            var text     = $(this).val();
            var search   = {
                action:  'owlabkbs_library_search',
                nonce:   owlabkbs_metabox.library_search,
                post_id: owlabkbs_metabox.id,
                search:  text
            };

            // Send the ajax request with a delay (500ms after the user stops typing).
            delay(function() {
                // Process the Ajax response and output all the necessary data.
                $.post(
                    owlabkbs_metabox.ajax,
                    search,
                    function(response) {
                        // Notify the load button that we have entered a search and reset the offset counter.
                        $('.owlabkbs-load-library').addClass('has-search').attr('data-owlabkbs-offset', parseInt(0));

                        // Append the response data.
                        if ( response )
                            $('.owlabkbs-slider').html(response.html);

                        // Remove the spinner.
                        $this.prev().hide();
                    },
                    'json'
                );
            }, '500');
        });

        // Process inserting slides into slider when the Insert button is pressed.
        $(document).on('click', '.owlabkbs-media-insert', function(e){
            e.preventDefault();
            var $this = $(this),
                text  = $this.text(),
                data  = {
                    action: 'owlabkbs_insert_slides',
                    nonce:   owlabkbs_metabox.insert_nonce,
                    post_id: owlabkbs_metabox.id,
                    images:  {},
                    videos:  {},
                    html:    {}
                },
                selected = false,
                video    = false,
                html     = false,
                insert_e = e;
            $this.text(owlabkbs_metabox.inserting);

            // Loop through potential data to send when inserting images.
            // First, we loop through the selected items and add them to the data var.
            $('.owlabkbs-media-frame').find('.attachment.selected:not(.owlabkbs-in-slider)').each(function(i, el){
                data.images[i] = $(el).attr('data-attachment-id');
                selected       = true;
            });

            // Next, we loop through any video slides that have been created.
            $('.owlabkbs-media-frame').find('.owlabkbs-video-slide-holder').each(function(i, el){
                data.videos[i] = {
                    title:   $(el).find('.owlabkbs-video-slide-title').val(),
                    url:     $(el).find('.owlabkbs-video-slide-url').val(),
                    thumb:   $(el).find('.owlabkbs-video-slide-thumbnail').val(),
                    caption: $(el).find('.owlabkbs-video-slide-caption').val()
                };
                video = true;
            });

            // Finally, we loop through any HTML slides that have been created.
            $('.owlabkbs-media-frame').find('.owlabkbs-html-slide-holder').each(function(i, el){
                data.html[i] = {
                    title: $(el).find('.owlabkbs-html-slide-title').val(),
                    code:  $(el).find('.owlabkbs-html-slide-code').val(),
                    thumb: $(el).find('.owlabkbs-html-slide-thumbnail').val()
                };
                html = true;
            });

            // Send the ajax request with our data to be processed.
            $.post(
                owlabkbs_metabox.ajax,
                data,
                function(response){
                    // Set small delay before closing modal.
                    setTimeout(function(){
                        // Re-append modal to correct spot and revert text back to default.
                        append_and_hide(insert_e);
                        $this.text(text);

                        // If we have selected items, be sure to properly load first images back into view.
                        if ( selected )
                            $('.owlabkbs-load-library').attr('data-owlabkbs-offset', 0).addClass('has-search').trigger('click');
                    }, 500);
                },
                'json'
            );

        });

        // Change content areas and active menu states on media router click.
        $(document).on('click', '.owlabkbs-media-frame .media-menu-item', function(e){
            e.preventDefault();
            var $this       = $(this),
                old_content = $this.parent().find('.active').removeClass('active').data('owlabkbs-content'),
                new_content = $this.addClass('active').data('owlabkbs-content');
            $('#owlabkbs-' + old_content).hide();
            $('#owlabkbs-' + new_content).show();
        });

        // Load in new video slides when the add video slide button is clicked.
        $(document).on('click', '.owlabkbs-add-video-slide', function(e){
            e.preventDefault();
            var number = parseInt($(this).attr('data-owlabkbs-video-number')),
                id     = 'owlabkbs-video-slide-' + $(this).attr('data-owlabkbs-html-number');
            $(this).attr('data-owlabkbs-video-number', number + 1 ).parent().before(owlabkbsGetVideoSlideMarkup(number));
        });

        function owlabkbsGetVideoSlideMarkup(number) {
            var html = '';
            html += '<div class="owlabkbs-video-slide-holder"><p class="no-margin-top"><a href="#" class="button button-secondary owlabkbs-delete-video-slide" title="' + owlabkbs_metabox.removeslide + '">' + owlabkbs_metabox.removeslide + '</a><label for="owlabkbs-video-slide-' + number + '-title"><strong>' + owlabkbs_metabox.videoslide + '</strong></label><br /><input type="text" class="owlabkbs-video-slide-title" id="owlabkbs-video-slide-' + number + '-title" value="" placeholder="' + owlabkbs_metabox.videoplace + '" /></p><p><label for="owlabkbs-video-slide-' + number + '"><strong>' + owlabkbs_metabox.videotitle + '</strong></label><br /><input type="text" class="owlabkbs-video-slide-url" id="owlabkbs-video-slide-' + number + '" value="" placeholder="' + owlabkbs_metabox.videooutput + '" /></p><p><label for="owlabkbs-video-slide-' + number + '-thumbnail"><strong>' + owlabkbs_metabox.videothumb + '</strong></label><br /><input type="text" class="owlabkbs-video-slide-thumbnail" id="owlabkbs-video-slide-' + number + '-thumbnail" value="" placeholder="' + owlabkbs_metabox.videosrc + '" /> <span><a href="#" class="owlabkbs-video-thumbnail button button-primary">' + owlabkbs_metabox.videoselect + '</a> <a href="#" class="owlabkbs-video-thumbnail-delete button button-secondary">' + owlabkbs_metabox.videodelete + '</a></span></p><p class="no-margin-bottom"><label for="owlabkbs-video-slide-' + number + '-caption"><strong>' + owlabkbs_metabox.videocaption + '</strong></label><br /><textarea class="owlabkbs-video-slide-caption" id="owlabkbs-video-slide-' + number + '-caption"></textarea></p></div>';
            return html;
        }

        // Enable easy video thumbnail selection.
        $(document).on('click', '.owlabkbs-video-thumbnail', function(e){
            e.preventDefault();

            var owlabkbs_media_frame = wp.media.frames.owlabkbs_media_frame = wp.media({
                className: 'media-frame owlabkbs-media-frame',
                frame: 'select',
                multiple: false,
                title: owlabkbs_metabox.videoframe,
                library: {
                    type: 'image'
                },
                button: {
                    text: owlabkbs_metabox.videouse
                }
            }),
                $this = $(this);

            owlabkbs_media_frame.on('select', function(){
                // Grab our attachment selection and construct a JSON representation of the model.
                var thumbnail = owlabkbs_media_frame.state().get('selection').first().toJSON();

                // Send the attachment URL to our custom input field via jQuery.
                $this.parent().prev().val(thumbnail.url);
            });

            // Now that everything has been set, let's open up the frame.
            owlabkbs_media_frame.open();
        });

        // Empty the video thumbnail field.
        $(document).on('click', '.owlabkbs-video-thumbnail-delete', function(e){
            e.preventDefault();
            $(this).parent().prev().val('');
        });

        // Delete a video slide from the DOM when the user clicks to remove it.
        $(document).on('click', '#owlabkbs-video-slides .owlabkbs-delete-video-slide', function(e){
            e.preventDefault();
            $(this).parent().parent().remove();
        });

        var owlabkbs_html_holder = {};

        // Initialize the code editor for HTML slides.
    	$('.owlabkbs-html').find('.owlabkbs-html-code').each(function(i, el){
    		var id = $(el).attr('id');
    		owlabkbs_html_holder[id] = CodeMirror.fromTextArea(el, {
    			enterMode: 		'keep',
    			indentUnit: 	4,
    			electricChars:  false,
    			lineNumbers: 	true,
    			lineWrapping: 	true,
    			matchBrackets: 	true,
    			mode: 			'php',
    			smartIndent:    false,
    			tabMode: 		'shift',
    			theme:			'solarized dark'
    		});
    		owlabkbs_html_holder[id].on('blur', function(obj){
    			$('#' + id).text(obj.getValue());
    		});
    		owlabkbs_html_holder[id].refresh();
    	});

    	// Load in new HTML slides when the add HTML slide button is clicked.
        $(document).on('click', '.owlabkbs-add-html-slide', function(e){
            e.preventDefault();
            var number = parseInt($(this).attr('data-owlabkbs-html-number')),
                id     = 'owlabkbs-html-slide-' + $(this).attr('data-owlabkbs-html-number');
            $(this).attr('data-owlabkbs-html-number', number + 1 ).parent().before(owlabkbsGetHtmlSlideMarkup(number));
            owlabkbs_html_holder[id] = CodeMirror.fromTextArea(document.getElementById(id), {
    			enterMode: 		'keep',
    			indentUnit: 	4,
    			electricChars:  false,
    			lineNumbers: 	true,
    			lineWrapping: 	true,
    			matchBrackets: 	true,
    			mode: 			'php',
    			smartIndent:    false,
    			tabMode: 		'shift',
    			theme:			'solarized dark'
    		});
    		owlabkbs_html_holder[id].on('blur', function(obj){
    			$('#' + id).text(obj.getValue());
    		});
    		owlabkbs_html_holder[id].refresh();
        });

        function owlabkbsGetHtmlSlideMarkup(number) {
            var html = '';
            html += '<div class="owlabkbs-html-slide-holder"><p class="no-margin-top"><a href="#" class="button button-secondary owlabkbs-delete-html-slide" title="' + owlabkbs_metabox.removeslide + '">' + owlabkbs_metabox.removeslide + '</a><label for="owlabkbs-html-slide-' + number + '-title"><strong>' + owlabkbs_metabox.htmlslide + '</strong></label><br /><input type="text" class="owlabkbs-html-slide-title" id="owlabkbs-html-slide-' + number + '-title" value="" placeholder="' + owlabkbs_metabox.htmlplace + '" /></p><p class="no-margin-bottom"><label for="owlabkbs-html-slide-' + number + '"><strong>' + owlabkbs_metabox.htmlcode + '</strong></label><br /><textarea class="owlabkbs-html-slide-code" id="owlabkbs-html-slide-' + number + '">' + owlabkbs_metabox.htmlstart + '</textarea><p><label for="owlabkbs-html-slide-' + number + '-thumbnail"><strong>' + owlabkbs_metabox.htmlthumb + '</strong></label><br /><input type="text" class="owlabkbs-html-slide-thumbnail" id="owlabkbs-html-slide-' + number + '-thumbnail" value="" placeholder="' + owlabkbs_metabox.htmlsrc + '" /> <span><a href="#" class="owlabkbs-html-thumbnail button button-primary">' + owlabkbs_metabox.htmlselect + '</a> <a href="#" class="owlabkbs-html-thumbnail-delete button button-secondary">' + owlabkbs_metabox.htmldelete + '</a></span></p></div>';
            return html;
        }

        // Enable easy HTML thumbnail selection.
        $(document).on('click', '.owlabkbs-html-thumbnail', function(e){
            e.preventDefault();

            var owlabkbs_media_frame = wp.media.frames.owlabkbs_media_frame = wp.media({
                className: 'media-frame owlabkbs-media-frame',
                frame: 'select',
                multiple: false,
                title: owlabkbs_metabox.htmlframe,
                library: {
                    type: 'image'
                },
                button: {
                    text: owlabkbs_metabox.htmluse
                }
            }),
                $this = $(this);

            owlabkbs_media_frame.on('select', function(){
                // Grab our attachment selection and construct a JSON representation of the model.
                var thumbnail = owlabkbs_media_frame.state().get('selection').first().toJSON();

                // Send the attachment URL to our custom input field via jQuery.
                $this.parent().prev().val(thumbnail.url);
            });

            // Now that everything has been set, let's open up the frame.
            owlabkbs_media_frame.open();
        });

        // Delete an HTML slide from the DOM when the user clicks to remove it.
        $(document).on('click', '#owlabkbs-html-slides .owlabkbs-delete-html-slide', function(e){
            e.preventDefault();
            $(this).parent().parent().remove();
        });

        // Empty the HTML thumbnail field.
        $(document).on('click', '.owlabkbs-html-thumbnail-delete', function(e){
            e.preventDefault();
            $(this).parent().prev().val('');
        });

        // Make slider items sortable.
        var slider = $('#owlabkbs-output');

        // Use ajax to make the images sortable.
        slider.sortable({
            containment: '#owlabkbs',
            items: 'li',
            cursor: 'move',
            forcePlaceholderSize: true,
            placeholder: 'dropzone',
            update: function(event, ui) {
                // Make ajax request to sort out items.
                var opts = {
                    url:      owlabkbs_metabox.ajax,
                    type:     'post',
                    async:    true,
                    cache:    false,
                    dataType: 'json',
                    data: {
                        action:  'owlabkbs_sort_images',
                        order:   slider.sortable('toArray').toString(),
                        post_id: owlabkbs_metabox.id,
                        nonce:   owlabkbs_metabox.sort
                    },
                    success: function(response) {
                        return;
                    },
                    error: function(xhr, textStatus ,e) {
                        return;
                    }
                };
                $.ajax(opts);
            }
        });

        // Process image removal from a slider.
        $(document).on('click', '#owlabkbs .owlabkbs-remove-slide', function(e){
            e.preventDefault();

            // Bail out if the user does not actually want to remove the image.
            var confirm_delete = confirm(owlabkbs_metabox.remove);
            if ( ! confirm_delete )
                return;

            // Prepare our data to be sent via Ajax.
            var attach_id = $(this).parent().attr('id'),
                remove = {
                    action:        'owlabkbs_remove_slide',
                    attachment_id: attach_id,
                    post_id:       owlabkbs_metabox.id,
                    nonce:         owlabkbs_metabox.remove_nonce
                };

            // Process the Ajax response and output all the necessary data.
            $.post(
                owlabkbs_metabox.ajax,
                remove,
                function(response) {
                    $('#' + attach_id).fadeOut('normal', function() {
                        $(this).remove();

                        // Refresh the modal view to ensure no items are still checked if they have been removed.
                        $('.owlabkbs-load-library').attr('data-owlabkbs-offset', 0).addClass('has-search').trigger('click');
                    });
                },
                'json'
            );
        });

        // Open up the media modal area for modifying slider metadata.
        var owlabkbs_main_frame_meta = false;
        $(document).on('click.owlabkbsModify', '#owlabkbs .owlabkbs-modify-slide', function(e){
            e.preventDefault();
            var attach_id = $(this).parent().data('owlabkbs-slide'),
                formfield = 'owlabkbs-meta-' + attach_id;

            // Show the modal.
            owlabkbs_main_frame_meta = true;
            $('#' + formfield).appendTo('body').show();

            // Refresh any HTML slides.
            $.each(owlabkbs_html_holder, function(){
    			this.refresh();
    		});

            // Close the modal window on user action
            var append_and_hide_meta = function(e){
                e.preventDefault();
                $('#' + formfield).appendTo('#' + attach_id).hide();
                owlabkbs_main_frame_meta = false;
                $(document).off('click.owlabkbsLink');
            };
            $(document).on('click.owlabkbsIframe', '.media-modal-close, .media-modal-backdrop', append_and_hide_meta);
            $(document).off('keydown.owlabkbsIframe').on('keydown.owlabkbsIframe', function(e){
                if ( 27 == e.keyCode && owlabkbs_main_frame_meta ) {
                    append_and_hide_meta(e);
                }
            });
            $(document).on('click.owlabkbsLink', '.ed_button', function(){
                // Set custom z-index for link dialog box.
                $('#wp-link-backdrop').css('zIndex', '170100');
                $('#wp-link-wrap').css('zIndex', '171005' );
            });
        });

        // Save the slider metadata.
        $(document).on('click', '.owlabkbs-meta-submit', function(e){
            e.preventDefault();
            var $this     = $(this),
                default_t = $this.text(),
                attach_id = $this.data('owlabkbs-item'),
                formfield = 'owlabkbs-meta-' + attach_id,
                meta      = {};

            // Output saving text...
            $this.text(owlabkbs_metabox.saving);

            // Add the title since it is a special field.
            meta.caption = $('#owlabkbs-meta-table-' + attach_id).find('textarea[name="_owlabkbs[meta_caption]"]').val();

            // Get all meta fields and values.
            $('#owlabkbs-meta-table-' + attach_id).find(':input').not('.ed_button').each(function(i, el){
                if ( $(this).data('owlabkbs-meta') )
                    meta[$(this).data('owlabkbs-meta')] = $(this).val();
            });

            // Prepare the data to be sent.
            var data = {
                action:    'owlabkbs_save_meta',
                nonce:     owlabkbs_metabox.save_nonce,
                attach_id: attach_id,
                post_id:   owlabkbs_metabox.id,
                meta:      meta
            };

            $.post(
                owlabkbs_metabox.ajax,
                data,
                function(res){
                    setTimeout(function(){
                        $('#' + formfield).appendTo('#' + attach_id).hide();
                        $this.text(default_t);
                    }, 500);
                },
                'json'
            );
        });

        // Append spinner when importing a slider.
        $(document).on('click', '#owlabkbs-import-submit', function(e){
            $(this).next().css('display', 'inline-block');
            if ( $('#owlabkbs-config-import-slider').val().length === 0 ) {
                e.preventDefault();
                $(this).next().hide();
                alert(owlabkbs_metabox.import);
            }
        });

        // Polling function for typing and other user centric items.
        var delay = (function() {
            var timer = 0;
            return function(callback, ms) {
                clearTimeout(timer);
                timer = setTimeout(callback, ms);
            };
        })();

        // Close the modal window on user action.
        var owlabkbs_main_frame = false;
        var append_and_hide = function(e){
            e.preventDefault();
            $('#owlabkbs-upload-ui').appendTo('#owlabkbs-upload-ui-wrapper').hide();
            owlabkbsRefresh();
            owlabkbs_main_frame = false;
        };
        $(document).on('click', '#owlabkbs-upload-ui .media-modal-close, #owlabkbs-upload-ui .media-modal-backdrop', append_and_hide);
        $(document).on('keydown', function(e){
            if ( 27 == e.keyCode && owlabkbs_main_frame )
                append_and_hide(e);
        });

        // Function to refresh images in the slider.
        function owlabkbsRefresh(){
            var data = {
                action:  'owlabkbs_refresh',
                post_id: owlabkbs_metabox.id,
                nonce:   owlabkbs_metabox.refresh_nonce
            };

            $('.max-upload-size').after('<span class="spinner owlabkbs-spinner owlabkbs-spinner-refresh"></span>');
            $('.owlabkbs-spinner-refresh').css({'display' : 'inline-block', 'margin-top' : '-3px'});

            $.post(
                owlabkbs_metabox.ajax,
                data,
                function(res){
                    if ( res && res.success ) {
                        $('#owlabkbs-output').html(res.success);
                        $('#owlabkbs-output').find('.wp-editor-wrap').each(function(i, el){
                            var qt = $(el).find('.quicktags-toolbar');
                            if ( qt.length > 0 ) {
                                return;
                            }

                            var arr = $(el).attr('id').split('-'),
                                id  = arr.slice(3, -1).join('-');
                            quicktags({id: 'owlabkbs-caption-' + id, buttons: 'strong,em,link,ul,ol,li,close'});
                            QTags._buttonsInit(); // Force buttons to initialize.
                        });

                        // Initialize any code editors that have been generated with HTML slides.
        				$('.owlabkbs-html').find('.owlabkbs-html-code').each(function(i, el){
        					var id = $(el).attr('id');
        					owlabkbs_html_holder[id] = CodeMirror.fromTextArea(el, {
        						enterMode: 		'keep',
        						indentUnit: 	4,
        						electricChars:  false,
        						lineNumbers: 	true,
        						lineWrapping: 	true,
        						matchBrackets: 	true,
        						mode: 			'php',
        						smartIndent:    false,
        						tabMode: 		'shift',
        						theme:			'solarized dark'
        					});
        					owlabkbs_html_holder[id].on('blur', function(obj){
        						$('#' + id).text(obj.getValue());
        					});
        					owlabkbs_html_holder[id].refresh();
        				});

                        // Trigger a custom event for 3rd party scripts.
                        $('#owlabkbs-output').trigger({ type: 'owlabkbsRefreshed', html: res.success, id: owlabkbs_metabox.id });
                    }

                    // Remove the spinner.
                    $('.owlabkbs-spinner-refresh').fadeOut(300, function(){
                        $(this).remove();
                    });
                },
                'json'
            );
        }

        // Function to show conditional fields.
        function owlabkbsConditionals() {
            var owlabkbs_mobile_option  = $('#owlabkbs-config-mobile');
            if ( owlabkbs_mobile_option.is(':checked') )
                $('#owlabkbs-config-mobile-size-box').fadeIn(300);
            owlabkbs_mobile_option.on('change', function(){
                if ( $(this).is(':checked') )
                    $('#owlabkbs-config-mobile-size-box').fadeIn(300);
                else
                    $('#owlabkbs-config-mobile-size-box').fadeOut(300);
            });
        }

        // Function to initialize plupload.
        function owlabkbsPlupload() {
            // Append the custom loading progress bar.
            $('#owlabkbs .drag-drop-inside').append('<div class="owlabkbs-progress-bar"><div></div></div>');

            // Prepare variables.
            owlabkbs_uploader     = new plupload.Uploader(owlabkbs_metabox.plupload);
            var owlabkbs_bar      = $('#owlabkbs .owlabkbs-progress-bar'),
                owlabkbs_progress = $('#owlabkbs .owlabkbs-progress-bar div'),
                owlabkbs_output   = $('#owlabkbs-output');

            // Only move forward if the uploader is present.
            if ( owlabkbs_uploader ) {
                owlabkbs_uploader.bind('Init', function(up) {
                    var uploaddiv = $('#owlabkbs-plupload-upload-ui');

                    // If drag and drop, make that happen.
                    if ( up.features.dragdrop && ! $(document.body).hasClass('mobile') ) {
                        uploaddiv.addClass('drag-drop');
                        $('#owlabkbs-drag-drop-area').bind('dragover.wp-uploader', function(){
                            uploaddiv.addClass('drag-over');
                        }).bind('dragleave.wp-uploader, drop.wp-uploader', function(){
                            uploaddiv.removeClass('drag-over');
                        });
                    } else {
                        uploaddiv.removeClass('drag-drop');
                        $('#owlabkbs-drag-drop-area').unbind('.wp-uploader');
                    }

                    // If we have an HTML4 runtime, hide the flash bypass.
                    if ( up.runtime == 'html4' )
                        $('.upload-flash-bypass').hide();
                });

                // Initialize the uploader.
                owlabkbs_uploader.init();

                // Bind to the FilesAdded event to show the progess bar.
                owlabkbs_uploader.bind('FilesAdded', function(up, files){
                    var hundredmb = 100 * 1024 * 1024,
                        max       = parseInt(up.settings.max_file_size, 10);

                    // Remove any errors.
                    $('#owlabkbs-upload-error').html('');

                    // Show the progress bar.
                    $(owlabkbs_bar).show().css('display', 'block');

                    // Upload the files.
                    plupload.each(files, function(file){
                        if ( max > hundredmb && file.size > hundredmb && up.runtime != 'html5' ) {
                            owlabkbsUploadError( up, file, true );
                        }
                    });

                    // Refresh and start.
                    up.refresh();
                    up.start();
                });

                // Bind to the UploadProgress event to manipulate the progress bar.
                owlabkbs_uploader.bind('UploadProgress', function(up, file){
                    $(owlabkbs_progress).css('width', up.total.percent + '%');
                });

                // Bind to the FileUploaded event to set proper UI display for slider.
                owlabkbs_uploader.bind('FileUploaded', function(up, file, info){
                    // Make an ajax request to generate and output the image in the slider UI.
                    $.post(
                        owlabkbs_metabox.ajax,
                        {
                            action:  'owlabkbs_load_image',
                            nonce:   owlabkbs_metabox.load_image,
                            id:      info.response,
                            post_id: owlabkbs_metabox.id
                        },
                        function(res){
                            $(owlabkbs_output).append(res);
                            $(res).find('.wp-editor-container').each(function(i, el){
                                var id = $(el).attr('id').split('-')[3];
                                quicktags({id: 'owlabkbs-caption-' + id, buttons: 'strong,em,link,ul,ol,li,close'});
                                QTags._buttonsInit(); // Force buttons to initialize.
                            });
                        },
                        'json'
                    );
                });

                // Bind to the UploadComplete event to hide and reset the progress bar.
                owlabkbs_uploader.bind('UploadComplete', function(){
                    $(owlabkbs_bar).hide().css('display', 'none');
                    $(owlabkbs_progress).removeAttr('style');
                });

                // Bind to any errors and output them on the screen.
                owlabkbs_uploader.bind('Error', function(up, error) {
                    var hundredmb = 100 * 1024 * 1024,
                        error_el  = $('#owlabkbs-upload-error'),
                        max;
                    switch (error) {
                        case plupload.FAILED:
                        case plupload.FILE_EXTENSION_ERROR:
                            error_el.html('<p class="error">' + pluploadL10n.upload_failed + '</p>');
                            break;
                        case plupload.FILE_SIZE_ERROR:
                            owlabkbsUploadError(up, error.file);
                            break;
                        case plupload.IMAGE_FORMAT_ERROR:
                            wpFileError(fileObj, pluploadL10n.not_an_image);
                            break;
                        case plupload.IMAGE_MEMORY_ERROR:
                            wpFileError(fileObj, pluploadL10n.image_memory_exceeded);
                            break;
                        case plupload.IMAGE_DIMENSIONS_ERROR:
                            wpFileError(fileObj, pluploadL10n.image_dimensions_exceeded);
                            break;
                        case plupload.GENERIC_ERROR:
                            wpQueueError(pluploadL10n.upload_failed);
                            break;
                        case plupload.IO_ERROR:
                            max = parseInt(uploader.settings.max_file_size, 10);

                            if ( max > hundredmb && fileObj.size > hundredmb )
                                wpFileError(fileObj, pluploadL10n.big_upload_failed.replace('%1$s', '<a class="uploader-html" href="#">').replace('%2$s', '</a>'));
                            else
                                wpQueueError(pluploadL10n.io_error);
                            break;
                        case plupload.HTTP_ERROR:
                            wpQueueError(pluploadL10n.http_error);
                            break;
                        case plupload.INIT_ERROR:
                            $('.media-upload-form').addClass('html-uploader');
                            break;
                        case plupload.SECURITY_ERROR:
                            wpQueueError(pluploadL10n.security_error);
                            break;
                        default:
                            wpFileError(fileObj, pluploadL10n.default_error);
                    }
                    up.refresh();
                });
            }
        }

        // Function for displaying file upload errors.
        function owlabkbsUploadError( up, file, over100mb ) {
            var message;

            if ( over100mb )
                message = pluploadL10n.big_upload_queued.replace('%s', file.name) + ' ' + pluploadL10n.big_upload_failed.replace('%1$s', '<a class="uploader-html" href="#">').replace('%2$s', '</a>');
            else
                message = pluploadL10n.file_exceeds_size_limit.replace('%s', file.name);

            $('#owlabkbs-upload-error').html('<p class="error">' + message + '</p>');
            up.removeFile(file);
        }
    });
}(jQuery));