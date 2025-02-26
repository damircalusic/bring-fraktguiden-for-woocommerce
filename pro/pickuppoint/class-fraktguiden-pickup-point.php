<?php
/**
 * This file is part of Bring Fraktguiden for WooCommerce.
 *
 * @package Bring_Fraktguiden
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * Fraktguiden_Pickup_Point class
 *
 * Process the checkout
 */
class Fraktguiden_Pickup_Point {

	const ID          = Fraktguiden_Helper::ID;
	const BASE_URL    = 'https://api.bring.com/pickuppoint/api/pickuppoint';
	const TEXT_DOMAIN = Fraktguiden_Helper::TEXT_DOMAIN;

	/**
	 * Initialize
	 *
	 * @return void
	 */
	public static function init() {
		// Enqueue checkout Javascript.
		add_action( 'wp_enqueue_scripts', array( __CLASS__, 'checkout_load_javascript' ) );
		// Enqueue admin Javascript.
		add_action( 'admin_enqueue_scripts', array( __CLASS__, 'admin_load_javascript' ) );
		// Admin save order items.
		add_action( 'woocommerce_saved_order_items', array( __CLASS__, 'admin_saved_order_items' ), 1, 2 );

		// Ajax.
		add_action( 'wp_ajax_bring_get_pickup_points', __CLASS__ . '::ajax_get_pickup_points' );
		add_action( 'wp_ajax_nopriv_bring_get_pickup_points', __CLASS__ . '::ajax_get_pickup_points' );

		add_action( 'wp_ajax_bring_shipping_info_var', array( __CLASS__, 'wp_ajax_get_bring_shipping_info_var' ) );
		add_action( 'wp_ajax_bring_get_rate', array( __CLASS__, 'wp_ajax_get_rate' ) );

		// Display order received and mail.
		add_filter( 'woocommerce_order_shipping_to_display_shipped_via', array( __CLASS__, 'checkout_order_shipping_to_display_shipped_via' ), 1, 2 );

		// Hide shipping meta data from order items (WooCommerce 2.6)
		// See https://github.com/woothemes/woocommerce/issues/9094 for reference.
		add_filter( 'woocommerce_hidden_order_itemmeta', array( __CLASS__, 'woocommerce_hidden_order_itemmeta' ), 1, 1 );
		add_filter( 'woocommerce_order_item_display_meta_key', array( __CLASS__, 'woocommerce_order_item_display_meta_key' ) );

		// Pickup points.
		// if ( 'yes' === Fraktguiden_Helper::get_option( 'pickup_point_enabled' ) ) {
			add_filter( 'bring_shipping_rates', __CLASS__ . '::insert_pickup_points', 10, 2 );
			// add_filter( 'bring_pickup_point_limit', __CLASS__ . '::limit_pickup_points' );
		// }
	}

	/**
	 * Add additional item meta
	 *
	 * @param  array $fields Fields.
	 * @return array
	 */
	public static function woocommerce_hidden_order_itemmeta( $fields ) {
		$fields[] = '_fraktguiden_pickup_point_postcode';
		$fields[] = '_fraktguiden_pickup_point_id';
		$fields[] = '_fraktguiden_pickup_point_info_cached';
		$fields[] = 'pickup_point_id';
		$fields[] = 'bring_product';
		$fields[] = 'expected_delivery_date';

		return $fields;
	}

	/**
	 * Add additional item meta
	 *
	 * @param  string $display_key Display key.
	 * @return string
	 */
	public static function woocommerce_order_item_display_meta_key( $display_key ) {

		if ( 'bring_fraktguiden_time_slot' === $display_key ) {
			return __( 'Selected time slot', 'bring-fraktguiden-for-woocommerce' );
		}
		return $display_key;
	}

	/**
	 * Load checkout javascript
	 */
	public static function checkout_load_javascript() {

		if ( ! is_checkout() ) {
			return;
		}

		wp_register_script( 'fraktguiden-common', plugins_url( 'assets/js/pickup-point-common.js', dirname( __FILE__ ) ), array( 'jquery' ), Bring_Fraktguiden::VERSION, true );
		wp_register_script( 'fraktguiden-pickup-point-checkout', plugins_url( 'assets/js/pickup-point-checkout.js', dirname( __FILE__ ) ), array( 'jquery' ), Bring_Fraktguiden::VERSION, true );
		wp_localize_script(
			'fraktguiden-pickup-point-checkout',
			'_fraktguiden_data',
			[
				'ajaxurl'               => admin_url( 'admin-ajax.php' ),
				'i18n'                  => self::get_i18n(),
				'country'               => Fraktguiden_Helper::get_option( 'from_country' ),
				'klarna_checkout_nonce' => wp_create_nonce( 'klarna_checkout_nonce' ),
				'nonce'                 => wp_create_nonce( 'bring_fraktguiden' ),
			]
		);

		wp_enqueue_script( 'fraktguiden-common' );
		wp_enqueue_script( 'fraktguiden-pickup-point-checkout' );
	}

