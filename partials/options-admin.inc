<div class="wrap">
  <h2>ComicPress Config</h2>
  <form method="post">
    <input type="hidden" name="cp[_nonce]" value="<?php echo $nonce ?>" />
    <table>
      <tr>
        <th scope="row">Master Comic Category</th>
        <td>
          <select name="cp[comic_category_id]">
            <?php echo $this->create_category_options($root_categories, $this->comicpress_options['comicpress_category_id']) ?>
          </select>
        </td>
      </tr>
    </table>
  </form>
</div>
