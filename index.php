<?php
  global $comicpress;

  comicpress_get_header();

  $nav_comics = $comicpress->get_nav_comics();
  $post = $nav_comics['last'];  
?>

<?php if (!is_paged()) { include_partial('index-display-comic'); } ?>
	
<div id="content" class="narrowcolumn">
  <?php if (!is_paged() && ($comicpress->comicpress_options['comic_space'] == "comic_only")) { include_partial('index-comic-post'); } ?>

  <?php include_partial('index-blog-header'); ?>

  <?php 
    foreach ($comicpress->get_index_blog_posts() as $post) {
      include_partial('index-blog-post');
    }
  ?>
</div>

<?php include(get_template_directory() . '/sidebar.php') ?>

<?php get_footer() ?>