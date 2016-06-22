<?php class util_res_class {



// Used by some function that may succeed or fail --
// but can't use the return value for "true" or
// "false" because that's needed for something else.
protected $vr_so;

// Used as main output-buffer of functions:
protected $vr_buf = "";

protected $vr_vars; // Array of LiturgiCode varspaces
protected $vr_res_name;
protected $rob_custom;
protected $rob_sm; // Theme-interface
protected $rob_ps_bridge;
protected $rob_string; // String-magic object
protected $vr_been_informed = false;



public function flushy ( ) {
  if ( $this->unprepared() ) { return; }
  
  echo $this->vr_buf;
  $this->vr_buf = "";
}


public function clear_vars ( ) {
  $this->vr_vars = array();
  $this->vr_vars["main"] = array(); // Main var-space
  $this->prv_clear_vars();
}

public function set_var ( $rg_nom, $rg_val ) {
  $this->vr_vars["main"][$rg_nom] = $rg_val;
}

protected function prv_findarg ( $rg_itm, $rg_all ) {
  foreach ( $rg_all as $echa )
  {
    $nom = $echa[0];
    $val = $echa[1];
    if ( $nom == $rg_itm )
    {
      return array(true,$val);
    }
  }
  return array(false,false);
}





public function inform ( $rg_name, $rg_all ) {
  $this->vr_res_name = $rg_name;

  list($didit,$found) = $this->prv_findarg("custom",$rg_all);
  if ( $didit ) { $this->rob_custom = $found; }
  else { $this->rob_custom = new lectionary_custom; }

  list($didit,$found) = $this->prv_findarg("theme",$rg_all);
  if ( $didit ) { $this->rob_sm = $found; }
  else { $this->rob_sm = new symbos_mainform; }

  list($didit,$found) = $this->prv_findarg("psalmbridge",$rg_all);
  if ( $didit ) { $this->rob_ps_bridge = $found; }
  else { $this->rob_ps_bridge = new psalm_bridging; }

  list($didit,$found) = $this->prv_findarg("stringwork",$rg_all);
  if ( $didit ) { $this->rob_string = $found; }
  else { $this->rob_string = new string_magic_res; }
  
  $mif = $GLOBALS["sn_mode_inf"];
  $redc = $mif["red_color"];
  $fnon = "<font color = \"#" . $redc . "\">";
  $this->vr_gener = array (
    "bigname" => "Book of " . $fnon . "N" . "</font>"
    ,"midname" => $fnon . "N" . "</font>"
    ,"abrv" => $fnon . "N" . "</font>"
  );
  $this->vr_gos_gener = array (
    "bigname" => "Gospel According to Saint " . $fnon . "N" . "</font>"
    ,"midname" => $fnon . "N" . "</font>"
    ,"abrv" => $fnon . "N" . "</font>"
  );
  
  $this->clear_vars();
  
  $this->prv_inform();
  
  $this->vr_been_informed = true;
}





public function unprepared ( ) {
 if ( $this->vr_been_informed ) { return false; }
 
 echo "\n<h1>ERROR: Illegal use of object "
   . "without first calling it's inform() method.</h1>\n";
 ;
 
 return true;
}









} ?>