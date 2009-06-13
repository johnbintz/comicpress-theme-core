<div id="sidebar">

	<ul>

		<?php if ( !function_exists('dynamic_sidebar') || !dynamic_sidebar() ) : ?>    

			<?php widget_comicpress_comic_bookmark() ?>
		
			<li>
				<?php get_calendar(); ?>
			</li>

			<li>
				<h2>Menu</h2>
				<ul>
					<?php wp_list_pages('title_li=' ); ?>
				</ul>
			</li>

			<?php widget_comicpress_latest_comics(); ?>

			<li>
				<h2>Monthly Archives</h2>
				<ul>
					<?php wp_get_archives('type=monthly'); ?>
				</ul>
			</li>

			<?php wp_list_categories('title_li=<h2>Categories</h2>'); ?>

			<?php wp_list_bookmarks(); ?>

			<li>
				<?php include (TEMPLATEPATH . '/searchform.php'); ?>
			</li>

		<?php endif; ?>

	</ul>

</div>