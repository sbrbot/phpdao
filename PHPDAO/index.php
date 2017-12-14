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
              <li> Provide your credentials and connect to your MySQL database scheme for which you want to generate DAO layer.</li>
              <li> DAO Builder will read all tables and views. Then define which tables/views should be included in your DAO layer and what should be the names of corresponding objects mapped to tables/views.</li>
              <li> DAO Builder will read all columns from selected tables/views. Then define what columns should be included, what method created, and what should be its names. According to column attribtes read (primary key, foreign key, existence of index, etc.), DAO Builder will by default suggest optimal settings of methods.</li>
              <li> Finally DAO layer classes will be created.</li>
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
