<div class="wrap">
  <h2><?php _e('ComicPress Config', 'comicpress') ?></h2>
  <form method="post">
    <input type="hidden" name="cp[_nonce]" value="<?php echo $nonce ?>" />
    <table>
      <tr>
        <th scope="row"><?php _e('Master Comic Category', 'comicpress') ?></th>
        <td>
          <select name="cp[comic_category_id]">
            <?php echo $this->create_category_options($root_categories, $this->comicpress_options['comicpress_category_id']) ?>
          </select>
        </td>
      </tr>
      <?php foreach (array(
        'comic_dimensions' => __('Comic Image Dimensions', 'comicpress'),
        'rss_dimensions' => __('RSS Feed Image Dimensions', 'comicpress'),
        'archive_dimensions' => __('Archive Image Dimensions', 'comicpress'),
      ) as $field => $name) { ?>
        <tr>
          <th scope="row"><?php echo $name ?></th>
          <td>
            <?php echo $this->create_dimension_selector('cp[' . $field . ']', $this->comicpress_options[$field]) ?>
          </td>
        </tr>
      <?php } ?>
      <tr>
        <td>&nbsp;</td>
        <td><input type="submit" value="Submit Changes" /></td>
      </tr>
    </table>
  </form>
</div>
