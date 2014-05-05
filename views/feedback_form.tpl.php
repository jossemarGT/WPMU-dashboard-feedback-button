<form class='feedback-form'>
	<?php echo $nonce_field ?>
	<input type="hidden" name="feedback-blog-id" value="<?php echo $blog_id; ?>" >
	<textarea name='feedback-text' class='feedback-text' placeholder='<?php _e( 'Let us know what you think', $locale_slug ) ?>'></textarea>
	<button type='submit' class='feedback-submit'><?php _e( 'Shout it!', $locale_slug ) ?></button>
</form>