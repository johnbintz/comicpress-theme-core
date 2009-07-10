<?php

include(dirname(__FILE__) . '/comicpress-config.php');

// If any errors occur while searching for a comic file, the error messages will be pushed into here.
$comic_pathfinding_errors = array();

// If ComicPress Manager is installed, use the date format defined there. If not, default to
// Y-m-d.. It's best to use CPM's date definition for improved operability between theme and plugin.

if (defined("CPM_DATE_FORMAT")) {
 define("CP_DATE_FORMAT", CPM_DATE_FORMAT);
} else {
 define("CP_DATE_FORMAT", "Y-m-d");
}

// If you want to run multiple comics on a single day, define your additional filters here.
// Example: you want to run an additional comic with the filename 2008-01-01-a-my-new-years-comic.jpg.
// Define an additional filter in the list below:
//
// $comic_filename_filters['secondary'] = "{date}-a*.*";
//
// Then show the second comic on your page by calling the_comic with your filter name (PHP tags munged
// to maintain valid file syntax):
//
// < ?php the_comic('secondary'); ? >
//
// Note that it's quite possible to slurp up the wrong file if your expressions are too broad.

$comic_filename_filters = array();
$comic_filename_filters['default'] = "{date}*.*";

require_once(dirname(__FILE__) . '/options.php');

// load all of the comic & non-comic category information
add_action('init', '__comicpress_init');

function __comicpress_init() {
  global $comicpress;
  
  foreach (glob(dirname(__FILE__) . '/classes/*.inc') as $file) {
    if (is_file($file)) { require_once($file); }
  }

  $comicpress = new ComicPress();
  $comicpress->init();

  if (is_dir($addons_dir = (dirname(__FILE__) . '/addons'))) {
    $entries = glob($addons_dir . '/*');
    if (is_array($entries)) {
      foreach ($entries as $entry) {
        if (is_dir($entry)) {
          $classname = basename($entry);
          if (file_exists($entry . "/${classname}.inc")) {
            require_once($entry . "/${classname}.inc");
            $classname = "ComicPressAddon${classname}";
            if (class_exists($classname)) {
              $addon = new $classname();
              $addon->init(&$comicpress);
              if (is_admin()) {
                add_action('admin_notices', array(&$addon, 'display_messages'));
                if (is_array($_POST['cp'])) {
                  if (isset($_POST['cp']['_nonce'])) {
                    if (wp_verify_nonce($_POST['cp']['_nonce'], 'comicpress')) {
                      $addon->handle_update();
                    }
                  }
                }
              }
            }
          }
        }
      }
    }
  }

  //get_all_comic_categories();
}

function the_comic_img_tag($url, $type, $additional_parameters = array()) {
  global $comicpress;
  
  echo $comicpress->get_comic_img_tag($url, $type, $additional_parameters);
}

/**
* Get the hyperlink to the first comic post in the database.
* @return string The hyperlink to the first comic post, or false.
*/
function get_first_comic_permalink() {
  global $comicpress;
  
  $terminal = $comicpress->get_first_comic();
  return !empty($terminal) ? get_permalink($terminal->ID) : false;
}

/**
* Get the hyperlink to the last comic post in the database.
* @return string The hyperlink to the first comic post, or false.
*/
function get_last_comic_permalink() {
  global $comicpress;
  
  $terminal = $comicpress->get_last_comic();
  return !empty($terminal) ? get_permalink($terminal->ID) : false;
}

/**
 * Get the link to the previous comic from the current one.
 */
function previous_comic_link($format, $link) {
  global $non_comic_categories;
  previous_post_link($format, $link, false, $non_comic_categories);
}

/**
 * Get the link to the next comic from the current one.
 */
function next_comic_link($format, $link) {
  global $non_comic_categories;
  next_post_link($format, $link, false, $non_comic_categories);
}

/**
 * Get the adjacent comic from the current one.
 * @param int $category The category to use.
 * @param boolean $next True if the next chronological comic should be retrieved.
 * @return array The WordPress post object for the comic post.
 */
function get_adjacent_comic($category, $next = false) {
}

