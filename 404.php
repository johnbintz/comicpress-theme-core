<?php get_header() ?>

<div id="content" class="narrowcolumn">
	<div class="post-page-head"></div>
	<div class="post-page">
		<h2 class="pagetitle">Page Not Found</h2>
		<p><a href="<?php bloginfo('url') ?>">Click here to return to the home page</a> or try a search:</p>
		<p><?php include (TEMPLATEPATH . '/searchform.php') ?></p>
		<br class="clear-margins" />
	</div>
	<div class="post-page-foot"></div>
</div>

<?php get_sidebar() ?>

<?php get_footer() ?>