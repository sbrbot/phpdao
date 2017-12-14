<?php

session_start();

require 'Engine.php';

try
{
  $Engine=new Engine();

  $tvs=$Engine->getTablesAndViews();

  if(file_exists('../models/dao.xml'))
  {
    $XML=simplexml_load_file('../models/dao.xml');
  }
  else
  {
    $XML=new SimpleXMLElement('<dao></dao>');
  }
  $XmlTables=$XML->dao;

?>
      <form method="post" action="dao2.php">

        <div class="row">

          <div class="col-sm-8 col-sm-offset-2">
            <div class="thumbnail">

              <div class="alert alert-info">Select tables and views you want to create objects for and then define how on tables/views mapped objects will be named in singular and plural forms.</div>

              <table class="table">
                <tr>
                  <th>DAO</th>
                  <th>NAME</th>
                  <th>ENGINE</th>
                  <th>TYPE</th>
                  <!--th>UPDATABLE</th-->
                  <th>COMMENT</th>
                  <th>OBJECT (singular)</th>
                  <th>OBJECTS (plural)</th>
                </tr>
<?php

  foreach($tvs as $tv)
  {
    if($XmlTable=$XmlTables->xpath("//table[@name='".$tv['TABLE_NAME']."']"))
    {
      $singular=$XmlTable[0]['singular'];
      $plural=$XmlTable[0]['plural'];
      $checked=($XmlTable[0]['active']=='yes') ? ' checked="checked"' : '';
    }
    else
    {
      $English=new English($tv['TABLE_NAME']);
      $singular=$English->singular;
      $plural=$English->plural;
      $checked=' checked="checked"';
    }
?>
                <tr>
                  <td align="center"><input type="checkbox" name="tvs[<?= $tv['TABLE_NAME'] ?>]"<?= $checked ?>></td>
                  <td><?= $tv['TABLE_NAME'] ?><?php if(!$XmlTable) echo ' <span class="label label-info">NEW</span>'; ?></td>
                  <td align="center"><?php if($tv['TABLE_TYPE']=='TABLE') echo $tv['ENGINE']; ?></td>
                  <td align="center"><input type="hidden" name="tvtypes[<?= $tv['TABLE_NAME'] ?>]" value="<?= $tv['TABLE_TYPE'] ?>"><?= $tv['TABLE_TYPE'] ?></td>
                  <!--td align="center"><input type="hidden" name="tvupdatable[<?= $tv['TABLE_NAME'] ?>]" value="<?= $tv['IS_UPDATABLE'] ?>" ><?php if($tv['IS_UPDATABLE']==='YES') echo ' <span class="glyphicon glyphicon-ok"></span>'; ?></td-->
                  <td align="center"><?php if($tv['TABLE_COMMENT']) echo '<span title="'.$tv['TABLE_COMMENT'].'" class="glyphicon glyphicon-comment"></span>'; ?></td>
                  <td><input type="text" name="tvobject[<?= $tv['TABLE_NAME'] ?>]" value="<?= $singular ?>"></td>
                  <td><input type="text" name="tvobjects[<?= $tv['TABLE_NAME'] ?>]" value="<?= $plural ?>"></td>
                </tr>
<?php
  }
?>
              </table>
<?php
}
catch(ConnectionException $ce)
{
?>
              <div class="alert alert-danger text-center">Cannot connect to database!</div>
<?php
}
?>
            </div><!-- .thumbnail -->
          </div><!-- .col -->
        </div><!-- .row -->

        <div class="row text-center">
          <div class="col-sm-4 col-sm-offset-4">
            <input type="submit" name="dao1" class="btn btn-info" value="Proceed">
          </div><!-- .col -->
        </div><!-- .row -->

      </form>
