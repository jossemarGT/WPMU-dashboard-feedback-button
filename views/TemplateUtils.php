<?php 
function pagination ( $page_size, $items_count, $ftype, $showunread) {
	if ( $page_size  < $items_count ) : ?>
<ul class="feedback-pager pager-list">
	<?php for( $count = 1; $page_size  < $items_count; $items_count -= $page_size, $count++ ):  ?>
	<li>
		<a href="#!/?page=<?php echo $count; ?>&ftype=<?php echo $ftype; ?>&read=<?php echo $showunread; ?>"><?php echo $count; ?></a>	</li>
	<?php endfor; ?>
</ul>
<?php endif;
}
?>