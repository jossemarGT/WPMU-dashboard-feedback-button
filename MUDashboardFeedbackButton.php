<?php
/**
 * MU-dashboard-feedback-button
 *
 * @package   mu-dashboard-feedback-button
 * @author    jossemarGT <hello@jossemargt.com>
 * @license   GPL-2.0
 * @link      http://jossemargt.com
 * @copyright 2014-04-13 _
 */

/**
 * MU-dashboard-feedback-button class.
 *
 * @package MUDashboardFeedbackButton
 * @author  jossemarGT <hello@jossemargt.com>
 */
class MUDashboardFeedbackButton{
	/**
	 * Plugin version, used for cache-busting of style and script file references.
	 *
	 * @since   1.0.0
	 *
	 * @var     string
	 */
	protected $version = "1.0.0";

	/**
	 * Unique identifier of text domain (i18)
	 *
	 * @since    1.0.0
	 *
	 * @var      string
	 */
	protected $plugin_slug = "mu-dashboard-feedback-button";

	/**
	 * Instance of this class.
	 *
	 * @since    1.0.0
	 *
	 * @var      object
	 */
	protected static $instance = null;

	/**
	 * Slug of the plugin screen.
	 *
	 * @since    1.0.0
	 *
	 * @var      string
	 */
	protected $plugin_screen_hook_suffix = null;

	/**
	 * Initialize the plugin by setting localization, filters, and administration functions.
	 *
	 * @since     1.0.0
	 */
	private function __construct() {

		// Load plugin text domain
		add_action("init", array($this, "load_plugin_textdomain"));

		// Add the options page and menu item.
		add_action("admin_menu", array($this, "add_plugin_admin_menu"));

		// Load admin style sheet and JavaScript.
		add_action("admin_enqueue_scripts", array($this, "enqueue_admin_styles"));
		add_action("admin_enqueue_scripts", array($this, "enqueue_admin_scripts"));

		// Load public-facing style sheet and JavaScript.
		// add_action("wp_enqueue_scripts", array($this, "enqueue_styles"));
		// add_action("wp_enqueue_scripts", array($this, "enqueue_scripts"));

		// Define custom functionality. Read more about actions and filters: http://codex.wordpress.org/Plugin_API#Hooks.2C_Actions_and_Filters
		//add_action('admin_bar_menu', 'add_feebback_button', 1);
		add_action( 'wp_before_admin_bar_render', array($this, 'add_feebback_button') );
		
		//add_action("TODO", array($this, "action_method_name"));
		//add_filter("TODO", array($this, "filter_method_name"));

	}

	/**
	 * Return an instance of this class.
	 *
	 * @since     1.0.0
	 *
	 * @return    object    A single instance of this class.
	 */
	public static function get_instance() {

		// If the single instance hasn't been set, set it now.
		if (null == self::$instance) {
			self::$instance = new self;
		}

		return self::$instance;
	}

	/**
	 * Fired when the plugin is activated.
	 *
	 * @since    1.0.0
	 *
	 * @param    boolean $network_wide    True if WPMU superadmin uses "Network Activate" action, false if WPMU is disabled or plugin is activated on an individual blog.
	 */
	public static function activate($network_wide) {
		// TODO: Define activation functionality here
		// Init DB table
	}

	/**
	 * Fired when the plugin is deactivated.
	 *
	 * @since    1.0.0
	 *
	 * @param    boolean $network_wide    True if WPMU superadmin uses "Network Deactivate" action, false if WPMU is disabled or plugin is deactivated on an individual blog.
	 */
	public static function deactivate($network_wide) {
		// TODO: Define deactivation functionality here
		// Drop DB table
	}

	/**
	 * Load the plugin text domain for translation.
	 *
	 * @since    1.0.0
	 */
	public function load_plugin_textdomain() {

		$domain = $this->plugin_slug;
		$locale = apply_filters("plugin_locale", get_locale(), $domain);

		load_textdomain($domain, WP_LANG_DIR . "/" . $domain . "/" . $domain . "-" . $locale . ".mo");
		load_plugin_textdomain($domain, false, dirname(plugin_basename(__FILE__)) . "/lang/");
	}

	/**
	 * Register and enqueue admin-specific style sheet.
	 *
	 * @since     1.0.0
	 *
	 * @return    null    Return early if no settings page is registered.
	 */
	public function enqueue_admin_styles() {
		// Always load toolbar styles
		wp_enqueue_style($this->plugin_slug . "-admin-toolbar-styles", plugins_url("css/toolbar.css", __FILE__), array(),
										 $this->version);
		
		if (!isset($this->plugin_screen_hook_suffix) ) {
			return;
		}
		
		$screen = get_current_screen();		
		// Just load  when the super admin looks the plugin page
		if ($screen->id == $this->plugin_screen_hook_suffix) {
			wp_enqueue_style($this->plugin_slug . "-admin-styles", plugins_url("css/admin.css", __FILE__), array(),
				$this->version);
		}

	}

