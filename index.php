<?php
  global $comicpress, $post, $nav_comics;

  $dbi = ComicPressDBInterface::get_instance();
  var_dump($dbi->get_terminal_post_in_category(3, true));
  var_dump($dbi->get_terminal_post_in_category(3, false));
  exit(0);

  comicpress_init();

  $nav_comics = array();
  $t = $post;
  $post = $nav_comics['last'];  
  setup_postdata($post);

  ob_start();

  if (!is_paged()) { include_partial('index-display-comic'); }

  $comic = ob_get_clean();

  ob_start();

  if (!is_paged() && ($comicpress->comicpress_options['comic_space'] == "comic_only")) { include_partial('index-comic-post'); }

  include_partial('index-blog-header');

  $index_posts_query = $comicpress->get_index_blog_posts_query();

  while ($index_posts_query->have_posts()) {
    $index_posts_query->the_post(); 
    include_partial('index-blog-post');
  }

  $content = ob_get_clean();
  $post = $t;
  
  include(get_template_directory() . '/layouts/' . $comicpress->comicpress_options['layout']);
?>