<?php
/**
 * MU-dashboard-feedback-button
 *
 * Let you know what does your sub-blog admin thinks about the network
 *
 * @package   mu-dashboard-feedback-button
 * @author    jossemarGT <hello@jossemargt.com>
 * @license   GPL-2.0
 * @link      http://jossemargt.com
 * @copyright 2014-04-13 _
 *
 * @wordpress-plugin
 * Plugin Name: MU Dashboard Feedback Button
 * Plugin URI:  http://jossemargt.com
 * Description: Let you know what does your sub-blog admin thinks about the network
 * Version:     1.0.0
 * Author:      jossemarGT
 * Author URI:  http://jossemargt.com
 * Text Domain: mu-dashboard-feedback-button-locale
 * License:     GPL-2.0
 * License URI: http://www.gnu.org/licenses/gpl-2.0.txt
 * Domain Path: /lang
 * Network:     true
 */

// If this file is called directly, abort.
if (!defined("WPINC")) {
	die;
}

require_once(plugin_dir_path(__FILE__) . "MUDashboardFeedbackButton.php");

// Register hooks that are fired when the plugin is activated, deactivated, and uninstalled, respectively.
register_activation_hook(__FILE__, array("MUDashboardFeedbackButton", "activate"));
register_deactivation_hook(__FILE__, array("MUDashboardFeedbackButton", "deactivate"));

MUDashboardFeedbackButton::get_instance();
