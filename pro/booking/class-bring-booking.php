<?php
/**
 * This file is part of Bring Fraktguiden for WooCommerce.
 *
 * @package Bring_Fraktguiden
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

// Frontend views.
require_once 'views/class-bring-booking-my-order-view.php';
add_filter( 'woocommerce_order_shipping_to_display', 'Bring_Booking_My_Order_View::order_display_tracking_info', 5, 2 );

// Consignment.
require_once 'classes/consignment/class-bring-consignment.php';
require_once 'classes/consignment/class-bring-mailbox-consignment.php';
require_once 'classes/consignment/class-bring-booking-consignment.php';

// Consignment request.
require_once 'classes/consignment-request/class-bring-consignment-request.php';
require_once 'classes/consignment-request/class-bring-booking-consignment-request.php';
require_once 'classes/consignment-request/class-bring-mailbox-consignment-request.php';

// Classes.
require_once 'classes/class-bring-booking-file.php';
require_once 'classes/class-bring-booking-customer.php';

if ( is_admin() ) {
	// Views.
	include_once 'views/class-bring-booking-labels.php';
	include_once 'views/class-bring-booking-waybills.php';
	include_once 'views/class-bring-booking-order-view-common.php';
	include_once 'views/class-bring-booking-orders-view.php';
	include_once 'views/class-bring-booking-order-view.php';
	include_once 'views/class-bring-waybill-view.php';
	Bring_Waybill_View::setup();
}

if ( Fraktguiden_Helper::booking_enabled() && Fraktguiden_Helper::pro_activated() ) {
	include_once 'classes/class-post-type-mailbox-waybill.php';
	include_once 'classes/class-post-type-mailbox-label.php';
	include_once 'classes/class-generate-mailbox-labels.php';
	Post_Type_Mailbox_Waybill::setup();
	Post_Type_Mailbox_Label::setup();
	Generate_Mailbox_Labels::setup();
}

// Register awaiting shipment status.
add_action( 'init', 'Bring_Booking::register_awaiting_shipment_order_status' );

// Add awaiting shipping to existing order statuses.
add_filter( 'wc_order_statuses', 'Bring_Booking::add_awaiting_shipment_status' );

/**
 * Bring_Booking class
 */
class Bring_Booking {

	const ID          = Fraktguiden_Helper::ID;
	const TEXT_DOMAIN = Fraktguiden_Helper::TEXT_DOMAIN;

	/**
	 * Initialize
	 *
	 * @return void
	 */
	public static function init() {
		if ( ! self::is_valid_for_use() ) {
			return;
		}

		Bring_Booking_Orders_View::init();
		Bring_Booking_Order_View::init();

		// Update status on printed orders
		add_action( 'init', __CLASS__ . '::update_printed_orders' );
	}

	/**
	 * Check if API UID and key are valid
	 *
	 * @return bool
	 */
	public static function is_valid_for_use() {
		$api_uid = self::get_api_uid();
		$api_key = self::get_api_key();

		return $api_uid && $api_key;
	}
	/**
	 * Change the status on printed orders
	 */
	public static function update_printed_orders() {
		// Create new status and order note.
		$status = Fraktguiden_Helper::get_option( 'auto_set_status_after_print_label_success' );
		$printed_orders = Fraktguiden_Helper::get_option( 'printed_orders' );
		if ( empty( $printed_orders ) ) {
			return;
		}
		foreach ($printed_orders as $order_id) {
			$order = wc_get_order( $order_id );
			if ( ! $order || is_wp_error( $order ) ) {
				continue;
			}
			if ( 'none' === $status || $status === $order->get_status() ) {
				continue;
			}

			// Update status.
			$order->update_status(
				$status,
				__( 'Changing status because the label was downloaded.', 'bring-fraktguiden-for-woocommerce' ) . PHP_EOL
			);
		}
		Fraktguiden_Helper::update_option( 'printed_orders', [] );
	}

	/**
	 * Register awaiting shipment order status.
	 */
	public static function register_awaiting_shipment_order_status() {
		// Be careful changing the post status name.
		// If orders has this status they will not be available in admin.
		register_post_status(
			'wc-bring-shipment',
			array(
				'label'                     => __( 'Awaiting Shipment', 'bring-fraktguiden-for-woocommerce' ),
				'public'                    => true,
				'exclude_from_search'       => false,
				'show_in_admin_all_list'    => true,
				'show_in_admin_status_list' => true,
				/* translators: %s: Number of awaiting shipments */
				'label_count'               => _n_noop( __( 'Awaiting Shipment', 'bring-fraktguiden-for-woocommerce' ) . ' <span class="count">(%s)</span>', __( 'Awaiting Shipment', 'bring-fraktguiden-for-woocommerce' ) . ' <span class="count">(%s)</span>' ),
			)
		);
	}

	/**
	 * Add awaiting shipment to order statuses.
	 *
	 * @param array $order_statuses Order statuses.
	 * @return array
	 */
	public static function add_awaiting_shipment_status( $order_statuses ) {
		$new_order_statuses = [];

		// Add the order status after processing.
		foreach ( $order_statuses as $key => $status ) {
			$new_order_statuses[ $key ] = $status;

			if ( 'wc-processing' === $key ) {
				$new_order_statuses['wc-bring-shipment'] = __( 'Awaiting Shipment', 'bring-fraktguiden-for-woocommerce' );
			}
		}

		return $new_order_statuses;
	}