	/**
	 * Load admin javascript
	 */
	public static function admin_load_javascript() {
		$screen = get_current_screen();

		// Only for order edit screen.
		if ( 'shop_order' !== $screen->id ) {
			return;
		}

		global $post;

		$order = new Bring_WC_Order_Adapter( new WC_Order( $post->ID ) );

		$make_items_editable = ! $order->order->is_editable();

		if ( ! is_null( filter_input( INPUT_GET, 'booking_step' ) ) ) {
			$make_items_editable = false;
		}

		if ( $order->is_booked() ) {
			$make_items_editable = false;
		}

		wp_register_script( 'fraktguiden-common', plugins_url( 'assets/js/pickup-point-common.js', dirname( __FILE__ ) ), array( 'jquery' ), Bring_Fraktguiden::VERSION, true );
		wp_register_script( 'fraktguiden-pickup-point-admin', plugins_url( 'assets/js/pickup-point-admin.js', dirname( __FILE__ ) ), array( 'jquery' ), Bring_Fraktguiden::VERSION, true );
		wp_localize_script(
			'fraktguiden-pickup-point-admin',
			'_fraktguiden_data',
			[
				'ajaxurl'             => admin_url( 'admin-ajax.php' ),
				'services'            => Fraktguiden_Helper::get_all_services(),
				'i18n'                => self::get_i18n(),
				'make_items_editable' => $make_items_editable,
			]
		);

		wp_enqueue_script( 'fraktguiden-common' );
		wp_enqueue_script( 'fraktguiden-pickup-point-admin' );
	}

	/**
	 * Get Bring shipping info for order
	 *
	 * @return array
	 */
	public static function get_bring_shipping_info_for_order() {
		$result = [];
		$screen = get_current_screen();

		if ( ( $screen && 'shop_order' === $screen->id ) || is_ajax() ) {
			global $post;

			$post_id = $post ? $post->ID : filter_input( INPUT_GET, 'post_id' );
			$order   = new Bring_WC_Order_Adapter( new WC_Order( $post_id ) );
			$result  = $order->get_shipping_data();
		}

		return $result;
	}

	/**
	 * Updates pickup points from admin/order items.
	 *
	 * @param int|string $order_id       Order ID.
	 * @param array      $shipping_items Shipping items.
	 */
	public static function admin_saved_order_items( $order_id, $shipping_items ) {
		$order = new Bring_WC_Order_Adapter( new WC_Order( $order_id ) );
		$order->admin_update_pickup_point( $shipping_items );
	}

	/**
	 * HTML for checkout recipient page / emails etc.
	 *
	 * @param string   $content  Content.
	 * @param WC_Order $wc_order Order.
	 * @return string
	 */
	public static function checkout_order_shipping_to_display_shipped_via( $content, $wc_order ) {
		$shipping_methods = $wc_order->get_shipping_methods();

		foreach ( $shipping_methods as $shipping_method ) {
			if (
				self::ID . ':servicepakke' === $shipping_method['method_id'] &&
				isset( $shipping_method['fraktguiden_pickup_point_info_cached'] ) &&
				$shipping_method['fraktguiden_pickup_point_info_cached']
			) {
				$info    = $shipping_method['fraktguiden_pickup_point_info_cached'];
				$content = $content . '<div class="bring-order-details-pickup-point"><div class="bring-order-details-selected-text">' . self::get_i18n()['PICKUP_POINT'] . ':</div><div class="bring-order-details-info-text">' . str_replace( '|', '<br>', $info ) . '</div></div>';
			}
		}

		return $content;
	}

