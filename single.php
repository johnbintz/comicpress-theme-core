 <?php 
  global $comicpress, $nav_comics;

  comicpress_get_header();

  ob_start();

  if (have_posts()) {
    the_post();
    if (in_comic_category()) { include_partial('single-display-comic'); }
  }
  rewind_posts();
  
  $comic = ob_get_clean();
  
  ob_start();

  $nav_comics = $comicpress->get_nav_comics();
  
  if (have_posts()) {
    while (have_posts()) { the_post();
      if (in_comic_category()) {
        if ($comicpress->comicpress_options['comic_space'] == "comic_only") {
          include_partial('single-comic-post');
        }
      } else {
        include_partial('single-blog-post');
      }
      
      comments_template();
    }
  } else {
    include_partial('single-no-matches');
  }
  
  $content = ob_get_clean();
  
  include(get_template_directory() . '/layouts/' . $comicpress->comicpress_options['layout']);  
?>