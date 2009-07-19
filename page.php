<?php get_header() ?>

<div id="content" class="narrowcolumn">
	<?php if (have_posts()) : while (have_posts()) : the_post() ?>
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
	<?php endwhile; endif; ?>
  <?php comments_template(); ?>
</div>

<?php get_sidebar() ?>

<?php get_footer() ?>