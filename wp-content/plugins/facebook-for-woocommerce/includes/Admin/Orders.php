<?php
/**
 * Copyright (c) Facebook, Inc. and its affiliates. All Rights Reserved
 *
 * This source code is licensed under the license found in the
 * LICENSE file in the root directory of this source tree.
 *
 * @package FacebookCommerce
 */

namespace SkyVerge\WooCommerce\Facebook\Admin;

defined( 'ABSPATH' ) or exit;

use SkyVerge\WooCommerce\Facebook\AJAX;
use SkyVerge\WooCommerce\Facebook\Commerce;
use SkyVerge\WooCommerce\Facebook\Utilities\Shipment;
use SkyVerge\WooCommerce\PluginFramework\v5_5_4 as Framework;

/**
 * General handler for order admin functionality.
 *
 * @since 2.1.0
 */
class Orders {


	/** @var string key used for setting a transient in the event of bulk actions fired on Commerce orders */
	private $bulk_order_update_transient = 'wc_facebook_bulk_order_update';


	/**
	 * Handler constructor.
	 *
	 * @since 2.1.0
	 */
	public function __construct() {

		$this->add_hooks();
	}


	/**
	 * Adds the necessary action & filter hooks.
	 *
	 * @since 2.1.0
	 */
	public function add_hooks() {

		add_action( 'admin_enqueue_scripts', [ $this, 'enqueue_assets' ] );

		add_action( 'admin_notices', [ $this, 'add_notices' ] );

		add_filter( 'handle_bulk_actions-edit-shop_order', [ $this, 'handle_bulk_update' ], -1, 3 );

		add_filter( 'woocommerce_admin_order_actions',         [ $this, 'remove_list_table_actions' ], 10, 2 );
		add_filter( 'woocommerce_admin_order_preview_actions', [ $this, 'remove_order_preview_actions' ], 10, 2 );

		add_filter( 'wc_order_is_editable', [ $this, 'is_order_editable' ], 10, 2 );

		add_action( 'admin_footer', [ $this, 'render_refund_reason_field' ] );

		add_action( 'woocommerce_refund_created', [ $this, 'handle_refund' ] );

		add_action( 'add_meta_boxes', [ $this, 'maybe_remove_order_metaboxes' ], 999 );
	}


