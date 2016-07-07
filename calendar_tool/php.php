<?php class calendar_tool__php {
// The main type of calendar tool is one that is based on PHP callback functions.
// (As a matter of fact, it is the first to be implemented.)


private $inited = false;
// The initia() function is really meant only to be called
// by the calendar_tool() function - and only upon creating
// the object. It is not a PHP-enforced constructor - but
// it is effectively a constructor.
pubic function initia ( $resdir ) {
  if ( $this->inited ) { return false; }
  $this->inited = true;
  
  call_user_func($this->load_callback($resdir . '/load_resources.php'));
  
  return true;
}


// This function is used to conveniently load a callback that
// is returned by a file
protected function load_callback ( $thefile )
{
  $clba = include realpath($thefile);
  $clbb = Closure::bind($clba,$this,$this);
  return $clbb;
}


} ?>