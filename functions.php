<?

function set_pref ( $prfid, $prfval )
{
  $GLOBALS['sn_mode_inf'][$prfid] = $prfval;
}

function get_pref ( $prfid )
{
  $prfval = $GLOBALS['sn_mode_inf'][$prfid];
  return $prfval;
}



function include_array_files ( $lc_raya )
{
  // Let us import certain global objects into the
  // local var-space.
  $sm = $GLOBALS['sm'];
  $lct = $GLOBALS['lct'];
  $strmagic = $GLOBALS['strmagic'];
  $credits = $GLOBALS['credits'];
  $lngu = $GLOBALS['lngu'];
  
  foreach ( $lc_raya as $lc_pref )
  {
    if ( file_exists($lc_pref) )
    {
      include(realpath($lc_pref));
    }
  }
}

function include_path_file ( $thepath, $thefile, $lnginfo )
{
  // Let us import certain global objects into the
  // local var-space.
  $sm = $GLOBALS['sm'];
  $lct = $GLOBALS['lct'];
  $strmagic = $GLOBALS['strmagic'];
  $credits = $GLOBALS['credits'];
  $lngu = $GLOBALS['lngu'];
  
  foreach ( $thepath as $thespot )
  {
    $theone = $thepath . '/' . $thefile;
    if ( file_exists($theone) )
    {
      if ( array_key_exists('pretx',$lnginfo) ) { echo $lnginfo['pretx']; }
      include $theone;
      if ( array_key_exists('postx',$lnginfo) ) { echo $lnginfo['postx']; }
      return true;
    }
  }
  return false;
}



?>