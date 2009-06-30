<?php
/*
Template Name: Comic Archive
*/
?>

<?php get_header() ?>

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

<div id="column">

		<div class="post-page-head"></div>
		<div class="post-page">
			<h2 class="pagetitle"><?php the_title() ?></h2>
			<div class="entry">
				<?php while (have_posts()) : the_post(); the_content(); endwhile; ?>
			</div>


	<?php $years = $wpdb->get_col("SELECT DISTINCT YEAR(post_date) FROM $wpdb->posts WHERE post_status = 'publish' ORDER BY post_date DESC");
	foreach ( $years as $year ) {
		if ($year != (0) ) { ?>
			<h3><?php echo $year ?></h3>
			<table class="month-table">
				<?php $comicArchive = new WP_Query(); $comicArchive->query('showposts=10000&cat='.get_all_comic_categories_as_cat_string().'&year='.$year);
				while ($comicArchive->have_posts()) : $comicArchive->the_post() ?>
					<tr><td class="archive-date"><?php the_time('M j') ?></td><td class="archive-title"><a href="<?php echo get_permalink($post->ID) ?>" rel="bookmark" title="Permanent Link: <?php the_title() ?>"><?php the_title() ?></a></td></tr>
				<?php endwhile; ?>
			</table>
	<?php } } ?>

			<br class="clear-margins" />
		
		</div>
		<div class="post-page-foot"></div>

</div>

<?php include(TEMPLATEPATH . '/sidebar.php') ?>

<?php get_footer() ?>