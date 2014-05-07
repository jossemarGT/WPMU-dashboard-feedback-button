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
		// The feedbackPreset object is in the global scope, It have been created via wp_localize_script
		var dataObject = {
			feedback_page: 1,
			feedback_type: "positive",
			feedback_showunread : "N",
			action : feedbackPreset.actions.fetch
		}
		
		// UI Setup ----
		
		// Init tabs UI
		$('#config-tab').easyResponsiveTabs({
			type: 'vertical',
			width: 'auto',
			fit: true
		});
		
		// Handle pagination
		$(".feedback-pager").on("click", "a" , function(e){
			var $this = $(this),
					thref = $this.attr('href');
			dataObject.feedback_page = getURLParameter(thref,"page");
			dataObject.feedback_type = getURLParameter(thref,"ftype");
			dataObject.feedback_showunread = getURLParameter(thref,"read");
			doAjaxCall(dataObject);
			e.preventDefault();
		});
		
		// The logic ----
		function doAjaxCall ( paramsObj ) {
			
			$.ajax({
				type : "post",
				dataType : feedbackPreset.response_type,
				url : feedbackPreset.ajax_url,
				data : paramsObj,
				success: function(response) {
					renderFeedback ( response, paramsObj.feedback_type, paramsObj.feedback_showunread == "N" ? "unread" : "all" );
				}
			});
		}
		
		function renderFeedback(data, ftype, parentType = "unread") {
			var $parentContainer = parentType == "unread" ? $("#unread-feedback-tab") : $("#all-feedback-tab");
			
			$(".feedback-list." + ftype, $parentContainer).loadTemplate($("#row-template"), data );
		}
		
		function getURLParameter(url, name) {
			return (RegExp(name + '=' + '(.+?)(&|$)').exec(url)||[,null])[1];
		}
		
	});
}(jQuery));