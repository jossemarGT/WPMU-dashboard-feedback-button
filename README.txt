=== WPMU Dashboard Feedback Button ===
Contributors: jossemarGT
Donate link: http://jossemargt.com
Tags: multisite, feedback, admin toolbar
Requires at least: 3.5.1
Tested up to: 3.9.1
Stable tag: 1.0.10
License: GPLv2
License URI: http://www.gnu.org/licenses/gpl-2.0.html

Lets you know what does your sub blog admin thinks about the network.

== Description ==

Lets you know what does your sub blog admin thinks about the network and other related services. Improve your network reading their positive and negative feedback or even questions.

== Installation ==

1. Upload `WPMU-dashboard-feedback-button` to the `/wp-content/plugins/` directory.
2. Network activate it through the `Plugins` menu in WordPress Network dashboard.
3. Check the feedback of your sub-site admins in `Network Dashboard > Users >  Network Users Feedback`

== Screenshots ==

1. Admin toolbar with feedback buttons.
2. Feedback form.

== Changelog ==

= 1.0.10 =
* Mark as read feature
* BUGFIX: Enqueue missing public styles
* BUGFIX: Pagination missing page

= 1.0.9 =
* Feedback pagination.
* BUGFIX: Use $wpdb->base_prefix instead of $wpdb->prefix
* BUGFIX: Use get_current_blog_id() instead of $current_site, eeyup, $current site holds network's info.

= 1.0.8 =
* Updated wrap for SQL SELECT query, now includes ORDER BY.

= 1.0.7 =
* Feedback display (without pagination)
* Improve db queries
* [plugin's page] Ajax feedback fetch.
* [plugin's page] JS templates integration.
* [plugin's page] Styles and js behavior.

= 1.0.6 =
* Basic template rendering.

= 1.0.5 =
* plugin_screen_hook_suffix network fix.
* Feedback written in plugin's DB table.

= 1.0.4 =
* Plugin's deactivation script (doesn't drop DB table).
* DB table update on plugin's update.
* Creation of DB table on plugin's activation.
* Plugin's page is now available via 'Network Dashboard > Users >  Network Users Feedback'.
* The plugin can be only network activated.

= 1.0.3 =
* Add > Ajax handling via admin-ajax.php and nonces
* Add > wpmu-dashboard-feedback-button.js
* Update > toolbar styles
* Update > README file

= 1.0.2 =
* Update > toolbar styles
* Update > README file
* Add > screenshots

= 1.0.1 =
* Update > README file

= 1.0.0 =
* Initial Commit
* Custom admin toolbar nodes styles
* Custom admin toolbar nodes
* $ yo wordpress-plugin

== TODO ==
* Uninstall hook and script
* Enhance the config page
* Don't fetch feedback twice (on ajax update)
* Feedback update (mark as read)