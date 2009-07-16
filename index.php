<?php
  global $comicpress;

  get_header();

  $nav_comics = $comicpress->get_nav_comics();
  $post = $nav_comics['last'];  
?>

<?php if (!is_paged()) { include_partial('index-display-comic'); } ?>
	
<div id="content" class="narrowcolumn">
  <?php if (!is_paged() && ($comicpress->comicpress_options['comic_space'] == "comic_only")) { include_partial('index-comic-post'); } ?>

  <?php include_partial('index-blog-header'); ?>

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
      include_partial('index-blog-post');
    }

    include_partial('index-blog-post');
  ?>
</div>

<?php include(get_template_directory() . '/sidebar.php') ?>

<?php get_footer() ?>