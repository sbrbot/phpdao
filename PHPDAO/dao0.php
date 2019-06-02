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

          <div class="col-md-4 offset-md-4 col-sm-6 offset-sm-3">

            <label for="DB_HOST" class="control-label">Hostname</label>
            <div class="input-group">
              <div class="input-group-prepend">
                <div class="input-group-text"><i class="fa fa-archive"></i></div>
              </div>
              <input id="DB_HOST" name="DB_HOST" type="text" class="form-control" value="localhost">
            </div>

            <label for="DB_NAME" class="control-label">Database</label>
            <div class="input-group">
              <div class="input-group-prepend">
                <div class="input-group-text"><i class="fa fa-database"></i></div>
              </div>
              <input id="DB_NAME" name="DB_NAME" type="text" class="form-control" value="<?= $db_name ?>">
            </div>

            <label for="DB_USER" class="control-label">Username</label>
            <div class="input-group">
              <div class="input-group-prepend">
                <div class="input-group-text"><i class="fa fa-user"></i></div>
              </div>
              <input id="DB_USER" name="DB_USER" type="text" class="form-control" value="<?= $db_user ?>">
            </div>

            <label for="DB_PASS" class="control-label">Password</label>
            <div class="input-group">
              <div class="input-group-prepend">
                <div class="input-group-text"><i class="fa fa-unlock-alt"></i></div>
              </div>
              <input id="DB_PASS" name="DB_PASS" type="password" class="form-control">
            </div>

            <label for="DB_CHAR" class="control-label">Charset</label>
            <div class="input-group">
              <div class="input-group-prepend">
                <div class="input-group-text"><i class="fa fa-globe"></i></div>
              </div>
              <select id="DB_CHAR" name="DB_CHAR" class="form-control">
                <option value="utf8" selected="selected">utf8</option>
                <option value="latin1">latin1</option>
                <option value="latin2">latin2</option>
              </select>
            </div>

            <br>

          </div><!-- .col -->

        </div><!-- .row -->

        <div class="row text-center">

          <div class="col-12">
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
