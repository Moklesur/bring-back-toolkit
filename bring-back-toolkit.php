<?php

/*
 Plugin Name: Bring Back Toolkit
 Plugin URI: #
 Description: This is a post type plugin and its only for bring back wordpress theme.
 Author Name: Themetim
 Author URI: https://www.themetim.com
 Text Domain: bring-back-toolkit
 Version: 1.0
 */

defined( 'ABSPATH' ) or die( 'Hey! what you need here?' );

// Plugin Path
define( 'BRING_BACK_TOOLKIT_PATH', plugin_dir_path( __FILE__ ) );
// Plugin Version
define( 'BRING_BACK_TOOLKIT_VERSION', '1.0' );

/**
 * Class BringBackToolkit
 * @pakage bring-back-toolkit
 */
class BringBackToolkit
{

    /**
     * BringBackToolkit constructor.
     */
    function __construct() {
        //Init Custom Post Types
        add_action( 'init', array( $this, 'custom_post_types' ) );
    }

    /**
     * Activation
     */
    function activate() {

        $this->custom_post_types();

        // clear the permalinks after the post type has been registered
        flush_rewrite_rules();
    }

    /**
     * Deactivation
     */
    function deactivate() {
        // clear the permalinks to remove our post type's rules from the database
        flush_rewrite_rules();
    }

    /**
     * Uninstall
     */
    function uninstall() {}

    /**
     * Custom Post Types
     *
     * Testimonial
     * Case Studies
     * Services
     */
    function custom_post_types() {

        $this->testimonial();
        $this->case_studies();
        $this->services();

    }

    /**
     * Testimonial
     */
    function testimonial(){
        /**
         * Testimonial
         * Label
         * Args
         */
        $testimonial = 'Testimonial';
        $TestimonialLabel = array(
            'name'               => _x( $testimonial, 'post type general name', 'bring-back-toolkit' ),
            'singular_name'      => _x( $testimonial, 'post type singular name', 'bring-back-toolkit' ),
            'menu_name'          => _x( $testimonial, 'admin menu', 'bring-back-toolkit' ),
            'name_admin_bar'     => _x( $testimonial, 'add new on admin bar', 'bring-back-toolkit' ),
            'add_new'            => _x( 'Add New', $testimonial, 'bring-back-toolkit' ),
            'add_new_item'       => __( 'Add New '.$testimonial, 'bring-back-toolkit' ),
            'new_item'           => __( 'New '.$testimonial, 'bring-back-toolkit' ),
            'edit_item'          => __( 'Edit '.$testimonial, 'bring-back-toolkit' ),
            'view_item'          => __( 'View '.$testimonial, 'bring-back-toolkit' ),
            'all_items'          => __( 'All '.$testimonial, 'bring-back-toolkit' )
        );
        $TestimoniaArgs = array(
            'labels'             => $TestimonialLabel,
            'public'             => true,
            'publicly_queryable' => true,
            'supports'           => array( 'title', 'editor', 'author', 'thumbnail'  )
        );

        /**
         * Register the "Testimonial"
         */

        register_post_type( 'testimonial', $TestimoniaArgs );
    }

    /**
     * Case Studies
     */
    function case_studies(){
        /**
         * Case Studies
         * Label
         * Args
         */

        $CaseStudies = 'Case Studies';
        $CaseStudiesLabel = array(
            'name'               => _x( $CaseStudies, 'post type general name', 'bring-back-toolkit' ),
            'singular_name'      => _x( $CaseStudies, 'post type singular name', 'bring-back-toolkit' ),
            'menu_name'          => _x( $CaseStudies, 'admin menu', 'bring-back-toolkit' ),
            'name_admin_bar'     => _x( $CaseStudies, 'add new on admin bar', 'bring-back-toolkit' ),
            'add_new'            => _x( 'Add New', $CaseStudies, 'bring-back-toolkit' ),
            'add_new_item'       => __( 'Add New '.$CaseStudies, 'bring-back-toolkit' ),
            'new_item'           => __( 'New '.$CaseStudies, 'bring-back-toolkit' ),
            'edit_item'          => __( 'Edit '.$CaseStudies, 'bring-back-toolkit' ),
            'view_item'          => __( 'View '.$CaseStudies, 'bring-back-toolkit' ),
            'all_items'          => __( 'All '.$CaseStudies, 'bring-back-toolkit' )
        );
        $CaseStudiesArgs = array(
            'labels'             => $CaseStudiesLabel,
            'public'             => true,
            'publicly_queryable' => true,
            'rewrite'            => array( 'slug' => 'case-studies' ),
            'supports'           => array( 'title', 'editor', 'author', 'thumbnail'  )
        );

        /**
         * Register the "Case Studies"
         */

        register_post_type( 'case_studies', $CaseStudiesArgs );

    }

    /**
     * Services
     */
    function services(){
        /**
         * Services
         * Label
         * Args
         */

        $Services = 'Services';
        $ServicesLabel = array(
            'name'               => _x( $Services, 'post type general name', 'bring-back-toolkit' ),
            'singular_name'      => _x( $Services, 'post type singular name', 'bring-back-toolkit' ),
            'menu_name'          => _x( $Services, 'admin menu', 'bring-back-toolkit' ),
            'name_admin_bar'     => _x( $Services, 'add new on admin bar', 'bring-back-toolkit' ),
            'add_new'            => _x( 'Add New', $Services, 'bring-back-toolkit' ),
            'add_new_item'       => __( 'Add New '.$Services, 'bring-back-toolkit' ),
            'new_item'           => __( 'New '.$Services, 'bring-back-toolkit' ),
            'edit_item'          => __( 'Edit '.$Services, 'bring-back-toolkit' ),
            'view_item'          => __( 'View '.$Services, 'bring-back-toolkit' ),
            'all_items'          => __( 'All '.$Services, 'bring-back-toolkit' )
        );
        $ServicesArgs = array(
            'labels'             => $ServicesLabel,
            'public'             => true,
            'publicly_queryable' => true,
            'rewrite'            => array( 'slug' => 'services' ),
            'supports'           => array( 'title', 'editor', 'author', 'thumbnail'  )
        );

        /**
         * Register the "Services"
         */

        register_post_type( 'services', $ServicesArgs );
    }
}

/**
 * New Object $bringBackToolkit
 */
if( class_exists( 'BringBackToolkit' ) ){
    $bringBackToolkit = new BringBackToolkit();
}

/**
 * Register Activation Hook
 */
register_activation_hook( BRING_BACK_TOOLKIT_PATH, array( $bringBackToolkit, 'activate' ) );

/**
 * Register Deactivation Hook
 */
register_deactivation_hook( BRING_BACK_TOOLKIT_PATH, array( $bringBackToolkit, 'deactivate' ) );
