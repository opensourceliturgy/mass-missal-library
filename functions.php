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
  foreach ( $lc_raya as $lc_pref )
  {
    if ( file_exists($lc_pref) )
    {
      include(realpath($lc_pref));
    }
  }
}

function include_path_file ( $thepath, $thefile )
{
  foreach ( $thepath as $thespot )
  {
    $theone = $thepath . '/' . $thefile;
    if ( file_exists($theone) )
    {
      include $theone;
      return;
    }
  }
}



?>