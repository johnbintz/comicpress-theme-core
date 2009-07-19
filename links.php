<?php
/*
Template Name: Links
*/
?>

<?php get_header() ?>

<div id="content" class="narrowcolumn">

	<div class="post-page-head">
		<div class="post-page">
			<h2 class="pagetitle"><?php the_title() ?></h2>
			<ul>
				<?php wp_list_bookmarks() ?>
			</ul>
			<br class="clear-margins" />
		</div>
		<div class="post-page-foot"></div>
  </div>
</div>
	
<?php ge_sidebar() ?>

<?php get_footer() ?>