<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Class WC_Gateway_Test
 *
 * Class responsible for displaying variation descriptions on the product page
 *
 * @class       WC_Variation_Description
 * @version     1.0.0
 * @author      Daniel Espinoza
 */

class WC_Gateway_Test extends WC_Payment_Gateway {

	/**
	 * Constructor
	 */
	public function __construct() {
		$this->id					= 'test';
		$this->method_title 		= __( 'Test Gateway', 'woocommerce-gateway-test' );
		$this->method_description   = __( 'A test credit card gateway that returns accepted with positive cc numbers and declined with negative.', 'woocommerce-gateway-test' );
		$this->has_fields 			= true;
		$this->supports 			= array(
 			'subscriptions',
			'products',
			'subscription_cancellation',
			'subscription_reactivation',
			'subscription_suspension',
			'subscription_amount_changes',
			'subscription_payment_method_change',
			'subscription_date_changes'
		);

		$this->init_form_fields();
		$this->init_settings();

		// Get setting values
		$this->title                 = $this->get_option( 'title' );
		$this->description           = $this->get_option( 'description' ); $this->settings['description'];
		$this->enabled               = $this->get_option( 'enabled' ); $this->settings['enabled'];
		$this->testmode              = true;
		$this->capture               = 'yes';

		if ( $this->testmode ) {
			$this->description .= ' ' .__( 'Use card 4242424242424242 for approval. Use card 4343434343434343 for decline.', 'woocommerce-gateway-test' );
			$this->description  = trim( $this->description );
		}

		// Hooks
		add_action( 'wp_enqueue_scripts', array( $this, 'payment_scripts' ) );
		add_action( 'woocommerce_update_options_payment_gateways_' . $this->id, array( $this, 'process_admin_options' ) );
	}

	/**
	 * Check if this gateway is enabled
	 *
	 * @return bool
	 */
	public function is_available() {
		if ( $this->enabled == "yes" ) {
			return true;
		}
		return false;
	}

	/**
	 * Initialise Gateway Settings Form Fields
	 */
	public function init_form_fields() {
		$this->form_fields = apply_filters( 'wc_gateway_test_settings', array(
			'enabled' => array(
				'title'       => __( 'Enable/Disable', 'woocommerce-gateway-test' ),
				'label'       => __( 'Enable Test Gateway', 'woocommerce-gateway-test' ),
				'type'        => 'checkbox',
				'description' => '',
				'default'     => 'no'
			),
			'title' => array(
				'title'       => __( 'Title', 'woocommerce-gateway-test' ),
				'type'        => 'text',
				'description' => __( 'This controls the title which the user sees during checkout.', 'woocommerce-gateway-test' ),
				'default'     => __( 'Credit card (Test)', 'woocommerce-gateway-test' )
			),
			'description' => array(
				'title'       => __( 'Description', 'woocommerce-gateway-test' ),
				'type'        => 'textarea',
				'description' => __( 'This controls the description which the user sees during checkout.', 'woocommerce-gateway-test' ),
				'default'     => __( 'This is a test gateway.', 'woocommerce-gateway-test')
			),
		) );
	}

	/**
	 * Payment form on checkout page
	 */
	public function payment_fields() {
		?>
		<fieldset>
			<div>
			<?php if ( $this->description ) : ?>
				<p><?php echo esc_html( $this->description ); ?></p>
			<?php endif; ?>

			<?php $this->credit_card_form( array( 'fields_have_names' => false ) ); ?>

			</div>
		</fieldset>
	<?php
	}


	/**
	 * Process the payment
	 */
	public function process_payment( $order_id ) {

		$order          = new WC_Order( $order_id );
		$card_number    = wc_clean( $_POST['cardnum'] );

		if ( '4242424242424242' == $card_number ) {

			// Payment complete
			$order->payment_complete();

			// Add order note
			$order->add_order_note( __( 'Test Credit Card Gateway Approval', 'woocommerce-gateway-test' ) );
			WC()->cart->empty_cart();

			// Return thank you page redirect
			return array(
				'result'   => 'success',
				'redirect' => $this->get_return_url( $order )
			);


		} else {
			// TODO maybe add a way to set order failed/on-hold based on cc number

			$order_note = __( 'Test payment gateway declined', 'woocommerce-gateway-test' );

			$order->add_order_note( $order_note );
			$order->update_status( 'failed', $order_note );

			wc_add_notice( __( 'Your payment has been declined.', 'woocommerce-gateway-test' ), 'error' );

		}

	}

}