	/**
	 * Text translation strings for ui JavaScript.
	 *
	 * @return array
	 */
	public static function get_i18n() {
		return [
			'PICKUP_POINT'               => __( 'Pickup point', 'bring-fraktguiden-for-woocommerce' ),
			'LOADING_TEXT'               => __( 'Please wait...', 'bring-fraktguiden-for-woocommerce' ),
			'VALIDATE_SHIPPING1'         => __( 'Fraktguiden requires the following fields', 'bring-fraktguiden-for-woocommerce' ),
			'VALIDATE_SHIPPING_POSTCODE' => __( 'Valid shipping postcode', 'bring-fraktguiden-for-woocommerce' ),
			'VALIDATE_SHIPPING_COUNTRY'  => __( 'Valid shipping postcode', 'bring-fraktguiden-for-woocommerce' ),
			'VALIDATE_SHIPPING2'         => __( 'Please update the fields and save the order first', 'bring-fraktguiden-for-woocommerce' ),
			'SERVICE_PLACEHOLDER'        => __( 'Please select service', 'bring-fraktguiden-for-woocommerce' ),
			'POSTCODE'                   => __( 'Postcode', 'bring-fraktguiden-for-woocommerce' ),
			'PICKUP_POINT_PLACEHOLDER'   => __( 'Please select pickup point', 'bring-fraktguiden-for-woocommerce' ),
			'SELECTED_TEXT'              => __( 'Selected pickup point', 'bring-fraktguiden-for-woocommerce' ),
			'PICKUP_POINT_NOT_FOUND'     => __( 'No pickup points found for postcode', 'bring-fraktguiden-for-woocommerce' ),
			'GET_RATE'                   => __( 'Get Rate', 'bring-fraktguiden-for-woocommerce' ),
			'PLEASE_WAIT'                => __( 'Please wait', 'bring-fraktguiden-for-woocommerce' ),
			'SERVICE'                    => __( 'Service', 'bring-fraktguiden-for-woocommerce' ),
			'RATE_NOT_AVAILABLE'         => __( 'Rate is not available for this order. Please try another service', 'bring-fraktguiden-for-woocommerce' ),
			'REQUEST_FAILED'             => __( 'Request was not successful', 'bring-fraktguiden-for-woocommerce' ),
			'ADD_POSTCODE'               => __( 'Please add postal code', 'bring-fraktguiden-for-woocommerce' ),
		];
	}

	/**
	 * Prints shipping info json
	 *
	 * Only available from admin
	 */
	public static function wp_ajax_get_bring_shipping_info_var() {
		wp_send_json( array( 'bring_shipping_info' => self::get_bring_shipping_info_for_order() ) );
	}

	/**
	 * Prints rate json for a bring service.
	 *
	 * Only available from admin.
	 */

	public static function wp_ajax_get_rate() {
		$result = [
			'success'  => false,
			'rate'     => null,
			'packages' => null,
		];

		$service = filter_input( INPUT_GET, 'service' );

		// Return false if neither integer nor string variable is representing a positive integer.
		$post_id = filter_var(
			filter_input( INPUT_GET, 'post_id' ),
			FILTER_VALIDATE_INT,
			array( 'options' => array( 'min_range' => 1 ) )
		);

		if ( is_null( $service ) || false === $post_id ) {
			wp_send_json( $result );
		}

		$order = wc_get_order( $post_id );

		$country = filter_input( INPUT_GET, 'country' );

		if ( is_null( $country ) ) {
			$country = $order->get_shipping_country();
		}

		$postcode = filter_input( INPUT_GET, 'postcode' );

		if ( is_null( $postcode ) ) {
			$postcode = $order->get_shipping_postcode();
		}

		$items = $order->get_items();

		$fake_cart = [];

		foreach ( $items as $item ) {
			$fake_cart[ uniqid() ] = [
				'quantity' => $item['qty'],
				'data'     => new WC_Product_Simple( $item['product_id'] ),
			];
		}

		$packer = new Fraktguiden_Packer();

		$product_boxes = $packer->create_boxes( $fake_cart );

		$packer->pack( $product_boxes, true );

		$package_params = $packer->create_packages_params();

		// @todo: share / filter
		$standard_params = array(
			'clientUrl'           => Fraktguiden_Helper::get_client_url(),
			'frompostalcode'      => Fraktguiden_Helper::get_option( 'from_zip' ),
			'fromcountry'         => Fraktguiden_Helper::get_option( 'from_country' ),
			'topostalcode'        => $postcode,
			'tocountry'           => $country,
			'postingatpostoffice' => ( Fraktguiden_Helper::get_option( 'post_office' ) === 'no' ) ? 'false' : 'true',
		);

		$shipping_method = new WC_Shipping_Method_Bring();

		$field_key = $shipping_method->get_field_key( 'services' );
		$evarsling = \Fraktguiden_Service::vas_for( $field_key, $service, [ '2084', 'EVARSLING' ] );

		$standard_params['additionalservice'] = ( $evarsling ? 'EVARSLING' : '' );

		$params = array_merge( $standard_params, $package_params );

		$url  = add_query_arg( $params, WC_Shipping_Method_Bring::SERVICE_URL );
		$url .= '&product=' . strtoupper( $service );

		// Make the request.
		$request  = new WP_Bring_Request();
		$response = $request->get( $url );

		if ( 200 !== $response->status_code ) {
			wp_send_json( $params );
		}

		$json  = json_decode( $response->get_body(), true );
		$rates = $shipping_method->get_services_from_response( $json );

		if ( empty( $rates ) ) {
			wp_send_json( $params );
		}

		$rate               = reset( $rates );
		$result['success']  = true;
		$result['rate']     = $rate['cost'];
		$result['packages'] = wp_json_encode( $package_params );

		wp_send_json( $result );
	}
	/**
	 * Get pickup points via AJAX
	 */
	public static function ajax_get_pickup_points() {

		$response = self::get_pickup_points(
			filter_input( INPUT_GET, 'country' ),
			filter_input( INPUT_GET, 'postcode' )
		);

		if ( 200 !== $response->status_code ) {
			wp_die();
		}

		wp_send_json( json_decode( $response->get_body(), true ) );
	}

