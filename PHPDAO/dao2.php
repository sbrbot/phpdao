<?php

session_start();

if(isset($_POST['dao1']) || isset($_SESSION['DB_NAME']))
{
  if(isset($_POST['dao1']))
  {
    $_SESSION['tvs']=$_POST['tvs'];
    $_SESSION['tvtypes']=$_POST['tvtypes'];
    //$_SESSION['tvupdatable']=$_POST['tvupdatable'];
    $_SESSION['tvobject']=$_POST['tvobject'];
    $_SESSION['tvobjects']=$_POST['tvobjects'];
  }
}
else
{
  header('Location: index.php');
  exit;
}

//~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

?>
<!DOCTYPE html>
<html lang="en">
  <head>
<?php
include('head.inc');
?>
  </head>
  <body>

<?php
include('navbar.inc');
?>

    <div class="container">

      <h1>Columns</h1>

      <hr>

      <div id="ajax">
        <div class="text-center"><img src="get.gif"><br>loading columns<br><span id="counter">0</span></div>
      </div>

<?php
include('foot.inc');
?>

    </div><!-- .container -->

    <script type="text/javascript">
      var c=1,counter=setInterval(function(){$("#counter").text(c++);},1000);
      $.post('ajax2.php','',function(data){$("#ajax").html(data);clearInterval(counter);});
    </script>

  </body>

</html>
