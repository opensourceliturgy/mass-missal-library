<?php class language_tool {

protected $initio = false;
protected $lgpath;
protected $lgpack;
protected $stacky = false;

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

// This function is a protected subsidiary of the public
// $this->part() function. It is called once per
// language-grouping.
protected function ec_part ( $langinf, $partid ) {
  $langray = $langinf['lst'];
  $this->stacky['group'] = $langinf;
  foreach ( $langray as $onelang )
  {
    $langids = $onelang['lang'];
    $this->stacky['lang'] = $langids;
    $langresx = $this->lgpack[$langids];
    foreach ( $langresx as $langresi )
    {
      $this->stacky['module'] = $langresx;
      $trgfile = $langresi . '/' . $partid . '.php';
      if ( file_exists($trgfile) )
      {
        $this->stacky['failed'] = false;
        $retval = include_with_obj($trgfile);
        return $retval;
      }
    }
  }
}

protected function stack_on ( ) {
  $oldstack = $this->stacky;
  $this->stacky = array(
    'prv' => $oldstack,
    'stacky' => true,
  );
}

protected function stack_off ( ) {
  if ( !is_array($this->stacky) ) { return false; }
  $oldstack = $this->stacky['prv'];
  $retval = $this->stacky['stacky'];
  $this->stacky = $oldstack;
  return $retval;
}

// This function attempts to find somewhere in the
// language search path the segment identified in
// the one argument provided - and invokes it if
// found.
//
// Though this may seem counter-intuitive, the
// return value is 'false' if successful and 'true'
// if not successful. The reason for this is that
// it is upon failure that the program would have
// to output the last-resort text.
//
// This functioin outputs at most one result per
// language-grouping. (It should be noted that the
// structure of $this->lgpath allows for the
// possibility of multiple languages per language
// grouping - but still - within each language-grouping,
// only the first version available of the chosen
// segment will be used.)
public function part ( $partid )
{
  $this->stack_on();
  $this->stacky['failed'] = true;
  foreach ( $this->lgpath as $eachlang )
  {
    $this->ec_part($eachlang,$partid);
  }
  return $this->stack_off();
}


// FUNCTION INCOMPLETE:
// This function attempts to find somewhere in the
// language search path the segment identified in
// the one argument provided - and invokes it if
// found.
//
// The return value is the same as in the public
// $this->part() function and for the same reason.
// It differs from the $this->part() function in
// the following respects:
//
// #1: Only uses one version of the segment maximum
// altogether in total (as opposed to one per
// language-grouping).
//
// #2: It favors the current language-module before
// even looking at other language-modules, even within
// the same language.
//
// #3: It favors the current language before even
// looking at other languages.
//
// #3: Upon failing to find something in the current
// language, it favors the current language-grouping,
// even if it isn't ordinarily the first one in
// the language path.
//
// FUNCTION INCOMPLETE:
public function subpart ( $partid )
{
}



} ?>