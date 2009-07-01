<?php
/*
Template Name: Comic Calendar Archive
*/
?>

<?php get_header() ?>

<style>
	.cpcal-month {
		width: 157px;
		height: 138px;
		padding: 5px 0 5px 5px;
		margin: 0 8px 8px 0;
		float: left;
		border: 1px solid #000;
		}
	.cpcal-monthtitle {
		width: 154px;
		height: 16px;
		padding: 0 0 5px 0;
		text-align: center;
		font-family: 'Georgia', serif;
		font-variant: small-caps;
		font-size: 16px;
		line-height: 16px;
		letter-spacing: -1px;
		}
	.cpcal-dayletter {
		width: 20px;
		height: 15px;
		margin: 0 2px 2px 0;
		display: block;
		float: left;
		text-align: center;
		font-size: 10px;
		line-height: 13px;
		}
	.cpcal-day {
		width: 18px;
		height: 13px;
		margin: 0 2px 2px 0;
		display: block;
		float: left;
		text-align: center;
		font-size: 10px;
		line-height: 13px;
		border: 1px solid #000;
		}
		.cpcal-day a {
			width: 18px;
			height: 13px;
			line-height: 13px;
			display: block;
			font-size: 12px;
			font-weight: bold;
			}
			.cpcal-day a:hover {
				color: #fff;
				background: #000;
				}
</style>

<?php

$dayWidth = 22; //set to .cpcal-day total width in pixels including: width, left & right border, left & right margin, left & right padding

$archive_year = $_GET['archive_year']; if (is_null($archive_year)) { $archive_year = date('Y'); }

$firstDayMargins = array();
for ($i = 1; $i <= 12; ++$i) {
	$dateInfo = getdate(mktime(0,0,0,$i,1,$archive_year));
	$firstDayMargins[$i] = $dateInfo['wday'] * $dayWidth;
}

$tempPost = $post;
$comicArchive = new WP_Query(); $comicArchive->query('&showposts=1000&cat='.get_all_comic_categories_as_cat_string().'&year='.$archive_year);
while ($comicArchive->have_posts()) : $comicArchive->the_post();
	$calTitle = get_the_title();
	$calLink = get_permalink();
	$calDay = get_the_time('j');
	$calMonth = get_the_time('F');
	$calComic[$calMonth.$calDay] = array('link' => $calLink, 'title' => $calTitle);
endwhile;
$post = $tempPost;

function leapYear($yr) {
	if ($yr % 4 != 0) {
		return 28;
	} else {
		if ($yr % 100 != 0) {
			return 29;
		} else {
			if ($yr % 400 != 0) {
				return 28;
            } else {
				return 29;
			}
		}
	}
}
$leapYear = leapYear($archive_year);

$month['1'] = array('month' => 'January', 'days' => '31');
$month['2'] = array('month' => 'February', 'days' => $leapYear);
$month['3'] = array('month' => 'March', 'days' => '31');
$month['4'] = array('month' => 'April', 'days' => '30');
$month['5'] = array('month' => 'May', 'days' => '31');
$month['6'] = array('month' => 'June', 'days' => '30');
$month['7'] = array('month' => 'July', 'days' => '31');
$month['8'] = array('month' => 'August', 'days' => '31');
$month['9'] = array('month' => 'September', 'days' => '30');
$month['10'] = array('month' => 'October', 'days' => '31');
$month['11'] = array('month' => 'November', 'days' => '30');
$month['12'] = array('month' => 'December', 'days' => '31');
		
?>

<div id="content" class="narrowcolumn">

	<div class="post-page-head"></div>
	<div class="post-page">

		<h2 class="pagetitle"><span class="archive-year"><?php echo $archive_year ?></span> <?php the_title() ?></h2>
		
		<div class="entry">
			<?php while (have_posts()) : the_post(); the_content(); endwhile; ?>
		</div>

		<div class="archive-yearlist">| 
			<?php $years = $wpdb->get_col("SELECT DISTINCT YEAR(post_date) FROM $wpdb->posts WHERE post_status = 'publish' ORDER BY post_date ASC");
				foreach ( $years as $year ) {
				if ($year != (0) ) { ?>	
				<a href="<?php echo add_query_arg('archive_year', $year) ?>"><strong><?php echo $year ?></strong></a> |
			<?php } } ?>
		</div>
		
		<?php $i=1; while($i<=12) { ?>
			<div class="cpcal-month" id="<?php echo $month[$i]['month'] ?>">
				<div class="cpcal-monthtitle"><?php echo $month[$i]['month']." ".$archive_year ?></div>
				<?php foreach(array("S", "M", "T", "W", "T", "F", "S") as $dow) { ?>
					<div class="cpcal-dayletter"><?php echo $dow ?></div>		
				<?php } ?>
				<div class="clear"></div>
				<?php $day=1; while($day<=$month[$i]['days']) {
					if ($day == 1) { ?>
						<div style="width:<?php echo $firstDayMargins[$i]; ?>px;height:15px;float:left;"></div>
					<?php } ?>
					<div class="cpcal-day">
						<?php if (isset($calComic[$month[$i]['month'].$day])) { ?>
							<a href="<?php echo $calComic[$month[$i]['month'].$day]['link'] ?>" title="<?php echo $calComic[$month[$i]['month'].$day]['title'] ?>"><?php echo $day ?></a>
						<?php } else {
							echo $day." ";
						} ?>
					</div>
					<?php ++$day;
				}
				++$i ?>
			</div>
		<?php } ?>
		
		<br class="clear-margins" />

	</div>
	<div class="post-page-foot"></div>

</div>

<?php include(TEMPLATEPATH . '/sidebar.php') ?>

<?php get_footer() ?>