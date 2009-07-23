<?php
/*
Layout Name: Classic
Sidebars: right
*/

?>

<?php get_header() ?>

<?php echo $comic ?>

<div id="content" class="narrowcolumn">
  <?php echo $content ?>
</div>

<?php get_sidebar('right') ?>

<?php get_footer() ?>
