<?php

class symbos_narrowform {
  
  protected $pt_v_notes = "";
  
  public function coreword ( $rg_a )
  {
    echo "\n<p>";
    echo "<font size = \"+2\">";
    echo $rg_a;
    echo "</font>";
    echo "</p>\n";
  }
  
  public function tx_cross ( )
  {
    $lc_rt = "<font color = \"";
    $lc_rt .= $GLOBALS["sn_mode_inf"]["red_color"];
    $lc_rt .= "\" size = \"+3\">&#10016;</font>";
    return $lc_rt;
  }
  
  public function cross ( )
  {
    ?><font color = "#<?php echo $GLOBALS["sn_mode_inf"]["red_color"]; ?>" size = "+3">&#10016;</font><?php
  }
  
  public function flushnotes ( )
  {
    if ( $this->pt_v_notes == "" ) { return; }
    
    echo "\n<table border = \"1\" cellpadding = \"2\" width = \"80%\"><tr>\n";
    echo "<td align = \"center\"><font size = \"+1\">Note:</font></td></tr>\n";
    echo "<td>" . $this->pt_v_notes . "</td>\n</tr></table>\n";
    
    $this->pt_v_notes = "";
  }
  
  public function gt ( $rg_a )
  {
    $this->flushnotes();
    echo "\n<h3>";
    echo htmlspecialchars($rg_a);
    echo "</h3>\n";
  }
  
  public function insparg ( $rg_a )
  {
    echo "\n<p>";
    $this->instx($rg_a);
    echo "</p>\n";
  }
  
  public function r_insparg ( $rg_a )
  {
    $lc_a = "\n<p>";
    $lc_a .= $this->r_instx($rg_a);
    $lc_a .= "</p>\n";
    return $lc_a;
  }
  
  public function instx ( $rg_a )
  {
    echo "<font color = \"#" . $GLOBALS["sn_mode_inf"]["red_color"] . "\">(";
    echo $rg_a;
    echo ")</font>";
  }
  
  public function r_instx ( $rg_a )
  {
    $lc_a = "<font color = \"#" . $GLOBALS["sn_mode_inf"]["red_color"] . "\">(";
    $lc_a .= $rg_a;
    $lc_a .= ")</font>";
    return $lc_a;
  }
  
  public function mcross ( )
  {
    ?><font color = "#<?php echo $GLOBALS["sn_mode_inf"]["red_color"]; ?>">&#10016;</font><?php
  }
  
  public function note ( $rg_a )
  {
    $lc_x = $this->pt_v_notes;
    
    if ( $lc_x != "" ) { $lc_x .= "</td></tr>\n<tr><td>"; }
    $lc_x .= $rg_a;
    
    $this->pt_v_notes = $lc_x;
  }
  
  public function parthead ( $rg_a )
  {
    $this->flushnotes();
    echo "\n<h2>";
    echo htmlspecialchars($rg_a);
    echo "</h2>\n";
  }
  
  public function t ( $rg_a )
  {
    $this->flushnotes();
    echo "\n<p><font color = \"#" . $GLOBALS["sn_mode_inf"]["offred_color"] . "\" size = \"+1\"><b><i>- - ";
    echo htmlspecialchars($rg_a);
    echo "</i></b></font></p>\n";
  }
  
  public function xt ( )
  {
    $this->t("");
  }
  
}

?>