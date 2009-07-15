<div id="sidebar">
	<ul>
		<?php if ( !function_exists('dynamic_sidebar') || !dynamic_sidebar() ) { ?>

			<li><?php get_calendar(); ?></li>

			<li>
				<h2>Menu</h2>
				<ul>
					<?php wp_list_pages('title_li=' ); ?>
				</ul>
			</li>

			<li>
				<h2>Monthly Archives</h2>
				<ul>
					<?php wp_get_archives('type=monthly'); ?>
				</ul>
			</li>

			<?php wp_list_categories('title_li=<h2>Categories</h2>'); ?>
			<?php wp_list_bookmarks(); ?>

			<li><?php include (get_template_directory() . '/searchform.php'); ?></li>
		<?php } ?>
	</ul>
</div>