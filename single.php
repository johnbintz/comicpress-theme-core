<?php get_header(); $first_comic = get_first_comic_permalink(); $last_comic = get_last_comic_permalink() ?>

<?php while (have_posts()) : the_post(); if (in_comic_category()) { ?>
	<div id="comic-head"></div>
	<div id="comic">
		<img src="<?php the_comic() ?>" alt="<?php the_title() ?>" title="<?php the_hovertext() ?>" />
	</div>
	<div id="comic-foot"></div>
	<?php // if (function_exists('buy_this_comic')) { buy_this_comic(); } ?>		
	<?php // if (function_exists('comic_navigation')) { comic_navigation(); } ?>
<?php } endwhile; ?>

<div id="content" class="narrowcolumn">
	
	<?php if (have_posts()) : while (have_posts()) : the_post() ?>
	
		<?php if (in_comic_category()) { ?>
			<div class="post-comic-head"></div>
			<div class="post-comic" id="post-<?php the_ID() ?>">
				<div class="nav">
					<?php if ( get_permalink() != $first_comic ) { ?><div class="nav-first"><a href="<?php echo $first_comic ?>">&lsaquo;&lsaquo; First</a></div><?php } ?>
					<div class="nav-previous"><?php previous_comic_link('%link', '&lsaquo; Previous') ?></div>
					<div class="nav-next"><?php next_comic_link('%link', 'Next &rsaquo;') ?></div>
					<?php if ( get_permalink() != $last_comic ) { ?><div class="nav-last"><a href="<?php echo $last_comic ?>">Last &rsaquo;&rsaquo;</a></div><?php } ?>
				</div>
				<div class="clear"></div>
				<?php if (get_option('comicpress-enable-storyline-support') == 1) { ?>
					<ul class="storyline-cats"><li class="storyline-root"><?php the_category(' &raquo; </li><li>', 'multiple') ?></li></ul>
				<?php } ?>
				<div class="comicdate">
					<div class="date"><span><?php the_time('M') ?></span> <?php the_time('d') ?></div>
				</div>
				<h2><?php the_title(); ?></h2>
				By <?php the_author(); ?> on <?php the_time('F jS, Y') ?> <?php edit_post_link('Edit Post', ' [ ', ' ] ') ?> <br />
				Posted in: <?php the_category(','); ?>
				<?php if(function_exists('the_ratings')) { the_ratings(); } ?> 
				<div class="entry">
					<?php the_content() ?>
					<?php the_transcript('styled') ?>			
					<div class="tags">
						<?php the_tags('&#9492; Tags: ', ', ', '');  ?> 
					</div>
					<?php wp_link_pages(array('before' => '<p><strong>Pages:</strong> ', 'after' => '</p>', 'next_or_number' => 'number')) ?>
				</div>
				<br class="clear-margins" />
			</div>
			<div class="post-comic-foot"></div>
			
		<?php } else { ?>
		
			<div class="post-head"></div>
			<div class="post" id="post-<?php the_ID() ?>">
				<div class="nav-blog">
					<div class="nav-blog-previous"><?php previous_post_link('%link','&lsaquo; Previous', TRUE) ?></div>
					<div class="nav-blog-next"><?php next_post_link('%link','Next &rsaquo;', TRUE) ?></div>
				</div>
				<div class="clear"></div>
				<div class="comicdate">
					<div class="date"><span><?php the_time('M') ?></span> <?php the_time('d') ?></div>
				</div>
				<h2><?php the_title(); ?></h2>
				By <?php the_author(); ?> on <?php the_time('F jS, Y') ?> <?php edit_post_link('Edit Post', ' [ ', ' ] '); ?> <br />
				Posted in: <?php the_category(','); ?>
				<?php if(function_exists('the_ratings')) { the_ratings(); } ?> 
				<div class="entry">
					<?php the_content() ?>
					<?php the_transcript('styled') ?>			
					<div class="tags">
						<?php the_tags('&#9492; Tags: ', ', ', ''); ?> 
					</div>
					<?php wp_link_pages(array('before' => '<p><strong>Pages:</strong> ', 'after' => '</p>', 'next_or_number' => 'number')) ?>
				</div>
				<br class="clear-margins" />
			</div>
			<div class="post-foot"></div>
			
		<?php } ?>

		<?php comments_template() ?>
		
	<?php endwhile; else: ?>
		
		<div class="post-head"></div>
		<div class="post">
			<p>Sorry, no posts matched your criteria.</p>
			<br class="clear-margins" />
		</div>
		<div class="post-foot"></div>
	
	<?php endif; ?>
	
</div>

<?php get_sidebar(); ?>

<?php get_footer() ?>