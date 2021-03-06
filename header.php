<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" <?php language_attributes() ?>>
<head profile="http://gmpg.org/xfn/11">
	<title><?php 
    bloginfo('name'); 
    if (is_home () ) {
      echo " - "; bloginfo('description');
    } elseif (is_category() ) {
      echo " - "; single_cat_title();
    } elseif (is_single() || is_page() ) { 
      echo " - "; single_post_title();
    } elseif (is_search() ) { 
      echo " search results: "; echo wp_specialchars($s);
    } else { 
      echo " - "; wp_title('',true);
    }
  ?></title>
	<meta http-equiv="Content-Type" content="<?php bloginfo('html_type') ?>; charset=<?php bloginfo('charset') ?>" />
	<link rel="stylesheet" href="<?php bloginfo('stylesheet_url') ?>" type="text/css" media="screen" />
	<link rel="alternate" type="application/rss+xml" title="<?php bloginfo('name') ?> RSS Feed" href="<?php bloginfo('rss2_url') ?>" />
	<link rel="alternate" type="application/atom+xml" title="<?php bloginfo('name') ?> Atom Feed" href="<?php bloginfo('atom_url') ?>" />
	<link rel="pingback" href="<?php bloginfo('pingback_url') ?>" />
	<?php wp_head() ?>
	<!--[if lt IE 7]><script type="text/javascript" src="<?php bloginfo('template_directory') ?>/ie6submenus.js"></script><![endif]-->
</head>

<body <?php if (function_exists('body_class')) { body_class(); } ?>>

<div id="page"><!-- Defines entire site width - Ends in Footer -->

<div id="header">
	<h1><a href="<?php echo get_settings('home') ?>"><?php bloginfo('name') ?></a></h1>
	<div class="description"><?php bloginfo('description') ?></div>
</div>

<div id="menubar">

	<ul id="menu">
		<li><a href="<?php bloginfo('url') ?>">Home</a></li>
		<?php wp_list_pages('sort_column=menu_order&depth=4&title_li=') ?>
		<li><a href="<?php bloginfo('rss2_url') ?>">Subscribe</a></li>
	</ul>

	<div class="clear"></div>
</div>