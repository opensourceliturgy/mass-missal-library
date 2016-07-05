<?php class language_tool {

protected $initio = false;
protected $lgpath;
protected $lgpack;
protected $stacky = false;
protected $framing = false;
protected $fram_set = false;

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

public function set_framing ( $framray )
{
  if ( $this->fram_set ) { return; }
  $this->framing = $framray;
}

protected function get_framing ( $param )
{
  if ( !is_array($this->framing) ) { return ''; }
  if ( !array_key_exists($param,$this->framing) ) { return ''; }
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

// If the language-template was invoked by a function
// that sends parameters, this function can retrieve
// a single parameter.
//   Upon failure, this function returns the alternate
// value specified in it's second argument.
public function get_pram ( $target, $altern ) {
  $theray = $this->get_prmx;
  if ( !array_key_exists($target,$theray) ) { return $altern; }
  return $theray[$target];
}

// If the language-template was invoked by a function
// that sends parameters, this function can retrieve
// the array of parameters.
//   Upon failure, this function returns an empty
// array.
//   It is implemented as a public function rather than
// as a protected utility of get_pram() just in case one
// language res-file needs to fetch this array in order
// to pass it to another.
public function get_prmx ( ) {
  if ( !is_array($this->stacky) ) { return array(); }
  if ( !array_key_exists('params',$this->stacky) ) { return array(); }
  $raykey = $this->stacky['params'];
  if ( is_array($raykey) ) { return $raykey; }
  return array();
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
  return $this->part_prm($partid,false);
}
public function part_prm ( $partid,$param )
{
  $this->stack_on();
  $this->stacky['failed'] = true;
  $this->stacky['params'] = $param;
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
  return subpart_prm ( $partid, false );
}
public function subpart_prm ( $partid, $param )
{
  # Of course -- if there is no stack,
  # then ne'ermind:
  if ( !is_array($this->stacky) ) { return $this->part_prm($partid,$param); }
  
  $this->stack_on();
  $this->stacky['failed'] = true;
  $this->stacky['params'] = $param;
  $orimodule = $this->stacky['prv']['module'];
  $orilang = $this->stacky['prv']['lang'];
  $this->stacky['module'] = $this->stacky['prv']['module'];
  $this->stacky['lang'] = $this->stacky['prv']['lang'];
  $this->stacky['group'] = $this->stacky['prv']['group'];
  
  # First, we try to get it within the module:
  $fila = $orimodule . '/' . $partid . '.php';
  if ( file_exists($fila) )
  {
    $this->stacky['failed'] = false;
    include_with_obj($fila);
    return $this->stack_off();
  }
  
  # Then, we try to get it by the language:
  $langids = $this->stacky['lang'];
  $langresx = $this->lgpack[$langids];
  foreach ( $langresx as $langresi )
  {
    $this->stacky['module'] = $langresx;
    $trgfile = $langresi . '/' . $partid . '.php';
    if ( file_exists($trgfile) )
    {
      $this->stacky['failed'] = false;
      $retval = include_with_obj($trgfile);
      return $this->stack_off();
    }
  }
  
  # Finally, we get it anywhere we can.
  foreach ( $this->lgpath as $eachlang )
  {
    $this->ec_part($eachlang,$partid);
  }
  return $this->stack_off();
}



} ?>