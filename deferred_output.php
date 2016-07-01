<?php

class deferred_output {
  protected $stuff = array();
  protected $content = '';
  
  public function on ( ) {
    ob_start();
  }
  
  public function off ( ) {
    $this->content .= ob_get_contents();
    ob_end_clean();
  }
  
  public function out ( ) {
    echo $this->content;
    $this->content = '';
  }
}

?>