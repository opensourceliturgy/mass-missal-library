<?php
require_once $libdir . "/util_res_class.php";
class saint_calendar_dlg extends util_res_class {


protected $vr_urgent = true;
protected $vr_hyper_lbl;
protected $vr_errdfer = "";


public function by_toc ( $rg_resloc, $rg_page, $rg_showerr, $rg_outflush ) {
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
      if ( $whatpage == $rg_page )
      {
        $thatpage = $this->rob_string->relativ($rg_resloc,$where);
        $isdone = $this->by_saintpage($thatpage,$rg_showerr,$rg_outflush);
        if ( $isdone ) { $hasdone = true; }
      }
    }
  }
  if ( $hasdone ) { return $this->vr_so; }
  
  if ( $rg_showerr )
  {
    echo "\n<h1>ERROR: no such canon page: " . $rg_page . "</h1>\n";
    echo "<h3>" . htmlspecialchars($rg_resloc) . "</h3>";
  }
  
  return false;
  
}


public function by_saintpage($rg_page,$rg_showerr,$rg_outflush)
{
  $this->vr_hyper_lbl = "";
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
  $conta = file_get_contents($rg_page);
  $contb = explode("\n",$conta);
  $isee = false;
  foreach ( $contb as $contc )
  {
    list($base,$allrg) = explode(":",$contc,2);
    if ( $base == $this->vr_vars["main"]["part"] )
    {
      $isee = true;
      list($comnd,$resrg) = explode(":",$allrg,2);
      $done = $this->prv_saint_trm($comnd,$resrg);
      if ( !($done) )
      {
        if ( $rg_showerr )
        {
          echo "\n<h1>Error: Unknown command: ";
          echo htmlspecialchars($comnd);
          echo ": (in: " . htmlspecialchars($base) . ")";
          echo "</h1>\n";
          echo "<h2>File: " . htmlspecialchars($rg_page);
          echo "</h2>\n";
        }
      }
    }
  }
  
  $this->vr_so = true;
  if ( !($isee) )
  {
    if ( $rg_showerr )
    {
      if ( $this->vr_urgent )
      {
        echo "\n<h1>ERROR: Missing page section: ";
        echo htmlspecialchars($this->vr_vars["main"]["part"]) . ":</h1>\n";
        echo "\n<h2>File: " . htmlspecialchars($rg_page);
        echo "</h2>\n";
        echo $this->vr_errdfer;
        $this->vr_errdfer = "";
      } else {
        $this->vr_errdfer .= "\n<h2>Also tried: ";
        $this->vr_errdfer .= htmlspecialchars($rg_page);
        $this->vr_errdfer .= "</h2>\n";
      }
    }
    $this->vr_so = false;
  }
  
  if ( $this->vr_so ) { $this->vr_errdfer = ""; }
  if ( $rg_outflush ) { $this->flushy(); }
  return true;
}

public function outflush ( ) {
  $ret = $this->vr_buf;
  $this->vr_buf = "";
  return $ret;
}


protected function prv_clear_vars ( ) {
}



protected function prv_inform ( ) {
}


public function patient ( ) {
  $this->vr_urgent = false;
}

public function urgent ( ) {
  $this->vr_urgent = true;
}


protected function prv_saint_trm ( $rg_com, $rg_rgx )
{
  if ( $rg_com == "" ) { return true; }
  
  if ( $rg_com == "tx" )
  {
    $this->vr_buf .= htmlspecialchars($rg_rgx);
    return true;
  }
  
  if ( $rg_com == "btx" )
  {
    $this->vr_buf .= htmlspecialchars($rg_rgx) . "\n";
    return true;
  }
  
  if ( $rg_com == "pt" )
  {
    $this->vr_hyper_lbl .= htmlspecialchars($rg_rgx);
    return true;
  }
  
  if ( $rg_com == "bpt" )
  {
    $this->vr_hyper_lbl .= htmlspecialchars($rg_rgx) . "\n";
    return true;
  }
  
  if ( $rg_com == "to" )
  {
    $this->vr_buf .= "<a href = \"" . $rg_rgx . "\" target = \"_blank\"";
    $this->vr_buf .= "\n>" . $this->vr_hyper_lbl . "</a>";
    $this->vr_hyper_lbl = "";
    return true;
  }
  
  if ( $rg_com == "lit" )
  {
    $this->vr_buf .= $rg_rgx;
    return true;
  }
  
  if ( $rg_com == "blit" )
  {
    $this->vr_buf .= $rg_rgx . "\n";
    return true;
  }
  
  if ( $rg_com == "slit" )
  {
    $remain = $rg_rgx;
    while ( $remain > 0.5 )
    {
      $this->vr_buf .= " ";
      $remain = ((int)($remain - 0.8));
    }
    return true;
  }
  
  return false;
}



} ?>