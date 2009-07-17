<?php

function buy_this_comic() {
	$buythiscomic = get_post_meta( get_the_ID(), "buythiscomic", true );
	if ( $buythiscomic !== 'sold' ) {
		echo '<div class="buythis"><form method="post" action="/shop/">';
		echo '<input type="hidden" name="comic" value="'.get_the_ID().'" />';
		echo '<button class="buythisbutton" type="submit" value="submit" name="submit"></button></form></div>';
		echo '<div class="clear"></div>';
		
	}
}
?>