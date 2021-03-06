<?php
/*
Template Name: Comic Year Archive
*/
?>

<?php get_header() ?>

<?php 
 if (isset($_GET['archive_year'])) {
   $archive_year = $_GET['archive_year'];
 } else {
	 $latest_comic = get_terminal_post_in_category(get_all_comic_categories_as_cat_string(),false);
	 $archive_year = get_post_time('Y', false, $latest_comic, true);
	 if (empty($archive_year)) $archive_year = date('Y');
 }  
?>

<style>
	.archive-date {
		padding: 0 5px 0 0;
		font-size: 11px;
		white-space: nowrap;
		}
	.archive-title {
		padding: 2px 5px;
		line-height: 11px;
		border-left: 1px solid #000;
		}
	.month-table{
		border-collapse: collapse;
		margin: 0 0 20px 0;
		}
</style>

<div id="content" class="narrowcolumn">

	<div class="post-page-head"></div>
	<div class="post-page">
    <?php while (have_posts()) : the_post() ?>
      <div class="entry">
        <h2 class="pagetitle"><span class="archive-year"><?php echo $archive_year; ?></span> <?php the_title() ?></h2>
        <?php the_content(); ?>
      </div>
    <?php endwhile; ?>

		<div class="archive-yearlist">| 
			<?php $years = $wpdb->get_col("SELECT DISTINCT YEAR(post_date) FROM $wpdb->posts WHERE post_status = 'publish' ORDER BY post_date ASC");
				foreach ( $years as $year ) {
				if ($year != (0) ) { ?>	
				<a href="<?php echo add_query_arg('archive_year', $year) ?>"><strong><?php echo $year ?></strong></a> |
			<?php } } ?>
		</div>

		<table class="month-table">
			<?php $comicArchive = new WP_Query(); $comicArchive->query('showposts=10000&cat='.get_all_comic_categories_as_cat_string().'&year='.$archive_year);
			while ($comicArchive->have_posts()) : $comicArchive->the_post() ?>
				<tr><td class="archive-date"><?php the_time('M j') ?></td><td class="archive-title"><a href="<?php echo get_permalink($post->ID) ?>" rel="bookmark" title="Permanent Link: <?php the_title() ?>"><?php the_title() ?></a></td></tr>
			<?php endwhile; ?>
		</table>
		
		<br class="clear-margins" />

	</div>
	<div class="post-page-foot"></div>
	
</div>

<?php get_sidebar() ?>

<?php get_footer() ?>