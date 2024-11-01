<?php
/**
 * Main class
 *
 * @author YITH
 * @package YITH WooCommerce Recently Viewed Products
 * @version 1.0.0
 */


if ( ! defined( 'YITH_WRVP' ) ) {
	exit;
} // Exit if accessed directly

if ( ! class_exists( 'YITH_WRVP' ) ) {
	/**
	 * YITH WooCommerce Recently Viewed Products
	 *
	 * @since 1.0.0
	 */
	class YITH_WRVP {

		/**
		 * Single instance of the class
		 *
		 * @var \YITH_WRVP
		 * @since 1.0.0
		 */
		protected static $instance;

		/**
		 * Plugin version
		 *
		 * @var string
		 * @since 1.0.0
		 */
		public $version = YITH_WRVP_VERSION;

		/**
		 * Returns single instance of the class
		 *
		 * @return \YITH_WRVP
		 * @since 1.0.0
		 */
		public static function get_instance(){
			if( is_null( self::$instance ) ){
				self::$instance = new self();
			}

			return self::$instance;
		}

		/**
		 * Constructor
		 *
		 * @return mixed YITH_WRVP_Admin | YITH_WRVP_Frontend
		 * @since 1.0.0
		 */
		public function __construct() {

            // Load Plugin Framework
            add_action( 'after_setup_theme', array( $this, 'plugin_fw_loader' ), 1 );

			if ( $this->is_admin() ) {
				// Class Admin
                include_once( 'class.yith-wrvp-admin.php' );
				YITH_WRVP_Admin();
			}
			else {
				// Class Frontend
                include_once( 'class.yith-wrvp-frontend.php' );
				YITH_WRVP_Frontend();
			}
		}

		/**
		 * Load Plugin Framework
		 *
		 * @since  1.0
		 * @access public
		 * @return void
		 * @author Andrea Grillo <andrea.grillo@yithemes.com>
		 */
		public function plugin_fw_loader() {
            if ( ! defined( 'YIT_CORE_PLUGIN' ) ) {
                global $plugin_fw_data;
                if( ! empty( $plugin_fw_data ) ){
                    $plugin_fw_file = array_shift( $plugin_fw_data );
                    require_once( $plugin_fw_file );
                }
            }
		}

        /**
         * Check if load admin classes
         *
         * @since 1.1.0
         * @author Francesco Licandro
         * @return boolean
         */
        public function is_admin(){
	        $check_ajax = defined( 'DOING_AJAX' ) && DOING_AJAX;
	        $check_context = isset( $_REQUEST['context'] ) && $_REQUEST['context'] == 'frontend';

	        return apply_filters( 'yith_wrvp_check_is_admin', is_admin() && ! ( $check_ajax && $check_context ) );
        }

		/**
		 * Filters woocommerce available mails, to add plugin related ones
		 *
		 * @param $emails array
		 *
		 * @return array
		 * @since 1.0
		 */
		public function add_woocommerce_emails( $emails ) {
			$emails['YITH_WRVP_Mail'] = include( YITH_WRVP_DIR . '/includes/class.yith-wrvp-mail.php' );
			return $emails;
		}

		/**
		 * Loads WC Mailer when needed
		 *
		 * @return void
		 * @since 1.0
		 * @author Francesco Licandro <francesco.licandro@yithemes.it>
		 */
		public function load_wc_mailer() {
			add_action( 'send_yith_wrvp_mail', array( 'WC_Emails', 'send_transactional_email' ), 10, 1 );
		}

		/**
		 * Load and register widgets
		 *
		 * @access public
		 * @since 1.0.0
		 * @author Francesco Licandro
		 */
		public function registerWidgets() {
			register_widget( 'YITH_WRVP_Widget' );
		}

		/**
		 * Init plugin
		 *
		 * @since 2.0.0
		 * @author Francesco Licandro <francesco.licandro@yithemes.com>
		 */
		public function init() {
			// Add compare page
			$this->_add_page();
			// register size
			$this->register_size();
			
			do_action( 'yith_wrvp_action_init_plugin' );
		}

		/**
		 * Add a page "Recently Viewed".
		 *
		 * @return void
		 * @since 1.0.0
		 */
		private function _add_page() {
			global $wpdb;

			$option_value = get_option( 'yith-wrvp-page-id' );

			if ( $option_value > 0 && get_post( $option_value ) )
				return;

			$page_found = $wpdb->get_var( "SELECT `ID` FROM `{$wpdb->posts}` WHERE `post_name` = 'recently-viewed-products' LIMIT 1;" );
			if ( $page_found ) {
				if ( ! $option_value )
					update_option( 'yith-wrvp-page-id', $page_found );
				return;
			}

			$page_data = array(
				'post_status' 		=> 'publish',
				'post_type' 		=> 'page',
				'post_author' 		=> 1,
				'post_name' 		=> esc_sql( _x( 'recently-viewed-products', 'page_slug', 'yith-woocommerce-recently-viewed-products' ) ),
				'post_title' 		=> __( 'Recently Viewed Products', 'yith-woocommerce-recently-viewed-products' ),
				'post_content' 		=> '[yith_recenlty_viewed_page]',
				'post_parent' 		=> 0,
				'comment_status' 	=> 'closed'
			);
			$page_id = wp_insert_post( $page_data );

			update_option( 'yith-wrvp-page-id', $page_id );
		}

        /**
         * Register new size image
         *
         * @access public
         * @since 1.0.0
         * @author Francesco Licandro
         */
        public function register_size(){

            $size = get_option( 'yith-wrvp-image-size', '' );

            if( ! $size ) {
                return;
            }

            $width  = isset( $size['width'] ) ? $size['width'] : 80;
            $height = isset( $size['height'] ) ? $size['height'] : 80;
            $crop   = isset( $size['crop'] ) ? $size['crop'] : false;

            add_image_size( 'ywrvp_image_size', $width, $height, $crop );
        }
	}
}

/**
 * Unique access to instance of YITH_WRVP class
 *
 * @return \YITH_WRVP
 * @since 1.0.0
 */
function YITH_WRVP(){
	return YITH_WRVP::get_instance();
}