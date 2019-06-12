<?php

/**
 * Description of Engine
 *
 * @author Stjepan Brbot <stjepan.brbot@gmail.com>
 */
class Engine
{
  private $DB_HOST;
  private $DB_NAME;
  private $DB_USER;
  private $DB_PASS;
  private $DB_CHAR;

  private $db;

  //----------------------------------------------------------------------------

  public function __construct()
  {
    $this->DB_HOST=$_SESSION['DB_HOST'];
    $this->DB_NAME=$_SESSION['DB_NAME'];
    $this->DB_USER=$_SESSION['DB_USER'];
    $this->DB_PASS=$_SESSION['DB_PASS'];
    $this->DB_CHAR=$_SESSION['DB_CHAR'];

    $this->db=new mysqli($this->DB_HOST,$this->DB_USER,$this->DB_PASS,$this->DB_NAME);
    $this->db->set_charset($this->DB_CHAR);
    if($this->db->connect_error)
    {
      throw new ConnectionException($this->db->connect_error,$this->db->connect_errno);
    }
  }

  public function __destruct()
  {
    $this->db->close();
  }

  //----------------------------------------------------------------------------

  /**
   * Returns Tables and Views with attribute if it is updatable
   * @return array of tables and views
   */
  public function getTablesAndViews()
  {
    $tableviews=[];

    $sql="SELECT t.TABLE_NAME,
                 CASE WHEN t.table_type='BASE TABLE' THEN 'TABLE'
                      WHEN t.table_type='VIEW' THEN 'VIEW'
                      ELSE NULL
                 END AS `TABLE_TYPE`,
                 t.ENGINE,
                 t.AUTO_INCREMENT,
                 t.TABLE_COMMENT,
                 IFNULL(v.is_updatable,'YES') AS `IS_UPDATABLE`
            FROM information_schema.TABLES AS t
 LEFT OUTER JOIN information_schema.VIEWS AS v ON v.table_name=t.table_name
           WHERE t.TABLE_SCHEMA='{$this->DB_NAME}'
        ORDER BY 2,1";

    $rst=$this->db->query($sql);

    while($tableview=$rst->fetch_assoc()) $tableviews[]=$tableview;

    $rst->free();

    return $tableviews;
  }

  /**
   * Returns only tables
   * @return array of tables
   */
  public function getTables()
  {
    $tables=[];

    $sql="SELECT *
            FROM information_schema.TABLES
           WHERE TABLE_SCHEMA='{$this->DB_NAME}' AND TABLE_TYPE='BASE TABLE'";

    $rst=$this->db->query($sql);

    while($table=$rst->fetch_assoc()) $tables[]=$table;

    $rst->free();

    return $tables;
  }

  /**
   * Returns only views
   * @return array of views
   */
  public function getViews()
  {
    $views=[];

    $sql="SELECT *
            FROM information_schema.VIEWS
           WHERE TABLE_SCHEMA='$this->DB_NAME'";

    $rst=$this->db->query($sql);

    while($view=$rst->fetch_assoc()) $views[]=$view;

    $rst->free();

    return $views;
  }

  /**
   * Returns array with column names
   * @param string $tableview
   * @return array
   */
  public function getColumns($tableview)
  {
    $columns=[];

    $sql="
      SELECT COLUMN_NAME
        FROM information_schema.COLUMNS
       WHERE TABLE_SCHEMA='{$this->DB_NAME}'
         AND TABLE_NAME='{$tableview}'";

    $rst=$this->db->query($sql);

    while($entity=$rst->fetch_assoc()) $columns[]=$entity;

    $rst->free();

    return $columns;
  }

