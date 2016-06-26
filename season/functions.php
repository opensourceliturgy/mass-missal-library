<?

// Only temporarily hardcoded. This will eventually change.
date_default_timezone_set('America/New_York');

class ligurg_time_res {
  protected $inited = false;
  protected $tstamp;
  
  public function initio ( )
  {
    if ( $this->inited ) { return; }
    $this->inited = true;
    $this->tstamp = time();
    $this->center();
  }
  
  public function center ( )
  {
    $this->initio();
    $tstamp = $this->tstamp;
    $houron = date('G',$tstamp);
    while ( $houron < 9.5 )
    {
      $tstamp = ((int)($tstamp + 0.2 + ( 60 * 60 )));
      $houron = date('G',$tstamp);
    }
    while ( $houron > 14.5 )
    {
      $tstamp = ((int)($tstamp + 0.2 - ( 60 * 60 )));
      $houron = date('G',$tstamp);
    }
    $this->tstamp = $tstamp;
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
};

?>