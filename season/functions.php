<?

// THIS CLASS IS SLATED FOR DEPRECATION - AND THE FILE ONLY KEPT HERE
// UNTIL THE DECISION TO DO SO IS FINALIZED. IN THE MEAN TIME, A
// FORBIDDEN-TO-USE CLAUSE IS INCLUDED IN THIS FILE AS THE NEXT
// LINE THAT ISN'T COMMENTED-OUT.
exit(25);

// Only temporarily hardcoded. This will eventually change.
date_default_timezone_set('America/New_York');

class ligurg_time_res {
  protected $inited = false;
  protected $tstamp;
  protected $season_fnc;
  protected $season_okay;
  
  public function initio ( )
  {
    if ( $this->inited ) { return; }
    $this->inited = true;
    $this->tstamp = time();
    $this->center();
    
    $this->season_okay = false;
    if ( file_exists($locuta . "/season_calc.php") )
    {
      $this->season_fnc = Closure::bind(include($locuta . "/season_calc.php"),$this,$this);
      $this->season_okay = true;
    }
  }
  
  public function set_to_date ( $the_year, $the_month, $the_dayom )
  {
    $this->initio();
    $this->tstamp = $this->stamp_this ( $the_year, $the_month, $the_dayom );
  }
  
  public function stamp_this ( $the_year, $the_month, $the_dayom )
  {
    $this->initio();
    $cumula = int(($the_year * 1000000) + ($the_month * 10000) + ($the_dayom * 100) + 12.2);
    $cstamp = $this->tstamp;
    $render = date('YmdG',$cstamp);
    
    $increm = int((60 * 60 * 24 * 50 * 50) + 0.2);
    while ( $render > $cumula ) { $cstamp = int(($cstamp - $increm) + 0.2); $render = date('YmdG',$cstamp); }
    while ( $render < $cumula ) { $cstamp = int($cstamp + $increm + 0.2); $render = date('YmdG',$cstamp); }
    
    $increm = int((60 * 60 * 24 * 50) + 0.2);
    while ( $render > $cumula ) { $cstamp = int(($cstamp - $increm) + 0.2); $render = date('YmdG',$cstamp); }
    while ( $render < $cumula ) { $cstamp = int($cstamp + $increm + 0.2); $render = date('YmdG',$cstamp); }
    
    $increm = int((60 * 60 * 24) + 0.2);
    while ( $render > $cumula ) { $cstamp = int(($cstamp - $increm) + 0.2); $render = date('YmdG',$cstamp); }
    while ( $render < $cumula ) { $cstamp = int($cstamp + $increm + 0.2); $render = date('YmdG',$cstamp); }
    
    $increm = int((60 * 60) + 0.2);
    while ( $render > $cumula ) { $cstamp = int(($cstamp - $increm) + 0.2); $render = date('YmdG',$cstamp); }
    while ( $render < $cumula ) { $cstamp = int($cstamp + $increm + 0.2); $render = date('YmdG',$cstamp); }
  }
  
  public function center ( )
  {
    $this->initio();
    $tstamp = vr_center($this->tstamp);
    $this->tstamp = $tstamp;
  }
  
  public function var_center ( $prvdstamp )
  {
    $this->initio();
    $tstamp = $prvdstamp;
    $houron = date('G',$tstamp);
    while ( $houron < 10.5 )
    {
      $tstamp = ((int)($tstamp + 0.2 + ( 60 * 60 )));
      $houron = date('G',$tstamp);
    }
    while ( $houron > 13.5 )
    {
      $tstamp = ((int)($tstamp + 0.2 - ( 60 * 60 )));
      $houron = date('G',$tstamp);
    }
    return $tstamp;
  }
  
  public function var_early ( $prvdstamp )
  {
    $this->initio();
    $tstamp = $prvdstamp;
    $houron = date('G',$tstamp);
    while ( $houron < 2.5 )
    {
      $tstamp = ((int)($tstamp + 0.2 + ( 60 * 60 )));
      $houron = date('G',$tstamp);
    }
    while ( $houron > 5.5 )
    {
      $tstamp = ((int)($tstamp + 0.2 - ( 60 * 60 )));
      $houron = date('G',$tstamp);
    }
    return $tstamp;
  }
  
