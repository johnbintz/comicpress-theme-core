<?php
/*
Template Name: Links
*/

global $comicpress;

comicpress_init();

ob_start();

?>
<div class="post-page-head"></div>
<div class="post-page">
	<h2 class="pagetitle"><?php the_title() ?></h2>
	<ul>
		<?php wp_list_bookmarks() ?>
	</ul>
	<br class="clear-margins" />
</div>
<div class="post-page-foot"></div>
<?php

$content = ob_get_clean();

include(get_template_directory() . '/layouts/' . $comicpress->comicpress_options['layout']);
?>