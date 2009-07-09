<?php

class ComicPressAddon {
  var $messages = array(
    'info' => array(),
    'warn' => array(),
    'error' => array()
  );

  function info($message) { $this->messages['info'][] = $message; }
  function warn($message) { $this->messages['warn'][] = $message; }
  function error($message) { $this->messages['error'][] = $message; }
  
  function display_messages() {
    foreach ($this->messages as $type => $messages) {
      if (!empty($messages)) {
        echo '<div class="updated fade cp-' . $type . '">';
          foreach ($messages as $message) {
            echo '<p>' . $message . '</p>';
          }
        echo '</div>';
      }
    }
  }
}

?>