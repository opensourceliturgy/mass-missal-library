<?php class symb_res {

public function mcross ( ) {
  $rt = "<font color = \"#";
  $rt .= $GLOBALS["sn_mode_inf"]["red_color"];
  $rt .= "\">&#10016;";
  $rt .= "</font>";
  //$rt .= "\">&#10016;</font>";
  return $rt;
}

public function cross ( ) {
  $rt = "<font color = \"#";
  $rt .= $GLOBALS["sn_mode_inf"]["red_color"];
  $rt .= "\" size = \"+3\">&#10016;";
  $rt .= "</font>";
  //$rt .= "\">&#10016;</font>";
  return $rt;
}

} ?>