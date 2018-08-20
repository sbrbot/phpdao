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

        <div class="col-sm-8 col-sm-offset-2">
          <div class="thumbnail">

            <div class="alert alert-info"><b>PHP DAO Builder</b> is web application which generates <b>persistance (DAO) layer</b> in PHP for <b>MySQL database</b>.</div>
            <p>The whole process consists of four steps:</p>
            <ol>
              <li> Provide your credentials and connect to your MySQL database scheme you want to generate DAO layer for.<br />
              (Then DAO Builder will read all tables and views in given scheme.)</li>
              <li> Define which tables/views should be included in your DAO layer and what should be the names of corresponding objects mapped to tables/views.<br />
              (Then DAO Builder will read all columns from selected tables/views.)</li>
              <li> Define what columns should be included, what methods created, and what should be its names.<br />
              (By default DAO Builder will suggest optimal settings of methods according to column attributes: primary keys, foreign keys, existence of index, etc.). You can change it.</li>
              <li> Proceed and DAO Builder will create DAO layer with classes.</li>
            </ol>
            <div class="alert alert-info"><b>PHP DAO Builder</b> stores all DAO setting (object/method names, etc.) into <b>model/dao.xml</b> for later DAO layer rebuilding.</div>

          </div><!-- .thumbnail -->
        </div><!-- .col -->
      </div><!-- .row -->

      <div class="row text-center">
        <div class="col-sm-12">
          <a href="dao0.php" class="btn btn-info">Let's go!</a>
        </div><!-- .col -->
      </div><!-- .row -->

<?php
include('foot.inc');
?>

    </div><!-- .container -->

  </body>

</html>