  /**
   * Returns columns from tables and views with additional attributes
   * PK=is column PRIMARY KEY
   * UQ=is columns SINGLE UNIQUE constrained
   * FK=is column FOREIGN KEY
   * IDX=does column have an index
   * AI=is column auto-incremented
   * NN=is not-null column
   * @return array of columns and its attributes
   */
  public function getColumnsWithAttributes($tableview)
  {
    $entities=[];

    $sql="
SELECT c.COLUMN_NAME,
        IF(EXISTS(SELECT *
                    FROM information_schema.KEY_COLUMN_USAGE k
                    JOIN information_schema.TABLE_CONSTRAINTS tc
                     ON (k.TABLE_SCHEMA=tc.TABLE_SCHEMA
                         AND k.TABLE_NAME=tc.TABLE_NAME
                         AND k.CONSTRAINT_NAME=tc.CONSTRAINT_NAME)
                   WHERE k.TABLE_SCHEMA=c.TABLE_SCHEMA
                         AND k.TABLE_NAME=c.TABLE_NAME
                         AND k.COLUMN_NAME=c.COLUMN_NAME
                         AND tc.CONSTRAINT_TYPE='PRIMARY KEY'),'PK',null) AS `PK`,
        IF((SELECT COUNT(k2.COLUMN_NAME)
              FROM information_schema.KEY_COLUMN_USAGE k
              JOIN information_schema.KEY_COLUMN_USAGE k2
                ON (k.TABLE_SCHEMA=k2.TABLE_SCHEMA AND k.TABLE_NAME=k2.TABLE_NAME AND k.CONSTRAINT_NAME=k2.CONSTRAINT_NAME)
              JOIN information_schema.TABLE_CONSTRAINTS tc
                ON (k2.TABLE_SCHEMA=tc.TABLE_SCHEMA AND k2.TABLE_NAME=tc.TABLE_NAME AND k2.CONSTRAINT_NAME=tc.CONSTRAINT_NAME AND tc.CONSTRAINT_TYPE='UNIQUE')
             WHERE k.TABLE_SCHEMA=c.TABLE_SCHEMA
               AND k.TABLE_NAME=c.TABLE_NAME
               AND k.COLUMN_NAME=c.COLUMN_NAME)=1,'UQ',null) AS `UQ`,
        IF(EXISTS(SELECT *
                    FROM information_schema.KEY_COLUMN_USAGE k
                    JOIN information_schema.TABLE_CONSTRAINTS tc
                     ON (k.TABLE_SCHEMA=tc.TABLE_SCHEMA
                         AND k.TABLE_NAME=tc.TABLE_NAME
                         AND k.CONSTRAINT_NAME=tc.CONSTRAINT_NAME)
                   WHERE k.TABLE_SCHEMA=c.TABLE_SCHEMA
                         AND k.TABLE_NAME=c.TABLE_NAME
                         AND k.COLUMN_NAME=c.COLUMN_NAME
                         AND tc.CONSTRAINT_TYPE='FOREIGN KEY'),'FK',null) AS `FK`,
        IF(EXISTS(SELECT *
                    FROM information_schema.STATISTICS s
                   WHERE s.TABLE_SCHEMA=c.TABLE_SCHEMA
                         AND s.TABLE_NAME=c.TABLE_NAME
                         AND s.COLUMN_NAME=c.COLUMN_NAME),'IDX',null) AS `IDX`,
       IF(c.EXTRA='auto_increment','AI',null) AS `AI`,
       IF(c.IS_NULLABLE='NO','NN',null) AS `NN`,
       c.DATA_TYPE,
       c.COLUMN_TYPE,
       c.CHARACTER_MAXIMUM_LENGTH,
       c.COLUMN_COMMENT,
       k.REFERENCED_TABLE_SCHEMA,
       k.REFERENCED_TABLE_NAME,
       k.REFERENCED_COLUMN_NAME
  FROM information_schema.COLUMNS c
  LEFT JOIN information_schema.KEY_COLUMN_USAGE k
       ON (k.TABLE_SCHEMA=c.TABLE_SCHEMA
       AND k.TABLE_NAME=c.TABLE_NAME
       AND k.COLUMN_NAME=c.COLUMN_NAME
       AND k.POSITION_IN_UNIQUE_CONSTRAINT IS NOT NULL)
     WHERE c.TABLE_SCHEMA='{$this->DB_NAME}'
       AND c.TABLE_NAME='{$tableview}'";

    $rst=$this->db->query($sql);

    while($entity=$rst->fetch_assoc()) $entities[$entity['COLUMN_NAME']]=$entity;

    $rst->free();

    return $entities;
  }

  public function getReferencedTables($table)
  {
    $references=[];

    $sql="SELECT *
            FROM information_schema.REFERENTIAL_CONSTRAINTS
           WHERE CONSTRAINT_SCHEMA='{$this->DB_NAME}'
             AND TABLE_NAME='{$table}'";

    $rst=$this->db->query($sql);

    while($reference=$rst->fetch_assoc()) $references[]=$reference;

    $rst->free();

    return $references;
  }

  public function getCompositeUniqueConstraints($table)
  {
    $constraints=[];

    $sql="SELECT tc.CONSTRAINT_NAME,
                 COUNT(kcu.COLUMN_NAME) AS `CONSTRAINED_NO`,
                 GROUP_CONCAT(kcu.COLUMN_NAME SEPARATOR ',') AS `CONSTRAINED_COLUMNS`
            FROM information_schema.TABLE_CONSTRAINTS tc
            JOIN information_schema.KEY_COLUMN_USAGE kcu
              ON (kcu.TABLE_SCHEMA=tc.TABLE_SCHEMA AND kcu.TABLE_NAME=tc.TABLE_NAME AND kcu.CONSTRAINT_NAME=tc.CONSTRAINT_NAME)
           WHERE tc.TABLE_SCHEMA='{$this->DB_NAME}' AND tc.TABLE_NAME='{$table}' AND tc.CONSTRAINT_TYPE='UNIQUE'
        GROUP BY tc.CONSTRAINT_NAME
          HAVING COUNT(kcu.COLUMN_NAME)>1";

    $rst=$this->db->query($sql);

    while($constraint=$rst->fetch_assoc()) $constraints[]=$constraint;

    $rst->free();

    return $constraints;
  }

