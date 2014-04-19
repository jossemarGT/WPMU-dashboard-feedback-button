=== mu-dashboard-feedback-button ===
Contributors: jossemarGT
Donate link: http://jossemargt.com
Tags: multisite, feedback, admin toolbar
Requires at least: 3.5.1
Tested up to: 3.8.1
Stable tag: 1.0.5
License: GPLv2
License URI: http://www.gnu.org/licenses/gpl-2.0.html

Lets you know what does your sub blog admin thinks about the network.

== Description ==

Lets you know what does your sub blog admin thinks about the network and other related services. Improve your network reading their positive and negative feedback or even questions.

== Installation ==

1. Upload 'MU-dashboard-feedback-button' to the '/wp-content/plugins/' directory.
2. Network activate it through the 'Plugins' menu in WordPress Network dashboard.
3. Check the feedback of your sub-site admins in 'Network Dashboard > Users >  Network Users Feedback'

== Screenshots ==

1. Admin toolbar with feedback buttons.
2. Feedback form.

== Changelog ==

= 1.1.0 = 
* Plugin's uninstall script (drop DB Table).
* The network feedback can be checked in the plugin's page.

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
* Add > mu-dashboard-feedback-button.js
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