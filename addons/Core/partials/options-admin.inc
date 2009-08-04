<div class="wrap">
  <h2><?php _e('ComicPress Config', 'comicpress') ?></h2>
  <form method="post">
    <input type="hidden" name="cp[_nonce]" value="<?php echo $nonce ?>" />
    <h3><?php _e('Global Options', 'comicpress') ?></h3>
    <table class="widefat fixed">
      <tr>
        <th scope="row" valign="top"><?php _e('Master Comic Category', 'comicpress') ?></th>
        <td>
          <select name="cp[comic_category_id]">
            <?php echo $this->create_category_options($root_categories, $this->comicpress->comicpress_options['comic_category_id']) ?>
          </select>
        </td>
      </tr>
      <tr>
        <th scope="row" valign="top"><?php _e('Layout', 'comicpress') ?></th>
        <td>
          <select name="cp[layout]">
            <?php echo $this->create_layout_options($this->comicpress->get_layout_choices(), $this->comicpress->comicpress_options['layout']) ?>
          </select>
        </td>
      </tr>
      <?php foreach (array(
        'comic_dimensions' => __('Comic Image Dimensions', 'comicpress'),
        'rss_dimensions' => __('RSS Feed Image Dimensions', 'comicpress'),
        'archive_dimensions' => __('Archive Image Dimensions', 'comicpress'),
        'mini_dimensions' => __('Mini Image Dimensions', 'comicpress'),
      ) as $field => $name) { ?>
        <tr>
          <th scope="row" valign="top"><?php echo $name ?></th>
          <td>
            <?php echo $this->create_dimension_selector('cp[' . $field . ']', $this->comicpress->comicpress_options[$field]) ?>
          </td>
        </tr>
      <?php } ?>
      <tr>
        <th scope="row" valign="top"><?php _e("Number of blog posts on home page", 'comicpress') ?></th>
        <td>
          <input type="text" name="cp[blogpost_count]" value="<?php echo $this->comicpress->comicpress_options['blogpost_count'] ?>" size="3" />
        </td>
      </tr>
      <tr>
        <th scope="row" valign="top"><?php _e("Set storyline category order", 'comicpress') ?></th>
        <td>
          <?php 
            foreach ($this->get_storyline_move_statuses() as $node => $statuses) {
              $parts = explode("/", $node);
              $category = get_category(end($parts));
              if (!empty($category)) {
                ?>
                <div class="storyline" style="padding-left: <?php echo (count($parts) - 2) * 20 ?>px">
                  <strong><?php echo $category->name ?></strong>
                  <?php
                    foreach (array(
                      "-1" => __("Up", 'comicpress'),
                      "1" => __("Down", 'comicpress')
                    ) as $direction => $label) {
                      $status = array_shift($statuses);
                      if ($status) {
                        $query = add_query_arg('cp[_nonce]', $nonce);
                        $query = add_query_arg('cp[category]', $category->term_id, $query);
                        $query = add_query_arg('cp[move_direction]', $direction, $query); ?>
                          | <a href="<?php echo $query ?>"><?php echo $label ?></a>
                        <?php 
                      }
                    }
                  ?>
                </div>
              <?php }
            }
          ?>
          <p><em>(categories can be modified on the Posts -> Categories page)</em></p>
        </td>
      </tr>
      <tr>
        <th scope="row"><?php _e('Insert into comic space:', 'comicpress') ?></th>
        <td>
          <?php foreach (array(
            "comic_only" => __("The comic only, with post content below", 'comicpress'),
            "post_content" => __("The post content", 'comicpress')
          ) as $value => $label) { ?>
            <label><input type="radio" name="cp[comic_space]" value="<?php echo $value ?>" <?php echo ($this->comicpress->comicpress_options['comic_space'] == $value) ? 'checked="checked"' : "" ?> /> <?php echo $label ?></label><br />
          <?php } ?>
        </td>
      </tr>
      <tr>
        <th scope="row"><?php _e('Use subcategories of the Master Comic Category as:', 'comicpress') ?></th>
        <td>
          <?php foreach (array(
            "storyline" => __("Storyline indicators for a single comic", 'comicpress'),
            "multicomic" => __("Multicomic indicators, with direct descendents being separate comics", 'comicpress')
          ) as $value => $label) { ?>
            <label><input type="radio" name="cp[category_usage]" value="<?php echo $value ?>" <?php echo ($this->comicpress->comicpress_options['category_usage'] == $value) ? 'checked="checked"' : "" ?> /> <?php echo $label ?></label><br />
          <?php } ?>
        </td>
      </tr>
    </table>
    <h3><?php _e('Admin Options', 'comicpress') ?></h3>
    <table class="widefat fixed">
      <tr>
        <th scope="row">
          Enable editing helpers
        </th>
        <td>
          <?php
            foreach (array(
              "show_partials_info" => __('Show partials info', 'comicpress'),
              "show_inline_comic_ordering" => __('Show inline comic ordering', 'comicpress')
            ) as $key => $label) { ?>
              <label><input type="checkbox" name="cp[helpers][<?php echo $key ?>]" value="yes" <?php echo (($this->comicpress->comicpress_options['helpers'][$key] === true) ? 'checked="checked"' : '') ?> /> <?php echo $label ?></label><br />
            <?php }
          ?>
        </td>
      </tr>
    </table>
    <input type="submit" value="<?php _e("Submit Changes", 'comicpress') ?>" />  </form>
</div>