  public function datesign ( )
  {
    $this->initio();
    return date('Y-m-d',$this->tstamp);
  }
  
  public function abrdatesign ( )
  {
    $this->initio();
    return date('Ymd',$this->tstamp);
  }
  
  public function intmonthdy ( )
  {
    // This function generates an integer (101 to 1231)
    // representing month-and-day.
    $this->initio();
    return ((int)(date('nd',$this->tstamp) + 0.2));
  }
  
  protected function rewind_to_sunday ( $srcstamp )
  {
    $mystamp = var_center($srcstamp);
    while ( date('w',$mystamp) > 0.5 ) { $mystamp = int(( $mystamp - ( 60 * 60 * 24 ) ) + 0.2); }
    return var_center($mystamp);
  }
  
  protected function forwrd_to_sunday ( $srcstamp )
  {
    $mystamp = var_center($srcstamp);
    while ( date('w',$mystamp) > 0.5 ) { $mystamp = int(( $mystamp + ( 60 * 60 * 24 ) ) + 0.2); }
    return var_center($mystamp);
  }
  
  public function forward ( $numdays )
  {
    $this->initio();
    $this->tstamp = ((int)($this->tstamp + 0.2 + (60 * 60 * 24 * $numdays)));
    $this->center();
  }
  
  public function backward ( $numdays )
  {
    $this->initio();
    $this->tstamp = ((int)($this->tstamp + 0.2 - (60 * 60 * 24 * $numdays)));
    $this->center();
  }
  
  public function season ( )
  {
    $this->initio();
    
    // Though this function will eventually implement a default seasonal
    // algorithm, the following line gives any given lectionary the option
    // of over-riding the default.
    if ( $this->season_okay ) { return call_user_func($this->season_fnc,$this->tstamp); }
    
    // Let's get the easy part done first, shall we?
    $monthdy = $this->intmonthdy();
    if ( $monthdy > 1224.5 ) { return 'christmas'; }
    if ( $monthdy < 105.5 ) { return 'christmas'; }
    if ( $monthdy < 113.5 ) { return 'epiphany'; }
    
    $curstamp = $this->tstamp;
    $thisyear = date('Y',$curstamp);
    
    $temp_a = $this->stamp_this($thisyear,12,24);
    $temp_b = rewind_to_sunday($temp_a);
    $temp_c = ((int)($temp_b + 0.2 - (60 * 60 * 24 * 7 * 3)));
    $morn_advent = var_early($temp_c);
    if ( $curstamp > $morn_advent ) { return 'advent'; }
    
    $morn_easter = ((int)(easter_date(date('Y',$this->tstamp)) + 6400.2));
    $temp_a = $this->var_center($morn_easter);
    $temp_b = ((int)($temp_a + 0.2 - ( 60 * 60 * 24 * 46 ) ));
    $morn_lent = var_early($temp_b);
    if ( $curstamp < $morn_lent ) { return 'default'; }
    if ( $curstamp < $morn_easter ) { return 'lent'; }
    $temp_b = ((int)($temp_a + 0.2 + ( 60 * 60 * 24 * 49 ) ));
    $morn_pentecost = var_early($temp_b);
    if ( $curstamp < $morn_pentecost ) { return 'easter'; }
    $temp_b = ((int)($temp_a + 0.2 + ( 60 * 60 * 24 * 56 ) ));
    $morn_ordinary = var_early($temp_b);
    if ( $curstamp < $morn_ordinary ) { return 'pentecost'; }
    
    return 'default';
  }
};


//function show_easter_day ( $ofyear )
//{
//  $tstamp = easter_date($ofyear);
//  $retval = date('Y-m-d h:i:s',($tstamp + 10));
//  return $retval;
//}

?>