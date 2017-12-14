    <!-- Navigation -->
    <nav class="navbar navbar-inverse navbar-fixed-top" role="navigation">
      <div class="container">
        <div class="navbar-header">
          <button type="button" class="navbar-toggle" data-toggle="collapse" data-target="#bs-example-navbar-collapse-1">
            <span class="sr-only">Toggle navigation</span>
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
          </button>
          <a class="navbar-brand" href="index.php">PHP DAO Builder v3.2</a>
        </div>
        <div class="collapse navbar-collapse" id="bs-example-navbar-collapse-1">
          <ul class="nav navbar-nav navbar-right">
<?php
$tabs=['dao0.php'=>'Connection','dao1.php'=>'Tables/Views','dao2.php'=>'Columns','dao3.php'=>'Builder','help.php'=>'Help'];
foreach($tabs as $page=>$tab)
{
  $class = ($page==basename($_SERVER['PHP_SELF'])) ? ' class="active"' : '';
  echo '            <li'.$class.'><a href="'.$page.'">'.$tab.'</a></li>'.PHP_EOL;
}
?>
          </ul>
        </div>
        <!-- /.navbar-collapse -->
      </div>
      <!-- /.container -->
    </nav>
