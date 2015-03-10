<?php
/**
 * This file represents an example of the code that themes would use to register
 * the required plugins.
 *
 * It is expected that theme authors would copy and paste this code into their
 * functions.php file, and amend to suit.
 *
 * @package	   TGM-Plugin-Activation
 * @subpackage Example
 * @version	   2.3.6
 * @author	   Thomas Griffin <thomas@thomasgriffinmedia.com>
 * @author	   Gary Jones <gamajo@gamajo.com>
 * @copyright  Copyright (c) 2012, Thomas Griffin
 * @license	   http://opensource.org/licenses/gpl-2.0.php GPL v2 or later
 * @link       https://github.com/thomasgriffin/TGM-Plugin-Activation
 */

/**
 * Include the TGM_Plugin_Activation class.
 */
require_once dirname( __FILE__ ) . '/class-tgm-plugin-activation.php';

add_action( 'tgmpa_register', 'owlab_register_required_plugins' );
/**
 * Register the required plugins for this theme.
 *
 * This function is hooked into tgmpa_init, which is fired within the
 * TGM_Plugin_Activation class constructor.
 */
function owlab_register_required_plugins() {

	/**
	 * Array of plugin arrays. Required keys are name and slug.
	 * If the source is NOT from the .org repo, then source is also required.
	 */
	$plugins = array(

		
		array(
            'name'     				=> 'owwwlab KenBurned Slideshow - For TORANJ',
            'slug'     				=> 'owwwlab-kenburn',
            'source'   				=> get_stylesheet_directory() . '/framework/plugin-register/plugins/owwwlab-kenburn.zip',
            'required' 				=> true,
            'version' 				=> '1.1.0',
            'force_activation' 		=> false,
            'force_deactivation' 	=> false,
        ),
		array(
            'name'     				=> 'owwwlab Gallery Plugin - For TORANJ',
            'slug'     				=> 'owwwlab-gallery',
            'source'   				=> get_stylesheet_directory() . '/framework/plugin-register/plugins/owwwlab-gallery.zip',
            'required' 				=> true,
            'version' 				=> '1.1.0',
            'force_activation' 		=> false,
            'force_deactivation' 	=> false,
        ),
        array(
            'name'     				=> 'owwwlab Portfolio Plugin - For TORANJ',
            'slug'     				=> 'owwwlab-portfolio',
            'source'   				=> get_stylesheet_directory() . '/framework/plugin-register/plugins/owwwlab-portfolio.zip',
            'required' 				=> true,
            'version' 				=> '1.1.0',
            'force_activation' 		=> false,
            'force_deactivation' 	=> false,
        ),
        array(
            'name'     				=> 'WPBakery Visual Composer',
            'slug'     				=> 'js_composer',
            'source'   				=> get_stylesheet_directory() . '/framework/plugin-register/plugins/js_composer.zip',
            'required' 				=> true,
            'version' 				=> '4.3.4',
            'force_activation' 		=> false,
            'force_deactivation' 	=> false,
        ),
        array(
            'name'     				=> 'Envato WordPress Toolkit',
            'slug'     				=> 'envato-wordpress-toolkit-master',
            'source'   				=> get_stylesheet_directory() . '/framework/plugin-register/plugins/envato-wordpress-toolkit-master.zip',
            'required' 				=> false,
            'version' 				=> '1.7.0',
            'force_activation' 		=> false,
            'force_deactivation' 	=> false,
        ),
        array(
            'name'     				=> 'Master Slider WP',
            'slug'     				=> 'masterslider',
            'source'   				=> get_stylesheet_directory() . '/framework/plugin-register/plugins/masterslider.zip',
            'required' 				=> false,
            'version' 				=> '2.7.2',
            'force_activation' 		=> false,
            'force_deactivation' 	=> false,
        ),
		array(
			'name' 					=> 'Contact Form 7',
			'slug' 					=> 'contact-form-7',
			'required' 				=> false,
		),

		array(
			'name' 					=> 'Simple Custom Post Order',
			'slug' 					=> 'simple-custom-post-order',
			'required' 				=> false,
		),

		array(
			'name' 					=> 'Category Order and Taxonomy Terms Order',
			'slug' 					=> 'taxonomy-terms-order',
			'required' 				=> false,
		),

	);

	/**
	 * Array of configuration settings. Amend each line as needed.
	 * If you want the default strings to be available under your own theme domain,
	 * leave the strings uncommented.
	 * Some of the strings are added into a sprintf, so see the comments at the
	 * end of each line for what each argument will be.
	 */
	$config = array(
		'domain'       		=> 'toranj',         	// Text domain - likely want to be the same as your theme.
		'default_path' 		=> '',                         	// Default absolute path to pre-packaged plugins
		'parent_menu_slug' 	=> 'themes.php', 				// Default parent menu slug
		'parent_url_slug' 	=> 'themes.php', 				// Default parent URL slug
		'menu'         		=> 'install-required-plugins', 	// Menu slug
		'has_notices'      	=> true,                       	// Show admin notices or not
		'is_automatic'    	=> false,					   	// Automatically activate plugins after installation or not
		'message' 			=> '',							// Message to output right before the plugins table
		'strings'      		=> array(
			'page_title'                       			=> 'Install Required Plugins',
			'menu_title'                       			=> 'Install Plugins',
			'installing'                       			=> 'Installing Plugin: %s', 
			'oops'                             			=> 'Something went wrong with the plugin API.',
			'notice_can_install_required'     			=> _n_noop( 'This theme requires the following plugin: %1$s.', 'This theme requires the following plugins: %1$s.'), // %1$s = plugin name(s)
			'notice_can_install_recommended'			=> _n_noop( 'This theme recommends the following plugin: %1$s.', 'This theme recommends the following plugins: %1$s.'), // %1$s = plugin name(s)
			'notice_cannot_install'  					=> _n_noop( 'Sorry, but you do not have the correct permissions to install the %s plugin. Contact the administrator of this site for help on getting the plugin installed.', 'Sorry, but you do not have the correct permissions to install the %s plugins. Contact the administrator of this site for help on getting the plugins installed.'), // %1$s = plugin name(s)
			'notice_can_activate_required'    			=> _n_noop( 'The following required plugin is currently inactive: %1$s.', 'The following required plugins are currently inactive: %1$s.'), // %1$s = plugin name(s)
			'notice_can_activate_recommended'			=> _n_noop( 'The following recommended plugin is currently inactive: %1$s.', 'The following recommended plugins are currently inactive: %1$s.' ), // %1$s = plugin name(s)
			'notice_cannot_activate' 					=> _n_noop( 'Sorry, but you do not have the correct permissions to activate the %s plugin. Contact the administrator of this site for help on getting the plugin activated.', 'Sorry, but you do not have the correct permissions to activate the %s plugins. Contact the administrator of this site for help on getting the plugins activated.'), // %1$s = plugin name(s)
			'notice_ask_to_update' 						=> _n_noop( 'The following plugin needs to be updated to its latest version to ensure maximum compatibility with this theme: %1$s.', 'The following plugins need to be updated to their latest version to ensure maximum compatibility with this theme: %1$s.'), // %1$s = plugin name(s)
			'notice_cannot_update' 						=> _n_noop( 'Sorry, but you do not have the correct permissions to update the %s plugin. Contact the administrator of this site for help on getting the plugin updated.', 'Sorry, but you do not have the correct permissions to update the %s plugins. Contact the administrator of this site for help on getting the plugins updated.' ), // %1$s = plugin name(s)
			'install_link' 					  			=> _n_noop( 'Begin installing plugin', 'Begin installing plugins' ),
			'activate_link' 				  			=> _n_noop( 'Activate installed plugin', 'Activate installed plugins' ),
			'return'                           			=> 'Return to Required Plugins Installer',
			'plugin_activated'                 			=> 'Plugin activated successfully.',
			'complete' 									=> 'All plugins installed and activated successfully. %s',
			'nag_type'									=> 'updated' // Determines admin notice type - can only be 'updated' or 'error'
		)
	);

	tgmpa( $plugins, $config );

}