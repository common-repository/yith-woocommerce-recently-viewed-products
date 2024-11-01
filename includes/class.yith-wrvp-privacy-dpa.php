<?php
if ( ! defined( 'YITH_WRVP' ) ) {
	exit; // Exit if accessed directly
}

if ( !class_exists( 'YITH_WRVP_Privacy_DPA' ) ) {
	/**
	 * Class YITH_WRVP_Privacy_DPA
	 * Privacy Class
	 *
	 * @author Francesco Licandro
	 */
	class YITH_WRVP_Privacy_DPA extends YITH_Privacy_Plugin_Abstract {

		/**
		 * YITH_YWRAQ_Privacy constructor.
		 */
		public function __construct() {
			parent::__construct( _x( 'YITH Woocommerce Recently Viewed Products Free', 'Privacy Policy Content', 'yith-woocommerce-recently-viewed-products' ) );
		}

		public function get_privacy_message( $section ) {
			$message = '';

			switch ( $section ){
				case 'collect_and_store':
					$message = '<p class="privacy-policy-tutorial">'. __( 'While you visit our site, we’ll track:', 'yith-woocommerce-recently-viewed-products' ) . '</p>'.
					           '<ul>' .
					           '<li>' . __( '- Products viewed and store products ID using a cookie.', 'yith-woocommerce-recently-viewed-products' ) . '</li>' .
					           '</ul>'.
                                '<p class="privacy-policy-tutorial">'.__( 'Those information will be used to show to the customer customized contents.', 'yith-woocommerce-recently-viewed-products').'</p>'.
					           '<p class="privacy-policy-tutorial">' . __( 'Note: you may want to further detail your cookie policy, and link to that section from here.', 'yith-woocommerce-recently-viewed-products' ) . '</p>';
					break;
			}


			return $message;
		}
	}
}

new YITH_WRVP_Privacy_DPA();