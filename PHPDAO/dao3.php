<?php

session_start();

if(isset($_POST['dao2']) || isset($_SESSION['DB_NAME']))
{
  if(isset($_POST['dao2']))
  {
    $_SESSION['getters']=$_POST['getters']; //2D array
    $_SESSION['setters']=$_POST['setters']; //2D array
    $_SESSION['finders']=$_POST['finders']; //2D array
    $_SESSION['constrs']=$_POST['constrs']; //2D array
    $_SESSION['methods']=$_POST['methods']; //2D array
    if(isset($_POST['backup']))
    {
      $_SESSION['backup']=$_POST['backup']; //backup
    }
    else
    {
      unset($_SESSION['backup']);
    }
  }
}
else
{
  header('Location: index.php');
  exit;
}

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

      <h1>Builder</h1>

      <hr>

      <div id="ajax">
        <div class="text-center"><img src="get.gif"><br>building DAO layer<br><span id="counter">0</span></div>
      </div>

<?php
include('foot.inc');
?>

    </div><!-- .container -->

    <script type="text/javascript">
      var c=1,counter=setInterval(function(){$("#counter").text(c++);},1000);
      $.post('ajax3.php','',function(data){$("#ajax").html(data);clearInterval(counter);});
    </script>

  </body>

</html>
