<?php
/**
 * WPMU-dashboard-feedback-button
 *
 * @package   wpmu-dashboard-feedback-button
 * @author    jossemarGT <hello@jossemargt.com>
 * @license   GPL-2.0
 * @link      http://jossemargt.com
 * @copyright 2014-04-13 _
 */

require_once(plugin_dir_path(__FILE__) . "/views/ViewManager.php");

/**
 * WPMU-dashboard-feedback-button class.
 *
 * @package MUDashboardFeedbackButton
 * @author  jossemarGT <hello@jossemargt.com>
 */
class WPMUDashboardFeedbackButton{
	/**
	 * Plugin version, used for cache-busting of style and script file references.
	 *
	 * @since   1.0.0
	 *
	 * @var     string
	 */
	protected static $version = "1.0.11";

	/**
	 * Unique identifier of text domain (i18)
	 *
	 * @since    1.0.0
	 *
	 * @var      string
	 */
	protected $plugin_slug = "wpmu-dashboard-feedback-button";

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
		if ( get_site_option( "mudashfeedback_network_enabled" )  ) {
			add_action("network_admin_menu", array($this, "add_plugin_admin_menu"));
		} else {
			add_action("users", array($this, "add_plugin_admin_menu"));
		}
		
		// Load admin style sheet and JavaScript.
		add_action("admin_enqueue_scripts", array($this, "enqueue_styles"));
		add_action("admin_enqueue_scripts", array($this, "enqueue_scripts"));
		
		// Load public-facing style sheet and JavaScript.
		add_action("wp_enqueue_scripts", array($this, "enqueue_styles"));
		add_action("wp_enqueue_scripts", array($this, "enqueue_scripts"));
		
		// Append new elements to the admin toolbar
		if( ! get_site_option("mudashfeedback_deactive_buttons") ) {
			add_action( 'wp_before_admin_bar_render', array($this, 'add_feebback_button') );
		}
		
