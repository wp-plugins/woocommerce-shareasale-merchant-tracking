<?php
/**
* Plugin Name: WooCommerce ShareASale Merchant Tracking
* Plugin URI: http://www.wpcube.co.uk/plugins/woocommerce-shareasale-merchant-tracking
* Version: 1.0.1
* Author: WP Cube
* Author URI: http://www.wpcube.co.uk
* Description: Adds ShareASale Merchant Tracking code to WooCommerce.
* License: GPL2
*/

/*  Copyright 2013 WP Cube (email : support@wpcube.co.uk)

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
* @version 1.0.1
* @copyright WP Cube
*/
class WCShareASaleMerchantTracking {
    /**
    * Constructor.
    */
    function WCShareASaleMerchantTracking() {
        // Plugin Details
        $this->plugin = new stdClass;
        $this->plugin->name = 'woocommerce-shareasale-merchant-tracking'; // Plugin Folder
        $this->plugin->displayName = 'WC ShareASale - Merchant'; // Plugin Name
        $this->plugin->version = '1.0.1';
        $this->plugin->folder = WP_PLUGIN_DIR.'/'.$this->plugin->name; // Full Path to Plugin Folder
        $this->plugin->url = WP_PLUGIN_URL.'/'.str_replace(basename( __FILE__),"",plugin_basename(__FILE__));
        
        // Dashboard Submodule
        if (!class_exists('WPCubeDashboardWidget')) {
			require_once($this->plugin->folder.'/_modules/dashboard/dashboard.php');
		}
		$dashboard = new WPCubeDashboardWidget($this->plugin); 
		
		// Hooks
        add_action('admin_enqueue_scripts', array(&$this, 'adminScriptsAndCSS'));
        add_action('admin_menu', array(&$this, 'adminPanelsAndMetaBoxes'));
        add_action('admin_notices', array(&$this, 'adminNotices'));
        add_action('plugins_loaded', array(&$this, 'loadLanguageFiles'));
        add_action('woocommerce_thankyou', array(&$this, 'frontendTrackingCode'));
    }
    
    /**
    * Register and enqueue any JS and CSS for the WordPress Administration
    */
    function adminScriptsAndCSS() {
    	// CSS
        wp_enqueue_style($this->plugin->name.'-admin', $this->plugin->url.'css/admin.css', array(), $this->plugin->version); 
    }
    
    /**
    * Register the plugin settings panel
    */
    function adminPanelsAndMetaBoxes() {
        add_menu_page($this->plugin->displayName, $this->plugin->displayName, 'manage_options', $this->plugin->name, array(&$this, 'adminPanel'), $this->plugin->url.'images/icons/small.png');
    }
    
    /**
    * Outputs Administration Notices if no merchant ID specified
    */
    function adminNotices() {
    	// Don't display on settings screen
    	if (isset($_GET['page']) AND $_GET['page'] == $this->plugin->name) return;
    	
    	// Check settings for merchant ID
		$settings = get_option($this->plugin->name);
		if (!isset($settings['merchantID']) OR empty($settings['merchantID'])) {
			echo ('<div class="error"><p>'.__('WooCommerce ShareASale Merchant Tracking requires your merchant ID before it can start tracking sales. <a href="admin.php?page='.$this->plugin->name.'" class="button">Do this now</a>', $this->plugin->name).'</p></div>');
    	}
    }
    
	/**
    * Output the Administration Panel
    * Save POSTed data from the Administration Panel into a WordPress option
    */
    function adminPanel() {
        // Save Settings
        if (isset($_POST['submit'])) {
        	if (isset($_POST[$this->plugin->name])) {
        		update_option($this->plugin->name, $_POST[$this->plugin->name]);
				$this->message = __('Settings Updated.', $this->plugin->name);
			}
        }
        
        // Get latest settings
        $this->settings = get_option($this->plugin->name);
        
		// Load Settings Form
        include_once(WP_PLUGIN_DIR.'/'.$this->plugin->name.'/views/settings.php');  
    }
    
    /**
	* Loads plugin textdomain
	*/
	function loadLanguageFiles() {
		load_plugin_textdomain($this->plugin->name, false, $this->plugin->name.'/languages/');
	}
    
    /**
    * Adds ShareASale tracking code to the WooCommerce thank you page
    *
    * @param int $orderID Order ID
    */
    function frontendTrackingCode($orderID) {
    	$order = new WC_Order($orderID);
    	$settings = get_option($this->plugin->name);
    	if (!isset($order) OR is_wp_error($order)) return;
    	if (!isset($settings['merchantID'])) return;
    	echo ('<img src="https://shareasale.com/sale.cfm?amount='.$order->get_order_total().'&tracking='.$orderID.'&transtype=sale&merchantID='.$settings['merchantID'].'" width="1" height="1">');
    }
}
$wcSASMT = new WCShareASaleMerchantTracking();
?>
