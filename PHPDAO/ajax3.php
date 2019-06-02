<?php

session_start();

$getters=$_SESSION['getters']; //2D array
$setters=$_SESSION['setters']; //2D array
$finders = isset($_SESSION['finders']) ? $_SESSION['finders'] : array(); //2D array
$constrs = isset($_SESSION['constrs']) ? $_SESSION['constrs'] : array(); //2D array
$methods=$_SESSION['methods']; //2D array (for finders and constraints (finders)

//@NOTE: do not share the same variable for regular and constraint finders
//       since in table can exist constraint name same as some table column name

//~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

function getCamelCase($input)
{
  return implode(array_map('ucfirst',explode('_',$input)));
}

//selected tables (on/off)
$tvs=$_SESSION['tvs']; //1D array

//object names in singular
$tvobject=$_SESSION['tvobject']; //1D array

//object names in plural
$tvobjects=$_SESSION['tvobjects']; //1D array

////////////////////////////////////////////////////////////////////////////////

if(!is_dir('../models'))
{
  mkdir('../models',0777) or die('Cannot create models folder!');
}

if(!is_dir('../models/DAO'))
{
  mkdir('../models/DAO',0777) or die('Cannot create models/DAO subfolder!');
}

//------------------------------------------------------------------------------

$file=fopen('../models/DAO/DAO.inc',"w");
$da=
"<?php

const DB_HOST='{$_SESSION['DB_HOST']}';
const DB_NAME='{$_SESSION['DB_NAME']}';
const DB_USER='{$_SESSION['DB_USER']}';
const DB_PASS='{$_SESSION['DB_PASS']}';
const DB_CHAR='{$_SESSION['DB_CHAR']}';

";
fwrite($file,$da);
fclose($file);

//------------------------------------------------------------------------------

copy("DAO.php","../models/DAO/DAO.php") or die('Unable to write file!');

//------------------------------------------------------------------------------

?>
      <div class="row">
        <div class="col-sm-4 offset-sm-4">
          <div class="img-thumbnail">
<?php

require 'Engine.php';

