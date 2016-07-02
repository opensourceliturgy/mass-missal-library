<?php class language_tool {

protected $initio = false;
protected $lgpath;
protected $lgpack;
protected $worked;
protected $stacky = array();

// The following function will only work once - and should
// be called right after the object is created.
// It simply sets the $lgpath variable (the multi-level array
// that controls the sequence by which various languages are
// checked) and the $lgpack variable (the multi-level array
// that identifies where the various language modules are for
// each language - as the same language may have different
// modules for different ways of using the language).
public function init ( $lgpath, $lgpack )
{
  if ( $this->initio ) { return; }
  $this->lgpath = $lgpath;
  $this->lgpack = $lgpack;
}

protected function ec_part ( $langinf, $partid ) {
  
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
        $this->worked = false;
        $this->stacky = array (
          'lang' => $langids,
          'pack' => $langresi,
        );
        return include_with_obj($trgfile);
      }
    }
  }
}

// This function attempts to use the 
public function part ( $partid )
{
  $stacky = $this->stacky;
  $this->worked = true;
  foreach ( $this->lgpath as $eachlang )
  {
    $this->ec_part($eachlang,$partid);
  }
  $this->stacky = $stacky;
  return $this->worked;
}


} ?>