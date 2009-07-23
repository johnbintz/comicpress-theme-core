<?php
  global $comicpress;

  comicpress_init();
  
  ob_start();
  
  ?>
	<div class="post-page-head"></div>
	<div class="post-page">
		<h2 class="pagetitle">Page Not Found</h2>
		<p><a href="<?php bloginfo('url') ?>">Click here to return to the home page</a> or try a search:</p>
		<p><?php include (get_template_directory() . '/searchform.php') ?></p>
		<br class="clear-margins" />
	</div>
	<div class="post-page-foot"></div>
  <?php
  
  $content = ob_get_clean();
  
  include(get_template_directory() . '/layouts/' . $comicpress->comicpress_options['layout']);
?>