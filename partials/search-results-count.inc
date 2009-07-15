<div class="post-page-head"></div>
<div class="post-page">
  <?php $count = $wp_query->post_count; ?>
  <h2 class="pagetitle">Search for &lsquo;<?php the_search_query() ?>&rsquo;</h2>
  Found <?php echo $count; ?> result<?php if ($count !== 1) { echo "s"; } ?>.
</div>
<div class="post-page-foot"></div>
