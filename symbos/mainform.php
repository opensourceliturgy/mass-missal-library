<?php

class symbos_mainform {
  protected $v_cite = '';
  
  public function coreword ( $rg_a )
  {
    echo "\n<p>";
    echo "<font size = \"+2\">";
    echo $rg_a;
    echo "</font>";
    echo "</p>\n";
  }
  
  protected function ct_encode ( $ctext )
  {
    $sorca = $ctext;
    $sorcb = str_replace('&','&amp;',$sorca); $sorca = $sorcb;
    $sorcb = str_replace('<','\\&lt;',$sorca); $sorca = $sorcb;
    $sorcb = str_replace('>','\\&gt;',$sorca); $sorca = $sorcb;
    $sorcb = str_replace("'",'\\&#39;',$sorca); $sorca = $sorcb;
    $sorcb = str_replace('"','\\&quot;',$sorca); $sorca = $sorcb;
    return $sorca;
  }
  
  public function cite ( $ctext )
  {
    $this->v_cite .= $this->ct_encode($ctext);
  }
  
  public function nl_cite ( $ctext )
  {
    $this->v_cite .= '<br/>' . $this->ct_encode($ctext);
  }
  
  public function lnk_cite ( $ctext, $urlo )
  {
    $this->v_cite .= '<a href = ' . $urlo . ' target = _blank >' . $this->ct_encode($ctext) . '</a>';
  }
  
  public function ct_line ( $cnum )
  {
    $lcount = $cnum;
    while ( $lcount > 0.5 )
    {
      $this->v_cite .= "<br/>";
      $lcount = ((int)($lcount - 0.8));
    }
  }
  
  public function ct_link ( $rgray )
  {
    echo "\n<span class = \"cite_link\"><i>(<a href = \"javascript:CiteNote('" . $this->v_cite . "')\">" . '**' . "</a>)</i></span>\n";
    $this->v_cite = '';
  }
  
  public function cite_form ( )
  {
?>
<p>
In this HTML rendering, source references are in the form of small links
labeled &quot;(**)&quot; that you click on to open a pop-up window with
source information.
For this to work, JavaScript must be enabled.
</p>
<?php
  }
  
  public function tx_cross ( )
  {
    $lc_rt = '<span class = "highlight_big">&#10016;</span>';
    return $lc_rt;
  }
  
  public function cross ( )
  {
    ?><span class = "highlight_big">&#10016;</span><?php
  }
  
  public function flushnotes ( )
  // This is a dummy function for interchangeability with
  // implementations that have notes at the end of sections
  // rather than interspersed within sections.
  {
  }
  
  public function gt ( $rg_a )
  {
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
    ?><span class = "highlight_reg">&#10016;</span><?php
  }
  
  public function note ( $rg_a )
  {
    echo "\n<table align = \"right\" border = \"1\" cellpadding = \"2\" width = \"40%\"><tr>\n";
    echo "<td align = \"center\"><font size = \"+1\">Note:</font></td></tr>\n";
    echo "<td>" . $rg_a . "</td>\n</tr></table>\n";
  }
  
  public function parthead ( $rg_a )
  {
    echo "\n<h2>";
    echo htmlspecialchars($rg_a);
    echo "</h2>\n";
  }
  
  public function t ( $rg_a )
  {
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