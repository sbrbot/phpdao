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

      <h1>PHP Data Access Objects Builder</h1>

      <hr>

      <div class="row">

        <div class="col-12">

          <div class="alert alert-info"><b>PHP DAO Builder</b> is web application which generates <b>persistance (DAO) layer</b> in PHP for <b>MySQL database</b>.</div>
          <p>The whole process consists of four steps:</p>
          <ol>
            <li> Provide your credentials and connect to your MySQL database schema you want to generate DAO layer for.<br>
                 (Then DAO Builder will read all tables and views in given schema.)</li>
            <br>
            <li> Define which tables/views should be included in your DAO layer and what should be the names of corresponding objects mapped to tables/views.<br>
                 (Then DAO Builder will read all columns from selected tables/views.)</li>
            <br>
            <li> Define what columns should be included, what methods created, and what should be its names.<br>
                 (DAO Builder will suggest initial settings of methods according to column attributes: primary keys, foreign keys, existence of index, etc.). You can change it.</li>
            <br>
            <li> Proceed and DAO Builder will create DAO layer with classes.</li>
          </ol>
          <div class="alert alert-info">
            <b>PHP DAO Builder</b> stores all DAO setting (object/method names, etc.) into <b>model/dao.xml</b> for later DAO layer rebuilding.<br>
            Next time when you run DAO Builder, it will read <b>dao.xml</b> and your schema, show if some DB entities were changed inside DB from last build up
            and recreate new DAO layer according to your new settings. Only DAO classes will be rebuilt/overwritten, not your entity (Business layer) classes.
          </div>

        </div><!-- .col -->
      </div><!-- .row -->

      <div class="row text-center">
        <div class="col-12">
          <a href="dao0.php" class="btn btn-info">Let's go!</a>
        </div><!-- .col -->
      </div><!-- .row -->

<?php
include('foot.inc');
?>

    </div><!-- .container -->

  </body>

</html>
