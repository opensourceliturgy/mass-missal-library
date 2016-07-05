<?php
require_once $libdir . "/util_res_class.php";
class lectionary_dialogue extends util_res_class {

// The following variables are used to communicate
// between prv_divide_readings() and it's subordinate
// functions.
protected $vr_hadcolon;
protected $vr_curchapt;

// Used for option control ----
protected $vr_opt_buf = "";
protected $vr_opt_nom;
protected $vr_opt_mnu = 0; // Options Menu Number
protected $vr_opt_num = 0; // Option Number (in menu)

// Used to store the info on the currently-being-read book:
protected $vr_info; // Basic book info
protected $vr_sectos; // The array of sections in this reading.
protected $vr_bookid;
protected $vr_bookref;
protected $vr_chaptid;
protected $vr_scriptres;
protected $vr_oneven_a;
protected $vr_oneven_z;
protected $vr_oneven_is;
protected $vr_prev_verse;



protected $vr_gener; // Book-info for generic missal
protected $vr_gos_gener; // Gospel Book-info for generic missal

protected $vr_prg_class_first;
protected $vr_prg_class_rest;

protected $vr_force_verse_line = false;

// So the current lectionary page needn't be passed by
// argument to every subsidiary function that might
// need it for error-output
protected $vr_cur_lect_page;



protected $vr_cur_officiant = 0;
protected $vr_officiants = array ( 0 => "Celebrant",
1 => "Lector",
2 => "Preacher"
);

protected function prv_prg_default ( ) {
  $this->prv_prg_set("","");
}

protected function prv_prg_set ( $rg_a, $rg_b )
{
  $this->vr_prg_class_first = $rg_a;
  $this->vr_prg_class_rest = $rg_b;
}


// $this->by_toc()
// This is the function actually called by the main liturgy program
// when it is time for the readings.
public function by_toc ( $rg_resloc, $rg_showerr ) {
  if ( $this->unprepared() ) { return; }
  
  if ( !(file_exists($rg_resloc)) )
  {
    if ( $rg_showerr )
    {
      echo "\n<h1>ERROR: no such file</h1>\n";
      echo "<h3>" . htmlspecialchars($rg_resloc) . "</h3>";
    }
    return false;
  }
  $hasdone = false;
  
  $conta = file_get_contents($rg_resloc);
  $contb = explode("\n",$conta);
  foreach ( $contb as $contc )
  {
    list($base,$allrg) = explode(":",$contc,2);
    if ( $base == "page" )
    {
      list($whatpage,$where) = explode(":",$allrg,2);
      if ( $whatpage == $this->vr_vars["main"]["lectpage"] )
      {
        $thatpage = $this->rob_string->relativ($rg_resloc,$where);
        $isdone = $this->by_lectpage($thatpage,$rg_showerr);
        if ( $isdone ) { $hasdone = true; }
      }
    }
  }
  if ( $hasdone ) { return true; }
  
  if ( $rg_showerr )
  {
    echo "\n<h1>ERROR: no such lectionary page: " . $this->vr_vars["main"]["lectpage"] . "</h1>\n";
    echo "<h3>" . htmlspecialchars($rg_resloc) . "</h3>";
  }
  
  return false;
}


public function by_lectpage ( $rg_page, $rg_showerr ) {
  $sm = $this->rob_sm;
  if ( !(file_exists($rg_page)) )
  {
    if ( $rg_showerr )
    {
      echo "\n<h1>ERROR: no such file</h1>\n";
      echo "<h3>" . htmlspecialchars($rg_page) . "</h3>";
    }
    return false;
  }
  $this->vr_cur_lect_page = $rg_page;
  $conta = file_get_contents($rg_page);
  $contb = explode("\n",$conta);
  foreach ( $contb as $contc )
  {
    $isee = false;
    list($base,$allrg) = explode(":",$contc,2);
    if ( $base == "" ) { $isee = true; }
    if ( $base != "" )
    {
      if ( substr($base,0,1) == "#" )
      {
        $isee = true;
        //echo "\n<h2>Comment: " . htmlspecialchars($contc) . ":</h2>\n";
      }
    }
    
    
    if ( $base == "floor" )
    {
      $this->floor($allrg);
      $isee = true;
    }
    
    if ( $base == "label" )
    {
      $this->label($allrg);
      $isee = true;
    }
    
    if ( $base == "psalm" )
    {
      $this->psalm($allrg);
      $isee = true;
    }
    
    if ( $base == "psalm-a" )
    {
      $this->psalm_a($allrg);
      $isee = true;
    }
    
    if ( $base == "psalm-c" )
    {
      $this->psalm_c($allrg);
      $isee = true;
    }
    
    if ( $base == "canticle" )
    {
      list($c_name,$c_book,$c_parts) = explode(":",$allrg,3);
      $this->canticle($c_book,$c_parts,$c_name);
      $isee = true;
    }
    
    if ( $base == "catfile" )
    {
      echo file_get_contents(realpath(dirname(realpath($rg_page)) . '/' . $allrg));
      $isee = true;
    }
    
    if ( $base == "part" )
    {
      $this->prv_part($allrg,$rg_showerr);
      $isee = true;
    }
    
    if ( $base == "xpart" )
    {
      $this->prv_xpart($allrg,$rg_showerr);
      $isee = true;
    }
    
    if ( $base == "ot-reading" )
    {
      list($book,$inbk) = explode(" ",$allrg,2);
      $this->ot_reading($book,$inbk);
      $isee = true;
    }
    
    if ( $base == "nt-reading" )
    {
      list($book,$inbk) = explode(" ",$allrg,2);
      $this->nt_reading($book,$inbk);
      $isee = true;
    }
    
    if ( $base == "gospel" )
    {
      list($book,$inbk) = explode(" ",$allrg,2);
      $this->gospel($book,$inbk);
      $isee = true;
    }
    
    if ( $base == "aleluia" )
    {
      $this->aleluia();
      $isee = true;
    }
    
    if ( $base == "opt-menu" )
    {
      list($mnlabel,$oplabel) = explode(":",$allrg,2);
      $this->opt_menu($mnlabel,$oplabel);
      $isee = true;
    }
    
    if ( $base == "opt-next" )
    {
      $this->opt_next($allrg);
      $isee = true;
    }
    
    if ( $base == "opt-done" )
    {
      $this->opt_done();
      $isee = true;
    }
    
    if ( $base == "flushy" )
    {
      $this->flushy();
      $isee = true;
    }
    
    if ( $base == "ins-note" )
    {
      $this->vr_buf .= $sm->r_insparg($allrg);
      $isee = true;
    }
    
    if ( $base == "wip-note" )
    {
      $this->vr_buf .= "\n<p><i>(" . $allrg . ")</i></p>\n";
      $isee = true;
    }
    
    if ( $base == "pre-ot" )
    {
      $this->rob_custom->ebuf($this->vr_gener);
      $this->vr_buf .= $this->rob_custom->ot_before();
      $isee = true;
    }
    
    if ( $base == "post-ot" )
    {
      $this->rob_custom->ebuf($this->vr_gener);
      $this->vr_buf .= $this->rob_custom->ot_after();
      $isee = true;
    }
    
    if ( $base == "pre-nt" )
    {
      $this->rob_custom->ebuf($this->vr_gener);
      $this->vr_buf .= $this->rob_custom->nt_before();
      $isee = true;
    }
    
    if ( $base == "post-nt" )
    {
      $this->rob_custom->ebuf($this->vr_gener);
      $this->vr_buf .= $this->rob_custom->nt_after();
      $isee = true;
    }
    
    if ( $base == "pre-gospel" )
    {
      $this->rob_custom->ebuf($this->vr_gos_gener);
      $this->vr_buf .= $this->rob_custom->gospel_before();
      $isee = true;
    }
    
    if ( $base == "post-gospel" )
    {
      $this->rob_custom->ebuf($this->vr_gos_gener);
      $this->vr_buf .= $this->rob_custom->gospel_after();
      $isee = true;
    }
    
    if ( !($isee) )
    {
      echo "\n<h1>UNKNOWN lect-page command: ";
      echo htmlspecialchars($base);
      echo ":</h1>\n";
      echo "<h2>Found in file: ";
      echo htmlspecialchars($rg_page);
      echo ":</h2>\n";
    }
  }
  
  $this->flushy();
  return true;
}

//public function inform ( $rg_noma, $rg_custom, $rg_theme,
// $rg_psalm, $rg_stringy ) {
//  $this->vr_res_name = $rg_noma;
//  $this->rob_custom = $rob_custom;
//  $this->rob_sm = $rg_theme;
//  $this->rob_ps_bridge = $rg_psalm;
//  $this->rob_string = $rg_stringy;
//  $this->clear_vars;
//  $this->vr_been_informed = true;
//}

protected function prv_inform ( ) {
}





public function floor ( $rg_new ) {
  if ( $this->unprepared() ) { return; }
  
  $sm = $this->rob_sm;
  $old = $this->vr_cur_officiant;
  $this->vr_cur_officiant = $rg_new;
  
  if ( $old != $rg_new )
  {
    $this->vr_buf .= $sm->r_insparg("At this point, the "
      . $this->vr_officiants[$old]
      . " cedes the floor to the "
      . $this->vr_officiants[$rg_new]
      . "."
    );
  }
  
  if ( $old == $rg_new )
  {
    $this->vr_buf .= $sm->r_insparg("At This point, there "
      . "may be a change between one "
      . $this->vr_officiants[$old]
      . " and another."
    );
  }
}

public function may_floor ( $rg_new ) {
  if ( $this->unprepared() ) { return; }
  $sm = $this->rob_sm;
  $old = $this->vr_cur_officiant;
  $this->vr_cur_officiant = $rg_new;
  
  if ( $old != $rg_new )
  {
    $this->vr_buf .= $sm->r_insparg("At this point, the "
      . $this->vr_officiants[$old]
      . " cedes the floor to the "
      . $this->vr_officiants[$rg_new]
      . "."
    );
  }
}


protected function prv_vrs_tog ( ) {
  $lc_a = true;
  if ( $this->vr_oneven_is ) { $lc_a = false; }
  $this->vr_oneven_is = $lc_a;
}

protected function prv_reading_now ( ) {
  $this->vr_buf .= "<p><font size = \"-1\"><b>(" . $this->vr_info["abrv"] . " " .
      $this->vr_bookref . ")</b></font>\n";
  ;
  $chaian = "<br/>\n";
  $this->vr_chaptid = "x";
  foreach ( $this->vr_sectos as $secta )
  {
    //$this->vr_buf .= "----- " . $secta . "<br/><br/>\n";
    $this->prv_read_it($this->vr_bookid,$secta,$chaian);
    $chaian = "<br/><br/>\n";
  }
  $this->vr_force_verse_line = false;
}

protected function prv_part ( $rg_a, $rg_showerr )
{
  list($gothere) = explode(":",$rg_a);
  $resloc = $this->vr_vars["part"][$gothere];
  
  
  if ( ( !($resloc) ) || ( $resloc == "" ) )
  {
    if ( $rg_showerr )
    {
      echo "\n<h1>ERROR: Nothing assigned to: ";
      echo htmlspecialchars($gothere);
      echo ":</h1>\n";
      echo "<h2>Referenced in file: ";
      echo htmlspecialchars($this->vr_cur_lect_page) . "</h2>\n";
    }
    return false;
  }
  return $this->prv_do_part($resloc,$rg_showerr);
}

protected function prv_xpart ( $rg_a, $rg_showerr )
{
  list($gothere,$goby) = explode(":",$rg_a);
  $rescode = $this->vr_vars["main"][$gothere];
  
  if ( ( !($rescode) ) || ( $rescode == "" ) )
  {
    if ( $rg_showerr )
    {
      echo "\n<h1>ERROR: No such variable: ";
      echo htmlspecialchars($gothere);
      echo ":</h1>\n";
      echo "<h2>Referenced in file: ";
      echo htmlspecialchars($this->vr_cur_lect_page) . "</h2>\n";
    }
    return false;
  }
  
  $resloc = $this->vr_vars["part"][$goby];
  
  if ( ( !($resloc) ) || ( $resloc == "" ) )
  {
    if ( $rg_showerr )
    {
      echo "\n<h1>ERROR: Nothing assigned to: ";
      echo htmlspecialchars($goby);
      echo ":</h1>\n";
      echo "<h2>Referenced in file: ";
      echo htmlspecialchars($this->vr_cur_lect_page) . "</h2>\n";
    }
    return false;
  }
  
  $resrtbl = $this->prv_look_part($resloc,$rg_showerr);
  $old_resloc = $resloc;
  $resloc = $resrtbl[$rescode];
  
  if ( ( !($resloc) ) || ( $resloc == "" ) )
  {
    if ( $rg_showerr )
    {
      echo "\n<h1>ERROR: Not found: ";
      echo htmlspecialchars($rescode);
      echo ":</h1>\n";
      echo "<h2>File: " . htmlspecialchars($old_resloc) . ":</h2>\n";
    }
    return false;
  }
  
  return $this->prv_do_part($resloc,$rg_showerr);
}

protected function prv_look_part($rg_resloc,$rg_showerr)
{
  if ( !(file_exists($rg_resloc)) )
  {
    if ( $rg_showerr )
    {
      echo "\n<h1>ERROR: no such file</h1>\n";
      echo "<h3>" . htmlspecialchars($rg_resloc) . "</h3>";
      echo "<h2>Referenced in file: ";
      echo htmlspecialchars($this->vr_cur_lect_page) . "</h2>\n";
    }
    return array();
  }
  
  $ret = array();
  $cola = file_get_contents($rg_resloc);
  $colb = explode("\n",$cola);
  foreach ( $colb as $colc )
  {
    list($mode,$nom,$val) = explode(":",$colc,3);
    $isee = false;
    
    if ( $mode == "" ) { $isee = true; }
    
    if ( $mode == "text" )
    {
      $ret[$nom] = $this->rob_string->relativ($rg_resloc,$val);
      $isee = true;
    }
    
    if ( !($isee) )
    {
      if ( $rg_showerr )
      {
        echo "\n<h1>ERROR: Unknown command: " . htmlspecialchars($mode) . ":</h1>\n";
        echo "<h2>In file: " . htmlspecialchars($rg_resloc) . ":</h2>\n";
      }
    }
  }
  return $ret;
}



public function render_file ( $rg_resloc, $rg_showerr )
{
  return $this->prv_do_part ( $rg_resloc, $rg_showerr );
}


protected function prv_do_part ( $rg_resloc, $rg_showerr )
{
  $inaparg = false;
  if ( !(file_exists($rg_resloc)) )
  {
    if ( $rg_showerr )
    {
      echo "\n<h1>ERROR: no such file</h1>\n";
      echo "<h3>" . htmlspecialchars($rg_resloc) . "</h3>";
    }
    return false;
  }
  
  $conta = file_get_contents($rg_resloc);
  $contb = explode("\n",$conta);
  foreach ( $contb as $contc )
  {
    $isee = false;
    list($comd,$rargs) = explode(":",$contc,2);
    
    if ( $comd == "title" )
    {
      if ( $inaparg ) { $this->vr_buf .= $pcloser . "</p>\n"; }
      $this->vr_buf .= "\n<div align = \"center\"><h3>";
      $this->vr_buf .= $rargs;
      $this->vr_buf .= "</h3></div>\n";
      $inaparg = false;
      $isee = true;
    }
    
    if ( $comd == "cite" )
    {
      if ( $inaparg ) { $this->vr_buf .= $pcloser . "</p>\n"; }
      $this->vr_buf .= "\n<p><b>(";
      $this->vr_buf .= $rargs;
      $this->vr_buf .= ")</b></p>\n";
      $inaparg = false;
      $isee = true;
    }
    
    if ( $comd == "up" )
    {
      if ( $inaparg ) { $this->vr_buf .= $pcloser . "</p>\n"; }
      $pcloser = "</u>";
      $this->vr_buf .= "\n<p><u>";
      $inaparg = true;
      $isee = true;
    }
    
    if ( $comd == "note" )
    {
      if ( $inaparg ) { $this->vr_buf .= $pcloser . "</p>\n"; }
      $pcloser = ")</i></font>";
      $this->vr_buf .= "\n<p><font size = \"-1\"><i>(";
      $inaparg = true;
      $isee = true;
    }
    
    if ( $comd == "p" )
    {
      if ( $inaparg ) { $this->vr_buf .= $pcloser . "</p>\n"; }
      $pcloser = "";
      $this->vr_buf .= "\n<p>";
      $inaparg = true;
      $isee = true;
    }
    
    if ( $comd == "insp" )
    {
      if ( $inaparg ) { $this->vr_buf .= $pcloser . "</p>\n"; }
      $pcloser = "</font>)";
      $this->vr_buf .= "\n<p>(<font color = \"#" . $GLOBALS["sn_mode_inf"]["red_color"] . "\">";
      $inaparg = true;
      $isee = true;
    }
    
    if ( $comd == "" )
    {
      $isee = true;
    }
    if ( $comd != "" )
    {
      if ( substr($comd,0,1) == "#" )
      {
        $isee = true;
        //echo "\n<h2>Comment: " . htmlspecialchars($contc) . ":</h2>\n";
      }
    }
    
    if ( $comd == "vr" )
    {
      if ( !($inaparg) )
      {
        if ( $rg_showerr )
        {
          echo "\n<h1>ERROR: \"tx\" command can't come";
          echo " before first paragraph opener:</h1>\n";
          echo "<h2>File: " . htmlspecialchars($rg_resloc) . "</h2>\n";
        }
      }
      $this->vr_buf .= "\n<font size = \"-2\">";
      $this->vr_buf .= htmlspecialchars($rargs);
      $this->vr_buf .= "</font>\n";
      $isee = true;
    }
    
    if ( $comd == "tx" )
    {
      if ( !($inaparg) )
      {
        if ( $rg_showerr )
        {
          echo "\n<h1>ERROR: \"tx\" command can't come";
          echo " before first paragraph opener:</h1>\n";
          echo "<h2>File: " . htmlspecialchars($rg_resloc) . "</h2>\n";
        }
      }
      $this->vr_buf .= htmlspecialchars($rargs) . "\n";
      $isee = true;
    }
    
    if ( $comd == "ltx" )
    {
      if ( !($inaparg) )
      {
        if ( $rg_showerr )
        {
          echo "\n<h1>ERROR: \"tx\" command can't come";
          echo " before first paragraph opener:</h1>\n";
          echo "<h2>File: " . htmlspecialchars($rg_resloc) . "</h2>\n";
        }
      }
      $this->vr_buf .= "\n<br/>\n" . htmlspecialchars($rargs) . "\n";
      $isee = true;
    }
    
    if ( $comd == "cross" )
    {
      if ( !($inaparg) )
      {
        if ( $rg_showerr )
        {
          echo "\n<h1>ERROR: \"tx\" command can't come";
          echo " before first paragraph opener:</h1>\n";
          echo "<h2>File: " . htmlspecialchars($rg_resloc) . "</h2>\n";
        }
      }
      $this->vr_buf .= $this->rob_sm->tx_cross();
      $isee = true;
    }
    
    
    if ( $comd == "ctx" )
    {
      if ( !($inaparg) )
      {
        if ( $rg_showerr )
        {
          echo "\n<h1>ERROR: \"ctx\" command can't come";
          echo " before first paragraph opener:</h1>\n";
          echo "<h2>File: " . htmlspecialchars($rg_resloc) . "</h2>\n";
        }
      }
      $this->vr_buf .= htmlspecialchars($rargs);
      $isee = true;
    }
    
    
    if ( $comd == "bctx" )
    {
      if ( !($inaparg) )
      {
        if ( $rg_showerr )
        {
          echo "\n<h1>ERROR: \"ctx\" command can't come";
          echo " before first paragraph opener:</h1>\n";
          echo "<h2>File: " . htmlspecialchars($rg_resloc) . "</h2>\n";
        }
      }
      $this->vr_buf .= "<b>" . htmlspecialchars($rargs) . "</b>";
      $isee = true;
    }
    
    if ( !($isee) )
    {
      if ( $rg_showerr )
      {
        echo "\n<h1>ERROR: Unknown command: " . htmlspecialchars($comd) . ":</h1>\n";
        echo "<h2>In file: " . htmlspecialchars($rg_resloc) . ":</h2>\n";
      }
    }
  }
  
  
  if ( $inaparg ) { $this->vr_buf .= $pcloser . "</p>\n"; }
}

// This function assigns a stock-textname to a liturgical
// text-res anywhere you can define.
public function assign ( $rg_name, $rg_loc )
{
  $this->vr_vars["part"][$rg_name] = $rg_loc;
}


protected function prv_clear_vars ( ) {
  $this->vr_vars["part"] = array();
}

protected function prv_read_it ( $rg_a, $rg_b, $rg_c ) {
  
  //echo "<br/>" . $rg_a . " - " . $rg_b . " - " . $rg_c . ":<br/>\n";
  
  $nparg = false;
  list($cha,$vrsx) = explode(":",$rg_b);
  list($vrs_a,$vrs_z) = explode("-",$vrsx);
  $chb = ((int)($cha + 0.2));
  $cha = $chb;
  if ( $chb < 9.5 ) { $cha = "0" . $cha; }
  if ( $chb < 99.5 ) { $cha = "0" . $cha; }
  $scripdr = $this->vr_scriptres;
  $pickfile = $scripdr . "/" . $rg_a . "-" . $cha . ".scrp";
  if ( !(file_exists($pickfile) ) )
  {
    echo "<br/><br/><h1>FILE MISSING FROM SCRIPTURE-RESOURCE:</h1>\n";
    echo $pickfile . "<br/><br/>\n";
    return;
  }
  $this->vr_buf .= $rg_c;
  
  $chpida = $chb . ":";
  $chpidb = $chpida;
  if ( $this->vr_chaptid == $chpida )
  {
    $chpidb = "";
  }
  $this->vr_chaptid = $chpida;
  
  $chapraw = file_get_contents($pickfile);
  $chaplins = explode("\n",$chapraw);
  
  
  // First round, we just find out what
  // the first and last verse are.
  foreach ( $chaplins as $chapline )
  {
    $drc = explode(":",$chapline,4);
    if ( $drc[0] == "verses" )
    {
      if ( $vrs_a == "x" ) { $vrs_a = $drc[1]; }
      if ( $vrs_z == "x" ) { $vrs_z = $drc[2]; }
    }
  }
  
  
  $lastvrs = "x"; // First verse we show can't have a previous
  $begany = false;
  foreach ( $chaplins as $chapline )
  {
    $drc = explode(":",$chapline,4);
    $oksa = true;
    if ( $oksa ) { if ( $drc[0] != "v" ) { $oksa = false; } }
    if ( $oksa ) { if ( $drc[1] < ( $vrs_a - 0.5 ) ) { $oksa = false; } }
    if ( $oksa ) { if ( $drc[1] > ( $vrs_z + 0.5 ) ) { $oksa = false; } }
    if ( $oksa )
    {
      if ( $drc[2] == "prg" )
      {
        if ( $begany ) { $this->vr_buf .= "\n<br/><br/>\n"; }
        $begany = false;
      }
      if ( $drc[2] == "lin" )
      {
        if ( $begany ) { $this->vr_buf .= "\n<br/><br/>\n"; }
        $begany = false;
        
        //$this->vr_buf .= "<br/>\n";
        
        $reman = $drc[3];
        while ( $reman > 0.5 )
        {
          $this->vr_buf .= "&nbsp; ";
          $reman = ((int)($reman - 0.8));
        }
      }
      if ( $drc[2] == "label" )
      {
        if ( $begany ) { $this->vr_buf .= "\n<br/><br/>\n"; }
        
        $this->vr_buf .= "<font size = \"+1\">" . htmlspecialchars($drc[3]) . "</font>";
        
        $this->vr_buf .= "\n<br/><br/>\n";
        $begany = false;
      }
      if ( $drc[2] == "txt" )
      {
        if ( $lastvrs != $drc[1] )
        {
          // Does the mode here require us to force a separate line for each
          // verse?
          if ( $this->vr_force_verse_line )
          {
            if ( $begany ) { $this->vr_buf .= "\n<br/><br/>\n"; }
            $begany = false;
          }
          
          // If this is the first text in a verse, let us show the verse number.
          $this->vr_buf .= "\n<font size = \"-2\">" . $chpidb . $drc[1] . "</font>\n";
          $chpidb = "";
          $lastvrs = $drc[1];
          if ( $this->vr_prev_verse != $lastvrs )
          {
            if ( $this->vr_prev_verse != "0" )
            {
              $this->prv_vrs_tog();
            }
            $this->vr_prev_verse = $lastvrs;
          }
        }
        if ( $this->vr_oneven_is ) { $this->vr_buf .= $this->vr_oneven_a; }
        //if ( $lastvrs == "0" ) { $this->vr_buf .= "<b>"; }
        $this->vr_buf .= htmlspecialchars($drc[3]);
        //if ( $lastvrs == "0" ) { $this->vr_buf .= "</b>"; }
        if ( $this->vr_oneven_is ) { $this->vr_buf .= $this->vr_oneven_z; }
        $this->vr_buf .= "\n";
        $begany = true;
      }
    }
  }
  
  
  
}



public function opt_menu($rg_nom,$rg_when) {
  if ( $this->unprepared() ) { return; }
  
  $this->flushy();
  $this->vr_opt_buf = "\n<br/><table border = \"1\" cellpadding = \"2\">";
  $this->vr_opt_buf .= "<tr><td>\n";
  $this->vr_opt_nom = $rg_nom;
  $this->vr_opt_buf .= "Options for " . htmlspecialchars($rg_nom);
  $this->vr_opt_mnu = ((int)($this->vr_opt_mnu + 1.2));
  $this->vr_opt_num = 1;
  $this->vr_opt_buf .= "<ul>\n";
  $this->prv_enter_option
      ($this->vr_opt_mnu,
      $this->vr_opt_num,
      $this->vr_opt_nom,
      $rg_when)
  ;
}

public function opt_next($rg_when) {
  if ( $this->unprepared() ) { return; }
  
  $this->vr_opt_num = ((int)($this->vr_opt_num + 1.2));
  $this->prv_enter_option
      ($this->vr_opt_mnu,
      $this->vr_opt_num,
      $this->vr_opt_nom,
      $rg_when)
  ;
}

public function opt_done() {
  if ( $this->unprepared() ) { return; }
  
  $md = $GLOBALS["sn_mode_inf"];
  
  
  $this->vr_opt_buf .= "</ul>\n</td></tr></table>\n<br/>\n";
  
  echo $this->vr_opt_buf;
  $this->vr_opt_buf = "";
  
  $this->vr_buf .= "<p><font color = \"#" . $md["offred_color"] . "\" size = \"+1\"><b><i>----\n";
  $this->vr_buf .= "End of options for " . htmlspecialchars($this->vr_opt_nom);
  $this->vr_buf .= "</i></b></font></p>\n";
  
  $this->flushy();
}

protected function prv_enter_option ( $rg_mnu, $rg_opt, $rg_nom, $rg_when ) {
  $md = $GLOBALS["sn_mode_inf"];
  $clac = "mnu" . $this->vr_res_name . "_" . $rg_mnu . "p" . $rg_opt;
  $this->vr_opt_buf .= "<li><p>";
  $this->vr_opt_buf .= "<a href = \"#" . $clac . "\">";
  $this->vr_opt_buf .= "Option " . $rg_opt;
  if ( $rg_when != "" )
  {
    $this->vr_opt_buf .= "\n--\n" . htmlspecialchars($rg_when);
  }
  $this->vr_opt_buf .= "</a>";
  $this->vr_opt_buf .= "</p></li>\n";
  
  $this->vr_buf .= "\n<a name = \"" . $clac . "\" />\n";
  $this->vr_buf .= "<p><font color = \"#" . $md["offred_color"] . "\" size = \"+1\"><b><i>----\n";
  $this->vr_buf .= htmlspecialchars($rg_nom) . " Option " . $rg_opt;
  if ( $rg_when != "" )
  {
    $this->vr_buf .= "\n--\n" . htmlspecialchars($rg_when);
  }
  $this->vr_buf .= "</i></b></font></p>\n";
}

public function label ( $rg_a )
{
  if ( $this->unprepared() ) { return; }
  
  $md = $GLOBALS["sn_mode_inf"];
  
  $this->vr_buf .= "<p><font color = \"#" . $md["offred_color"] . "\" size = \"+1\"><b><i>----\n";
  
  $this->vr_buf .= htmlspecialchars($rg_a);
  
  $this->vr_buf .= "</i></b></font></p>\n";
}


public function ot_reading ( $rg_book, $rg_parts ) {
  if ( $this->unprepared() ) { return; }
  
  $this->vr_scriptres = $GLOBALS["scriptura"];
  $this->vr_oneven_a = "";
  $this->vr_oneven_z = "";
  $this->prv_reading_pre ( $rg_book, $rg_parts );
  if ( !($this->vr_so) ) { return; }
  
  $this->rob_custom->ebuf($this->vr_info);
  $this->vr_buf .= $this->rob_custom->ot_before();
  
  $this->prv_reading_now();
  
  $this->vr_buf .= $this->rob_custom->ot_after();
}


public function psalm ( $rg_parts ) {
  if ( $this->unprepared() ) { return; }
  
  $this->vr_scriptres = $GLOBALS["psalmsres"];
  $this->vr_oneven_a = "<u>";
  $this->vr_oneven_z = "</u>";
  $this->prv_reading_pre ( "ps", $rg_parts );
  if ( !($this->vr_so) ) { return; }
  //$this->vr_buf .= "\n<p>A reading from the " .
  //  $this->vr_info["bigname"] . "</p>\n";
  //;
  
  $this->vr_buf .= $this->rob_custom->psalm_before();
  
  $this->prv_reading_now();
  
  $this->vr_buf .= $this->rob_custom->psalm_after();
  
  //$this->vr_buf .= 
  //  "\n<p>Glory be to the Father and to the Son and to the Holy Spirit</p>\n"
  //  . "\n<p><u>As it was in the beginning, is now, and ever shall be"
  //  . " - world without end. Amen.</u></p>\n";
  //;
    
}


public function psalm_a ( $rg_parts ) {
  if ( $this->unprepared() ) { return; }
  
  $this->vr_scriptres = $GLOBALS["psalmsres"];
  $this->vr_oneven_a = "<u>";
  $this->vr_oneven_z = "</u>";
  $this->prv_reading_pre ( "ps", $rg_parts );
  if ( !($this->vr_so) ) { return; }
  
  $this->vr_buf .= $this->rob_custom->psalm_before();
  
  $this->prv_reading_now();
  
  $this->vr_buf .= $this->rob_custom->psalm_tween_a();
}


public function psalm_c ( $rg_parts ) {
  if ( $this->unprepared() ) { return; }
  
  $this->vr_scriptres = $GLOBALS["psalmsres"];
  $this->vr_oneven_a = "<u>";
  $this->vr_oneven_z = "</u>";
  $this->prv_reading_pre ( "ps", $rg_parts );
  if ( !($this->vr_so) ) { return; }
  
  $this->vr_buf .= $this->rob_custom->psalm_tween_c();
  
  $this->prv_reading_now();
  
  $this->vr_buf .= $this->rob_custom->psalm_after();
}


public function canticle ( $rg_book, $rg_parts, $rg_name ) {
  if ( $this->unprepared() ) { return; }
  
  $this->vr_force_verse_line = true; // Canticles must force line-per-verse.
  $this->vr_scriptres = $GLOBALS["scriptura"];
  $this->vr_oneven_a = "<u>";
  $this->vr_oneven_z = "</u>";
  $this->prv_reading_pre ( $rg_book, $rg_parts );
  if ( !($this->vr_so) ) { return; }
  //$this->vr_buf .= "\n<p>A reading from the " .
  //  $this->vr_info["bigname"] . "</p>\n";
  //;
  
  $this->vr_buf = $this->rob_custom->canticle_before($rg_name);
  
  $this->prv_reading_now();
  
  $this->vr_buf .= $this->rob_custom->canticle_after();
  ;
    
}


public function nt_reading ( $rg_book, $rg_parts ) {
  if ( $this->unprepared() ) { return; }
  
  $this->vr_scriptres = $GLOBALS["scriptura"];
  $this->vr_oneven_a = "";
  $this->vr_oneven_z = "";
  $this->prv_reading_pre ( $rg_book, $rg_parts );
  if ( !($this->vr_so) ) { return; }
  
  $this->rob_custom->ebuf($this->vr_info);
  $this->vr_buf .= $this->rob_custom->nt_before();
  
  $this->prv_reading_now();
  
  $this->vr_buf .= $this->rob_custom->nt_after();
}


public function gospel ( $rg_book, $rg_parts ) {
  if ( $this->unprepared() ) { return; }
  
  $this->vr_scriptres = $GLOBALS["scriptura"];
  $this->vr_oneven_a = "";
  $this->vr_oneven_z = "";
  $this->prv_reading_pre ( $rg_book, $rg_parts );
  if ( !($this->vr_so) ) { return; }
  
  $this->rob_custom->ebuf($this->vr_info);
  $this->vr_buf .= $this->rob_custom->gospel_before();
  
  $this->prv_reading_now();
  
  $this->vr_buf .= $this->rob_custom->gospel_after();
}


public function aleluia ( ) {
  if ( $this->unprepared() ) { return; }
  
  $this->vr_buf .= $this->rob_custom->aleluia();
}

protected function maychange ( $rg_why )
{
  $this->vr_buf .= "\n<p><font size = \"-2\"><i>(";
  $this->vr_buf .= "This text is subject to possible";
  $this->vr_buf .= " change because:\n";
  $this->vr_buf .= htmlspecialchars($rg_why);
  $this->vr_buf .= ")</i></font></p>\n";
}


protected function prv_reading_pre ( $rg_book, $rg_parts ) {
  $this->vr_oneven_is = true;
  $this->vr_prev_verse = "x";
  $slclist = $this->prv_divide_readings($rg_parts);
  $infos = $this->loady($rg_book);
  if ( !($this->vr_so) ) { return; }
  $this->vr_info = $infos;
  $this->vr_sectos = $slclist;
  
  $this->vr_bookid = $rg_book;
  $this->vr_bookref = $rg_parts;
  //foreach ($slclist as $kio)
  //{
  //  //echo "\n<br/>----\n" . $kio . "<br/>\n";
  //}
}

protected function loady ( $rg_book )
{
  $scripdr = $this->vr_scriptres;
  $this->vr_so = true;
  $pickfile = $scripdr . "/" . $rg_book . ".info";
  if ( !(file_exists($pickfile) ) )
  {
    echo "<br/><br/><h1>FILE MISSING FROM SCRIPTURE-RESOURCE:</h1>\n";
    echo $pickfile . "<br/><br/>\n";
    $this->vr_so = false;
    return;
  }
  $infcon = file_get_contents($pickfile);
  $reta = array();
  $rawy = explode("\n",$infcon);
  foreach ( $rawy as $lina )
  {
    $neos = explode(":",$lina,2);
    $nom = $neos[0];
    $val = $neos[1];
    if ( $nom != "" )
    {
      $reta[$nom] = $val;
    }
  }
  
  $targix = array("bigname","midname","abrv");
  foreach ( $targix as $targon ) {
    if ( $reta[$targon] == "" )
    {
      echo "<br/><br/><h1>SCRIPTURE-RESOURCE FILE MISSING INFO:</h1>\n";
      echo "File: " . $pickfile . ":<br/>\n";
      echo "Variable: " . $targon . ":<br/>\n";
      echo "<br/>\n";
      $this->vr_so = false;
      return;
    }
  }
  
  return $reta;
}

protected function prv_divide_readings ( $rg_raw )
{
  $this->vr_hadcolon = false;
  $reta = array();
  $djonx = explode(", ",$rg_raw);
  foreach ( $djonx as $djone )
  {
    list($bfor,$afta) = explode("-",$djone);
    if ( $afta == "" ) { $afta = $bfor; }
    list($pre_c,$pre_v) = $this->prv_chapverse($bfor);
    list($pos_c,$pos_v) = $this->prv_chapverse($afta);
    $cur_c = $pre_c;
    $cur_v = $pre_v;
    
    if ( $cur_c == $pos_c )
    {
      $reta[] = $pre_c . ":" . $pre_v . "-" . $pos_v;
      $cur_c = ((int)($cur_c + 1.2)); $cur_v = "x";
    }
    
    while ( $cur_c < ( $pos_c - 0.5 ) )
    {
      $reta[] = $cur_c . ":" . $cur_v . "-x";
      $cur_c = ((int)($cur_c + 1.2)); $cur_v = "x";
    }
    
    if ( $cur_c == $pos_c )
    {
      $reta[] = $cur_c . ":" . $cur_v . "-" . $pos_v;
      $cur_c = ((int)($cur_c + 1.2)); $cur_v = "x";
    }
    
    
    
    //$reta[] = $pre_c . ":" . $pre_v . "-" . $pos_c . ":" . $pos_v;
  }
  return $reta;
}

protected function prv_chapverse ( $rg_raw )
{
  list($chap,$vrs) = explode(":",$rg_raw);
  if ( $vrs == "" )
  {
    if ( $this->vr_hadcolon )
    {
      return array($this->vr_curchapt,$chap);
    }
    return array($chap,"x");
  }
  $this->vr_hadcolon = true;
  $this->vr_curchapt = $chap;
  return array($chap,$vrs);
}



} ?>