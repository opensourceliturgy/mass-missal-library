<?php class language_tool {

protected $initio = false;
protected $lgpath;
protected $lgpack;
protected $content = '';

public function lrs_on ( ) {
  ob_start();
}

public function lrs_off ( ) {
  $this->content .= ob_get_contents();
  ob_end_clean();
}


public function init ( $lgpath, $lgpack )
{
  if ( $this->initio ) { return; }
  $this->lgpath = $lgpath;
  $this->lgpack = $lgpack;
}

protected function ec_part ( $langinf, $partid ) {
  // Let us import certain global objects into the
  // local var-space.
  $sm = $GLOBALS['sm'];
  $lct = $GLOBALS['lct'];
  $strmagic = $GLOBALS['strmagic'];
  $credits = $GLOBALS['credits'];
  $lngu = $GLOBALS['lngu'];
  
  
  $langray = $langinf['lst'];
  foreach ( $langray as $onelang )
  {
    $langids = $onelang['lang'];
    $langresx = $this->lgpack[$langids];
    foreach ( $langresx as $langresi )
    {
      $trgfile = $langresi . '/' . $partid . '.php';
      if ( file_exists($trgfile) )
      {
        $this->content = '';
        return include realpath($trgfile);
      }
    }
  }
}

public function part ( $partid )
{
  foreach ( $this->lgpath as $eachlang )
  {
    $this->ec_part($eachlang,$partid);
  }
  echo $this->content;
}


} ?>