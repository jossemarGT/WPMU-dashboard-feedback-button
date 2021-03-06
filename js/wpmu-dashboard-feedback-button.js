/**
 * Form's Behavioral management via jQuery
 *
 * @package   wpmu-dashboard-feedback-button
 * @author    jossemarGT <hello@jossemargt.com>
 * @license   GPL-2.0
 * @link      http://jossemargt.com
 * @copyright 2014-04-13 _
 */

(function ($) {
	"use strict";
	$(function () {
		
		var feedbackGroupDOM = document.getElementById("wp-admin-bar-feedback_button_group");
		
		$(".feedback-text").on("focus", function(){
			$(this).closest(".menupop" , feedbackGroupDOM).addClass("feedback-focus");
		}).on("blur", function(){
			$(this).closest(".menupop" , feedbackGroupDOM).removeClass("feedback-focus");
		});
		
		
		$(".feedback-form").on("submit", function(evt){
			evt.stopPropagation();
			evt.preventDefault();
			
			var $this = $(this),
					$feedContent = $(".feedback-text", $this ),
					feedbackType = $this.parent().hasClass("positive") ? "positive" : "negative";

			if ( $feedContent.val().length < 10 ) {
				console.log("nope");
				return false;
			}
			
			var cdata = $this.serialize() + "&action=site_admin_feedback&feedback-type=" + feedbackType ;
			
			$.ajax({
				type : "post",
				dataType : "json",
				url : ajaxObject.ajax_url,
				data : cdata,
				success: function(response) {
					alert(response.message)
					$this[0].reset();
				}
			});
			
		});
	});
}(jQuery));