/**
* Find a comic file in the filesystem.
* @param string $folder The folder name to search.
* @param string $override_post A WP Post object to use in place of global $post.
* @param string $filter The $comic_filename_filters to use.
* @return string The relative path to the comic file, or false if not found.
*/
function get_comic_path($folder = 'comic', $override_post = null, $filter = 'default') {
 global $post, $comic_filename_filters, $comic_folder, $archive_comic_folder, $rss_comic_folder, $comic_pathfinding_errors;

 if (isset($comic_filename_filters[$filter])) {
  $filter_to_use = $comic_filename_filters[$filter];
 } else {
  $filter_to_use = '{date}*.*';
 }

 switch ($folder) {
  case "rss": $folder_to_use = $rss_comic_folder; break;
  case "archive": $folder_to_use = $archive_comic_folder; break;
  case "comic": default: $folder_to_use = $comic_folder; break;
 }

 $post_to_use = (is_object($override_post)) ? $override_post : $post;
 $post_date = mysql2date(CP_DATE_FORMAT, $post_to_use->post_date);

 $filter_with_date = str_replace('{date}', $post_date, $filter_to_use);

 if (count($results = glob("${folder_to_use}/${filter_with_date}")) > 0) {
  return reset($results);
 }

 $comic_pathfinding_errors[] = sprintf(__("Unable to find the file in the <strong>%s</strong> folder that matched the pattern <strong>%s</strong>. Check your WordPress and ComicPress settings.", 'comicpress'), $folder, $filter_with_date);
 return false;
}

/**
* Find a comic file in the filesystem and return an absolute URL to that file.
* @param string $folder The folder name to search.
* @param string $override_post A WP Post object to use in place of global $post.
* @param string $filter The $comic_filename_filters to use.
* @return string The absolute URL to the comic file, or false if not found.
*/
function get_comic_url($folder = 'comic', $override_post = null, $filter = 'default') {
 if (($result = get_comic_path($folder, $override_post, $filter)) !== false) {
  return get_option('home') . '/' . $result;
 }

 return false;
}

// ComicPress Template Functions

function the_comic($filter = 'default') { echo get_comic_url('comic', null, $filter); }
	//The following is deprecated...
	function comic_display($filter = 'default') { echo get_comic_url('comic', null, $filter); }

function the_comic_archive($filter = 'default') { echo get_comic_url('archive', null, $filter); }
	//The following is deprecated...
	function comic_archive($filter = 'default') { echo get_comic_url('archive', null, $filter); }

function the_comic_rss($filter = 'default') { echo get_comic_url('rss', null, $filter); }
	//The following is deprecated...
	function comic_rss($filter = 'default') { echo get_comic_url('rss', null, $filter); }

/**
 * Display the list of Storyline categories.
 */
function comicpress_list_storyline_categories($args = "") {
  global $category_tree;

  $defaults = array(
    'style' => 'list', 'title_li' => __('Storyline')
  );

  $r = wp_parse_args($args, $defaults);

  extract($r);

  $categories_by_id = get_all_category_objects_by_id();

  $output = '';
  if ($style == "list") { $output .= '<li class="categories storyline">'; }
  if ($title_li && ($style == "list")) { $output .= $title_li; }
  if ($style == "list") { $output .= "<ul>"; }
  $current_depth = 0;
  foreach ($category_tree as $node) {
    $parts = explode("/", $node);
    $category_id = end($parts);
    $target_depth = count($parts) - 2;
    if ($target_depth > $current_depth) {
      $output .= str_repeat("<li><ul>", ($target_depth - $current_depth));
    }
    if ($target_depth < $current_depth) {
      $output .= str_repeat("</ul></li>", ($current_depth - $target_depth));
    }
    $output .= '<li><a href="' . get_category_link($category_id) . '">';
    $output .= $categories_by_id[$category_id]->cat_name;
    $output .= "</a></li>";
    $current_depth = $target_depth;
  }
  if ($current_depth > 0) {
    $output .= str_repeat("</ul></li>", $current_depth);
  }
  if ($style == "list") { $output .= "</ul></li>"; }
  echo $output;
}

/**
* Display text when image (comic) is hovered
* Text is taken from a custom field named "hovertext"
*/
function the_hovertext() {
  echo get_the_hovertext();
}

