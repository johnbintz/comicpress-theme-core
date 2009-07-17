<?php
/*
Template Name: Buy Print
Template Author: Philip M. Hofer (Frumph)
Template URL: http://webcomicplanet.com/forum/
Template Author Email: philip@frumph.net
*/
    get_header();
    if (isset($_REQUEST['comic'])) $comicnum = $_REQUEST['comic'];
?>

<?php get_header() ?>

<div id="content" class="narrowcolumn">
	<?php if (!empty($comicnum)): ?>
		<?php $temppost = $post; ?>
		<?php $post = & get_post( $comicnum ); ?>
		<div class="post-page-head"></div>
		<div class="post-page">
			<div style="float:right;">
				<br />
				<img src="<?php bloginfo('stylesheet_directory'); ?>/images/paypal.png" alt="Powered by Paypal" /><br />
			</div>
			<div style="float:left;">
				<h2 class="pagetitle">Buy Print!</h2>
			</div>
			<div class="clear"></div>
			Comic ID - #<?php echo $comicnum; ?><br />
			Title: 	<?php echo the_title(); ?><br />
			<br />
			<?php $post = & get_post( $comicnum ); ?>
			<center>
			<img src="<?php echo the_comic_archive(); ?>" alt="<?php the_title(); ?>" title="<?php the_title(); ?>" width="<?php echo $archive_comic_width; ?>" /><br />
			<br />
			<table>
			<tr>
				<td align="left" valign="center">
						<form target="paypal" action="https://www.paypal.com/cgi-bin/webscr" method="post">
						<input type="hidden" name="add" value="1">
						<input type="hidden" name="cmd" value="_cart">
						<input type="hidden" name="shipping2" value="5.75">
						<input type="hidden" name="cn" value="Special Instructions (optional)">
						<input type="hidden" name="cancel_return" value="http://comic.frumph.net/shop/">
						<input type="hidden" name="item_name" value="Comic Strip">
						<input type="hidden" name="notify_url" value="http://comic.frumph.net/shop/">
						<input type="hidden" name="page_style" value="Frumph Agencies">
						<input type="hidden" name="return" value="http://comic.frumph.net/shop/">
						<input type="hidden" name="amount" value="23.00">
						<input type="hidden" name="item_number" value="Comic ID (<?php echo $comicnum; ?>) - <?php echo the_title(); ?>">
						<input type="hidden" name="business" value="frumph_dragon@yahoo.com">
						<input type="hidden" name="shipping" value="4.75">
						US/Canada<br>
						$23 + $4.75 shipping<br />
						<input type="image" src="<?php bloginfo('stylesheet_directory'); ?>/images/buynow_paypal.png" name="submit32" alt="Make payments with PayPal - it's fast, free and secure!" /> 
						</form>
				</td>
				<td align="left" valign="center">
					<form target="paypal" action="https://www.paypal.com/cgi-bin/webscr" method="post">
						<input type="hidden" name="add" value="1">
						<input type="hidden" name="cmd" value="_cart">
						<input type="hidden" name="shipping2" value="20.00">
						<input type="hidden" name="cn" value="Special Instructions (optional)">
						<input type="hidden" name="cancel_return" value="http://comic.frumph.net/shop/">
						<input type="hidden" name="item_name" value="Comic Strip">
						<input type="hidden" name="notify_url" value="http://comic.frumph.net/shop/">
						<input type="hidden" name="page_style" value="Frumph Agencies">
						<input type="hidden" name="return" value="http://comic.frumph.net/shop/">
						<input type="hidden" name="amount" value="23.00">
						<input type="hidden" name="item_number" value="Comic ID (<?php echo $comicnum; ?>) - <?php echo the_title(); ?>">
						<input type="hidden" name="business" value="frumph_dragon@yahoo.com">
						<input type="hidden" name="shipping" value="12.00">
						International<br>
						$23 + $12.00 shipping<br />
						<input type="image" src="<?php bloginfo('stylesheet_directory'); ?>/images/buynow_paypal.png" name="submit32" alt="Make payments with PayPal - it's fast, free and secure!" />
					</form>
				</td>
			</tr>
			</table>
			<br />
			</center>
			The purchase of this strip is based on availability.   A Print of this strip is what you are purchasing.<br />
			<br />
			<?php $post = $temppost; ?>
			<div class="clear"></div>
		</div>
	<?php else: ?>
	    <?php if (have_posts()) : while (have_posts()) : the_post() ?>
		    <div class="post-page-head"></div>
		    <div class="post-page" id="post-<?php the_ID() ?>">
			    <h2 class="pagetitle"><?php the_title() ?></h2>
			    <div class="entry">
				    <?php the_content() ?>
				    <?php wp_link_pages(array('before' => '<p><strong>Pages:</strong> ', 'after' => '</p>', 'next_or_number' => 'number')) ?>
			    </div>
			    <?php edit_post_link('Edit this page.', '<p>', '</p>') ?>
			    <br class="clear-margins" />
		    </div>
		    <div class="post-page-foot"></div>
	    <?php endwhile; endif; ?>
        <?php comments_template(); ?>
	<?php endif; ?>	
</div>

<?php get_sidebar(); ?>

<?php get_footer(); ?>