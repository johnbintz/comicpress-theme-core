<?php
/*
Template Name: Archives
*/
?>

<?php get_header() ?>

<div id="content" class="narrowcolumn">
	<div class="post-page-head"></div>
	<div class="post-page">
		<h2>Archives by Month:</h2>
		<ul><?php wp_get_archives('type=monthly') ?></ul>
		<h2>Archives by Subject:</h2>
		<ul><?php wp_list_categories() ?></ul>
		<br class="clear-margins" />
	</div>
	<div class="post-page-foot"></div>
</div>

<?php get_sidebar() ?>

<?php get_footer() ?>