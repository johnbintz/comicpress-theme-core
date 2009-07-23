<?php
/*
Layout Name: Three Column
Sidebars: left,right
*/

?>

<?php get_header() ?>

<?php echo $comic ?>

<?php get_sidebar('left') ?>

<div id="content" class="narrowcolumn">
  <?php echo $content ?>
</div>

<?php get_sidebar('right') ?>

<?php get_footer() ?>
