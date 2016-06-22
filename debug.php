<?php

function debug ( $rg_a )
{
  echo "\n<h1>DEBUG: " . htmlspecialchars($rg_a) . "</h1>\n";
}

function debug_v ( $rg_a, $rg_v )
{
  echo "\n<h1>DEBUG: " . htmlspecialchars($rg_a) . "</h1>\n";
  echo "\n<pre>";
  var_dump($rg_v);
  echo "</pre>\n";
}


?>