<?php

 /**
  * The plugin bootstrap file
 
  * @link                  https://www.themetim.com
  * @since                 1.0.0
  * @package               
  *
  * @wordpress-plugin
  *
  *
  * Plugin Name:           Bring Back Toolkit
  * Plugin URI:            https://www.themetim.com/plugin-name-uri/
  * Description:           
  * Plugin URI:            https://wpdevelopers.net/plugin_name/
  * Author:                 Themetim
  * Author URI:             https://www.themetim.com/
  * Version:                1.0.0
  * Author URI:             https://wpdeveloper.net/
  * License:                GPL-2.0+
  * License URI:            http://www.gnu.org/licenses/gpl-2.0.txt
  * Text Domain:            bring-back-toolkit
  * Domain Path:            /languages
  */
 
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly


if( ! class_exists('BringBackToolkit') ) {

    /**
     * Class BringBackToolkit
     * @pakage bring-back-toolkit
     */
    class BringBackToolkit
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
         * @return Global_Woo_Gallery
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
         * BringBackToolkit constructor.
         */
        public function __construct() {
            $this->define_constants();
            add_action( 'init', array( $this, 'register_post_types' ) );
        }

        public function define( $name, $value, $case_insensitive = false ) {
            if ( ! defined( $name ) ) {
                define( $name, $value, $case_insensitive );
            }
        }

        public function define_constants() {
            $this->define( 'BRINGBACK_PLUGIN_FILE', __FILE__ );
            $this->define( 'BRINGBACK_PLUGIN_URI', plugin_dir_url( __FILE__ ) );
            $this->define( 'BRINGBACK_PLUGIN_PATH', plugin_dir_path( __FILE__ ) );
            $this->define( 'BRINGBACK_VERSION', $this->version );
            $this->define( 'BRINGBACK_PLUGIN_INCLUDE_PATH', trailingslashit( plugin_dir_path( __FILE__ ) . 'includes' ) );
            $this->define( 'BRINGBACK_PLUGIN_DIRNAME', dirname( plugin_basename( __FILE__ ) ) );
            $this->define( 'BRINGBACK_PLUGIN_BASENAME', plugin_basename( __FILE__ ) );
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
                    'name'      => __( 'Testimonials', '' ),
                    'label'     => __( 'Testimonial', '' ),
                    'supports'  => [ 'title', 'editor', 'author', 'thumbnail' ]
                ],
                [
                    'post_type' => 'bb-services',
                    'name'      => __( 'Services', '' ),
                    'label'     => __( 'Service', '' ),
                    'supports'  => [ 'title', 'editor', 'author', 'thumbnail' ]
                ],
                [
                    'post_type' => 'bb-case-studies',
                    'name'      => __( 'Case Studies', '' ),
                    'label'     => __( 'Case Studies', '' ),
                    'supports'  => [ 'title', 'editor', 'author', 'thumbnail' ]
                ],
            ];
        }

        /**
         * Registering post types
         * 
         * @return voids
         */
        public function register_post_types() {
            foreach($this->post_types() as $post) {
                extract($post);

                    if($post_type) {
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
                        'labels'             => $labels,
                        'public'             => true,
                        'publicly_queryable' => true,
                        'supports'           => $supports
                    ];

                    register_post_type($post_type, $args );
                }

            }
        }

    }

}

if( ! function_exists('run_bringback_toolkit') ) {
    function run_bringback_toolkit() {
        BringBackToolkit::get_instance();
    }
    add_action( 'plugins_loaded', 'run_bringback_toolkit' );
}