	/**
	 * Enqueue the assets.
	 *
	 * @internal
	 *
	 * @since 2.1.0
	 */
	public function enqueue_assets() {
		global $post;

		if ( ! $this->is_edit_order_screen() ) {
			return;
		}

		$order = wc_get_order( $post );

		if ( ! $order instanceof \WC_Order ) {
			return;
		}

		wp_enqueue_script( 'wc-facebook-commerce-orders', facebook_for_woocommerce()->get_plugin_url() . '/assets/js/admin/orders.min.js', [
			'jquery',
			'wc-backbone-modal',
			'facebook-for-woocommerce-modal',
		], \WC_Facebookcommerce::VERSION );

		$shipment_utilities = new Shipment();
		$shipment_tracking  = array_filter( (array) $order->get_meta( '_wc_shipment_tracking_items', true ) );

		if ( ! empty( $shipment_tracking ) ) {

			$shipment_tracking = array_map( function ( $shipment ) use ( $shipment_utilities ) {

				$shipment['carrier_code'] = $shipment_utilities->convert_shipment_tracking_carrier_code( $shipment['tracking_provider'] );

				return $shipment;

			}, $shipment_tracking );
		}

		// limit the order status field to statuses that can be handled by Facebook
		switch ( $order->get_status() ) {

			case 'processing':
				$allowed_statuses = [ 'wc-processing', 'wc-completed', 'wc-cancelled' ];
			break;

			case 'completed':
				$allowed_statuses = [ 'wc-completed', 'wc-refunded' ];
			break;

			case 'refunded':
				$allowed_statuses = [ 'wc-refunded' ];
			break;

			case 'cancelled':
				$allowed_statuses = [ 'wc-cancelled' ];
			break;

			default:
				$allowed_statuses = [ 'wc-pending' ];
			break;
		}

		wp_localize_script( 'wc-facebook-commerce-orders', 'wc_facebook_commerce_orders', [
			'order_id'                  => $order->get_id(),
			'order_status'              => $order->get_status(),
			'is_commerce_order'         => Commerce\Orders::is_commerce_order( $order ),
			'shipment_tracking'         => $shipment_tracking,
			'allowed_commerce_statuses' => $allowed_statuses,
			'complete_order_action'     => AJAX::ACTION_COMPLETE_ORDER,
			'complete_order_nonce'      => wp_create_nonce( AJAX::ACTION_COMPLETE_ORDER ),
			'cancel_order_action'       => AJAX::ACTION_CANCEL_ORDER,
			'cancel_order_nonce'        => wp_create_nonce( AJAX::ACTION_CANCEL_ORDER ),
			'complete_modal_message'    => $this->get_complete_modal_message(),
			'complete_modal_buttons'    => $this->get_complete_modal_buttons(),
			'refund_modal_message'      => $this->get_refund_modal_message(),
			'refund_modal_buttons'      => $this->get_refund_modal_buttons(),
			'cancel_modal_message'      => $this->get_cancel_modal_message(),
			'cancel_modal_buttons'      => $this->get_cancel_modal_buttons(),
			'i18n' => [
				'unknown_error'                 => __( 'An unknown error occurred.', 'facebook-for-woocommerce' ),
				'missing_tracking_number_error' => __( 'Tracking Number is missing.', 'facebook-for-woocommerce' ),
				'refund_reason_label'           => __( 'Refund reason:', 'facebook-for-woocommerce' ),
				'refund_reason_tooltip'         => __( 'Choose the reason for refunding this order.', 'facebook-for-woocommerce' ),
				'refund_description_label'      => __( 'Refund description (optional):', 'facebook-for-woocommerce' ),
				'refund_description_tooltip'    => __( 'Note: the refund description will be visible by the customer.', 'facebook-for-woocommerce' ),
			],
		] );
	}


	/**
	 * Adds admin notices.
	 *
	 * @internal
	 *
	 * @since 2.1.0
	 */
	public function add_notices() {
		global $post;

		if ( ! $this->is_edit_order_screen() ) {
			return;
		}

		$order  = wc_get_order( $post );
		$plugin = facebook_for_woocommerce();

		if ( Commerce\Orders::is_order_pending( $order ) ) {

			$message = sprintf(
				/* translators: Placeholders: %1$s - HTML <strong> tag, %2$s - HTML </strong> tag */
				__( 'This order is currently being held by Instagram and cannot be edited. Once released by Instagram, it will move to %1$sProcessing%2$s or %1$sCancelled%2$s status.', 'facebook-for-woocommerce' ),
				'<strong>', '</strong>'
			);

			$plugin->get_admin_notice_handler()->add_admin_notice( $message, $plugin::PLUGIN_ID . '_commerce_order_pending_' . $order->get_id(), [
				'dismissible'  => true,
				'notice_class' => 'notice-info',
			] );
		}

		$commerce_orders = get_transient( $this->bulk_order_update_transient );

		if ( ! empty( $commerce_orders ) ) {

			// if there were orders managed by Instagram updated in bulk, we need to warn the merchant that it wasn't updated
			facebook_for_woocommerce()->get_message_handler()->add_error( sprintf(
				_n(
					/* translators: Placeholder: %s - order ID */
					'Heads up! Instagram order statuses can’t be updated in bulk. Please update Instagram order %s so you can provide order details required by Instagram.',
					/* translators: Placeholder: %s - order IDs list */
					'Heads up! Instagram order statuses can’t be updated in bulk. Please update Instagram orders %s individually so you can provide order details required by Instagram.',
					count( $commerce_orders ),
					'facebook-for-woocommerce'
				),
				implode( ', ', $commerce_orders )
			) );

			delete_transient( $this->bulk_order_update_transient );

			facebook_for_woocommerce()->get_message_handler()->show_messages();
		}
	}


