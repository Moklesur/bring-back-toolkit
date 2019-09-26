<?php
/**
 *
 * @link                  https://www.themetim.com
 * @since                 1.0.0
 * @package
 *
 * @wordpress-plugin
 *
 *
 * Plugin Name:           Bring Back Toolkit
 * Plugin URI:            #
 * Description:           Bring Back Toolkit
 * Plugin URI:            #
 * Author:                Themetim
 * Author URI:            https://www.themetim.com/
 * Version:               1.0.0
 * Author URI:            https://www.themetim.com/
 * License:               GPL-2.0+
 * License URI:           http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:           bring-back-toolkit
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly


if( ! class_exists('Bring_Back_Toolkit') ) {

    /**
     * Class Bring_Back_Toolkit
     * @pakage bring-back-toolkit
     */
    class Bring_Back_Toolkit
    {
        /**
         * Instance of this class
         *
         * @access protected
         */
        protected static $_instance = null;

        /**
         * Get instance of this class
         *
         * @return Bring_Back_Toolkit|null
         */
        public static function get_instance() {

            if ( is_null( self::$_instance ) ) {
                self::$_instance = new self();
            }

            return self::$_instance;
        }

        /**
         * Version of this plugin.
         *
         * @access private string
         */
        private $version = '1.0.0';

        /**
         * Bring_Back_Toolkit constructor.
         */
        public function __construct() {
            $this->define_constants();
            // Post Type Init
            add_action( 'init', array( $this, 'register_post_types' ) );
            // Meta Box
            add_action('save_post', array( $this, 'case_studies_save_metabox' ) );
            // Social Share
            add_action('init', array( $this,'social_share_init' ) );
        }

        /**
         * @param $name
         * @param $value
         * @param bool $case_insensitive
         */
        public function define( $name, $value, $case_insensitive = false ) {
            if ( ! defined( $name ) ) {
                define( $name, $value, $case_insensitive );
            }
        }

        /**
         * Define Constants
         *
         * @access Public
         */
        public function define_constants() {
            $this->define( 'Bring_Back_Toolkit_PLUGIN_FILE', __FILE__ );
            $this->define( 'Bring_Back_Toolkit_PLUGIN_URI', plugin_dir_url( __FILE__ ) );
            $this->define( 'Bring_Back_Toolkit_PLUGIN_PATH', plugin_dir_path( __FILE__ ) );
            $this->define( 'Bring_Back_Toolkit_VERSION', $this->version );
            //$this->define( 'Bring_Back_Toolkit_PLUGIN_INCLUDE_PATH', trailingslashit( plugin_dir_path( __FILE__ ) . 'includes' ) );
            $this->define( 'Bring_Back_Toolkit_PLUGIN_DIRNAME', dirname( plugin_basename( __FILE__ ) ) );
            $this->define( 'Bring_Back_Toolkit_PLUGIN_BASENAME', plugin_basename( __FILE__ ) );
        }

        /**
         * Available post types
         *
         * @access protected
         * @return array
         */
        protected function post_types() {
            return [
                [
                    'post_type' => 'bb-testimonials',
                    'name'      => __( 'Testimonials', 'bring-back-toolkit' ),
                    'label'     => __( 'Testimonial', 'bring-back-toolkit' ),
                    'supports'  => [ 'title', 'editor', 'author', 'thumbnail' ],
                    'register_meta_box_cb'  => []
                ],
                [
                    'post_type' => 'bb-services',
                    'name'      => __( 'Services', 'bring-back-toolkit' ),
                    'label'     => __( 'Service', 'bring-back-toolkit' ),
                    'supports'  => [ 'title', 'editor', 'author', 'thumbnail' ],
                    'register_meta_box_cb'  => []
                ],
                [
                    'post_type' => 'bb-case-studies',
                    'name'      => __( 'Case Studies', 'bring-back-toolkit' ),
                    'label'     => __( 'Case Studies', 'bring-back-toolkit' ),
                    'supports'  => [ 'title', 'editor', 'author', 'thumbnail' ],
                    'register_meta_box_cb'  => [ $this, 'bb_case_studies_add_metabox' ]
                ],
            ];
        }

        /**
         * Permalink Flush
         */
        public function permalink_flush() {
            flush_rewrite_rules();
        }

        /**
         * Registering post types
         */
        public function register_post_types() {

            foreach( $this->post_types() as $post ) {

                extract( $post );

//                echo "<pre>";
//                var_dump( $post );
//                die();

                if( $post_type ) {
                    $labels = [
                        'name'               => _x( $name, 'post type general name', 'bring-back-toolkit' ),
                        'singular_name'      => _x( $label, 'post type singular name', 'bring-back-toolkit' ),
                        'menu_name'          => _x( $label, 'admin menu', 'bring-back-toolkit' ),
                        'name_admin_bar'     => _x( $label, 'add new on admin bar', 'bring-back-toolkit' ),
                        'add_new'            => _x( 'Add New', $label, 'bring-back-toolkit' ),
                        'add_new_item'       => __( 'Add New '.$label, 'bring-back-toolkit' ),
                        'new_item'           => __( 'New '.$label, 'bring-back-toolkit' ),
                        'edit_item'          => __( 'Edit '.$label, 'bring-back-toolkit' ),
                        'view_item'          => __( 'View '.$label, 'bring-back-toolkit' ),
                        'all_items'          => __( 'All '.$label, 'bring-back-toolkit' )
                    ];
                    $args = [
                        'labels'                => $labels,
                        'public'                => true,
                        'publicly_queryable'    => true,
                        'supports'              => $supports,
                        'register_meta_box_cb'  => $register_meta_box_cb
                    ];

                    // Permalink Flush
                    $this->permalink_flush();

                    register_post_type( $post_type, $args );
                }
            }
        }

        /**
         * case_studies
         * Adds the meta box.
         *
         * @access Public
         */
        public function bb_case_studies_add_metabox() {
            add_meta_box(
                'bb-case-studies-meta-box',
                __( 'Case Studies', 'bring-back-toolkit' ),
                [ $this, 'case_studies_metabox_callback' ],
                'bb-case-studies',
                'advanced',
                'default'
            );
        }

        /**
         * Get post meta in a callback
         *
         * @param $post
         * @access Public
         */
        public function case_studies_metabox_callback( $post ) {
            // Nonce field to validate form request came from current site
            wp_nonce_field( 'bb_case_studies_meta_action', 'bb_case_studies_meta_nonce' );

            foreach( $this->case_studies_metabox_fields() as $mbFields ) {

                extract( $mbFields );

                // Use get_post_meta to retrieve an existing value from the database.
                $post_meta_key = get_post_meta( $post->ID, $mbFields['id'], true );

                switch ( $mbFields['type'] ) {

                    case 'text' :
                        echo '<p><label for="'. $mbFields['id'] .'">'. $mbFields['title'] .'</label><input class="widefat" type="'. $mbFields['type'] .'" id="'. $mbFields['id'] .'" name="'. $mbFields['name'] .'" value="'. ( $post_meta_key ? $post_meta_key : $mbFields['default'] ) . '" /></p>';
                        break;

                }
            }
        }

        /**
         * Case Studies Meta fields
         *
         * @access Public
         * @return array
         */
        public function case_studies_metabox_fields() {
            return [
                [
                    'id'    => 'bb_case_studies_project_name',
                    'name'  => 'bb_case_studies_project_name',
                    'type'  => 'text',
                    'title' => 'Project Name',
                    'default' => ''
                ],
                [
                    'name'  => 'bb_case_studies_duration',
                    'title'  => 'Duration',
                    'id'    => 'bb_case_studies_duration',
                    'type'  => 'text',
                    'default' => ''
                ],
                [
                    'name'  => 'bb_case_studies_clients',
                    'title'  => 'Clients',
                    'id'    => 'bb_case_studies_clients',
                    'type'  => 'text',
                    'default' => ''
                ]
            ];
        }

        /**
         *
         * Save meta box
         *
         * @access Public
         * @param $post_id
         * @param $post
         */
        public function case_studies_save_metabox( $post_id ) {

            // Add nonce for security and authentication.
            $bb_nonce   = isset( $_POST['bb_case_studies_meta_nonce'] ) ? $_POST['bb_case_studies_meta_nonce'] : '';
            $bb_nonce_action = 'bb_case_studies_meta_action';

            // Check if nonce is valid.
            if ( ! wp_verify_nonce( $bb_nonce, $bb_nonce_action ) ) {
                return;
            }

            // Check if user has permissions to save data.
            if ( ! current_user_can( 'edit_post', $post_id ) ) {
                return;
            }

            // Check if not an autosave.
            if ( wp_is_post_autosave( $post_id ) ) {
                return;
            }

            // Check if not a revision.
            if ( wp_is_post_revision( $post_id ) ) {
                return;
            }

            foreach( $this->case_studies_metabox_fields() as $mbFields ) {

                extract( $mbFields );

                // Use get_post_meta to retrieve an existing value from the database.
                $old_key = get_post_meta( $post_id, $mbFields['name'], true );
                $new_key = $_POST[$mbFields['name']];

                if ( $new_key && $new_key != $old_key ) {

                    update_post_meta( $post_id, $mbFields['name'], $new_key );

                } elseif ( '' == $new_key && $old_key ) {

                    delete_post_meta( $post_id, $mbFields['name'], $old_key );

                }
            }
        }

        /**
         * social_share_init
         *
         * Social Share shortcode init
         * Register the Shortcode
         */
        public function social_share_init(){


            function bb_social_share( $atts = [], $content = null ){
                echo '<ul class="list-unstyled social-links">';
                foreach ( $this->social_share_type() as $social_type ){

                    extract( $social_type );

                    if( $social_type ) {

                        echo '<li class="list-inline-item"><a target="_blank" href="'. $social_type['href'] .'" class="'.$social_type['class'].'"><i class="icofont-'.$social_type['name'].'"></i></a></li>';

                    }
                }
                echo '</ul>';
                echo '</ul>';

               return $content;
           }

           // Register the Shortcode
           add_shortcode('bb_social_share_shortcode', 'bb_social_share');
        }

        /**
         * Social share type
         */
        public function social_share_type(){
            return [
                [
                    'name' => 'facebook',
                    'href' => 'https://www.facebook.com/sharer/sharer.php?u='.esc_url( get_the_permalink() ),
                    'class' => 'fb'
                ],
                [
                    'name' => 'twitter',
                    'href' => 'https://twitter.com/home?status='.esc_url( get_the_permalink() ),
                    'class' => 'tw'
                ],
                [
                    'name' => 'linkedin',
                    'href' => 'https://www.linkedin.com/shareArticle?mini=true&url='.esc_url( get_the_permalink() ),
                    'class' => 'lin'
                ]
            ];
        }

    }
}

if( ! function_exists('run_bring_back_toolkit') ) {

    function run_bring_back_toolkit() {
        Bring_Back_Toolkit::get_instance();
    }

    add_action( 'plugins_loaded', 'run_bring_back_toolkit' );
}