function get_the_hovertext() {
  $hovertext = get_post_meta(get_the_ID(), "hovertext", true );
  return (empty($hovertext)) ? get_the_title() : $hovertext;
}

/**
* Display the comic transcript
* Transcript must be entered into a custom field named "transcript"
* @param string $displaymode, "raw" (straight from the field), "br" (includes html line breaks), "styled" (fully css styled with JavaScript expander)
*/
function the_transcript($displaymode = 'raw') {
	$transcript = get_post_meta( get_the_ID(), "transcript", true );
  switch ($displaymode) {
    case "raw":
      echo $transcript;
      break;
    case "br":
      echo nl2br($transcript);
      break;
    case "styled":
      if (!empty($transcript)) { ?>
        <script type='text/javascript'>
          <!--
            function toggle_expander(id) {
              var e = document.getElementById(id);
              if(e.style.height == 'auto')
                e.style.height = '1px';
              else
              e.style.height = 'auto';
            }
          //-->
        </script>
        <div class="transcript-border"><div id="transcript"><a href="javascript:toggle_expander('transcript-content');" class="transcript-title">&darr; Transcript</a><div id="transcript-content"><?php echo nl2br($transcript); ?><br /><br /></div></div></div>
        <script type='text/javascript'>
          <!--
            document.getElementById('transcript-content').style.height = '1px';
          //-->
        </script><?php
      }
      break;
  }
}

//Insert the comic image into the RSS feed
function comic_feed() { ?>
	<p><a href="<?php the_permalink() ?>"><img src="<?php the_comic_rss() ?>" border="0" alt="<?php the_title() ?>" title="<?php the_hovertext() ?>" /></a></p><?php
}

function insert_comic_feed($content) {
	if (is_feed() && in_comic_category()) {
		return comic_feed() . $content;
	} else {
		return $content;
	}
}
add_filter('the_content','insert_comic_feed');

//Generate a random comic page - to use simply create a URL link to "/?randomcomic"
function random_comic() {
	$randomComicQuery = new WP_Query(); $randomComicQuery->query('showposts=1&orderby=rand&cat='.get_all_comic_categories_as_cat_string());
	while ($randomComicQuery->have_posts()) : $randomComicQuery->the_post();
		$random_comic_id = get_the_ID();
	endwhile;
	wp_redirect( get_permalink( $random_comic_id ) );
	exit;
}

if ( isset( $_GET['randomcomic'] ) )
	add_action( 'template_redirect', 'random_comic' );
	
	
// Register Sidebar and Define Widgets
	
if ( function_exists('register_sidebar') )
    register_sidebar();

function widget_comicpress_calendar() { ?>
	<li>
		<?php get_calendar(); ?>
	</li>
	<?php } if ( function_exists('register_sidebar_widget') )
	register_sidebar_widget(__('Calendar'), 'widget_comicpress_calendar');

function widget_comicpress_search() { ?>
	<li>
		<?php include (TEMPLATEPATH . '/searchform.php'); ?>
	</li>
	<?php } if ( function_exists('register_sidebar_widget') )
	register_sidebar_widget(__('Search'), 'widget_comicpress_search');

function widget_comicpress_search_transcript() { ?>
	<li>
		<?php include (TEMPLATEPATH . '/searchform-transcript.php'); ?>
	</li>
	<?php } if ( function_exists('register_sidebar_widget') )
	register_sidebar_widget(__('Search Transcripts'), 'widget_comicpress_search_transcript');
	
 function widget_comicpress_latest_comics() { ?>
	<li>
		<h2>Latest Comics</h2>
		<ul>	
			 <?php global $post;
			$latestcomics = get_posts('numberposts=5&category='.get_all_comic_categories_as_cat_string());
			foreach($latestcomics as $post) : ?>
				<li><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></li>
			<?php endforeach; ?>
     	</ul>
	</li>
	<?php } if ( function_exists('register_sidebar_widget') )
	register_sidebar_widget(__('Latest Comics'), 'widget_comicpress_latest_comics');
 
