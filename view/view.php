<?php

if(isset($_GET['class']))
{
  $class=$_GET['class'];

  require 'model/'.$class.'.php';

  $Object=new $class();

  $Objects=$Object->getList();

  echo '<table>';
  foreach($Objects as $Object)
  {
    echo '<tr>';
    foreach($Object as $propery=>$value)
    {
      echo '<td>'.$property.'</td>';
    }
    echo '</tr>';
  }
  echo '</table>';
}
