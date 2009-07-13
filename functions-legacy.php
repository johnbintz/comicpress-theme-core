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

