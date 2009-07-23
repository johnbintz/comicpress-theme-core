<?php
/*
Layout Name: Classic
*/

if (function_exists('register_sidebar')) {
  register_sidebar('left');
  register_sidebar('right');
}

?>

<?php get_header() ?>

<?php echo $comic ?>

<?php get_sidebar('left') ?>

<div id="content" class="narrowcolumn">
  <?php echo $content ?>
</div>

<?php get_sidebar('right') ?>

<?php get_footer() ?>
