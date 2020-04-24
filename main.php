<?php

/**
 * Plugin Name:       BBPress Notifications
 * Plugin URI:        https://pennerwebdesign.com
 * Description:       Better and simple notifications system for BBPress
 * Version:           1.1.0
 * Author:            Daniel Griffiths
 * Author URI:        https://pennerwebdesign.com
 * License:           GPL v2 or later
 * License URI:       https://www.gnu.org/licenses/gpl-2.0.html
 */

require_once(plugin_dir_path(__FILE__) . 'BBPNActionHandler.php');
require_once(plugin_dir_path(__FILE__) . 'BBPNotifications.php');

BBPNotifications::instance();