function widget_comicpress_random_comic() { ?> 
	<li>
		<h2><a href="?randomcomic"><span class="random-comic-icon">?</span> Random Comic</a></h2>
	</li>
	<?php } if ( function_exists('register_sidebar_widget') )
	register_sidebar_widget(__('Random Comic'), 'widget_comicpress_random_comic');
  
function widget_comicpress_archive_dropdown() { ?>
	<li class="archive-dropdown-wrap">
		<select name="archive-dropdown" class="archive-dropdown" onChange='document.location.href=this.options[this.selectedIndex].value;'> 
		<option value=""><?php echo attribute_escape(__('Archives...')); ?></option> 
		<?php wp_get_archives('type=monthly&format=option&show_post_count=1'); ?> </select>
	</li>
	<?php } if ( function_exists('register_sidebar_widget') )
	register_sidebar_widget(__('Archive Dropdown'), 'widget_comicpress_archive_dropdown');
      
function widget_comicpress_comic_bookmark() { ?>
	<div class="comic-bookmark">
		<script language="javascript" type="text/javascript">
			<!--

				/* Bookmark Config Settings */

				var cl = 31;
				var imgTag = '<?php bloginfo('template_directory'); ?>/1.gif';		//add tag image.
				var imgClearOff = '<?php bloginfo('template_directory'); ?>/3a.gif';	//no comic tagged, clear not possible
				var imgGotoOff = '<?php bloginfo('template_directory'); ?>/2a.gif';	//no comic tagged, goto not possible
				var imgClearOn = '<?php bloginfo('template_directory'); ?>/3.gif';	//clear a tag, shows when comic previously tagged
				var imgGotoOn = '<?php bloginfo('template_directory'); ?>/2.gif';	//shows when a comic is tagged  
				var imgInfo = '<?php bloginfo('template_directory'); ?>/4.gif';  	//img that displays the help
				var comicDir = '/'; 		//alter this if you run multiple comics in different directories on your site.

				/* Now write out the applicable links */

				createCookie('t', 1);
				var c = readCookie('t');
				if(c && document.getElementById) {
					var l = readCookie('bm');
					var gt = imgGotoOff;
					var ct = imgClearOff;
					if(l) {
						gt = imgGotoOn;
						ct = imgClearOn;
					}
					document.write('<div id="bmh" style="width: 173px; margin: 15px 0 0 0; padding: 5px; position: absolute; color: #eee; font-size: 11px; background-color:#222; border: 1px solid #ccc; visibility: hidden;"><b>COMIC BOOKMARK</b><br />Click "Tag Page" to bookmark a comic page. When you return to the site, click "Goto Tag" to continue where you left off.</div>');
					<?php if (is_home()) { ?>
						document.write('<a href="#" onClick="bmhome();return false;"><img src="'+imgTag+'" alt="Tag This Page" border="0"></a>');
						document.write('<a href="#" onClick="gto();return false;"><img src="'+gt+'" alt="Goto Tag" border="0" id="gtc"></a>');
						document.write('<a href="#" onClick="bmc();return false;"><img src="'+ct+'" alt="Clear Tag" border="0" id="rmc"></a>');
						document.write('<a href="#" onMouseOver="document.getElementById(\'bmh\').style.visibility=\'visible\';" onMouseOut="document.getElementById(\'bmh\').style.visibility=\'hidden\';" onClick="return false;"><img src="'+imgInfo+'" alt="" border="0"></a>');
					<?php } elseif (is_single() & in_comic_category()) { ?>
						document.write('<a href="#" onClick="bm();return false;"><img src="'+imgTag+'" alt="Tag This Page" border="0"></a>');
						document.write('<a href="#" onClick="gto();return false;"><img src="'+gt+'" alt="Goto Tag" border="0" id="gtc"></a>');
						document.write('<a href="#" onClick="bmc();return false;"><img src="'+ct+'" alt="Clear Tag" border="0" id="rmc"></a>');
						document.write('<a href="#" onMouseOver="document.getElementById(\'bmh\').style.visibility=\'visible\';" onMouseOut="document.getElementById(\'bmh\').style.visibility=\'hidden\';" onClick="return false;"><img src="'+imgInfo+'" alt="" border="0"></a>');
					<?php } ?>
				}

				/* Below are our functions for this little script */

				<?php	$comicFrontpage = new WP_Query(); $comicFrontpage->query('showposts=1&cat='.get_all_comic_categories_as_cat_string());
				while ($comicFrontpage->have_posts()) : $comicFrontpage->the_post(); ?>
					function bmhome() {
						if(document.getElementById) {
							document.getElementById('gtc').src = imgGotoOn;
							document.getElementById('rmc').src = imgClearOn;
						}
						createCookie("bm", "<?php the_permalink(); ?>", cl);	      
					}
				<?php endwhile; ?>

				function bm() {
					if(document.getElementById) {
						document.getElementById('gtc').src = imgGotoOn;
						document.getElementById('rmc').src = imgClearOn;
					}
					createCookie("bm", window.location, cl);
				}

				function bmc() {
					if(document.getElementById) {
						document.getElementById('gtc').src = imgGotoOff;
						document.getElementById('rmc').src = imgClearOff;
					}
					createCookie("bm","",-1);
				}
		      
				function gto() {
					var g = readCookie('bm');
					if(g) {
						window.location = g;
					}	
				}

				/* The follow functions have been borrowed from Peter-Paul Koch. Please find them here: http://www.quirksmode.org */

				function createCookie(name,value,days) {
					if (days) {
						var date = new Date();
						date.setTime(date.getTime()+(days*24*60*60*1000));
						var expires = "; expires="+date.toGMTString();
					} else var expires = "";
					document.cookie = name+"="+value+expires+"; path="+comicDir;
				}
				function readCookie(name) {
					var nameEQ = name + "=";
					var ca = document.cookie.split(';');
					for(var i=0;i < ca.length;i++) {
						var c = ca[i];
						while (c.charAt(0)==' ') c = c.substring(1,c.length);
						if (c.indexOf(nameEQ) == 0) return c.substring(nameEQ.length,c.length);
					}
					return null;
				}
			//-->
		</script>
	</div>
	<?php } if ( function_exists('register_sidebar_widget') )
	register_sidebar_widget(__('Comic Bookmark'), 'widget_comicpress_comic_bookmark');


