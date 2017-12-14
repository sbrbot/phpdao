<!DOCTYPE html>
<html>
  <head>
    <meta charset="UTF-8">
    <title>PHPDAO</title>
  </head>
  <body>

    <div align="center">

      <h1>DAO</h1>

      <table>
        <tr><td align="center"><a href="?class=Category">Category</td></tr>
        <tr><td align="center"><a href="?class=Employee">Employee</td></tr>
        <tr><td align="center"><a href="?class=EmployeeProject">EmployeeProject</td></tr>
        <tr><td align="center"><a href="?class=Project">Project</td></tr>

      </table>
<?php

if(isset($_GET['class']))
{
  $class=$_GET['class'];

  require '../model/'.$class.'.php';

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

?>

    </div>
  </body>
</html>