	/**
	 * Removes order metaboxes if the order is a Commerce pending order.
	 *
	 * @internal
	 *
	 * @since 2.1.0
	 */
	public function maybe_remove_order_metaboxes() {
		global $post;

		if ( ! $post instanceof \WP_Post || ! $this->is_edit_order_screen() ) {
			return;
		}

		$order = wc_get_order( $post );

		if ( ! $order || ! $order->has_status( 'pending' ) || ! Commerce\Orders::is_commerce_order( $order ) ) {
			return;
		}

		remove_meta_box( 'woocommerce-order-actions', get_current_screen(), 'side' );
	}


	/**
	 * Gets the markup for the buttons used in a modal.
	 *
	 * @since 2.1.0
	 *
	 * @param string $submit_label label for the submit button
	 * @return string
	 */
	private function get_modal_buttons( $submit_label ) {

		ob_start();

		?>
		<button
			id="btn-ok"
			class="button button-large button-primary"
		><?php esc_html_e( $submit_label ); ?></button>
		<button
			class="wc-facebook-modal-cancel-button button button-large"
			onclick="jQuery( '.modal-close' ).trigger( 'click' )"
		><?php esc_html_e( 'Cancel', 'facebook-for-woocommerce' ); ?></button>
		<?php

		return ob_get_clean();
	}


	/**
	 * Gets the markup for the message used in the Complete modal.
	 *
	 * @since 2.1.0
	 *
	 * @return string
	 */
	private function get_complete_modal_message() {

		ob_start();

		$shipment_utilities = new Shipment();

		echo '<div class="woocommerce_options_panel">',
		'<p>', esc_html__( 'Select the carrier and tracking number for this order:', 'facebook-for-woocommerce' ), '</p>';

		woocommerce_wp_select( [
			'id'      => 'wc_facebook_carrier',
			'label'   => __( 'Carrier', 'facebook-for-woocommerce' ),
			'options' => $shipment_utilities->get_carrier_options(),
		] );

		woocommerce_wp_text_input( [
			'id'    => 'wc_facebook_tracking_number',
			'label' => __( 'Tracking number', 'facebook-for-woocommerce' ),
		] );

		echo '</div>';

		return ob_get_clean();
	}


	/**
	 * Gets the markup for the buttons used in the Complete modal.
	 *
	 * @since 2.1.0
	 *
	 * @return string
	 */
	private function get_complete_modal_buttons() {

		return $this->get_modal_buttons( __( 'Submit order', 'facebook-for-woocommerce' ) );
	}


	/**
	 * Gets the markup for the message used in the Refund modal.
	 *
	 * @since 2.1.0
	 *
	 * @return string
	 */
	private function get_refund_modal_message() {

		ob_start();

		?>
		<p><?php esc_html_e( 'Select a reason for refunding this order:', 'facebook-for-woocommerce' ); ?></p>
		<?php

		$this->render_refund_reason_field( 'wc_facebook_refund_reason_modal', false );

		return ob_get_clean();
	}


	/**
	 * Gets the markup for the buttons used in the Refund modal.
	 *
	 * @since 2.1.0
	 *
	 * @return string
	 */
	private function get_refund_modal_buttons() {

		return $this->get_modal_buttons( __( 'Submit refund', 'facebook-for-woocommerce' ) );
	}


	/**
	 * Gets the markup for the message used in the Cancel modal.
	 *
	 * @since 2.1.0
	 *
	 * @return string
	 */
	private function get_cancel_modal_message() {

		ob_start();

		?>
		<p><?php esc_html_e( 'Select a reason for cancelling this order:', 'facebook-for-woocommerce' ); ?></p>
		<?php

		woocommerce_wp_select( [
			'id'      => 'wc_facebook_cancel_reason',
			'label'   => '',
			'options' => facebook_for_woocommerce()->get_commerce_handler()->get_orders_handler()->get_cancellation_reasons(),
		] );

		return ob_get_clean();
	}


	/**
	 * Gets the markup for the buttons used in the Cancel modal.
	 *
	 * @since 2.1.0
	 *
	 * @return string
	 */
	private function get_cancel_modal_buttons() {

		return $this->get_modal_buttons( __( 'Submit cancellation', 'facebook-for-woocommerce' ) );
	}


