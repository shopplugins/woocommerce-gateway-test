<?php
/*
 * Plugin Name: WooCommerce Gateway Test
 * Plugin URI: https://github.com/shopplugins/woocommerce-gateway-test
 * Description: A simple gateway to test transactions with WooCommerce
 * Version: 0.1.0
 * Author: Shop Plugins
 * Author URI: http://shopplugins.com
 * Text Domain: woocommerce-gateway-test
 *
 * License: GNU General Public License v3.0
 * License URI: http://www.gnu.org/licenses/gpl-3.0.html
 *
 * Copyright Shop Plugins (support@shopplugins.com)
 *
 *     This file is part of WooCommerce Gateway Test,
 *     a plugin for WordPress.
 *
 *     WooCommerce Gateway Test is free software:
 *     You can redistribute it and/or modify it under the terms of the
 *     GNU General Public License as published by the Free Software
 *     Foundation, either version 3 of the License, or (at your option)
 *     any later version.
 *
 *     WooCommerce Gateway Test is distributed in the hope that
 *     it will be useful, but WITHOUT ANY WARRANTY; without even the
 *     implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR
 *     PURPOSE. See the GNU General Public License for more details.
 *
 *     You should have received a copy of the GNU General Public License
 *     along with WordPress. If not, see <http://www.gnu.org/licenses/>.
 *
 * @package    WC-Gateway-Test
 * @author     Shop Plugins
 * @category   Enhancement
 * @copyright  Copyright (c) 2013-2014, Shop Plugins
 * @license    http://www.gnu.org/licenses/gpl-3.0.html GNU General Public License v3.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * WooCommerce Gateway Test Main Class
 *
 * @package  WooCommerce Gateway Test
 */

class WooCommerce_Gateway_Test {

	protected static $instance = null;

	/**
	 *  Constructor
	 */
	function __construct() {

		if ( class_exists( 'WooCommerce' ) ) {

			add_action( 'plugins_loaded', array( $this, 'init' ), 0 );

		} else {

			add_action( 'admin_notices', array( $this, 'woocommerce_missing_notice' ) );

		}

	}

	/**
	 * Init al the things
	 */
	public function init(){
		if ( ! class_exists( 'WC_Payment_Gateway' ) ) {
			return;
		}

		// Includes
		include_once( 'includes/class-wc-gateway-test.php' );

		// Localisation
		load_plugin_textdomain( 'woocommerce-gateway-test', false, dirname( plugin_basename( __FILE__ ) ) . '/languages' );

	}

	/**
	 * Start the Class when called
	 *
	 * @return WooCommerce_Gateway_Test
	 */
	public static function get_instance() {
		// If the single instance hasn't been set, set it now.
		if ( null == self::$instance ) {
			self::$instance = new self;
		}
		return self::$instance;
	}


	/**
	 * WooCommerce fallback notice.
	 *
	 * @return string
	 */
	public function woocommerce_missing_notice() {
		echo '<div class="error"><p>' . sprintf( __( 'WooCommerce Gateway Test requires %s to be installed and active.', 'woocommerce-gateway-test' ), '<a href="http://woocommerce.com/" target="_blank">' . __( 'WooCommerce', 'woocommerce-gateway-test' ) . '</a>' ) . '</p></div>';
	}


}


add_action( 'plugins_loaded', array( 'WooCommerce_Gateway_Test', 'get_instance' ) );