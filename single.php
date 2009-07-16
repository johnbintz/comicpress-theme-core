<?php 
  global $comicpress;

  get_header();

  if (have_posts()) {
    the_post();
    if (in_comic_category()) { include_partial('single-display-comic') }
  }
  rewind_posts();

  $nav_comics = $comicpress->get_nav_comics();
?>

<div id="content" class="narrowcolumn">
	<?php 
    if (have_posts()) {
      while (have_posts()) { the_post();
        if (in_comic_category()) {
          if ($comicpress->comicpress_options['comic_space'] == "comic_only")) {
            include(get_template_directory() . '/partials/single-comic-post.inc');
          }
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