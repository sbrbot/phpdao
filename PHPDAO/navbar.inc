    <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
      <div class="container">
        <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarResponsive" aria-controls="navbarResponsive" aria-expanded="false" aria-label="Toggle navigation">
          <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarResponsive">
          <ul class="navbar-nav" role="menu">
<?php
$tabs1=['index.php'=>'<i class="fa fa-home"></i>','dao0.php'=>'Connection','dao1.php'=>'Tables/Views','dao2.php'=>'Columns','dao3.php'=>'Builder'];
foreach($tabs1 as $page=>$tab)
{
  $active = ($page==basename($_SERVER['PHP_SELF'])) ? ' active' : '';
  echo '            <li class="nav-item'.$active.'"><a class="nav-link" role="menuitem" href="'.$page.'">'.$tab.'</a></li>'.PHP_EOL;
}
?>
          </ul>
          <ul class="nav navbar-nav ml-auto" role="menu">
<?php
$tabs2=['help.php'=>'<i class="fa fa-question-circle"></i>','about.php'=>'<i class="fa fa-info-circle"></i>'];
foreach($tabs2 as $page=>$tab)
{
  $active = ($page==basename($_SERVER['PHP_SELF'])) ? ' active' : '';
  echo '            <li class="nav-item'.$active.'"><a class="nav-link" role="menuitem" href="'.$page.'">'.$tab.'</a></li>'.PHP_EOL;
}
?>
          </ul>
        </div>
      </div><!-- container -->
    </nav>
