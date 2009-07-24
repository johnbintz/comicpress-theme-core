<div class="post-page-head"></div>
<div class="post-page" id="post-<?php the_ID() ?>">
	<h2 class="pagetitle"><?php the_title() ?></h2>
	<div class="entry">
		<?php the_content() ?>
		<?php wp_link_pages(array('before' => '<p><strong>Pages:</strong> ', 'after' => '</p>', 'next_or_number' => 'number')) ?>
	</div>
	<?php edit_post_link('Edit this page.', '<p>', '</p>') ?>
	<br class="clear-margins" />
</div>
<div class="post-page-foot"></div>
