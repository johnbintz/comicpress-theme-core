<?php get_header(); global $blog_postcount, $blogcat; $first_comic = get_first_comic_permalink() ?>

<?php if (!(is_paged())) { ?>

	<?php $wp_query->in_the_loop = true; $comicFrontpage = new WP_Query(); $comicFrontpage->query('showposts=1&cat='.get_all_comic_categories_as_cat_string());
	while ($comicFrontpage->have_posts()) : $comicFrontpage->the_post() ?>
		<div id="comic-head"></div>
		<div id="comic">
			<img src="<?php the_comic() ?>" alt="<?php the_title() ?>" title="<?php the_hovertext() ?>" />
		</div>
		<div id="comic-foot"></div>
		<?php comic_navigation(); ?>
	<?php endwhile; ?>

	<div id="content" class="narrowcolumn">

		<?php while ($comicFrontpage->have_posts()) : $comicFrontpage->the_post() ?>
			<div class="post-comic-head"></div>
			<div class="post-comic">
				<div class="nav">
					<div class="nav-first"><a href="<?php echo $first_comic ?>" title="Go to the First Comic">&lsaquo;&lsaquo; First</a></div>
					<div class="nav-previous"><?php global $wp_query; $wp_query->is_single = true; previous_comic_link('%link', '&lsaquo; Previous'); $wp_query->is_single = false ?></div>
				</div>
				<div class="clear"></div>
				<?php if (get_option('comicpress-enable-storyline-support') == 1) { ?>
					<ul class="storyline-cats"><li class="storyline-root"><?php the_category(' &raquo; </li><li>', multiple) ?></li></ul>
				<?php } ?>
				<div class="comicdate">
					<div class="date"><span><?php the_time('M') ?></span> <?php the_time('d') ?></div>
				</div>
				<h2><a href="<?php the_permalink(); ?>" rel="bookmark" title="Permanent Link to <?php the_title(); ?>"><?php the_title(); ?></a></h2>
				By <?php the_author(); ?> on <?php the_time('F jS, Y'); ?> <?php edit_post_link('Edit Post', ' [ ', ' ] '); ?> <br />
				Posted in: <?php the_category(','); ?>
				<?php if(function_exists('the_ratings')) { the_ratings(); } ?> 
				<div class="entry">
					<?php the_content('&darr; Read the rest of this entry...') ?>
				</div>
				<?php the_transcript('styled') ?>
				<div class="tags">
					<?php the_tags('&#9492; Tags: ', ', ', ''); ?> 
				</div>
				<div class="comment-link">
					<?php if ('open' == $post->comment_status) { comments_popup_link('&ldquo;Comment&rdquo;', '&ldquo;1 Comment&rdquo;', '&ldquo;% Comments&rdquo;'); } ?>
				</div>
				<br class="clear-margins" />
			</div>
			<div class="post-comic-foot"></div>
		<?php endwhile; ?>

		<div id="blogheader"><!-- This area can be used for a heading above your main page blog posts --></div>

<?php } else { ?>

	<div id="content" class="narrowcolumn">

<?php } ?>
	
	<?php $temp = $wp_query; $wp_query= null; $wp_query = new WP_Query(); $wp_query->query('showposts='.$blog_postcount.'&cat=-'.$comiccat.'&paged='.$paged);
 while ($wp_query->have_posts()) : $wp_query->the_post() ?>
		<div class="post-head"></div>
		<div class="post" id="post-<?php the_ID() ?>">
			<div class="comicdate">
				<div class="date"><span><?php the_time('M') ?></span> <?php the_time('d') ?></div>
			</div>
			<h2><a href="<?php the_permalink(); ?>" rel="bookmark" title="Permanent Link to <?php the_title(); ?>"><?php the_title(); ?></a></h2>
			By <?php the_author(); ?> on <?php the_time('F jS, Y') ?> <?php edit_post_link('Edit Post', ' [ ', ' ] '); ?> <br />
			Posted in: <?php the_category(','); ?>
			<?php if(function_exists('the_ratings')) { the_ratings(); } ?> 
			<div class="entry">
				<?php the_content('&darr; Read the rest of this entry...') ?>
			</div> 
			<div class="tags">
				<?php the_tags('&#9492; Tags: ', ', ', ''); ?>
			</div>
			<div class="comment-link">
				<?php if ('open' == $post->comment_status) { comments_popup_link('&ldquo;Comment&rdquo;', '&ldquo;1 Comment&rdquo;', '&ldquo;% Comments&rdquo;'); } ?>
			</div>
			<div class="clear-margins"><br /></div>
		</div>
		<div class="post-foot"></div>
	<?php endwhile; ?>

  <?php if(function_exists('wp_page_numbers')) { wp_page_numbers(); } else { ?>
    <div class="pagenav">
      <div class="pagenav-right"><?php previous_posts_link('Newer Entries &uarr;') ?></div>
      <div class="pagenav-left"><?php next_posts_link('&darr; Previous Entries') ?></div>
      <div class="clear"></div>
    </div>	
  <?php } ?>
	<?php $wp_query = null; $wp_query = $temp ?>

</div>

<?php get_sidebar(); ?>

<?php get_footer() ?>