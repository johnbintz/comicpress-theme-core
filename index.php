<?php
  global $comicpress;

  get_header();
?>

<?php if (!is_paged()) { ?>
  <div id="comic-head"></div>
  <div id="comic"><?php do_action('show_comic', null); ?></div>
  <div id="comic-foot"></div>
<?php } ?>
	
<div id="content" class="narrowcolumn">

<?php if (!is_paged()) {
  $nav_comics = $comicpress->get_nav_comics();

  $post = $nav_comics['last'];
  
  ?>
	<div class="post-comic-head"></div>
	<div class="post-comic">
		<div class="nav">
		  <?php if ($nav_comics['show_first']) { ?>
  			<div class="nav-first">
  			  <a href="<?php echo get_permalink($nav_comics['first']->ID) ?>" 
  			     title="<?php _e('Go to the first comic', 'comicpress') ?>">&lsaquo;&lsaquo; First</a>
  			</div>
  	  <?php } ?>
			<?php if ($nav_comics['show_previous']) { ?>
  			<div class="nav-previous">
  			  <a href="<?php echo get_permalink($nav_comics['previous']->ID) ?>"
  			  title="<?php _e('Go to the previous comic', 'comicpress') ?>">&lsaquo; Previous</a>
  			</div>
  	  <?php } ?>
		</div>
		<div class="comicdate">
			<?php the_date('F jS, Y') ?>
		</div>
		<div class="clear"></div>
		<?php if (get_option('comicpress-enable-storyline-support') == 1) { ?>
			<ul class="storyline-cats"><li class="storyline-root"><?php the_category(' &raquo; </li><li>', multiple) ?></li></ul>
		<?php } ?>
		<h2>
			<a href="<?php get_permalink($last_comic_post->ID) ?>" rel="bookmark" title="Permanent Link to <?php the_title() ?>"><?php the_title() ?></a>
		</h2>
		<div class="entry">
			<?php the_content('&darr; Read the rest of this entry...') ?>
		</div>
		<?php the_transcript('styled') ?>
		<div class="tags">
			<?php the_tags('&#9492; Tags: ', ', ', ''); edit_post_link('Edit Post', ' [ ', ' ] ') ?> 
		</div>
		<div class="comment-link">
			<?php if ('open' == $post->comment_status) { comments_popup_link('&ldquo;Comment&rdquo;', '&ldquo;1 Comment&rdquo;', '&ldquo;% Comments&rdquo;'); } ?>
		</div>
		<br class="clear-margins" />
	</div>
	<div class="post-comic-foot"></div>
<?php } ?>

<div id="blogheader"><!-- This area can be used for a heading above your main page blog posts --></div>

<?php 
	$wp_query = new WP_Query();
	$wp_query->query(
	  'showposts=' . 
	  (int)$comicpress->comicpress_options['blogpost_count'] .
	  '&cat=-' . 
	  $comicpress->comicpress_options['comic_category_id'] .
	  '&paged=' . 
	  $paged
	);
	
  while ($wp_query->have_posts()) {
    $wp_query->the_post() ?>
		<div class="post-head"></div>
		<div class="post" id="post-<?php the_ID() ?>">
			<h3>
				<a href="<?php the_permalink() ?>" rel="bookmark" title="Permanent Link to <?php the_title() ?>"><?php the_title() ?></a>
			</h3>
			<div class="postdate">
				<?php the_time('F jS, Y') ?>
			</div>
			<div class="entry">
				<?php the_content('&darr; Read the rest of this entry...') ?>
			</div> 
			<div class="tags">
				<?php the_tags('&#9492; Tags: ', ', ', ''); edit_post_link('Edit Post', ' [ ', ' ] ') ?>
			</div>
			<div class="comment-link">
				<?php if ('open' == $post->comment_status) { comments_popup_link('&ldquo;Comment&rdquo;', '&ldquo;1 Comment&rdquo;', '&ldquo;% Comments&rdquo;'); } ?>
			</div>
			<div class="clear-margins"><br /></div>
		</div>
		<div class="post-foot"></div>
	<?php } ?>
	
	<div class="pagenav">
		<div class="pagenav-right"><?php previous_posts_link('Newer Entries &uarr;') ?></div>
		<div class="pagenav-left"><?php next_posts_link('&darr; Previous Entries') ?></div>
		<div class="clear"></div>
	</div>	
</div>

<?php include(dirname(__FILE__) . '/sidebar.php') ?>

<?php get_footer() ?>