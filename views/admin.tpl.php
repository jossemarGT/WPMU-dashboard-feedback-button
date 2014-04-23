<?php
/**
 * Represents the view for the administration dashboard.
 *
 * This includes the header, options, and other information that should provide
 * The User Interface to the end user.
 *
 * @package   mu-dashboard-feedback-button
 * @author    jossemarGT <hello@jossemargt.com>
 * @license   GPL-2.0
 * @link      http://jossemargt.com
 * @copyright 2014-04-13 _
 */
?>
<div class="wrap">
	
	<?php screen_icon(); ?>
	<h2><?php echo esc_html( get_admin_page_title() ); ?></h2>

	<div id="verticalTab">
		<ul class="resp-tabs-list">
			<li>Unread Feedback</li>
			<li>All</li>
			<li>Configuration</li>
		</ul>
		
		<div class="resp-tabs-container">
			
			<div class="ui-tab-panel column-container clearfix">
				
				<div class="column-float half-size feedback-positive" >
					<h3>Positive Feedback (4/100)</h3>
					<ul class="feedback-list">
						<li><input type="checkbox" class="feedback-check"><div id="feedback-1001" class="feedback-content">data data data data data data data data data data data data data data data data data data data data data data data data data data data data data data data data data data data data data data data data data data data data data data data data data data data data data data data data data data data </div></li>
						<li><input type="checkbox" class="feedback-check"><div id="feedback-1001" class="feedback-content">data</div></li>
						<li><input type="checkbox" class="feedback-check"><div id="feedback-1001" class="feedback-content">data</div></li>
						<li><input type="checkbox" class="feedback-check"><div id="feedback-1001" class="feedback-content">data</div></li>
					</ul>
				</div>
				
				<div class="column-float half-size feedback-negative" >
					<h3>Negative Feedback (4/50)</h3>
					<ul class="feedback-list">
						<li><input type="checkbox" class="feedback-check"><div id="feedback-1001" class="feedback-content">data</div></li>
						<li><input type="checkbox" class="feedback-check"><div id="feedback-1001" class="feedback-content">data</div></li>
						<li><input type="checkbox" class="feedback-check"><div id="feedback-1001" class="feedback-content">data</div></li>
						<li><input type="checkbox" class="feedback-check"><div id="feedback-1001" class="feedback-content">data</div></li>
					</ul>
				</div>
				
				<div class="feedback-tools">
					<button type="button">Mark as read</button>
				</div>
				
			</div>
			
			<div class="ui-tab-panel column-container">
				
				<div class="column-float half-size feedback-positive" >
					<h3>Positive Feedback (4/100)</h3>
					<ul>
						<li>data</li>
						<li>data</li>
						<li>data</li>
						<li>data</li>
					</ul>
				</div>
				
				<div class="column-float half-size feedback-negative" >
					<h3>Negative Feedback</h3>
					<ul>
						<li>data</li>
						<li>data</li>
						<li>data</li>
						<li>data</li>
					</ul>
				</div>
				
			</div>
			
			<div>
				<p>Suspendisse blandit velit Integer laoreet placerat suscipit. Sed sodales scelerisque commodo. Nam porta cursus lectus. Proin nunc erat, gravida a facilisis quis, ornare id lectus. Proin consectetur nibh quis Integer laoreet placerat suscipit. Sed sodales scelerisque commodo. Nam porta cursus lectus. Proin nunc erat, gravida a facilisis quis, ornare id lectus. Proin consectetur nibh quis urna gravid urna gravid eget erat suscipit in malesuada odio venenatis.</p>
			</div>
		</div>
	</div>

</div>
