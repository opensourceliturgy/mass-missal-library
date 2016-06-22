<?php

require_once $libdir . "/symbos/mainform.php";
require_once $libdir . "/symbos/narrowform.php";


$sx = true;

if ( $sx ) { if ( $stylo == "narrow" ) {
  $sm = new symbos_narrowform;
  $sx = false;
} }
if ( $sx ) { $sm = new symbos_mainform; }


?>