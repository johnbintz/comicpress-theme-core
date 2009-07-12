<?php 
  global $comicpress;

  get_header();

  if (have_posts()) {
    the_post();
    if (in_comic_category()) { ?>
      <div id="comic-head"></div>
      <div id="comic"><?php do_action('show_comic'); ?></div>
      <div id="comic-foot"></div>
    <?php }
  }
  rewind_posts();

  $nav_comics = $comicpress->get_nav_comics();
?>

<div id="content" class="narrowcolumn">
	<?php if (have_posts()) { ?>
    <?php while (have_posts()) { the_post() ?>
      <?php if (in_comic_category()) { ?>
        <div class="post-comic-head"></div>
        <div class="post-comic" id="post-<?php the_ID() ?>">
          <div class="comicdate">
            <?php include(dirname(__FILE__) . '/partials/nav.inc') ?>
            <?php the_time('F jS, Y') ?>
          </div>
          <?php if (get_option('comicpress-enable-storyline-support') == 1) { ?>
            <ul class="storyline-cats"><li class="storyline-root"><?php the_category(' &raquo; </li><li>', 'multiple') ?></li></ul>
          <?php } ?>
          <h2><?php the_title() ?></h2>
          <div class="entry">
            <?php the_content() ?>
            <?php the_transcript('styled') ?>			
            <div class="tags">
              <?php the_tags('&#9492; Tags: ', ', ', ''); edit_post_link('Edit Post', ' [ ', ' ] ') ?> 
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
          <h2><?php the_title() ?></h2>
          <div class="postdate"><?php the_time('F jS, Y') ?></div>
          <div class="entry">
            <?php the_content() ?>
            <?php the_transcript('styled') ?>			
            <div class="tags">
              <?php the_tags('&#9492; Tags: ', ', ', ''); edit_post_link('Edit Post', ' [ ', ' ] ') ?> 
            </div>
            <?php wp_link_pages(array('before' => '<p><strong>Pages:</strong> ', 'after' => '</p>', 'next_or_number' => 'number')) ?>
          </div>
          <br class="clear-margins" />
        </div>
        <div class="post-foot"></div>
        
      <?php } ?>

      <?php comments_template() ?>
      
    <?php }
  } else { ?>
		
		<div class="post-head"></div>
		<div class="post">
			<p>Sorry, no posts matched your criteria.</p>
			<br class="clear-margins" />
		</div>
		<div class="post-foot"></div>
	
	<?php } ?>
	
</div>

<?php include(TEMPLATEPATH . '/sidebar.php') ?>

<?php get_footer() ?>