		// Register ajax handler for feedback form
		add_action( 'wp_ajax_site_admin_feedback', array($this, 'handle_site_admin_feedback') );
		add_action( 'wp_ajax_fetch_feedback', array($this, 'fetch_admin_feedback') );
		add_action( 'wp_ajax_mark_as_read_feedback', array($this, 'mark_as_read_admin_feedback') );
		add_action( 'wp_ajax_form_options', array($this, 'fetch_admin_feedback') );

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
			wp_die("Sorry, but you can't run this plugin, it requires to be \"Network Activated\" as WPMU superadmin.", "WPMU Dashboard Feedback Buttons - Activation" , array( 'response'=>403, 'back_link'=>true )); 
		}
		
		// Init DB Table
		global $wpdb;
		
		$table_name = $wpdb->base_prefix . self::$db_table_name_no_prefix ;
		
		$sql = "CREATE TABLE $table_name (
		id mediumint(9) NOT NULL AUTO_INCREMENT,
		timelog datetime DEFAULT '0000-00-00 00:00:00' NOT NULL,
		blogid mediumint(9) NOT NULL,
		blogname tinytext DEFAULT '' NOT NULL,
		blogurl VARCHAR(200) DEFAULT '#' NOT NULL,
		sitedomain VARCHAR(200) DEFAULT '' NOT NULL,
		sitedomain VARCHAR(200) DEFAULT '' NOT NULL,
		blogadmin_email VARCHAR(100) DEFAULT '' NOT NULL,
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
		add_site_option( "mudashfeedback_feedback_page_size", 10 );
		add_site_option( "mudashfeedback_deactive_buttons", false );
	}

	/**
	 * Fired when the plugin is deactivated.
	 *
	 * @since    1.0.0
	 *
	 * @param    boolean $network_wide    True if WPMU superadmin uses "Network Deactivate" action, false if WPMU is disabled or plugin is deactivated on an individual blog.
	 */
	public static function deactivate($network_wide) {
		
		if ($network_wide) {
			delete_site_option( "mudashfeedback_db_version" );
			delete_site_option( "mudashfeedback_network_enabled" );
			delete_site_option( "mudashfeedback_feedback_page_size");
			delete_site_option( "mudashfeedback_deactive_buttons");
		} 

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
	public function enqueue_styles() {
		// Always load toolbar styles
		wp_enqueue_style($this->plugin_slug . "-admin-toolbar-styles", plugins_url("css/toolbar.css", __FILE__), array(),
										 self::$version);
		
		if (!isset($this->plugin_screen_hook_suffix) ) {
			return;
		}
		
		$screen = get_current_screen();
		$screen_id = $screen->is_network ? substr( $screen->id, 0, -8 ) : $screen->id;
		
		// Just load  when the super admin looks the plugin page
		if ($screen_id == $this->plugin_screen_hook_suffix) {
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
	public function enqueue_scripts() {
		wp_enqueue_script($this->plugin_slug . "-toolbar-script", plugins_url("js/wpmu-dashboard-feedback-button.js", __FILE__), array("jquery"), self::$version);
		
		wp_localize_script( $this->plugin_slug . "-toolbar-script", "ajaxObject",
            array( "ajax_url" => admin_url( "admin-ajax.php" ), "response_type" => "json" ) );

		if (!isset($this->plugin_screen_hook_suffix)) {
			return;
		}

		$screen = get_current_screen();
		$screen_id = $screen->is_network ? substr( $screen->id, 0, -8 ) : $screen->id;
		
		if ($screen_id == $this->plugin_screen_hook_suffix) {
			wp_enqueue_script("easy-accordion-tabs-jplugin", plugins_url("js/easyResponsiveTabs.js", __FILE__),
				array("jquery"));
			wp_enqueue_script("load-template-jquery", plugins_url("js/jquery.loadTemplate-1.4.3.min.js", __FILE__),
				array("jquery"));
			wp_enqueue_script($this->plugin_slug . "-admin-script", plugins_url("js/wpmu-dashboard-feedback-button-admin.js", __FILE__),
				array("jquery"), self::$version);
			
			wp_localize_script( $this->plugin_slug . "-toolbar-script", "feedbackPreset",
            array( 
							"ajax_url" => admin_url( "admin-ajax.php" ),
							"response_type" => "json",
							"actions" => array( "fetch" => "fetch_feedback",
																 "mark_read" => "mark_as_read_feedback",
																 "update" => "")
						) );
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
			__("Network Users Feedback - [WPMU Dashboard Feedback]", $this->plugin_slug), // Page Title
			__("Network Users Feedback", $this->plugin_slug), //Menu title
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
		$limit =  get_site_option( "mudashfeedback_feedback_page_size" );
		$offset = 0;
		
		// --- First feedback fetch ----
		
		// Initial query
		$args = array (
			"attributes" => array (), // empty means *
			"where" => array (
				"feedback_type" => "positive",
				"feadback_read" => "N"
			),
			"order" => array("timelog DESC"),
			"limit" => array( $limit, $offset )
		);
		
		$args_count = array (
			"attributes" => array ("COUNT(*)"),
			"where" => array (
				"feedback_type" => "positive",
				"feadback_read" => "N"
			)
		);
		
		// Unread positive
		$unread_positive = $this->fetch_db_feedback($args);
		$unread_positive_count = $this->fetch_db_feedback($args_count, ARRAY_N);
		
		// Unread negative
		$args["where"]["feedback_type"] = "negative";
		$unread_negative = $this->fetch_db_feedback($args);
		
		$args_count["where"]["feedback_type"] = "negative";
		$unread_negative_count = $this->fetch_db_feedback($args_count, ARRAY_N);
		
		// All negative
		unset($args["where"]["feedback_read"]);
		$all_negative = $this->fetch_db_feedback($args);
		
		unset($args_count["where"]["feedback_read"]);
		$all_negative_count = $this->fetch_db_feedback($args_count, ARRAY_N);
		
		// All positive
		$args["where"]["feedback_type"] = "positive";
		$all_positive = $this->fetch_db_feedback($args);
		
		$args_count["where"]["feedback_type"] = "positive";
		$all_positive_count = $this->fetch_db_feedback($args_count, ARRAY_N);

		// --- Template render ---
		$tplVars = array(
			"locale_slug" => $this->plugin_slug,
			"positive_unread" => $unread_positive,
			"negative_unread" => $unread_negative,
			"positive_all" => $all_positive,
			"negative_all" => $all_negative,
			"positive_unread_count" => $unread_positive_count[0][0],
			"negative_unread_count" => $unread_negative_count[0][0],
			"positive_all_count" => $all_positive_count[0][0],
			"negative_all_count" => $all_negative_count[0][0],
			"page_size" => $limit
		);
		$viewMan =	new ViewManager();
		$viewMan->render("admin.tpl.php", $tplVars);
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
		
		// Get blog id, because we're talking about n sites request in the same handler
		$blog_id = get_current_blog_id();
		
		$viewMan = new ViewManager();
		
		// Form node args
		$args = array(
			'meta'  => array( 
				'class' => "feedback-form positive feedback-button-plugin",
				'html' => $viewMan->partialRender(
					"feedback_form.tpl.php", 
					array( 
						"nonce_field" => $nonce,
						"locale_slug" => $this->plugin_slug,
						"blog_id" => $blog_id )
				)
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
			die( __( "Access denied.", $this->plugin_slug ) );
		
		global $wpdb;
		
		$clean_blogid = filter_var($_POST["feedback-blog-id"], FILTER_SANITIZE_NUMBER_INT);
		$clean_feedback = sanitize_text_field($_POST["feedback-text"]);
		$clean_feedback_type = sanitize_text_field($_POST["feedback-type"]);
		
		$the_site = get_blog_details( $clean_blogid, true );
		
		$contact_email = "";
		
		switch_to_blog($clean_blogid);
		
		$contact_email = get_bloginfo("admin_email" , $contact_email);
		
		restore_current_blog();
		
		$table_name = $wpdb->base_prefix . self::$db_table_name_no_prefix ;
		
		$query_args = array( 
			"timelog" => current_time("mysql"),
			"blogid" => $the_site->blog_id,
			"blogname" => $the_site->blogname,
			"blogadmin_email" => $contact_email,
			"sitedomain" => $the_site->domain,
			"blogurl" => $the_site->siteurl,
			"feedback" => $clean_feedback,
			"feedback_type" => $clean_feedback_type
		);
		
		$rows_affected = $wpdb->insert( $table_name, $query_args );
		
		$response = array(
			"message" => __( "Thanks for your feedback", $this->plugin_slug ),
			// "site_obj" => $the_site
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
		
		$limit = get_site_option( "mudashfeedback_feedback_page_size" );
		$offset = $limit * ( filter_var($_POST["feedback_page"], FILTER_SANITIZE_NUMBER_INT) - 1 ) ;
		$clean_feedback_type = sanitize_text_field($_POST["feedback_type"]);
		$show_unread = isset($_POST["feedback-showunread"]) && $_POST["feedback_showunread"] == "Y" ;
		$orderby = "timelog DESC";
		
		$args = array (
			"attributes" => array (), // empty means *
			"where" => array (
				"feedback_type" => $clean_feedback_type,
				"feadback_read" => $show_unread ? "Y" : "N"
			),
			"order" => array($orderby),
			"limit" => array( $limit, $offset )
		);
		
		$result = $this->fetch_db_feedback($args);
		wp_send_json($result);
	}
	
	/**
	 * Fetch new feedback from DB via ajax call
	 *
	 * @since    1.0.10
	 */
	public function mark_as_read_admin_feedback() {
		
		//Only the super admin can check the feedback
		if ( ! current_user_can("manage_network") ) 
			die(__( "Access denied.", $this->plugin_slug ));
		
		
		$args = array (
			"attributes" => array (), // empty means *
			"where" => array (
				"feedback_type" => $clean_feedback_type,
				"feadback_read" => $show_unread ? "Y" : "N"
			),
			"order" => array($orderby),
			"limit" => array( $limit, $offset )
		);
		
		$result = $this->update_db_feedback($args);
		wp_send_json($result);
		
	}
	
	/**
	 * Fetch new feedback from DB
	 *
	 * @since    1.0.7
	 */
	protected function fetch_db_feedback ( $args = array(), $output_type = OBJECT ) {
		global $wpdb;
		$table_name = $wpdb->base_prefix . self::$db_table_name_no_prefix;
		
		$attributes = empty( $args["attributes"] ) ? "*" : implode(", ", $args["attributes"] );
		$where = implode(
			" AND " ,
			array_map(function ($v, $k) { return $k . "= \"" . $v . "\""; },
								$args["where"],
								array_keys($args["where"])
							 )
		);
		
		$order = isset( $args["order"] ) ? implode(", ", $args["order"] ) : "";
		$limit = isset( $args["limit"] ) ? $args["limit"] : false ;

		$rows = $wpdb->get_results( 
			"SELECT $attributes " .
			"FROM $table_name " .
			( $where ? " WHERE $where " : "") .
			( $order ? " ORDER BY $order" : "" ) .
			( $limit ? " LIMIT $limit[0]" : "" ) .
			( $limit && isset($limit[1]) ? " OFFSET $limit[1]" : "" ) 
		, $output_type );

		return $rows;
	}
	
	/**
	 * Updates feedback from DB
	 *
	 * @since    1.0.10
	 */
	protected function update_db_feedback ( $args = array() ) {
		global $wpdb;
		$table_name = $wpdb->base_prefix . self::$db_table_name_no_prefix;

		$attributes = empty( $args["attributes"] ) ? "*" : implode(", ", $args["attributes"] );
		$where = implode(" AND ", $args["where"] );
		$limit = isset( $args["limit"] ) ? $args["limit"] : false ;
		
		/*
		$rows = $wpdb->get_results( 
			"UPDATE $attributes " .
			"FROM $table_name " .
			( $where ? " WHERE $where " : "") .
			( $limit ? " LIMIT $limit[0]" : "" ) .
			( $limit && isset($limit[1]) ? " OFFSET $limit[1]" : "" ) 
		, OBJECT );
		*/
		
		return $rows;
	}
}