	/**
	 * Renders the refund reason field.
	 *
	 * @internal
	 *
	 * @since 2.1.0
	 */
	public function render_refund_reason_field( $select_id = '', $hidden = true ) {

		if ( ! $this->is_edit_order_screen() ) {
			return;
		}

		?>
		<select id="<?php echo esc_attr( $select_id ?: 'wc_facebook_refund_reason' ); ?>" <?php echo $hidden ? 'style="display: none;"' : ''; ?>>
			<option value="<?php echo esc_attr( Commerce\Orders::REFUND_REASON_BUYERS_REMORSE ); ?>"><?php esc_html_e( 'Customer request', 'facebook-for-woocommerce' ); ?></option>
			<option value="<?php echo esc_attr( Commerce\Orders::REFUND_REASON_DAMAGED_GOODS ); ?>"><?php esc_html_e( 'Damaged product', 'facebook-for-woocommerce' ); ?></option>
			<option value="<?php echo esc_attr( Commerce\Orders::REFUND_REASON_NOT_AS_DESCRIBED ); ?>"><?php esc_html_e( 'Product not as described', 'facebook-for-woocommerce' ); ?></option>
			<option value="<?php echo esc_attr( Commerce\Orders::REFUND_REASON_QUALITY_ISSUE ); ?>"><?php esc_html_e( 'Quality issue', 'facebook-for-woocommerce' ); ?></option>
			<option value="<?php echo esc_attr( Commerce\Orders::REFUND_REASON_WRONG_ITEM ); ?>"><?php esc_html_e( 'Wrong item', 'facebook-for-woocommerce' ); ?></option>
			<option value="<?php echo esc_attr( Commerce\Orders::REFUND_REASON_OTHER ); ?>"><?php esc_html_e( 'Other', 'facebook-for-woocommerce' ); ?></option>
		</select>
		<?php
	}


	/**
	 * Sends a refund request to the Commerce API when a WC refund is created.
	 *
	 * @internal
	 *
	 * @since 2.1.0
	 *
	 * @param int $refund_id refund ID
	 * @throws Framework\SV_WC_Plugin_Exception
	 */
	public function handle_refund( $refund_id ) {

		$order_refund = wc_get_order( $refund_id );

		if ( $order_refund instanceof \WC_Order_Refund ) {

			$reason_code = isset( $_POST['wc_facebook_refund_reason'] ) ? $_POST['wc_facebook_refund_reason'] : null;

			facebook_for_woocommerce()->get_commerce_handler()->get_orders_handler()->add_order_refund( $order_refund, $reason_code );
		}
	}


	/**
	 * Sets a transient to display a notice regarding bulk updates for Commerce orders' statuses.
	 *
	 * @internal
	 *
	 * @since 2.1.0
	 *
	 * @param string $redirect_url redirect URL carrying results
	 * @param string $action bulk action
	 * @param int[] $order_ids IDs of orders affected by the bulk action
	 * @return string
	 */
	public function handle_bulk_update( $redirect_url, $action, $order_ids ) {

		// listen for order status change actions
		if ( empty( $action ) || empty( $order_ids ) || ! Framework\SV_WC_Helper::str_starts_with( $action, 'mark_' ) ) {
			return $redirect_url;
		}

		$commerce_orders = [];

		foreach ( $order_ids as $index => $order_id ) {

			$order = wc_get_order( $order_id );

			if ( ! $order ) {
				continue;
			}

			if ( Commerce\Orders::is_commerce_order( $order ) ) {

				unset( $order_ids[ $index ] );

				$commerce_orders[] = $order->get_order_number();
			}
		}

		if ( ! empty( $commerce_orders ) ) {

			// set the orders that are not going to be updated in the transient for reference
			set_transient( $this->bulk_order_update_transient, $commerce_orders, MINUTE_IN_SECONDS );

			// this will prevent WooCommerce to keep processing the orders we don't want to be changed in bulk
			add_filter( 'woocommerce_bulk_action_ids', static function( $ids ) use ( $order_ids ) {
				return $order_ids;
			} );

			// finally, parse the URL (main filter callback param)
			if ( empty( $order_ids ) ) {

				$redirect_url = admin_url( 'edit.php?post_type=shop_order' );

			} else {

				$redirect_url = add_query_arg(
					[
						'post_type'   => 'shop_order',
						'bulk_action' => $action,
						'changed'     => count( $order_ids ),
						'ids'         => implode( ',', $order_ids ),
					],
					$redirect_url
				);
			}
		}

		return $redirect_url;
	}


