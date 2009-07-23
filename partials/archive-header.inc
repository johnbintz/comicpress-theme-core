<?php global $posts ?>
<div class="post-page-head"></div>
<div class="post-page"> 
  <?php $post = $posts[0]; // Hack. Set $post so that the_date() works. ?>
  <?php /* Category Archive */ if (is_category()) { ?>
    <div class="content"><h2 class="pagetitle">Archive for &#8216;<?php single_cat_title() ?>&#8217;</h2></div>
  <?php /* Tag Archive */ } elseif(is_tag()) { ?>
    <div class="content"><h2 class="pagetitle">Posts Tagged &#8216;<?php single_tag_title() ?>&#8217;</h2></div>
  <?php /* Daily Archive */ } elseif (is_day()) { ?>
    <div class="content"><h2 class="pagetitle">Archive for <?php the_time('F jS, Y') ?></h2></div>
  <?php /* Monthly Archive */ } elseif (is_month()) { ?>
    <div class="content"><h2 class="pagetitle">Archive for <?php the_time('F, Y') ?></h2></div>
  <?php /* Yearly Archive */ } elseif (is_year()) { ?>
    <div class="content"><h2 class="pagetitle">Archive for <?php the_time('Y') ?></h2></div>
  <?php /* Author Archive */ } elseif (is_author()) { ?>
    <div class="content"><h2 class="pagetitle">Author Archive</h2></div>
  <?php /* Paged Archive */ } elseif (isset($_GET['paged']) && !empty($_GET['paged'])) { ?>
    <div class="content"><h2 class="pagetitle">Archives</h2></div>
  <?php } ?>
  <br class="clear-margins" />
</div>
<div class="post-page-foot"></div>
