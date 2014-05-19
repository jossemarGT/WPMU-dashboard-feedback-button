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

include("TemplateUtils.php");
?>
<div class="wrap">
	
	<?php screen_icon(); ?>
	<h2><?php echo esc_html( get_admin_page_title() ); ?></h2>

	<div id="config-tab">
		<ul class="resp-tabs-list">
			<li><?php _e("Unread Feedback", $locale_slug); ?></li>
			<li><?php _e("All", $locale_slug); ?></li>
			<li><?php _e("Configuration", $locale_slug); ?></li>
		</ul>
		
		<div class="resp-tabs-container">
			<!-- Unread feedback tab -->
			<div id="unread-feedback-tab" class="ui-tab-panel column-container clearfix" data-positive-total="<?php echo $positive_unread_count; ?>" data-negative-total="<?php echo $negative_all_count; ?>" data-page-size="<?php echo $page_size; ?>">
				<div class="clearfix">
						<div class="column-float half-size feedback-positive" >
							<h3><?php _e("Positive Feedback", $locale_slug); ?></h3>
							<ul class="feedback-list positive">
								<?php foreach( $positive_unread as $row ): ?>
								<li>
									<input name="check-mark-read" type="checkbox" class="feedback-check" value="<?php echo $row->id; ?>">
									<div class="feedback-content" data-time="<?php echo $row->timelog; ?>" > 
										<?php echo $row->feedback; ?>
										<span class="feedback-author-sitename"><a href="<?php echo $row->blogurl ?>"><?php echo $row->blogname; ?></a> // <?php echo $row->timelog; ?> </span>
									</div>
								</li>
								<?php endforeach; ?>
							</ul>

							<!-- Pagination -->
							<?php pagination ( $page_size, $positive_unread_count, "positive", "N"); ?>
						</div>

						<div class="column-float half-size feedback-negative" >
							<h3><?php _e("Negative Feedback", $locale_slug); ?></h3>
							<ul class="feedback-list negative">
								<?php foreach( $negative_unread as $row ): ?>
								<li>
									<input name="check-mark-read" type="checkbox" class="feedback-check" value="<?php echo $row->id; ?>">
									<div class="feedback-content" data-time="<?php echo $row->timelog; ?>" > 
										<?php echo $row->feedback; ?>
										<span class="feedback-author-sitename"><a href="<?php echo $row->blogurl ?>"><?php echo $row->blogname; ?></a> // <?php echo $row->timelog; ?> </span>
									</div>
								</li>
								<?php endforeach; ?>
							</ul>

							<!-- Pagination -->
							<?php pagination ( $page_size, $negative_unread_count, "negative", "N"); ?>
						</div>
				</div>
				<div class="feedback-tools">
					<button type="button" class="feedback-tool mark-as-read"><?php _e("Mark as read", $locale_slug); ?></button>
				</div>
				
			</div>
			
			<!-- All feedback tab read/unread -->
			<div id="all-feedback-tab" class="ui-tab-panel column-container clearfix"  data-positive-total="<?php echo $positive_all_count; ?>" data-negative-total="<?php echo $negative_all_count; ?>" data-page-size="<?php echo $page_size; ?>">
				
				<div class="column-float half-size feedback-positive" >
					<h3>Positive Feedback</h3>
					<ul class="feedback-list positive">
						<?php foreach( $positive_all as $row ): ?>
						<li>
							<div class="feedback-content" data-id="<?php echo $row->id; ?>" data-time="<?php echo $row->timelog; ?>" > 
								<?php echo $row->feedback; ?>
								<span class="feedback-author-sitename"><a href="<?php echo $row->blogurl ?>"><?php echo $row->blogname; ?></a> // <?php echo $row->timelog; ?> </span>
							</div>
						</li>
						<?php endforeach; ?>
					</ul>
					
					<!-- Pagination -->
					<?php pagination ( $page_size, $positive_all_count, "positive", "Y"); ?>
				</div>
				
				<div class="column-float half-size feedback-negative" >
					<h3>Negative Feedback</h3>
					<ul class="feedback-list negative">
						<?php foreach( $negative_all as $row ): ?>
						<li>
							<div class="feedback-content" data-id="<?php echo $row->id; ?>" data-time="<?php echo $row->timelog; ?>" > 
								<?php echo $row->feedback; ?>
								<span class="feedback-author-sitename"><a href="<?php echo $row->blogurl ?>"><?php echo $row->blogname; ?></a> // <?php echo $row->timelog; ?> </span>
							</div>
						</li>
						<?php endforeach; ?>
					</ul>
					
					<?php pagination ( $page_size, $negative_all_count, "negative", "Y"); ?>
				</div>
				
			</div>
			
			<!-- Configurations tab -->
			<div id="plugin-config-tab" class="ui-tab-panel">
				
				<form method="post" action="#">
					<fieldset>
						<legend><?php _e("Feedback review page options", $locale_slug); ?></legend>
						<label for="feedback-page-size"><?php _e("Max. quatity of feedback per page", $locale_slug); ?></label>
						<input type="number" id="feedback-page-size" name="feedback-page-size" value="<?php echo $page_size; ?>" />
					</fieldset>
					
					<fieldset>
						<legend><?php _e("Toolbar buttons options", $locale_slug); ?></legend>
						<label for="disable-feedback-form"><?php _e("Disable feedback buttons from toolbar", $locale_slug); ?> </label>
						<input type="checkbox" id="disable-feedback-form" name="disable-feedback-form" />
					</fieldset>
					
					<button type="submit"><?php _e("Save changes", $locale_slug); ?></button>
				</form>
				
			</div>
		</div>
	</div>

</div>

<!-- Datarow tpl, for ajax data -->
<script type="text/html" id="row-template">
    <li>
			<input name="check-mark-read" type="checkbox" class="feedback-check" data-value="id">
			<div class="feedback-content" data-content-prepend="feedback" data-time="timelog">
				<span class="feedback-author-sitename" data-content-append="timelog">
					<a data-href="blogurl" data-content="blogname"></a> // 
				</span>
			</div>
		</li>
</script>
	