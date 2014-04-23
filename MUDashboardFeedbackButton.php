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

require_once(plugin_dir_path(__FILE__) . "/views/ViewManager.php");

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
	protected static $version = "1.1.0";

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
	 * Slug of the plugin screen.
	 *
	 * @since    1.0.0
	 *
	 * @var      string
	 */
	public static $db_table_name_no_prefix = "mu_usr_feedback";
	
	/**
	 * Initialize the plugin by setting localization, filters, and administration functions.
	 *
	 * @since     1.0.0
	 */
	private function __construct() {

		// Load plugin text domain
		add_action("init", array($this, "load_plugin_textdomain"));
		
		// Check DB updates 
		add_action( 'plugins_loaded', array($this, "update_db_check") );

		// Add the options page and menu item, only visible at network dashboard
		if ( get_site_option( "mudashfeedback_db_version" )  ) {
			add_action("network_admin_menu", array($this, "add_plugin_admin_menu"));
		}
		
		// Load admin style sheet and JavaScript.
		add_action("admin_enqueue_scripts", array($this, "enqueue_admin_styles"));
		add_action("admin_enqueue_scripts", array($this, "enqueue_admin_scripts"));
		
		// Append new elements to the admin toolbar
		add_action( 'wp_before_admin_bar_render', array($this, 'add_feebback_button') );
		
		// Register ajax handler for feedback form
		add_action( 'wp_ajax_site_admin_feedback', array($this, 'handle_site_admin_feedback') );
		add_action( 'wp_ajax_fetch_feedback', array($this, 'fetch_admin_feedback') );
		

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
		
		// Check if it's a network wide installation,
		if ( $network_wide === false ) {
			deactivate_plugins(basename(__FILE__)); 
			wp_die("Sorry, but you can't run this plugin, it requires to be \"Network Activated\" as WPMU superadmin.", "MU Dashboard Feedback Buttons - Activation" , array( 'response'=>403, 'back_link'=>true )); 
		}
		
		// Init DB Table
		global $wpdb;
		
		$table_name = $wpdb->prefix . self::$db_table_name_no_prefix ;
		
		$sql = "CREATE TABLE $table_name (
		id mediumint(9) NOT NULL AUTO_INCREMENT,
		timelog datetime DEFAULT '0000-00-00 00:00:00' NOT NULL,
		siteid mediumint(9) NOT NULL,
		sitename tinytext DEFAULT '' NOT NULL,
		sitedomain VARCHAR(200) DEFAULT '' NOT NULL,
		feedback text NOT NULL,
		feedback_type VARCHAR(8) DEFAULT 'positive' NOT NULL,
		feadback_read VARCHAR(1) DEFAULT 'N' NOT NULL,
		UNIQUE KEY id (id)
    );";
		
		require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
		dbDelta( $sql ); // Install or update table
		
		// Save current plugin's version as option
		add_site_option( "mudashfeedback_db_version", self::$version );
		add_site_option( "mudashfeedback_network_enabled", true );
	}

	/**
	 * Fired when the plugin is deactivated.
	 *
	 * @since    1.0.0
	 *
	 * @param    boolean $network_wide    True if WPMU superadmin uses "Network Deactivate" action, false if WPMU is disabled or plugin is deactivated on an individual blog.
	 */
	public static function deactivate($network_wide) {
		delete_site_option( "mudashfeedback_db_version" );
		delete_site_option( "mudashfeedback_network_enabled" );
	}
	
	/**
	 * Check after every load if the database table is updated.
	 *
	 * @since    1.0.4
	 */
	public function update_db_check() {
    if (get_site_option( "mudashfeedback_db_version" ) != self::$version) {
			self::activate(true);
			update_site_option( "mudashfeedback_db_version", self::$version );
		}
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
										 self::$version);
		
		if (!isset($this->plugin_screen_hook_suffix) ) {
			return;
		}
		
		$screen = get_current_screen();		
		// Just load  when the super admin looks the plugin page
		if ($screen->id == $this->plugin_screen_hook_suffix) {
			wp_enqueue_style($this->plugin_slug . "-admin-styles", plugins_url("css/admin.css", __FILE__), array(),
				self::$version);
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
		wp_enqueue_script($this->plugin_slug . "-toolbar-script", plugins_url("js/mu-dashboard-feedback-button.js", __FILE__), array("jquery"), self::$version);
		
		wp_localize_script( $this->plugin_slug . "-toolbar-script", "ajaxObject",
            array( "ajax_url" => admin_url( "admin-ajax.php" ), "response_type" => "json" ) );

		if (!isset($this->plugin_screen_hook_suffix)) {
			return;
		}

		$screen = get_current_screen();
		
		if ($screen->id == $this->plugin_screen_hook_suffix) {
			wp_enqueue_script($this->plugin_slug . "-admin-script", plugins_url("js/mu-dashboard-feedback-button-admin.js", __FILE__),
				array("jquery"), self::$version);
			wp_enqueue_script("easy-accordion-tabs-jplugin", plugins_url("js/easyResponsiveTabs.js", __FILE__),
				array("jquery"));
		}

	}

	/**
	 * Register the administration menu for this plugin into the WordPress Dashboard menu.
	 *
	 * @since    1.0.0
	 */
	public function add_plugin_admin_menu() {
		$this->plugin_screen_hook_suffix = add_submenu_page(
			"users.php",
			__("Network Users Feedback - [MU Dashboard Feedback]", $this->plugin_slug), // Page Title
			__("Network Users Feedback", $this->plugin_slug), //Menu title
			"manage_network", // Capability
			$this->plugin_slug, // slug
			array($this, "display_plugin_admin_page") //callback
		);
		
		if ( get_option( "mudashfeedback_db_version" ) ) {
			$this->plugin_screen_hook_suffix .= "-network" ;
		}
	}

	/**
	 * Render the settings page for this plugin.
	 *
	 * @since    1.0.0
	 */
	public function display_plugin_admin_page() {
		ViewManager::render("admin.tpl.php");
	}

	/**
	 * Append the 2 new buttons to the admin toolbar
	 * 
	 * @since			1.0.0
	 */	
	public function add_feebback_button() {
		global $wp_admin_bar;
		
		// Only visible for site admins and super admins
		if ( ! current_user_can('manage_options') )
			return ;
		
		// TODO: Check user roles visibility, with the options
		
		// --- Toolbar section/group ---
		// Add section/group to the toolbar
		$args = array(
				'id'    => 'feedback_button_group',
				'title' => __( 'Feedback group', $this->plugin_slug ),
				'group' => true,
				'meta'  => array( 'class' => "feedback-button-group feedback-button-plugin" )
		);			
		$wp_admin_bar->add_group( $args );
		
		
		// --- Toolbar buttons ---
		
		// Add :) button to the toolbar
		$args = array(
				'id'    => 'feedback_button_positive',
				'title' =>  __( 'What I liked is...', $this->plugin_slug ),
				'parent' => 'feedback_button_group',
				'meta'  => array( 
					'class' => "feedback-button positive feedback-button-plugin",
					)
		);
		$wp_admin_bar->add_node( $args );
		
		// Add :( button to the toolbar
		$args = array(
				'id'    => 'feedback_button_negative',
				'title' =>  __( 'What I didn\'t like is...', $this->plugin_slug ),
				'parent' => 'feedback_button_group',
				'meta'  => array( 'class' => "feedback-button negative feedback-button-plugin" )
		);
		$wp_admin_bar->add_node( $args );

		
		// --- Toolbar forms ---
		
		// Generate form nonce
		$nonce = wp_nonce_field( 'site_admin_feedback', 'site_admin_feedback_nonce', false, false );
		
		// Form node args
		$args = array(
			'meta'  => array( 
				'class' => "feedback-form positive feedback-button-plugin",
				'html' => ViewManager::partialRender("feedback_form.tpl.php", array( "nonce_field" => $nonce, "locale_slug" => $this->plugin_slug ))
			)
		);
		
		// Add positive feedback form to the :) button
		$args["id"] = "feedback_button_positive_form";
		$args["parent"] = "feedback_button_positive";
		$wp_admin_bar->add_node( $args );
		
		// Add negative feedback form to the :( button
		$args["id"] = "feedback_button_negative_form";
		$args["parent"] = "feedback_button_negative";
		$args["meta"]["class"] = "feedback-form negative feedback-button-plugin";
		$wp_admin_bar->add_node( $args );
	}
	

	/**
	 * Handles the feedback form's submitted data via ajax
	 *
	 * @since    1.0.3
	 */
	public function handle_site_admin_feedback() {
		$nonce = $_POST["site_admin_feedback_nonce"];
		if (empty($_POST) || !wp_verify_nonce($nonce, "site_admin_feedback") ) 
			die( __( "Security check", $this->plugin_slug ) );
		
		global $wpdb;
		$the_site = get_current_site();
		$clean_feedback = sanitize_text_field($_POST["feedback-text"]);
		$clean_feedback_type = sanitize_text_field($_POST["feedback-type"]);
		
		$table_name = $wpdb->prefix . self::$db_table_name_no_prefix ;
		$rows_affected = $wpdb->insert( $table_name, array( 
			"timelog" => current_time("mysql"),
			"siteid" => $the_site->id,
			"sitename" => $the_site->site_name,
			"sitedomain" => $the_site->domain,
			"feedback" => $clean_feedback,
			"feedback_type" => $clean_feedback_type
		));
		
		$response = array(
			"message" => __( "Thanks for your feedback", $this->plugin_slug ),
			"site_obj" => $the_site
		);
		
		wp_send_json($response);
		die();
	}
	
	/**
	 * Fetch new feedback from DB via ajax call
	 *
	 * @since    1.0.3
	 */
	public function fetch_admin_feedback() {
		
		//Only the super admin can check the feedback
		if ( ! current_user_can("manage_network") ) 
			die(__( "Access denied.", $this->plugin_slug ));
		
		global $wpdb;
		$offset = filter_var($_POST["feedback_offset"], FILTER_SANITIZE_NUMBER_INT);
		$clean_feedback_type = sanitize_text_field($_POST["feedback_type"]);
		$limit = isset($_POST["feedback-limit"]) ? $_POST["feedback_limit"] : 10 ;
		$show_unread = isset($_POST["feedback-showuread"]) && $_POST["feedback_showuread"] == "y" ;
		
		$table_name = $wpdb->prefix . self::$db_table_name_no_prefix ;
		
		if ( $show_unread ) {
			$rows = $wpdb->get_results( 
				" 
				SELECT * 
				FROM $table_name
				WHERE feedback_type = '$clean_feedback_type'
				LIMIT $limit OFFSET $offset
				"
			, OBJECT );
		} else {
			$rows = $wpdb->get_results( 
				" 
				SELECT * 
				FROM $table_name
				WHERE feadback_read = 'N'
					AND feedback_type = '$clean_feedback_type'
				LIMIT $limit OFFSET $offset
				"
			, OBJECT );
		}
		
		wp_send_json($rows);
		die();
	}
}
