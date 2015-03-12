<?php
/**
* Plugin Name: WooCommerce ShareASale Merchant Tracking
* Plugin URI: http://www.wpcube.co.uk/plugins/woocommerce-shareasale-merchant-tracking
* Version: 1.0.4
* Author: WP Cube
* Author URI: http://www.wpcube.co.uk
* Description: Adds ShareASale Merchant Tracking code to WooCommerce.
* License: GPL2
*/

/*  Copyright 2015 WP Cube (email : support@wpcube.co.uk)

    This program is free software; you can redistribute it and/or modify
    it under the terms of the GNU General Public License, version 2, as 
    published by the Free Software Foundation.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with this program; if not, write to the Free Software
    Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
*/

/**
* WooCommerce ShareASale Merchant Tracking Class
* 
* @package WP Cube
* @subpackage WooCommerce ShareASale Merchant Tracking
* @author Tim Carr
* @version 1.0.4
* @copyright WP Cube
*/
class WCShareASaleMerchantTracking {

    /**
    * Constructor.
    */
    function __construct() {

        // Plugin Details
        $this->plugin               = new stdClass;
        $this->plugin->name         = 'woocommerce-shareasale-merchant-tracking';
        $this->plugin->displayName  = 'WC ShareASale - Merchant';
        $this->plugin->version      = '1.0.4';
        $this->plugin->folder       = plugin_dir_path( __FILE__ );
        $this->plugin->url          = WP_PLUGIN_URL .'/'. str_replace( basename( __FILE__), "", plugin_basename(__FILE__) );
        
        // Dashboard Submodule
        if ( ! class_exists( 'WPCubeDashboardWidget' ) ) {
            require_once( $this->plugin->folder . '/_modules/dashboard/dashboard.php' );
        }
        $dashboard = new WPCubeDashboardWidget( $this->plugin ); 
        
        // Hooks
        add_action( 'admin_menu', array( &$this, 'admin_menu' ) );
        add_action( 'admin_notices', array( &$this, 'admin_notices') );
        add_action( 'woocommerce_thankyou', array( &$this, 'tracking_code' ) );
        add_action( 'plugins_loaded', array( &$this, 'load_language_files' ) );

    }
    
    /**
    * Register the plugin settings panel
    *
    * @since 1.0.0
    */
    function admin_menu() {

        add_menu_page( $this->plugin->displayName, $this->plugin->displayName, 'manage_options', $this->plugin->name, array( &$this, 'admin_screen' ), 'dashicons-cart' );

    }
    
    /**
    * Outputs Administration Notices if no merchant ID specified
    *
    * @since 1.0.0
    */
    function admin_notices() {
        
        // Don't display on settings screen
        if ( isset( $_GET['page'] ) && $_GET['page'] == $this->plugin->name ) {
            return;
        }
        
        // Check settings for merchant ID
        $settings = get_option( $this->plugin->name );
        if ( ! isset( $settings['merchantID'] ) || empty( $settings['merchantID'] ) ) {
            echo ( '<div class="error"><p>' . __( 'WooCommerce ShareASale Merchant Tracking requires your merchant ID before it can start tracking sales. <a href="admin.php?page=' . $this->plugin->name . '" class="button">Do this now</a>', $this->plugin->name ) . '</p></div>' );
        }
        
    }

    /**
    * Output the Administration Screens
    * Save POSTed data from the Administration Panel into a WordPress option
    *
    * @since 1.0.0
    */
    function admin_screen() {

        // Save Settings
        if ( isset( $_POST['submit'] ) ) {
            // Check nonce
            if ( ! isset( $_POST[ $this->plugin->name . '_nonce' ] ) ) {
                // Missing nonce    
                $this->errorMessage = __( 'nonce field is missing. Settings NOT saved.', $this->plugin->name );
            } elseif ( ! wp_verify_nonce( $_POST[ $this->plugin->name . '_nonce' ], $this->plugin->name ) ) {
                // Invalid nonce
                $this->errorMessage = __( 'Invalid nonce specified. Settings NOT saved.', $this->plugin->name );
            } else {            
                if ( isset( $_POST[ $this->plugin->name ] ) ) {
                    update_option( $this->plugin->name, $_POST[ $this->plugin->name ] );
                    $this->message = __( 'Settings Updated.', $this->plugin->name );
                }
            }
        }
        
        // Get latest settings
        $this->settings = get_option( $this->plugin->name );
        
        // Load Settings Form
        include_once( $this->plugin->folder . 'views/settings.php' ); 
    }
    
    /**
    * Loads plugin textdomain
    */
    function load_language_files() {

        load_plugin_textdomain( $this->plugin->name, false, $this->plugin->name . '/languages/' );
        
    }

    /**
    * Adds ShareASale tracking code to the WooCommerce thank you page
    *
    * @since 1.0.0
    *
    * @param int $orderID Order ID
    */
    function tracking_code( $orderID ) {
    	
        $order = new WC_Order($orderID);
    	
        // Check if a merchant ID has been set
    	$settings = get_option( $this->plugin->name );
    	if ( ! isset( $order ) || is_wp_error( $order ) ) {
    		return;
    	}
    	if ( ! isset( $settings['merchantID'] ) ) {
    		return;
    	}
    	
    	// Calculate total, excluding taxes
    	$total = 0;
    	$items = $order->get_items();
    	foreach ( $items as $item ) {
	    	$total += $item['line_total'];
    	}
    	
    	// Deduct discounts
    	$discounts = $order->get_order_discount();
    	
    	// Final Total (ex. tax + after discounts)
    	$total = $total - $discounts;
    	
    	$tracked = get_post_meta( $orderID, $this->plugin->name, true );
    	if ( empty( $tracked ) ) {
    		echo ( '<img src="https://shareasale.com/sale.cfm?amount=' . $total . '&tracking=' . $orderID . '&transtype=sale&merchantID=' . $settings['merchantID'] . '" width="1" height="1">' );
			update_post_meta( $orderID, $this->plugin->name, 1 );
		}

    }

}

$wcSASMT = new WCShareASaleMerchantTracking();