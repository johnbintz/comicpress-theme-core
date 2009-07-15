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
	<?php 
    if (have_posts()) {
      while (have_posts()) { the_post();
        if (in_comic_category()) {
          include(get_template_directory() . '/partials/single-comic-post.inc');
        } else {
          include(get_template_directory() . '/partials/single-blog-post.inc');
        }
        
        comments_template();
      }
    } else {
      include(get_template_directory() . '/partials/single-no-matches.inc');
    }
  ?>
</div>

<?php include(get_template_directory() . '/sidebar.php') ?>

<?php get_footer() ?>