<?php get_header() ?>

<div id="column" class="archive">

	<div class="post-page-head"></div>
	<div class="post-page">			
		<h2 class="pagetitle">Transcript Search for &lsquo;<?php the_search_query() ?>&rsquo;</h2>
	</div>
	<div class="post-page-foot"></div>

	<?php if (have_posts()) : ?>

		<?php $posts = query_posts($query_string.'&order=asc');
		while (have_posts()) : the_post() ?>

				<?php if (in_comic_category()) { ?>

					<div class="post-comic-head"></div>
					<div class="post-comic">
						<div class="comicarchiveframe" style="width:<?php echo $archive_comic_width ?>px;">
							<a href="<?php the_permalink() ?>"><img src="<?php the_comic_archive() ?>" alt="<?php the_title() ?>" title="<?php the_transcript() ?>" width="<?php echo $archive_comic_width ?>" /><br />
							<h3><?php the_title() ?></h3>
							<small><?php the_time('F jS, Y') ?></small></a>
						</div>
						<br class="clear-margins" />
					</div>
					<div class="post-comic-foot"></div>

				<?php } else { ?>

					<div class="post-head"></div>
					<div class="post">
						<h3><a href="<?php the_permalink() ?>" rel="bookmark" title="Permanent Link: <?php the_title() ?>"><?php the_title() ?></a></h3>
						<div class="postdate"><?php the_time('F jS, Y') ?></div>
						<?php the_excerpt() ?>
						<br class="clear-margins" />
					</div>
					<div class="post-foot"></div>
					
				<?php } ?>

		<?php endwhile; ?>

		<div class="pagenav">
			<div class="pagenav-right"><?php next_posts_link('Next Page &rsaquo;') ?></div>
			<div class="pagenav-left"><?php previous_posts_link('&lsaquo; Previous Page') ?></div>
			<div class="clear"></div>
		</div>

	<?php else : ?>

		<div class="post-page-head"></div>
		<div class="post-page">
			<h3>No transcripts found.</h3>
			<p>Try another search?</p>
			<p><?php include (TEMPLATEPATH . '/searchform-transcript.php') ?></p>
			<br class="clear-margins" />
		</div>
		<div class="post-page-foot"></div>

	<?php endif; ?>

</div>

<?php include(TEMPLATEPATH . '/sidebar.php') ?>

<?php get_footer() ?>