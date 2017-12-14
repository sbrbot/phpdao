<?php

if(file_exists('../models/dao.xml'))
{
  $XML=simplexml_load_file('../models/dao.xml');
  $XmlDatabase=$XML->database;
  $db_host=$XmlDatabase[0]['host'];
  $db_name=$XmlDatabase[0]['name'];
  $db_user=$XmlDatabase[0]['user'];
}
else
{
  $db_host='localhost';
  $db_name='';
  $db_user='';
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

      <h1>Connection</h1>

      <hr>

      <form method="post" action="dao1.php">

        <div class="row">

          <div class="col-sm-4 col-sm-offset-4">
            <div class="thumbnail">

              <label for="DB_HOST" class="control-label">Hostname</label>
              <input id="DB_HOST" name="DB_HOST" type="text" class="form-control" value="localhost">

              <label for="DB_NAME" class="control-label">Database</label>
              <input id="DB_NAME" name="DB_NAME" type="text" class="form-control" value="<?= $db_name ?>">

              <label for="DB_USER" class="control-label">Username</label>
              <input id="DB_USER" name="DB_USER" type="text" class="form-control" value="<?= $db_user ?>">

              <label for="DB_PASS" class="control-label">Password</label>
              <input id="DB_PASS" name="DB_PASS" type="password" class="form-control">

              <br>

            </div><!-- .thumbnail -->
          </div><!-- .col -->

        <div class="row text-center">

          <div class="col-sm-4 col-sm-offset-4">
              <input type="submit" name="dao0" class="btn btn-info" value="Connect">
          </div><!-- .col -->

        </div><!-- .row -->

      </form>

<?php
include('foot.inc');
?>

    </div><!-- .container -->

  </body>

</html>