  public function getReferredTables($table)
  {
    $references=[];

    $sql="SELECT *
            FROM information_schema.REFERENTIAL_CONSTRAINTS
           WHERE CONSTRAINT_SCHEMA='{$this->DB_NAME}'
             AND REFERENCED_TABLE_NAME='{$table}'";

    $rst=$this->db->query($sql);

    while($reference=$rst->fetch_assoc()) $references[]=$reference;

    $rst->free();

    return $references;
  }

  public function getReferenceColumns($table,$reftable)
  {
    $references=[];

    $sql="SELECT rc.CONSTRAINT_NAME,rc.UPDATE_RULE,rc.DELETE_RULE,kcu.TABLE_NAME,kcu.COLUMN_NAME,kcu.REFERENCED_TABLE_NAME,kcu.REFERENCED_COLUMN_NAME
            FROM information_schema.REFERENTIAL_CONSTRAINTS AS rc
            JOIN information_schema.KEY_COLUMN_USAGE AS kcu ON kcu.CONSTRAINT_NAME=rc.CONSTRAINT_NAME
           WHERE rc.TABLE_NAME='{$table}' AND rc.REFERENCED_TABLE_NAME='{$reftable}'
             AND kcu.TABLE_SCHEMA='{$this->DB_NAME}' AND kcu.REFERENCED_TABLE_SCHEMA='{$this->DB_NAME}'";

    $rst=$this->db->query($sql);

    while($reference=$rst->fetch_assoc()) $references[$reference['COLUMN_NAME']]=$reference;

    $rst->free();

    return $references;
  }

//  public function getViewColumns($view)
//  {
//
//    $sql="SELECT c.COLUMN_NAME,
//       IF(c.IS_NULLABLE='YES','NN',null) AS `NN`,
//       c.DATA_TYPE,
//       c.COLUMN_TYPE,
//       c.CHARACTER_MAXIMUM_LENGTH,
//       c.COLUMN_COMMENT
//  FROM information_schema.COLUMNS c
// WHERE c.TABLE_SCHEMA='{$this->DB_NAME}'
//   AND c.TABLE_NAME='{$view}'";
//
//    $rst=$this->db->query($sql);
//
//    while($entity=$rst->fetch_assoc()) $entities[$entity['COLUMN_NAME']]=$entity;
//
//    $rst->free();
//
//    return $entities;
//  }
}

//------------------------------------------------------------------------------

/**
 * Very simple English Grammar class for singular/plural
 */
class English
{
  public $singular,$plural,$camel;

  public function __construct($input)
  {
    //split words separated by underscore and capitalize first letter (CamelCase)
    //$word=implode(array_map('ucfirst',explode('_',str_replace('s_','_',$input))));
    $word=implode(array_map('ucfirst',explode('_',$input)));

    //these are words that will be capitalized inside other words
    $uppers=['type'.'name','date','rate','categor','value','number','attrib'];
    foreach($uppers as $upper) $word=str_replace($upper,ucfirst($upper),$word);

    if(substr($word,-1)==='s') // assume that word already is in plural
    {
      $this->plural=$word;
      //
      if(substr($word,-3)==='ies') $this->singular=substr($word,0,-3).'y';
      elseif(substr($word,-3)==='ves') $this->singular=substr($word,0,-3).'f';
      else $this->singular=substr($word,0,-1);
    }
    else // assume that word is in singular
    {
      $this->singular=$word;
      //
      //if(substr($word,-1)==='y' && in_array(substr($word,-2,1),array('a','e','i','o','u'))) $this->plural=substr($word,-2).'ies';
      if(substr($word,-1)==='y') $this->plural=substr($word,0,-1).'ies';
      elseif(in_array(substr($word,-1),array('x'))) $this->plural=$word.'es';
      elseif(substr($word,-1)==='f') $this->plural=substr($word,-1).'ves';
      elseif(substr($word,-2)==='fe') $this->plural=substr($word,-2).'ves';
      else $this->plural = $word.'s';
    }

    $this->camel=$word;
    //if(substr($word,-2)==='on') $this->camel=substr($word,0,-2).'On';
    if(substr($word,-2)==='by') $this->camel=substr($word,0,-2).'By';
    if(substr($word,-2)==='id') $this->camel=substr($word,0,-2).'Id';
    $this->camel=str_replace('type','Type',$this->camel);
  }
}

/**
 * Connection Exception
 */
class ConnectionException extends Exception
{
  public function __construct(string $message="",int $code=0,\Throwable $previous=null)
  {
    parent::__construct($message,$code,$previous);
  }

  public function errorMessage()
  {
    return 'Error '.$this->getCode().' - '.$this->getMessage()."\n";
  }
}