try
{
  $Engine=new Engine();
?>
            <table class="table bg-light table-sm">
              <tr><td><div class="alert alert-info"><i class="fa fa-info-circle"></i> Look at the "models" subdirectory in your project, the following DAO classes are created:</div></td></tr>
<?php

  $XML=new SimpleXMLElement('<dao></dao>');

  $tvsall=$Engine->getTablesAndViews();

  $XMLtable=$XML->addChild('database');
  $XMLtable->addAttribute('host',$_SESSION['DB_HOST']);
  $XMLtable->addAttribute('name',$_SESSION['DB_NAME']);
  $XMLtable->addAttribute('user',$_SESSION['DB_USER']);
  $XMLtable->addAttribute('charset',$_SESSION['DB_CHAR']);

  foreach($tvsall as $table)
  {
    $tvname=$table['TABLE_NAME'];

    $XMLtable=$XML->addChild('table');
    $XMLtable->addAttribute('name',$tvname);
    $XMLtable->addAttribute('singular',$tvobject[$tvname]);
    $XMLtable->addAttribute('plural',$tvobjects[$tvname]);

    if(array_key_exists($tvname,$tvs))
    {
      $XMLtable->addAttribute('active','yes');

      $primkeys=[];

      $class=$tvobject[$tvname];

      $daocontent='<?php'.PHP_EOL.PHP_EOL
                  . "require_once 'DAO.php';".PHP_EOL.PHP_EOL
                  . '/**'.PHP_EOL
                  . " * Class {$class}DAO".PHP_EOL;
      if($table['TABLE_COMMENT']) $daocontent.=' * COMMENT: '.$table['TABLE_COMMENT'].PHP_EOL;
      $daocontent.='*/'.PHP_EOL;

      $daofilename="../models/DAO/{$class}.dao.php";

      $time=date('YmdHis');

      // backup ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

      if(file_exists($daofilename) && isset($_SESSION['backup']))
      {
        copy("../models/DAO/{$class}.dao.php","../models/DAO/{$class}.dao.php.{$time}.bak");
      }

      // variables ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

      $fields=[];

      $daovars='';

      $columns=$Engine->getColumnsWithAttributes($tvname);

      //$XMLcolumns=$XMLtable->addChild('columns');

      foreach($columns as $columnname=>$column)
      {
        $XMLcolumn=$XMLtable->addChild('column');//$XMLcolumn=$XMLcolumns->addChild('column');
        $XMLcolumn->addAttribute('name',$columnname);

        //Primary key variable is always included
        if($column['PK'] || array_key_exists($columnname,$getters[$tvname]) || array_key_exists($columnname,$setters[$tvname]))
        {
          $daovars.=PHP_EOL
                  . "  /**".PHP_EOL
                  . "   * Protected variable".PHP_EOL;
          if($column['COLUMN_COMMENT']) $daovars.='   * COMMENT: '.$column['COLUMN_COMMENT'].PHP_EOL;
          if($column['PK'])
          {
            $primkeys[]=$columnname;
            $daovars.= '   * (PK)->Primary key'.PHP_EOL;
          }
          else
          {
            if($column['UQ']) $daovars.= '   * (UQ)->Unique key'.PHP_EOL;
            $fields[]=$columnname;
          }

          if($column['FK'])
          {
            $daovars.= '   * (FK)->'.$column['REFERENCED_TABLE_NAME'].'.'.$column['REFERENCED_COLUMN_NAME'].PHP_EOL;
          }

          $XMLcolumn->addAttribute('method',$methods[$tvname][$columnname]);

          $daovars.="   * @var {$column['DATA_TYPE']} \${$columnname}".PHP_EOL
                  . '   */'.PHP_EOL
                  . "  protected \${$columnname};".PHP_EOL;

          if($column['PK'] || array_key_exists($columnname,$getters[$tvname]))
          {
            $daovars.=PHP_EOL."  public function get".getCamelCase($methods[$tvname][$columnname])."() {return \$this->{$columnname};}".PHP_EOL;
            $XMLcolumn->addAttribute('getter','yes');
          }
          else
          {
            $XMLcolumn->addAttribute('getter','no');
          }

          if($table['TABLE_TYPE']=='TABLE' && ($column['PK'] || array_key_exists($columnname,$setters[$tvname])))
          {
            $daovars.=PHP_EOL."  public function set".getCamelCase($methods[$tvname][$columnname])."(\${$columnname}) {\$this->{$columnname}=\${$columnname};}".PHP_EOL;
            $XMLcolumn->addAttribute('setter','yes');
          }
          else
          {
            $XMLcolumn->addAttribute('setter','no');
          }
        }
        if(array_key_exists($tvname,$finders) && array_key_exists($columnname,$finders[$tvname]))
        {
          $XMLcolumn->addAttribute('finder','yes');
        }
        else
        {
          $XMLcolumn->addAttribute('finder','no');
        }
      }

      // references ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

      $daorefs='';

      $referenced=$Engine->getReferencedTables($tvname);

      foreach($referenced as $reference)
      {
        $reftablename=$reference['REFERENCED_TABLE_NAME'];
        $object=$tvobject[$reftablename];

        if(array_key_exists($reftablename,$tvs))
        {
          $daorefs.='  /**'.PHP_EOL
                  . "   * {$object} - referenced table".PHP_EOL
                  . '   * @returns object'.PHP_EOL
                  . '   */'.PHP_EOL
                  . "  public function get{$object}()".PHP_EOL
                  . '  {'.PHP_EOL;

          $wheres=[];
          $referencedcolumns=$Engine->getReferenceColumns($tvname,$reftablename);
          foreach($referencedcolumns as $referencedcolumn)
          {
            $wheres[]="{$referencedcolumn['REFERENCED_COLUMN_NAME']}='{\$this->{$referencedcolumn['COLUMN_NAME']}}'";
          }
          $daorefs.='    $sql="SELECT * FROM '.$reftablename.' WHERE '.implode(' AND ',$wheres).' LIMIT 1";'.PHP_EOL

                  . "    return \$this->getObject(\$sql,'$object');".PHP_EOL
                  . '  }'.PHP_EOL.PHP_EOL;
        }
      }

      $referred=$Engine->getReferredTables($tvname);

      foreach($referred as $reference)
      {
        $reftablename=$reference['TABLE_NAME'];
        $object=$tvobject[$reftablename];
        $objects=$tvobjects[$reftablename];

        if(array_key_exists($reftablename,$tvs))
        {
          $daorefs.='  /**'.PHP_EOL
                  . "   * {$objects} - referred table".PHP_EOL
                  . '   * @returns object[]'.PHP_EOL
                  . '   */'.PHP_EOL
                  . "  public function get{$objects}()".PHP_EOL
                  . '  {'.PHP_EOL;

          $wheres=[];
          $referredcolumns=$Engine->getReferenceColumns($reftablename,$tvname);
          foreach($referredcolumns as $referredcolumn)
          {
            $wheres[]="{$referredcolumn['COLUMN_NAME']}='{\$this->{$referredcolumn['REFERENCED_COLUMN_NAME']}}'";
          }
          $daorefs.='    $sql="SELECT * FROM '.$reftablename.' WHERE '.implode(' AND ',$wheres).'";'.PHP_EOL

                  . "    return \$this->getObjects(\$sql,'$object');".PHP_EOL
                  . '  }'.PHP_EOL.PHP_EOL;
        }
      }

      // finders ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

      $daofinders='';

      foreach($columns as $columnname=>$column)
      {
        if(array_key_exists($tvname,$finders) && array_key_exists($columnname,$finders[$tvname]))
        {
          if($column['UQ'] || ($column['PK'] && count($primkeys)===1)) // if it is unique or single PK
          {
            $daofinders.='  /**'.PHP_EOL;
            $daofinders.= $column['PK'] ? '  /* Primary Key Finder'.PHP_EOL : '  /* Unique Key Finder'.PHP_EOL;
            $daofinders.='   * @return object'.PHP_EOL
                       . '   */'.PHP_EOL
                       . '  public function findBy'.getCamelCase($methods[$tvname][$columnname]).'($'.$columnname.')'.PHP_EOL
                       . '  {'.PHP_EOL
                       . '    $sql="SELECT * FROM '.$tvname.' WHERE '.$columnname.'=\'$'.$columnname.'\' LIMIT 1";'.PHP_EOL
                       . '    return $this->getSelfObject($sql);'.PHP_EOL
                       . '  }'.PHP_EOL.PHP_EOL;
          }
          else
          {
            $daofinders.='  /**'.PHP_EOL
                       . '   * Column '.$columnname.' Finder'.PHP_EOL
                       . '   * @return object[]'.PHP_EOL
                       . '   */'.PHP_EOL
                       . '  public function findBy'.getCamelCase($methods[$tvname][$columnname]).'($'.$columnname.')'.PHP_EOL
                       . '  {'.PHP_EOL
                       . '    $sql="SELECT * FROM '.$tvname.' WHERE '.$columnname.'=\'$'.$columnname.'\'";'.PHP_EOL
                       . '    return $this->getSelfObjects($sql);'.PHP_EOL
                       . '  }'.PHP_EOL.PHP_EOL;
          }
        }
      }

      if(count($primkeys)>1) //always make additional composite primary key Finder
      {
        $pknames=[];
        foreach($columns as $columnname=>$column)
        {
          if($column['PK']) $pknames[]=getCamelCase($methods[$tvname][$columnname]);
        }
        $vars=implode(',',array_map(function($x){return '$'.$x;},$primkeys));
        $where=implode(' AND ',array_map(function($x){return $x."='$".$x."'";},$primkeys));
        $daofinders.='  /**'.PHP_EOL
                   . '   * Composite Primary Key Finder'.PHP_EOL
                   . '   * @return object'.PHP_EOL
                   . '   */'.PHP_EOL
                   . '  public function findBy'.implode('And',$pknames).'('.$vars.')'.PHP_EOL
                   . '  {'.PHP_EOL
                   . '    $sql="SELECT * FROM '.$tvname.' WHERE '.$where.' LIMIT 1";'.PHP_EOL
                   . '    return $this->getSelfObject($sql);'.PHP_EOL
                   . '  }'.PHP_EOL.PHP_EOL;
      }


      // constraints ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

      if($constraints=$Engine->getCompositeUniqueConstraints($tvname))
      {
        foreach($constraints as $constraint)
        {
          $constraintname=$constraint['CONSTRAINT_NAME'];
          $constrainedcolumns=$constraint['CONSTRAINED_COLUMNS'];
          $constrainedcolumnslist=explode(',',$constrainedcolumns);

          $XMLcolumn=$XMLtable->addChild('constraint');
          $XMLcolumn->addAttribute('name',$constraintname);
          $XMLcolumn->addAttribute('columns',$constrainedcolumns);
          $XMLcolumn->addAttribute('method',$methods[$tvname][$constraintname]);

          if(array_key_exists($tvname,$constrs) && array_key_exists($constraintname,$constrs[$tvname]))
          {
            $XMLcolumn->addAttribute('finder','yes');
            $vars=implode(',',array_map(function($x){return '$'.$x;},$constrainedcolumnslist));
            $where=implode(' AND ',array_map(function($x){return $x."='$".$x."'";},$constrainedcolumnslist));
            $daofinders.=PHP_EOL
                       . "  /**".PHP_EOL
                       . "   * Composite Unique Constraint Finder".PHP_EOL
                       . "   * Columns: ".$constrainedcolumns.PHP_EOL
                       . '   * @return object'.PHP_EOL
                       . '   */'.PHP_EOL
                       . "  public function findBy".$methods[$tvname][$constraintname].'('.$vars.')'.PHP_EOL
                       . '  {'.PHP_EOL
                       . '    $sql="SELECT * FROM '.$tvname.' WHERE '.$where.' LIMIT 1";'.PHP_EOL
                       . '    return $this->getSelfObject($sql);'.PHP_EOL
                       . '  }'.PHP_EOL.PHP_EOL;
          }
          else
          {
            $XMLcolumn->addAttribute('finder','no');
          }
        }
      }

      //========================================================================

      $daocontent.="abstract class {$class}DAO extends EntityBase".PHP_EOL
                 . '{'.PHP_EOL
                 . $daovars.PHP_EOL
                 . '  /**'.PHP_EOL
                 . '   * Constructor'.PHP_EOL
                 . '   * @var mixed $id'.PHP_EOL
                 . '   */'.PHP_EOL
                 . '  public function __construct($id=0)'.PHP_EOL
                 . '  {'.PHP_EOL
                 . '    parent::__construct();'.PHP_EOL
                 . "    \$this->table='{$tvname}';".PHP_EOL
                 . '    $this->primkeys=';
      $daocontent.= $primkeys ? "['".implode("','",$primkeys)."'];".PHP_EOL : "[];".PHP_EOL;
      $daocontent.='    $this->fields=';
      $daocontent.= $fields ? "['".implode("','",$fields)."'];".PHP_EOL : "[];".PHP_EOL;
      $daocontent.='    $this->sql="SELECT * FROM {$this->table}";'.PHP_EOL
                 . '    if($id) $this->read($id);'.PHP_EOL
                 . '  }'.PHP_EOL.PHP_EOL;

      $daocontent.=$daorefs;

      $daocontent.=$daofinders;

      $daocontent.='  // ==========!!!DO NOT PUT YOUR OWN CODE (BUSINESS LOGIC) HERE!!!========== //'.PHP_EOL
                 . '  // EXTEND THIS DAO CLASS WITH YOUR OWN CLASS CONTAINING YOUR BUSINESS LOGIC //'.PHP_EOL
                 . '  //  BECAUSE THIS CLASS FILE WILL BE OVERWRITTEN UPON EACH NEW PHPDAO BUILD  //'.PHP_EOL
                 . '  // ======================================================================== //'.PHP_EOL
                 . '}'.PHP_EOL.PHP_EOL;

      $daofile=fopen($daofilename,'w');
      fwrite($daofile,$daocontent);
      fclose($daofile);

      // MODELS ////////////////////////////////////////////////////////////////

      $filename="../models/{$class}.php";
      if(!file_exists($filename))
      {
        $file=fopen($filename,'w');

        $content= '<?php'.PHP_EOL
                . PHP_EOL
                . "require_once 'DAO/{$class}.dao.php';".PHP_EOL
                . PHP_EOL
                . '/**'.PHP_EOL
                . ' * Class '.$class.PHP_EOL
                . ' */'.PHP_EOL
                . 'class '.$class.' extends '.$class.'DAO'.PHP_EOL
                . '{'.PHP_EOL
                . '  // PUT YOUR BUSINESS LOGIC HERE'.PHP_EOL
                . '}'.PHP_EOL
                . PHP_EOL.PHP_EOL;

        fwrite($file,$content);
        fclose($file);
      }

      echo '              <tr><td align="center"><b>'.$tvobject[$tvname].'</b></td></tr>'.PHP_EOL;
    }
    else
    {
      $XMLtable->addAttribute('active','no');

      $columns=$Engine->getColumnsWithAttributes($tvname);
    }
  }
?>
              </table>
<?php
}
catch(ConnectionException $ce)
{
?>
              <div class="alert alert-critical text-center">Cannot connect to database!<br><?= $ce->getHtmlMessage() ?></div>
<?php
}

$DOM=new DOMDocument('1.0');
$DOM->preserveWhiteSpace = false;
$DOM->formatOutput = true;
$DOM->loadXML($XML->asXML());
$DOM->save('../models/dao.xml');

?>

          </div><!-- .thumbnail -->
        </div><!-- .col -->
      </div><!-- .row -->