	/**
	 * Get pickup points
	 *
	 * @param  string $country  Country.
	 * @param  string $postcode Postcode.
	 * @return string
	 */
	public static function get_pickup_points( $country, $postcode ) {
		$request = new WP_Bring_Request();
		return $request->get( self::BASE_URL . '/' . $country . '/postalCode/' . $postcode . '.json' );
	}

	/**
	 * Limit pickup points
	 *
	 * @param  int $default_limit Default limit.
	 * @return int
	 */
	public static function limit_pickup_points( $default_limit ) {
		return Fraktguiden_Helper::get_option( 'pickup_point_limit' ) ?: $default_limit;
	}

	/**
	 * Filter: Insert pickup points
	 *
	 * @param array $rates Rates.
	 * @hook bring_shipping_rates
	 *
	 * @return array
	 */
	public static function insert_pickup_points( $rates, $shipping_rate ) {

		$field_key            = $shipping_rate->get_field_key( 'services' );
		$services             = \Fraktguiden_Service::all( $field_key );

		$rate_key        = false;
		$service_package = false;
		$bring_product   = false;

		foreach ( $rates as $key => $rate ) {
			// Service package identified.
			$service_package = $rate;
			$bring_product   = strtoupper( $rate['bring_product'] );

			if ( empty( $services[ $bring_product ] ) ) {
				continue;
			}

			$service = $services[ $bring_product ];

			if ( empty( $service->settings['pickup_point_cb'] ) ) {
				continue;
			}
			// Remove this package.
			$rate_key = $key;
			break;
		}



		if ( false === $rate_key ) {
			// Service package is not available.
			// That means it's the end of the line for pickup points.
			return $rates;
		}

		$pickup_point_limit = apply_filters( 'bring_pickup_point_limit', (int) $service->settings['pickup_point'] );
		$postcode           = esc_html(
			apply_filters( 'bring_pickup_point_postcode', WC()->customer->get_shipping_postcode() )
		);
		$country            = esc_html(
			apply_filters( 'bring_pickup_point_country', WC()->customer->get_shipping_country() )
		);
		$response           = self::get_pickup_points( $country, $postcode );

		if ( 200 !== $response->status_code ) {
			sleep( 1 );
			$response = self::get_pickup_points( $country, $postcode );
		}

		if ( 200 !== $response->status_code ) {
			return $rates;
		}

		// Remove service package.
		unset( $rates[ $rate_key ] );

		$pickup_point_count = 1;
		$pickup_points      = json_decode( $response->get_body(), 1 );
		$new_rates          = [];

		foreach ( $pickup_points['pickupPoint'] as $pickup_point ) {
			$rate = [
				'id'            => "bring_fraktguiden:{$bring_product}-{$pickup_point['id']}",
				'bring_product' => $bring_product,
				'expected_delivery_date' => $service_package['expected_delivery_date'],
				'cost'          => $service_package['cost'],
				'label'         => $pickup_point['name'],
				'meta_data'     => [
					'pickup_point_id'   => $pickup_point['id'],
					'pickup_point_data' => $pickup_point,
				],
			];

			$new_rates[] = $rate;
			if ( $pickup_point_limit && $pickup_point_limit <= $pickup_point_count ) {
				break;
			}
			$pickup_point_count++;
		}

		foreach ( $rates as $key => $rate ) {
			$new_rates[] = $rate;
		}

		return $new_rates;
	}
}