	/**
	 * Send booking
	 *
	 * @param WC_Order $wc_order WooCommerce order.
	 */
	public static function send_booking( $wc_order ) {
		// Bring_WC_Order_Adapter.
		$adapter = new Bring_WC_Order_Adapter( $wc_order );

		// One booking request per order shipping item (WC_Order_Item_Shipping).
		foreach ( $adapter->get_fraktguiden_shipping_items() as $shipping_item ) {
			// Create the consignment.
			$consignment_request = Bring_Consignment_Request::create( $shipping_item );
			$consignment_request->fill(
				[
					'shipping_date_time' => self::get_shipping_date_time(),
					'customer_number'    => (string) filter_input( Fraktguiden_Helper::get_input_request_method(), '_bring-customer-number' ),
				]
			);

			$original_order_status = $wc_order->get_status();

			// Set order status to awaiting shipping.
			$wc_order->update_status( 'wc-bring-shipment' );

			// Send the booking.
			$response = $consignment_request->post();


			if ( ! in_array( $response->get_status_code(), [ 200, 201, 202, 203, 204 ], true ) ) {

				// @TODO: Error message
				// wp_send_json( json_decode('['.$response->get_status_code().','.$request_data['body'].','.$response->get_body().']',1) );die;
			}

			// Save the response json to the order.
			$adapter->update_booking_response( $response );

			// Download labels pdf.
			if ( $adapter->has_booking_errors() ) {
				// If there are errors, set the status back to the original status.
				$status      = $original_order_status;
				$status_note = __( 'Booking errors. See the Bring Booking box for details.', 'bring-fraktguiden-for-woocommerce' ) . PHP_EOL;
				$wc_order->update_status( $status, $status_note );

				continue;
			}

			// Download the labels.
			$consigments = Bring_Consignment::create_from_response( $response, $wc_order->get_id() );
			foreach ( $consigments as $consignment ) {
				$consignment->download_label();
			}

			// Create new status and order note.
			$status = Fraktguiden_Helper::get_option( 'auto_set_status_after_booking_success' );
			if ( 'none' === $status ) {
				// Set status back to the previous status.
				$status = $original_order_status;
			}

			$status_note = __( 'Booked with Bring', 'bring-fraktguiden-for-woocommerce' ) . PHP_EOL;

			// Update status.
			$wc_order->update_status( $status, $status_note );
		}
	}

	/**
	 * Create a shipping date
	 *
	 * @return array
	 */
	public static function create_shipping_date() {
		return array(
			'date'   => date_i18n( 'Y-m-d' ),
			'hour'   => date_i18n( 'H', strtotime( '+1 hour', current_time( 'timestamp' ) ) ),
			'minute' => date_i18n( 'i' ),
		);
	}

	/**
	 * Get a shipping date time
	 *
	 * @return array
	 */
	public static function get_shipping_date_time() {
		$input_request = Fraktguiden_Helper::get_input_request_method();

		$date         = filter_input( $input_request, '_bring-shipping-date' );
		$date_hour    = filter_input( $input_request, '_bring-shipping-date-hour' );
		$date_minutes = filter_input( $input_request, '_bring-shipping-date-minutes' );

		// Get the shipping date.
		if ( $date && $date_hour && $date_minutes ) {
			return $date . 'T' . $date_hour . ':' . $date_minutes . ':00';
		}

		$shipping_date = self::create_shipping_date();

		return $shipping_date['date'] . 'T' . $shipping_date['hour'] . ':' . $shipping_date['minute'] . ':00';
	}

	/**
	 * Bulk booking requests
	 *
	 * @param array $post_ids Array of WC_Order IDs.
	 */
	public static function bulk_send_booking( $post_ids ) {
		$report = [];
		foreach ( $post_ids as $post_id ) {
			$order = new Bring_WC_Order_Adapter( new WC_Order( $post_id ) );
			try {
				if ( ! $order->has_booking_consignments() ) {
					self::send_booking( $order->order );
				}
			} catch ( Exception $e ) {
				$report[ $post_id ] = [
					'status'  => 'error',
					'message' => $e->getMessage(),
					'url'     => get_edit_post_link( $post_id ),
				];
				continue;
			}
			$report[ $post_id ] = [
				'status'  => 'ok',
				'message' => '',
				'url'     => get_edit_post_link( $post_id, 'edit' ),
			];
		}

		return $report;
	}

	/**
	 * Check if the plugin works in a test mode
	 *
	 * @return boolean
	 */
	public static function is_test_mode() {
		return 'yes' === Fraktguiden_Helper::get_option( 'booking_test_mode_enabled' );
	}

	/**
	 * Get API UID
	 *
	 * @return bool|string
	 */
	public static function get_api_uid() {
		return Fraktguiden_Helper::get_option( 'mybring_api_uid' );
	}

	/**
	 * Get API key
	 *
	 * @return bool|string
	 */
	public static function get_api_key() {
		return Fraktguiden_Helper::get_option( 'mybring_api_key' );
	}
}
