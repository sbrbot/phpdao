<?php

session_start();

if(isset($_POST['dao0']) || isset($_SESSION['DB_NAME']))
{
  if(isset($_POST['dao0']))
  {
    $_SESSION['DB_HOST']=$_POST['DB_HOST'];
    $_SESSION['DB_NAME']=$_POST['DB_NAME'];
    $_SESSION['DB_USER']=$_POST['DB_USER'];
    $_SESSION['DB_PASS']=$_POST['DB_PASS'];
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

      <h1>Tables/Views</h1>

      <hr>

      <div id="ajax">
        <div class="text-center"><img src="get.gif"><br>loading tables/views<br><span id="counter">0</span></div>
      </div>

<?php
include('foot.inc');
?>

    </div><!-- .container -->

    <script type="text/javascript">
      var c=1,counter=setInterval(function(){$("#counter").text(c++);},1000);
      $.post('ajax1.php','',function(data){$("#ajax").html(data);clearInterval(counter);});
    </script>

  </body>

</html>
