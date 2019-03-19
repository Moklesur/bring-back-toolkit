<?php

/*

 Plugin Name: Bring Back Toolkit
 Plugin URI: #
 Description: This is a post type plugin for bring back wordpress theme.
 Author Name: Themetim
 Author URI: https://www.themetim.com
 Text Domain: bring-back-toolkit
 Version: 1.0

 */

defined( 'ABSPATH' ) or die( 'Hey! what you need here?' );


/**
 * Class BringBackToolkit
 * @pakage bring-back-toolkit
 */
class BringBackToolkit
{

    function __construct() {
        add_action( 'init', array( $this, 'custom_post_types' ) );
    }

    // Activation
    function activate() {
        // clear the permalinks after the post type has been registered
        flush_rewrite_rules();
    }

    // Deactivation
    function deactivate() {

        // clear the permalinks to remove our post type's rules from the database
        flush_rewrite_rules();
    }

    // Uninstall
//    function uninstall() {
//
//    }

    // Custom Post Types
    function custom_post_types() {

        // register the "Testimonial"
        register_post_type( 'testimonial', ['public' => true, 'label' => 'Testimonial' ] );

        // register the "Case Studies"
        register_post_type( 'case_studies', ['public' => true, 'label' => 'Case Studies' ] );

        // register the "Services"
        register_post_type( 'services', ['public' => true, 'label' => 'Services' ] );

    }


    // Testimonial
    function testimonial(){

    }

    // Case Studies
    function case_studies(){

    }

    // Services
    function services(){

    }
}

if( class_exists( 'BringBackToolkit' ) ){
    $bringBackToolkit = new BringBackToolkit();
}

// Activation
register_activation_hook( __FILE__, array( $bringBackToolkit, 'activate' ) );

// Deactivation
register_deactivation_hook( __FILE__, array( $bringBackToolkit, 'deactivate' ) );
