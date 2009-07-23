<div class="post-page-head"></div>
<div class="post-page" id="post-<?php the_ID() ?>">
	<h2 class="pagetitle">
		<a href="<?php echo get_permalink($post->post_parent) ?>" rev="attachment"><?php echo get_the_title($post->post_parent) ?></a>
	</h2>
	<div class="gallery-image">
		<a href="<?php echo wp_get_attachment_url($post->ID) ?>" target="_blank" title="Click for full size." ><img src="<?php echo wp_get_attachment_url($post->ID) ?>" alt="<?php the_title() ?>" /></a>
	</div>
	<div class="gallery-caption">
		<?php the_excerpt() ?>
	</div>
	<div class="imagenav-wrap">
		<div class="imagenav">
			<div class="imagenav-bg">
				<?php previous_image_link() ?>
			</div>
			<div class="imagenav-arrow">
				&lsaquo;
			</div>
			<div class="imagenav-link">
				<?php previous_image_link() ?>
			</div>
		</div>
		<div class="imagenav-center">
			<a href="<?php echo wp_get_attachment_url($post->ID) ?>" target="_blank" title="Click for full size." class="imagetitle"><?php the_title() ?></a><br />
			<a href="<?php echo get_permalink($post->post_parent) ?>" rev="attachment">&larr; Back to Gallery</a>
		</div>
		<div class="imagenav">
			<div class="imagenav-bg">
				<?php next_image_link() ?>
			</div>
			<div class="imagenav-arrow">
				&rsaquo;
			</div>
			<div class="imagenav-link">
				<?php next_image_link() ?>
			</div>
		</div>					
		<div class="clear"></div>
	</div>
	<?php the_content() ?>
	<br class="clear-margins" />
</div>
<div class="post-page-foot"></div>

<?php comments_template() ?>