/**
 * This is the main javascript file for the MU-dashboard-feedback-button plugin's main administration view.
 *
 * @package   mu-dashboard-feedback-button
 * @author    jossemarGT <hello@jossemargt.com>
 * @license   GPL-2.0
 * @link      http://jossemargt.com
 * @copyright 2014-04-13 _
 */

(function ($) {
	"use strict";
	$(function () {
		
		// UI Setup
		$('#verticalTab').easyResponsiveTabs({
			type: 'vertical',
			width: 'auto',
			fit: true
		});
		
		var ajaxPreset = {
			feedback_offset : 0,
			feedback_type: "positive",
			feedback_limit: 10,
			feedback_showuread: "n",
			action : "fetch_feedback"
		}
		
		var doAjaxCall = function () {
			$.ajax({
				type : "post",
				//dataType : "json",
				url : ajaxObject.ajax_url,
				data : ajaxPreset,
				success: function(response) {
					console.log( response );
				}
			});
		}
		
		doAjaxCall();
		
	});
}(jQuery));