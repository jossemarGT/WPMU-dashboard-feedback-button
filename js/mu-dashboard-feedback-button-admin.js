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
		$('#config-tab').easyResponsiveTabs({
			type: 'vertical',
			width: 'auto',
			fit: true
		});
		
		// logic
		
		// The feedbackPreset object is in the global scope, It have been created via wp_localize_script
		var dataObject = {
			feedback_page: 1,
			feedback_type: "positive",
			feedback_showuread : "N",
			action : feedbackPreset.actions.fetch
		}
		

		function doAjaxCall () {
			
			$.ajax({
				type : "post",
				dataType : feedbackPreset.response_type,
				url : feedbackPreset.ajax_url,
				data : dataObject,
				success: function(response) {
					renderFeedback ( response, response[0].feedback_type );
				}
			});
		}
		
		function renderFeedback(data, ftype, parentType = "unread") {
			var $parentContainer = parentType == "unread" ? $("#unread-feedback-tab") : $("#all-feedback-tab");
			
			$(".feedback-list." + ftype, $parentContainer).loadTemplate($("#row-template"), data );
		}
		
		doAjaxCall();
		
	});
}(jQuery));