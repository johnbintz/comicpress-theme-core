<?php
/*
Template Name: Comic Storyline Archive
*/
?>

<?php get_header() ?>

<style>
	#storyline, #storyline ul {
		padding: 0;
		margin: 0;
		list-style: none;
		}
	#storyline li {
		padding: 0;
		margin: 0;
		}
	#storyline li img {
		height: 50px;
		display: none;
		}
	#storyline li li img {
		display: block;
		float: right;
		padding: 0 0 0 10px;
		}
	#storyline ul ul {
		margin: 0 0 0 20px;
		}
	#storyline li li .storyline-title {
		font-size: 24px;
		font-weight: bold;
		display: block;
		color: #000;
		}
	#storyline li li .storyline-title:hover {
		color: #900;
		}
	#storyline li li li a.storyline-title {
		font-size: 18px;
		}
	#storyline li li li li a.storyline-title {
		font-size: 14px;
		}
	.storyline-description {
		font-size: 11px;
		}
	.storyline-foot {
		clear: both;
		margin: 0 0 10px 0;
		height: 10px;
		border-bottom: 4px solid #000;
		}
	#storyline li li .storyline-foot {
		border-bottom: 2px solid #000;
		}
	#storyline li li li .storyline-foot {
		border-bottom: 1px solid #000;
		}
</style>


<div id="content" class="narrowcolumn">

	<div class="post-page-head"></div>
    <?php while (have_posts()) : the_post() ?>
      <div class="entry">
        <h2 class="pagetitle"><?php the_title() ?></h2>
        <?php the_content(); ?>
      </div>
    <?php endwhile; ?>
		<ul id="storyline" class="level-0">
			<?php if (get_option('comicpress-enable-storyline-support') == 1) {
				if (($result = get_option("comicpress-storyline-category-order")) !== false) {
					$categories_by_id = get_all_category_objects_by_id();
					$current_depth = 0;
					$storyline_root = " class=\"storyline-root\"";
					foreach (explode(",", $result) as $node) {
						$parts = explode("/", $node);
						$target_depth = count($parts) - 2;
						$category_id = end($parts);
						$category = $categories_by_id[$category_id];
						$description = $category->description;
						$first_comic_in_category = get_terminal_post_in_category($category_id);
						$first_comic_permalink = get_permalink($first_comic_in_category->ID);
						$archive_image = null;
						foreach (array("archive", "rss", "comic") as $type) {
							if (($requested_archive_image = get_comic_url("archive", $first_comic_in_category)) !== false) {
								$archive_image = $requested_archive_image; break;
							}
						}
						if ($target_depth < $current_depth) {
							echo str_repeat("</ul></li>", ($current_depth - $target_depth));
						}
						if ($target_depth > $current_depth) {
							for ($i = $current_depth; $i < $target_depth; ++$i) {
								$next_i = $i + 1;
								echo "<li><ul class=\"level-${next_i}\">";
							}
						} ?>
						
						<li id="storyline-<?php echo $category->category_nicename ?>"<?php echo $storyline_root; $storyline_root = null ?>>
							<a href="<?php echo get_category_link($category_id) ?>" class="storyline-title"><?php echo $category->cat_name ?></a>
							<div class="storyline-description">
								<?php if (!empty($description)) { ?>
									<?php echo $description ?>
								<?php } ?>
								<?php if (!empty($first_comic_in_category)) { ?>
									Begins with &ldquo;<a href="<?php echo $first_comic_permalink ?>"><?php echo $first_comic_in_category->post_title ?></a>&rdquo;.
								<?php } ?>
							</div>
							<div class="storyline-foot"></div>
						</li>
						
						<?php $current_depth = $target_depth;
					}
					if ($current_depth > 0) {
						echo str_repeat("</ul></li>", $current_depth);
					}
				}
			} else { ?>
				<li><h3>Storyline Support is not currently enabled on this site.</h3><br /><br /><strong>Note to the Administrator:</strong><br /> To enable storyline support and manage storyline categories make sure you are running the latest version of the <a href="http://wordpress.org/extend/plugins/comicpress-manager/">ComicPress Manager</a> plugin and check your storyline settings from it's administration menu.</h3></li>
			<?php } ?>
		</ul>
		<br class="clear-margins" />
	</div>
	<div class="post-page-foot"></div>
	
</div>

<?php include(TEMPLATEPATH . '/sidebar.php') ?>

<?php get_footer() ?>