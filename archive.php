<?php get_header() ?>

<div id="content" class="archive">

	<?php if (have_posts()) : ?>

		<div class="post-page-head"></div>
		<div class="post-page">	
	
			<?php $post = $posts[0]; // Hack. Set $post so that the_date() works. ?>
			<?php /* Category Archive */ if (is_category()) { ?>
				<div class="content"><h2 class="pagetitle">Archive for &#8216;<?php single_cat_title() ?>&#8217;</h2></div>
			<?php /* Tag Archive */ } elseif( is_tag() ) { ?>
				<div class="content"><h2 class="pagetitle">Posts Tagged &#8216;<?php single_tag_title() ?>&#8217;</h2></div>
			<?php /* Daily Archive */ } elseif (is_day()) { ?>
				<div class="content"><h2 class="pagetitle">Archive for <?php the_time('F jS, Y') ?></h2></div>
			<?php /* Monthly Archive */ } elseif (is_month()) { ?>
				<div class="content"><h2 class="pagetitle">Archive for <?php the_time('F, Y') ?></h2></div>
			<?php /* Yearly Archive */ } elseif (is_year()) { ?>
				<div class="content"><h2 class="pagetitle">Archive for <?php the_time('Y') ?></h2></div>
			<?php /* Author Archive */ } elseif (is_author()) { ?>
				<div class="content"><h2 class="pagetitle">Author Archive</h2></div>
			<?php /* Paged Archive */ } elseif (isset($_GET['paged']) && !empty($_GET['paged'])) { ?>
				<div class="content"><h2 class="pagetitle">Archives</h2></div>
			<?php } ?>
			<br class="clear-margins" />
		</div>
		<div class="post-page-foot"></div>

		<?php $posts = query_posts($query_string.'&order=asc');
		while (have_posts()) : the_post() ?>

			<?php if (in_comic_category()) { ?>

				<div class="post-comic-head"></div>
				<div class="post-comic">
					<div class="comicarchiveframe" style="width:<?php echo $archive_comic_width; ?>px;">
						<a href="<?php the_permalink() ?>">
              <?php do_action('show_archive') ?>
            <br />
						<h3><?php the_title() ?></h3>
						<small><?php the_time('F jS, Y') ?></small></a>
					</div>
					<br class="clear-margins" />
				</div>
				<div class="post-comic-foot"></div>

			<?php } else { ?>

				<div class="post-head"></div>
				<div class="post archive">
					<h3 id="post-<?php the_ID() ?>"><a href="<?php the_permalink() ?>" rel="bookmark" title="Permanent Link to <?php the_title_attribute() ?>"><?php the_title() ?></a></h3>
					<div class="postdate"><?php the_time('F jS, Y') ?></div>
					<div class="entry"><?php the_content() ?></div> 
					<div class="tags">
						<?php the_tags('&#9492; Tags: ', ', ', ''); edit_post_link('Edit Post', ' [ ', ' ] ') ?>
					</div>
					<div class="comment-link">
						<?php if ('open' == $post->comment_status) { comments_popup_link('&ldquo;Comment&rdquo;', '&ldquo;1 Comment&rdquo;', '&ldquo;% Comments&rdquo;'); } ?>
					</div>
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

		<div class="post-head"></div>
		<div class="post">
			<h3>No entries found.</h3>
			<p>Try another search?</p>
			<p><?php include (TEMPLATEPATH . '/searchform.php') ?></p>
			<br class="clear-margins" />
		</div>
		<div class="post-foot"></div>

	<?php endif; ?>

</div>

<?php include(TEMPLATEPATH . '/sidebar.php') ?>

<?php get_footer() ?>