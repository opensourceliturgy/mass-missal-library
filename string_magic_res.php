<?php class string_magic_res {


public function relativ ( $rg_sorc, $rg_where ) {
  $sfar = $rg_sorc;
  $neos = $rg_where;
  
  if ( substr($rg_where,0,1) == "/" )
  {
    $sfar = "";
    $neos = substr($rg_where,1);
  }
  
  if ( file_exists($sfar) )
  {
    $sfar = dirname($sfar);
  }
  $partow = explode("/",$neos);
  foreach ( $partow as $zori )
  {
    $kau = true;
    if ( $kau ) { if ( $zori == ".." ) { $sfar = dirname($sfar); $kau = false; } }
    if ( $kau ) { if ( $zori == "." ) { $kau = false; } }
    
    if ( $kau )
    {
      if ( $sfar == "/" ) { $sfar = ""; }
      $sfar .= "/" . $zori;
    }
  }
  return $sfar;
}



} ?>