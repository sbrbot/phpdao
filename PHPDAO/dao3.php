<?php

session_start();

if(isset($_POST['dao2']) || isset($_SESSION['DB_NAME']))
{
  if(isset($_POST['dao2']))
  {
    $_SESSION['getters'] = isset($_POST['getters']) ? $_POST['getters'] : []; //2D array
    $_SESSION['setters'] = isset($_POST['setters']) ? $_POST['setters'] : []; //2D array
    $_SESSION['finders'] = isset($_POST['finders']) ? $_POST['finders'] : []; //2D array
    $_SESSION['constrs'] = isset($_POST['constrs']) ? $_POST['constrs'] : []; //2D array
    $_SESSION['methods'] = isset($_POST['methods']) ? $_POST['methods'] : []; //2D array
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
        <div class="ajax text-center">
          <i class="fa fa-cog fa-spin fa-5x fa-fw"></i>
          <br>
          building DAO layer
          <br>
          <span id="counter">0</span>
        </div>
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
