<div class="clear"></div><!-- Clears floated columns and sidebars -->

<div id="footer">
	<center>
		<?php if (function_exists('the_project_wonderful_ad')) { the_project_wonderful_ad('footer'); } ?>
	</center>
	<p>
		<?php bloginfo('name') ?> is powered by <a href="http://wordpress.org/">WordPress</a> with <a href="http://comicpress.org/">ComicPress</a>
		| Subscribe: <a href="<?php bloginfo('rss2_url') ?>">RSS Feed</a> | <a href="#outside" onclick="scrollup(); return false;">Return to Top &nbsp;</a><br />
		<a href="http://english-156752579142.spampoison.com"><img src="http://pics4.inxhost.com/images/sticker.gif" border="0" width="80" height="15"/></a>
		<!-- <?php echo get_num_queries() ?> queries. <?php timer_stop(1) ?> seconds. -->
	</p>
</div>
 
</div><!-- Ends "page" -->
 
<?php wp_footer() ?>

</body>

</html>