/*
Plugin Name: Search Custom Fields
Plugin URI: http://guff.szub.net/search-custom-fields/
Description: Search post custom field values. Also provides for an alternative theme 'search' template: search-custom.php.
Author: Kaf Oseo
Version: R1.beta1
Author URI: http://szub.net

	Copyright (c) 2006 Kaf Oseo (http://szub.net)
	Search Custom Fields is released under the GNU General Public License
	(GPL) http://www.gnu.org/licenses/gpl.txt

	This is a WordPress 2 plugin (http://wordpress.org).
*/

function szub_search_custom_join($join) {
	global $wpdb;
	if( is_search() && szub_is_search_key() ) {
		$join = " LEFT JOIN $wpdb->postmeta ON $wpdb->posts.ID = $wpdb->postmeta.post_id ";
	}
	return $join;
}
add_filter('posts_join', 'szub_search_custom_join');

function szub_search_custom_where($where) {
	global $wp_query, $wp_version, $wpdb;
	if( !empty($wp_query->query_vars['s']) && szub_is_search_key() ) {
		$search = $wp_query->query_vars['s'];
		$key = $_GET['key'];
		$status = ($wp_version >= 2.1) ? 'post_type = \'post\' AND post_status = \'publish\'' : 'post_status = \'publish\'';
		$where = " AND $wpdb->postmeta.meta_key = '$key' AND $wpdb->postmeta.meta_value LIKE '%$search%' AND $status ";
	}
	return $where;
}
add_filter('posts_where', 'szub_search_custom_where');

function szub_search_custom_template($template) {
	if( is_search() && szub_is_search_key() && file_exists(TEMPLATEPATH . '/search-transcript.php') )
		$template = TEMPLATEPATH . '/search-transcript.php';

	if( !$template )
		$template = get_query_template('search');
	return $template;
}
add_filter('search_template', 'szub_search_custom_template');

function szub_is_search_key($key='') {
	if( isset($_GET['key']) ) {
		if( !empty($_GET['key']) || (!empty($key) && ($key = $_GET['key'])) )
			return true;
	}
	return false;
}
 
?>
