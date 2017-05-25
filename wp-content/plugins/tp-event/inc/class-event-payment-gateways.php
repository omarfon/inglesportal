<?php

defined( 'ABSPATH' ) || exit();

class TP_Event_Payment_Gateways {

	/**
	 * gateways method
	 * @var type array
	 */
	public $gateways = array();
	public static $instance = null;

	public function __construct() {
		$this->init();
	}

	public function init() {
		$payment_gateways = array( 'TP_Event_Payment_Gateway_Paypal' );

		foreach ( $payment_gateways as $gateway ) {
			$gateway                      = is_string( $gateway ) ? new $gateway : $gateway;
			$this->gateways[$gateway->id] = $gateway;
		}

		return $this->gateways;
	}

	/**
	 * Get payment gateways available
	 *
	 * @return array
	 */
	public function get_payment_gateways() {
		$gateways = $this->gateways;

		$gateways = apply_filters( 'tp_event_payment_gateways', $gateways );

		$available = array();
		foreach ( $gateways as $id => $gateway ) {
			if ( $gateway->is_available() ) {
				$available[$id] = $gateway;
			}
		}
		return $available;
	}

	/**
	 * Get payment gateways enable
	 *
	 * @return array
	 */
	public function get_payment_gateways_enable() {
		$gateways = $this->get_payment_gateways();

		$enable = array();
		foreach ( $gateways as $id => $gateway ) {
			if ( $gateway->is_enable() ) {
				$enable[$id] = $gateway;
			}
		}
		return $enable;
	}

	public static function instance() {
		if ( !self::$instance ) {
			self::$instance = new self();
		}
		return self::$instance;
	}

}

TP_Event_Payment_Gateways::instance();