	/**
	 * Register and enqueue admin-specific JavaScript.
	 *
	 * @since     1.0.0
	 *
	 * @return    null    Return early if no settings page is registered.
	 */
	public function enqueue_admin_scripts() {

		if (!isset($this->plugin_screen_hook_suffix)) {
			return;
		}

		$screen = get_current_screen();
		if ($screen->id == $this->plugin_screen_hook_suffix) {
			wp_enqueue_script($this->plugin_slug . "-admin-script", plugins_url("js/mu-dashboard-feedback-button-admin.js", __FILE__),
				array("jquery"), $this->version);
		}

	}

	/**
	 * Register and enqueue public-facing style sheet.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_styles() {
		wp_enqueue_style($this->plugin_slug . "-plugin-styles", plugins_url("css/public.css", __FILE__), array(),
			$this->version);
	}

	/**
	 * Register and enqueues public-facing JavaScript files.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_scripts() {
		wp_enqueue_script($this->plugin_slug . "-plugin-script", plugins_url("js/public.js", __FILE__), array("jquery"),
			$this->version);
	}

	/**
	 * Register the administration menu for this plugin into the WordPress Dashboard menu.
	 *
	 * @since    1.0.0
	 */
	public function add_plugin_admin_menu() {
		$this->plugin_screen_hook_suffix = add_plugins_page(
			__("MU Dashboard Feedback - Administration", $this->plugin_slug), // Page Title
			__("MU Dashboard Feedback", $this->plugin_slug), //Menu title
			"manage_network", // Capability
			$this->plugin_slug, // slug
			array($this, "display_plugin_admin_page") //callback
		);
	}

	/**
	 * Render the settings page for this plugin.
	 *
	 * @since    1.0.0
	 */
	public function display_plugin_admin_page() {
		include_once("views/admin.php");
	}

	/**
	 * Append the 2 new buttons to the admin toolbar
	 * 
	 * @since			1.0.0
	 */	
	public function add_feebback_button() {
		global $wp_admin_bar;
		
		// Add section/group to the toolbar
		$args = array(
				'id'    => 'feedback_button_group',
				'title' => __( 'Feedback group', $this->plugin_slug ),
				'group' => true,
				'meta'  => array( 'class' => "feedback-button-group feedback-button-plugin" )
		);
			
		$wp_admin_bar->add_group( $args );
		
		// Add :) button to the toolbar
		$args = array(
				'id'    => 'feedback_button_positive',
				'title' =>  __( 'Positive feedback', $this->plugin_slug ),
				'parent' => 'feedback_button_group',
				'meta'  => array( 
					'class' => "feedback-button positive feedback-button-plugin",
					//'html' => "<h2>:)</h2>"
					)
		);
		
		$wp_admin_bar->add_node( $args );
		
		// Add positive feedback form to the :) button
		$args = array(
				'id'    => 'feedback_button_positive_form',
				'title' =>  __( 'What did you like?', $this->plugin_slug ),
				'parent' => 'feedback_button_positive',
				'meta'  => array( 
					'class' => "feedback-button positive feedback-button-plugin",
					'html' => "<form action='#' method='post'>
					<textarea></textarea>
					</form>"
					)
		);
		
		$wp_admin_bar->add_node( $args );
		
		// Add :( button to the toolbar
		$args = array(
				'id'    => 'feedback_button_negative',
				'title' =>  __( 'Negative feedback', $this->plugin_slug ),
				'parent' => 'feedback_button_group',
				'meta'  => array( 'class' => "feedback-button negative feedback-button-plugin" )
		);
		
		$wp_admin_bar->add_node( $args );
	}
	
	/**
	 * NOTE:  Actions are points in the execution of a page or process
	 *        lifecycle that WordPress fires.
	 *
	 *        WordPress Actions: http://codex.wordpress.org/Plugin_API#Actions
	 *        Action Reference:  http://codex.wordpress.org/Plugin_API/Action_Reference
	 *
	 * @since    1.0.0
	 */
	public function action_method_name() {
		// TODO: Define your action hook callback here
	}

	/**
	 * NOTE:  Filters are points of execution in which WordPress modifies data
	 *        before saving it or sending it to the browser.
	 *
	 *        WordPress Filters: http://codex.wordpress.org/Plugin_API#Filters
	 *        Filter Reference:  http://codex.wordpress.org/Plugin_API/Filter_Reference
	 *
	 * @since    1.0.0
	 */
	public function filter_method_name() {
		// TODO: Define your filter hook callback here
	}

}
