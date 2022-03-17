<?php
/**
 * This file is part of Bring Fraktguiden for WooCommerce.
 *
 * @package Bring_Fraktguiden
 */

namespace Bring_Fraktguiden_Pro\Booking\Actions;

use Bring_Booking_Consignment_Request;
use Fraktguiden_Helper;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * Bring_Booking_Customer class
 */
class Get_Booking_Data_Action {
	public function __invoke( \Bring_WC_Order_Adapter $order ): array {
		$packages = [];
		foreach ( $order->get_fraktguiden_shipping_items() as $shipping_method ) {
			// 1. Create Booking Consignment
			$consignment = new Bring_Booking_Consignment_Request( $shipping_method );

			// 2. Get packages from that consignment
			foreach ( $consignment->create_packages( true ) as $package ) {
				$key = $package['shipping_item_info']['shipping_method']['service'];
				ray( $key, Fraktguiden_Helper::get_service_data_for_key( $key ) );
				$packages[] = [
					'id'          => $package['shipping_item_info']['item_id'],
					'key'         => $key,
					'serviceData' => Fraktguiden_Helper::get_service_data_for_key( $key ),
					'pickupPoint' => $package['shipping_item_info']['shipping_method']['pickup_point_id'],
					'dimensions'  => $package['dimensions'],
					'weightInKg'  => $package['weightInKg'],
				];
			}
		}

		return [
			'orderId'      => $order->order->get_id(),
			'orderItemIds' => array_keys( $order->get_fraktguiden_shipping_items() ),
			'services'     => array_values( Fraktguiden_Helper::get_all_services() ),
			'vas'          => $this->get_vas_settings(),
			'packages'     => $packages,
			'i18n'         => [
				'tip'                                 => __( 'Shipping item id', 'bring-fraktguiden-for-woocommerce' ),
				'orderID'                             => __( 'Order ID', 'bring-fraktguiden-for-woocommerce' ),
				'product'                             => __( 'Product', 'bring-fraktguiden-for-woocommerce' ),
				'width'                               => __( 'Width', 'bring-fraktguiden-for-woocommerce' ) . '(cm)',
				'height'                              => __( 'Height', 'bring-fraktguiden-for-woocommerce' ) . '(cm)',
				'length'                              => __( 'Length', 'bring-fraktguiden-for-woocommerce' ) . '(cm)',
				'weight'                              => __( 'Weight', 'bring-fraktguiden-for-woocommerce' ) . '(kg)',
				'pickupPoint'                         => __( 'Pickup point', 'bring-fraktguiden-for-woocommerce' ),
				'delete'                              => __( 'Delete', 'bring-fraktguiden-for-woocommerce' ),
				'add'                                 => __( 'Add', 'bring-fraktguiden-for-woocommerce' ),
				'bag_on_door'                         => esc_html__( 'Bag on door (mailbox)' ),
				'bag_on_door_description'             => esc_html__( 'Mailbox Parcel (Pakke i postkassen) is a parcel that will be delivered in the recipient’s mailbox. If the parcel for various reasons does not fit in the mailbox, the sender may, against a surcharge, choose to leave the parcel on the door handle (in a special bag) to avoid it being sent to the pickup point. It’s recommended that this delivery option is actively confirmed by the receiver upon booking in the sender’s webshop. When the parcel is delivered as a bag on the door, the bar code is scanned and the recipient will receive an SMS/email. Note that if the parcel is delivered in the mailbox the additional fee will not occur.' ),
				'id_verification'                     => esc_html__( 'ID verification' ),
				'id_verification_description'         => esc_html__( 'ID is checked upon delivery. Any person (other than the recipient) can receive the shipment, but must legitimize before receiving it.' ),
				'individual_verification'             => esc_html__( 'Individual verification' ),
				'individual_verification_description' => esc_html__( 'Only the specified recipient can receive the shipment by showing identification. Use of authorization is not possible.' ),
			]
		];
	}

	public function get_vas_settings() {
		return [
			
		];
	}
}
