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
            add_filter( 'the_content', array( $this, 'bb_social_share' ) );
            // Breadcrumbs
            add_shortcode( 'bb_breadcrumbs', array( $this, 'bring_back_breadcrumbs' ) );
            // Folder Path
            //add_action( 'init', array( $this, 'folderPath' ) );
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
            $this->define( 'Bring_Back_Toolkit_PLUGIN_INCLUDE_PATH', trailingslashit( plugin_dir_path( __FILE__ ) . 'includes' ) );
            $this->define( 'Bring_Back_Toolkit_PLUGIN_DIRNAME', dirname( plugin_basename( __FILE__ ) ) );
            $this->define( 'Bring_Back_Toolkit_PLUGIN_BASENAME', plugin_basename( __FILE__ ) );
        }

        /**
         * Folder Path
         */
        public function folderPath(){
            //require_once Bring_Back_Toolkit_PLUGIN_INCLUDE_PATH.'breadcrumbs.php';
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
                        //'taxonomies'  => array( 'category' ),
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
         * bb_social_share
         *
         * Social Share after content
         */
        public function bb_social_share( $content ){

            if ( is_singular( 'bb-case-studies' ) ) {

                $content .= '<div class="clearfix"></div><!-- .social-share-tags start --><div class="social-share-tags overflow-hidden align-items-lg-center d-lg-flex"><div class="ml-lg-auto social-share-fix"><ul class="list-unstyled social-links">';

                foreach ( $this->social_share_type() as $social_type ){

                    extract( $social_type );

                    if( $social_type ) {
                        $content .= '<li class="list-inline-item"><a target="_blank" href="'. $social_type['href'] .'" class="'.$social_type['class'].'"><i class="icofont-'.$social_type['name'].'"></i></a></li>';
                    }

                }

                $content .= '</ul></div></div><!-- .social-share-tags end -->';

            }

            return $content;
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


        /**
         * @param $args
         * @return string|void
         *
         * Breadcrumbs
         */
        public function bring_back_breadcrumbs( $args ) {

            if ( is_front_page() ) {
                return;
            }

            global $post;
            $defaults  = array(
                'separator_icon'      => '',
                'breadcrumbs_id'      => 'breadcrumb',
                'breadcrumbs_classes' => ' text-center text-capitalize',
                'home_title'          => esc_html__( 'Home', 'bring-back' )
            );
            $args      = apply_filters( 'bring_back_breadcrumbs_args', wp_parse_args( $args, $defaults ) );

            // Open the breadcrumbs
            $html = '<div class="breadcrumb-wrapper"><div id="' . esc_attr( $args['breadcrumbs_id'] ) . '" class="' . esc_attr( $args['breadcrumbs_classes'] ) . '"><div class="col-12">';

            $title = '<h1 class="page-title">'.get_the_title().'</h1>';

            // Add Homepage link & separator (always present)

            $home = '<span class="item-home"><a class="bread-link bread-home" href="' . esc_url(get_home_url()) . '" title="' . esc_attr( $args['home_title'] ) . '">' .  esc_html($args['home_title']) . '</a></span>';

            // Post
            if ( is_singular( 'post' ) ) {

                $category = get_the_category();
                $category_values = array_values( $category );
                $last_category = end( $category_values );
                $cat_parents = rtrim( get_category_parents( $last_category->term_id, true, ',' ), ',' );
                $cat_parents = explode( ',', $cat_parents );
                $html .= '<h1 class="page-title">'.esc_html( get_the_title() ).'</h1>';
                $html .= $home;
                foreach ( $cat_parents as $parent ) {
                    $html .= '<span class="item-cat">' . wp_kses( $parent, wp_kses_allowed_html( 'a' ) ) . '</span>';
                }

                $html .= '<span class="item-current item-' . $post->ID . '"><span class="bread-current bread-' . $post->ID . '" title="' . esc_attr( get_the_title() ) . '">' . esc_html( get_the_title() ) . '</span></span>';
            } elseif ( is_singular( 'page' ) ) {
                $html .= $title;
                $html .= $home;
                if ( $post->post_parent ) {
                    $parents = get_post_ancestors( $post->ID );
                    $parents = array_reverse( $parents );

                    foreach ( $parents as $parent ) {
                        $html .= '<span class="item-parent item-parent-' . esc_attr( $parent ) . '"><a class="bread-parent bread-parent-' . esc_attr( $parent ) . '" href="' . esc_url( get_permalink( $parent ) ) . '" title="' . esc_attr( get_the_title( $parent ) ) . '">' . esc_html( get_the_title( $parent ) ) . '</a></span>';

                    }
                }
                $html .= '<span class="item-current item-' . $post->ID . '"><span title="' . esc_attr( get_the_title() ) . '"> ' . esc_html( get_the_title() ) . '</span></span>';
            } elseif ( is_singular( 'attachment' ) ) {

                $parent_id        = $post->post_parent;
                $parent_title     = get_the_title( $parent_id );
                $parent_permalink = esc_url( get_permalink( $parent_id ) );
                $html .= $title;
                $html .= $home;
                $html .= '<span class="item-parent"><a class="bread-parent" href="' . esc_url( $parent_permalink ) . '" title="' . esc_attr( $parent_title ) . '">' . esc_html( $parent_title ) . '</a></span>';

                $html .= '<span class="item-current item-' . $post->ID . '"><span title="' . esc_attr( get_the_title() ) . '"> ' . esc_html( get_the_title() ) . '</span></span>';
            } elseif ( is_singular() ) {

                $post_type         = get_post_type();
                $post_type_object  = get_post_type_object( $post_type );
                $post_type_archive = get_post_type_archive_link( $post_type );
                $html .= $title;
                $html .= $home;
                $html .= '<span class="item-cat item-custom-post-type-' . esc_attr( $post_type ) . '"><a class="bread-cat bread-custom-post-type-' . esc_attr( $post_type ) . '" href="' . esc_url( $post_type_archive ) . '" title="' . esc_attr( $post_type_object->labels->name ) . '">' . esc_html( $post_type_object->labels->name ) . '</a></span>';

                $html .= '<span class="item-current item-' . $post->ID . '"><span class="bread-current bread-' . $post->ID . '" title="' . esc_attr($post->post_title) . '">' . esc_html($post->post_title) . '</span></span>';
            } elseif ( is_category() ) {

                $parent = get_queried_object()->category_parent;

                if ( $parent !== 0 ) {

                    $parent_category = get_category( $parent );
                    $category_link   = get_category_link( $parent );

                    $html .= '<span class="item-parent item-parent-' . esc_attr( $parent_category->slug ) . '"><a class="bread-parent bread-parent-' . esc_attr( $parent_category->slug ) . '" href="' . esc_url( $category_link ) . '" title="' . esc_attr( $parent_category->name ) . '">' . esc_html( $parent_category->name ) . '</a></span>';

                }
                $html .= '<h1 class="page-title">'.single_cat_title( '', false ).'</h1>';;
                $html .= $home;
                $html .= '<span class="item-current item-cat"><span class="bread-current bread-cat" title="' . esc_attr($post->ID) . '">' . single_cat_title( '', false ) . '</span></span>';
            } elseif ( is_tag() ) {
                $html .= '<h1 class="page-title">'.single_tag_title( '', false ).'</h1>';
                $html .= $home;
                $html .= '<span class="item-current item-tag"><span class="bread-current bread-tag">' . single_tag_title( '', false ) . '</span></span>';
            } elseif ( is_author() ) {
                $html .= '<h1 class="page-title">'.esc_html( get_queried_object()->display_name  ).'</h1>';
                $html .= $home;
                $html .= '<span class="item-current item-author"><span class="bread-current bread-author">' . get_queried_object()->display_name . '</span></span>';
            } elseif ( is_day() ) {
                $html .= '<h1 class="page-title">'.get_the_date().'</h1>';
                $html .= $home;
                $html .= '<span class="item-current item-day"><span class="bread-current bread-day">' . get_the_date() . '</span></span>';
            } elseif ( is_month() ) {
                $html .= '<h1 class="page-title">'.get_the_date( 'F Y' ).'</h1>';
                $html .= $home;
                $html .= '<span class="item-current item-month"><span class="bread-current bread-month">' . get_the_date( 'F Y' ) . '</span></span>';
            } elseif ( is_year() ) {
                $html .= '<h1 class="page-title">'.get_the_date( 'Y' ).'</h1>';
                $html .= $home;
                $html .= '<span class="item-current item-year"><span class="bread-current bread-year">' . get_the_date( 'Y' ) . '</span></span>';
            } elseif ( is_archive() ) {
                $custom_tax_name = get_queried_object()->name;
                $html .= '<h1 class="page-title">'.esc_html( $custom_tax_name ).'</h1>';
                $html .= $home;
                $html .= '<span class="item-current item-archive"><span class="bread-current bread-archive">' . esc_html( $custom_tax_name ) . '</span></span>';
            } elseif ( is_search() ) {
                $html .= '<h1 class="page-title">'.get_search_query().'</h1>';
                $html .= $home;
                $html .= '<span class="item-current item-search"><span class="bread-current bread-search">'.__('Search results for : ','bring-back').' ' . get_search_query() . '</span></span>';
            } elseif ( is_404() ) {
                $html .= '<h1 class="page-title">'.esc_html( '404' ).'</h1>';
                $html .= $home;
                $html .= '<span>' . __( 'Error 404', 'bring-back' ) . '</span>';
            } elseif ( is_home() ) {
                $html .= '<h1 class="page-title">'.esc_html( 'Blog' ).'</h1>';
                $html .= $home;
                $html .= '<span>' . esc_html( get_the_title( get_option( 'page_for_posts' ) ) ) . '</span>';
            }

            $html .= '</div></div></div>';
            $html = apply_filters( 'bring_back_breadcrumbs_filter', $html );

            return wp_kses_post( $html );
        }

    }
}

if( ! function_exists('run_bring_back_toolkit') ) {

    function run_bring_back_toolkit() {
        Bring_Back_Toolkit::get_instance();
    }

    add_action( 'plugins_loaded', 'run_bring_back_toolkit' );
}