	/**
	 * Removes the status actions from the order list table rows.
	 *
	 * @internal
	 *
	 * @since 2.1.0
	 *
	 * @param array $actions existing actions
	 * @param \WC_Order $order order object
	 * @return array
	 */
	public function remove_list_table_actions( $actions, $order ) {

		if ( $order instanceof \WC_Order && Commerce\Orders::is_commerce_order( $order ) ) {
			unset( $actions['processing'], $actions['complete'] );
		}

		return $actions;
	}


	/**
	 * Removes the status actions from the list table order preview modal.
	 *
	 * @internal
	 *
	 * @since 2.1.0
	 *
	 * @param array $actions existing actions
	 * @param \WC_Order $order order object
	 * @return array
	 */
	public function remove_order_preview_actions( $actions, $order ) {

		if ( $order instanceof \WC_Order && Commerce\Orders::is_commerce_order( $order ) ) {
			unset( $actions['status'] );
		}

		return $actions;
	}


	/**
	 * Prevents sending emails for Commerce orders.
	 *
	 * @internal
	 *
	 * @since 2.1.0
	 *
	 * @param bool $is_enabled whether the email is enabled in the first place
	 * @param \WC_Order $order order object
	 * @return bool
	 */
	public function maybe_stop_order_email( $is_enabled, $order ) {

		// will decide whether to allow $is_enabled to be filtered
		$is_previously_enabled = $is_enabled;

		// checks whether or not the order is a Commerce order
		$is_commerce_order = $order instanceof \WC_Order && \SkyVerge\WooCommerce\Facebook\Commerce\Orders::is_commerce_order( $order );

		// decides whether to disable or to keep emails enabled
		$is_enabled = $is_enabled && ! $is_commerce_order;

		if ( $is_previously_enabled && $is_commerce_order ) {

			/**
			 * Filters the flag used to determine whether the email is enabled.
			 *
			 * @param bool $is_enabled whether the email is enabled
			 * @param \WC_Order $order order object
			 * @param Orders $this admin orders instance
			 * @since 2.1.0
			 *
			 */
			$is_enabled = (bool) apply_filters( 'wc_facebook_commerce_send_woocommerce_emails', $is_enabled, $order, $this );
		}

		return $is_enabled;
	}


	/**
	 * Determines whether or not the order is editable.
	 *
	 * @internal
	 *
	 * @since 2.1.0
	 *
	 * @param bool $maybe_editable whether the order is editable in the first place
	 * @param \WC_Order $order order object
	 * @return bool
	 */
	public function is_order_editable( $maybe_editable, $order ) {

		// if the order is a WC_Order, determines whether it is pending or not
		$is_order_pending = $order instanceof \WC_Order && Commerce\Orders::is_order_pending( $order );

		return $maybe_editable && ! $is_order_pending;
	}


	/**
	 * Determines whether or not the current screen is an orders screen.
	 *
	 * @internal
	 *
	 * @since 2.1.0
	 *
	 * @return bool
	 */
	public function is_orders_screen() {

		return Framework\SV_WC_Helper::is_current_screen( 'edit-shop_order' ) ||
		       Framework\SV_WC_Helper::is_current_screen( 'shop_order' );
	}


	/**
	 * Determines whether or not the current screen is an order edit screen.
	 *
	 * @internal
	 *
	 * @since 2.1.0
	 *
	 * @return bool
	 */
	public function is_edit_order_screen() {

		return Framework\SV_WC_Helper::is_current_screen( 'shop_order' );
	}


}
