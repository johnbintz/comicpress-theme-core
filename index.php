<?php
  global $comicpress;

  get_header();

  $nav_comics = $comicpress->get_nav_comics();
  $post = $nav_comics['last'];  
?>

<?php if (!is_paged()) { ?>
  <div id="comic-head"></div>
  <div id="comic"><?php do_action('show_comic', null); ?></div>
  <div id="comic-foot"></div>
<?php } ?>
	
<div id="content" class="narrowcolumn">
  <?php if (!is_paged()) { include(dirname(__FILE__) . '/partials/index-comic-post.inc'); } ?>

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
      $wp_query->the_post();
      include(dirname(__FILE__) . '/partials/index-blog-post.inc');
    }

    include(dirname(__FILE__) . '/partials/index-blog-post.inc');
  ?>
</div>

<?php include(dirname(__FILE__) . '/sidebar.php') ?>

<?php get_footer() ?>