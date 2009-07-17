<?php

function comic_navigation() {
global $post, $wp_query;
    $temppost = $post;
    $temp_query = $wp_query;
    $temp_single = $wp_query->is_single;
	echo '<div id="comic_navi_wrapper">';
	echo '	<div id="comic_navi_prev">';
    $first_comic = get_first_comic_permalink();
    if ($first_comic != get_permalink()) {
		echo '		<a href="'.$first_comic.'" class="rollfirst" title="First">&nbsp;</a>';
	} else {
		echo '		<img src="'.get_bloginfo('stylesheet_directory').'/images/disabled_firstroll.png" alt="At First" style="float:left;" class="disabled_navi" />';
	}
    $wp_query->is_single = true;
    $prev_comic = get_permalink(get_adjacent_post(true, '', true));
    if (!empty($prev_comic) && (get_permalink() != $first_comic)) {
		echo '		<a href="'.$prev_comic.'" class="rollprev" title="Previous">&nbsp;</a>';
	} else { 
		echo '		<img src="'.get_bloginfo('stylesheet_directory').'/images/disabled_prevroll.png" alt="No Previous" style="float:left;" class="disabled_navi" />';
	}
    $wp_query->is_single = $temp_single;
	echo '	</div>';
	echo '	<div id="comic_navi_next">';
    $last_comic = get_last_comic_permalink();
    if ($last_comic != get_permalink()) {
        echo '		<a href="/" class="rolllast" title="Last">&nbsp;</a>';
    } else {
        echo '		<img src="'.get_bloginfo('stylesheet_directory').'/images/disabled_lastroll.png" alt="No Last" style="float: right;" class="disabled_navi" />';
    }
    $next_comic = get_permalink(get_adjacent_post(true, '', false));
    if (!empty($next_comic) && (get_permalink() != $last_comic)) {
		echo '		<a href="'.$next_comic.'" class="rollnext" title="Next">&nbsp;</a>';
	} else {
		echo '		<img src="'.get_bloginfo('stylesheet_directory').'/images/disabled_nextroll.png" alt="No Next" style="float: right;" class="disabled_navi" />';
	}
	echo '	</div>';
	echo '	<div class="clear"></div>';
	echo '</div>';
    $wp_query = $temp_query;
    $wp_query->is_single = $temp_single;
    $post = $temp_post;
    $temp_post = null;
    $temp_query = null;
    $temp_single = null;
}

?>