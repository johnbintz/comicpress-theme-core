<?php
/*
Template Name: Comic Archive
*/

global $comicpress;



get_header();
?>

<div id="content" class="narrowcolumn">
    <div class="post-page-head"></div>
    <div class="post-page">
      <h2 class="pagetitle"><?php the_title() ?></h2>
      <div class="entry">
        <?php while (have_posts()) { the_post(); the_content(); } ?>
      </div>

      <?php 
        $years = $wpdb->get_col("SELECT DISTINCT YEAR(post_date) FROM $wpdb->posts WHERE post_status = 'publish' ORDER BY post_date DESC");
        foreach ( $years as $year ) {
          if (!empty($year)) { ?>
            <h3><?php echo $year ?></h3>
            <table class="month-table">
              <?php 
                $comic_archive = new WP_Query(); 
                $comic_archive->query('showposts=10000&cat=' . $comicpress->get_all_comic_categories_as_cat_string() . '&year='.$year);
                while ($comic_archive->have_posts()) {
                  $comic_archive->the_post(); ?>
                  <tr>
                    <td class="archive-date"><?php the_time('M j') ?></td>
                    <td class="archive-title"><a href="<?php echo get_permalink($post->ID) ?>" rel="bookmark" title="Permanent Link: <?php the_title() ?>"><?php the_title() ?></a></td>
                  </tr>
                <?php }
              ?>
            </table>
          <?php }
        }
      ?>

    <br class="clear-margins" />
    </div>
    <div class="post-page-foot"></div>
</div>

<?php get_sidebar() ?>

<?php get_footer() ?>