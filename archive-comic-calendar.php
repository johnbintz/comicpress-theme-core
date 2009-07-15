<?php
/*
Template Name: Comic Calendar Archive
*/

global $comicpress;

$comicpress->additional_stylesheets[] = '/style/archive-comic-calendar.css';
?>

<?php get_header() ?>

<?php

$dayWidth = 22; //set to .cpcal-day total width in pixels including: width, left & right border, left & right margin, left & right padding

$archive_year = !empty($_GET['archive_year']) ? $_GET['archive_year'] : date('Y');

$temp_post = $post;
$comic_archive_posts = array();
$comic_archive_query = new WP_Query();
$comic_archive_query->query('&showposts=1000&cat=' . $comicpress->get_all_comic_categories_as_cat_string() . '&year='.$archive_year);
while ($comic_archive_query->have_posts()) {
  $comic_archive_query->the_post();
  $comic_archive_posts[get_the_time('d-m')] = array(
    'link' => get_permalink(),
    'title' => get_the_title()
  );
}
$post = $temp_post;

$days_of_week = array();
for ($i = 0; $i < 7; ++$i) {
  $days_of_week[] = substr(date("l", gmmktime(0, 0, 0, 7, 13 + $i, 2009)), 0, 1);
}

?>

<div id="content" class="narrowcolumn">
	<div class="post-page-head"></div>
	<div class="post-page">
    <?php while (have_posts()) { the_post(); ?> 
      <h2 class="pagetitle"><span class="archive-year"><?php echo $archive_year ?></span> <?php the_title() ?></h2>
		
      <div class="entry"><?php the_content() ?></div>
    <?php } ?>

		<div class="archive-yearlist">| 
			<?php 
        $years = $wpdb->get_col("SELECT DISTINCT YEAR(post_date) FROM $wpdb->posts WHERE post_status = 'publish' ORDER BY post_date ASC");
				foreach ( $years as $year ) {
          if ($year != 0 ) { ?>	
            <a href="<?php echo add_query_arg('archive_year', $year) ?>"><strong><?php echo $year ?></strong></a> |
          <?php }
        }
      ?>
		</div>
		
		<?php for ($month = 1; $month <= 12; ++$month) {
      $current_time = mktime(0, 0, 0, $month, 1, $archive_year); ?>
			<div class="cpcal-month" id="<?php echo strtolower(date("F", $current_time)) ?>">
				<div class="cpcal-monthtitle"><?php echo date("F", $current_time) . " " . $archive_year ?></div>
				
        <?php foreach($days_of_week as $dow) { ?>
					<div class="cpcal-dayletter"><?php echo $dow ?></div>		
				<?php } ?>
        
				<div class="clear"></div>
        
        <?php for ($day = 1, $end_day = date("t", $current_time); $day < $end_day; ++$day) {
          $day_key = date('d-m', $current_time); ?>
					<?php if ($day == 1) { ?>
						<div style="width:<?php echo date('w', $current_time) * $dayWidth ?>px;height:15px;float:left;"></div>
					<?php } ?>
          
					<div class="cpcal-day">
						<?php if (isset($comic_archive_posts[$day_key])) { ?>
							<a href="<?php echo $comic_archive_posts[$day_key]['link'] ?>" title="<?php echo $comic_archive_posts[$day_key]['title'] ?>"><?php echo $day ?></a>
						<?php } else {
							echo $day . " ";
						} ?>
					</div>
					<?php
          $current_time += 86400;
				} ?>
			</div>
		<?php } ?>
		
		<br class="clear-margins" />

	</div>
	<div class="post-page-foot"></div>

</div>

<?php include(dirname(__FILE__) . '/sidebar.php') ?>

<?php get_footer() ?>