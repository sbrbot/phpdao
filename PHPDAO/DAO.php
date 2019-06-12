<?php

include 'DAO.inc';

////////////////////////////////////////////////////////////////////////////////

// <editor-fold defaultstate="collapsed" desc="DataBase">

/**
 * DataBase singleton class
 * @internal
 */
class DataBase
{
  /*
   * Database variable
   * @var mysqli
   */
  protected static $db;

  /**
   * Constructor
   */
  final protected function __construct()
  {
    //no public constructor for singleton class
  }

  /**
   * Instantiator static method
   * As of PHP 5.3.0, PHP implements a feature called late static bindings which
   * can be used to reference the called class in a context of static inheritance.
   * @return MySQLi
   */
  public static function getInstance()
  {
    if(!is_object(self::$db))
    {
      mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT); //Make MySQLi throw exceptions
      try
      {
        self::$db = @new mysqli(DB_HOST,DB_USER,DB_PASS,DB_NAME);
        self::setCharset(DB_CHAR);
      }
      catch(mysqli_sql_exception $ex)
      {
        throw new ConnectionException('',$ex->getMessage(),$ex->getCode());
      }
    }
    return self::$db;
  }

  /**
   * Returns the database character set
   * @return string character set code
   */
  public static function getCharset()
  {
    return self::$db->character_set_name();
  }

  /**
   * Sets the database character set
   * @param string $charset
   */
  public static function setCharset($charset)
  {
    self::$db->set_charset($charset);
  }

  /**
   * Destructor for garbage collector
   */
  protected function __destruct()
  {
    if(self::$db) self::$db->close();
  }

  /**
   * Forbid cloning in sigleton class
   */
  protected function __clone()
  {
    //no possibility for cloning of singleton class
  }
}

// </editor-fold>

////////////////////////////////////////////////////////////////////////////////

/**
 * Active Record Pattern paradigm
 * Database Entity Wrapper
 * @ignore
 */
abstract class EntityBase
{
  /**
   * Database shared connector
   * @var MySQLi connector
   */
  protected $db;

  //----------------------------------------------------------------------------

  /**
   * Database table name
   * @var string
   */
  protected $table;

  /**
   * Main (table) SQL primary key (field) names
   * @var array
   */
  protected $primkeys;

  /**
   * Allowed CRUD fields (table columns)
   * @var array
   */
  protected $fields;

  /**
   * Main (table) SQL query
   * @var string
   */
  protected $sql;

  //////////////////////////////////////////////////////////////////////////////

  /**
   * Constructor - retrieve DB connection from singleton class
   */
  public function __construct()
  {
    $this->db=DataBase::getInstance();
  }

  //////////////////////////////////////////////////////////////////////////////

  /**
   * Get single primary key value
   * @return mixed
   */
  public function getId()
  {
    $pkfield=$this->primkeys[0];
    return $this->$pkfield;
  }

  /**
   * Returns primary keys as associative array
   * @return array
   */
  public function getIds()
  {
    $ids=[];
    foreach($this->primkeys as $pk)
    {
      $ids[$pk]=$this->$pk;
    }
    return $ids;
  }

  /**
   * Fetches row from database and populates variables
   * @param string $sql SQL statement
   * @throws NotFoundException
   * @throws ReadException
   */
  public function fetch($sql)
  {
    if($rst=$this->db->query($sql))
    {
      if($rst->num_rows)
      {
        $row=$rst->fetch_assoc();
        $this->populate($row);
        $rst->free();
      }
      else
      {
        throw new NotFoundException($sql,$this->db->error,$this->db->errno);
      }
    }
    else
    {
      throw new ReadException($sql,$this->db->error,$this->db->errno);
    }
  }

  //----------------------------------------------------------------------------

  /**
   * Fills class variables of child object with array elements
   * @param array $array array of column=>value elements
   */
  public function populate(array $array)
  {
    foreach($array as $key=>$val)
    {
      if(in_array($key,array_merge($this->primkeys,$this->fields))) //include only allowed fields
      {
        $this->$key=$val;
      }
    }
  }

  /**
   * Sanitizes and fills class variables of child object with array elements
   * @param array $array array of column=>value elements
   */
  public function sanitize(array $array)
  {
    foreach($array as $key=>$val)
    {
      if(in_array($key,array_merge($this->primkeys,$this->fields))) //include only allowed fields
      {
        $this->$key=$this->db->real_escape_string($val);
      }
    }
  }

  //////////////////////////////////////////////////////////////////////////////

  /**
   * Adds table fields (columns) to SQL query
   * @param string $sql SQL statement (by reference)
   */
  protected function addFields(&$sql)
  {
    $fields=[];

    foreach($this as $var=>$val)
    {
      if(in_array($var,$this->fields)) //include only allowed fields
      {
        if(is_null($val) || strtoupper($val)==='NULL')
        {
          $fields[]="$var=NULL";
        }
        else
        {
          $fields[]="$var='{$this->db->real_escape_string($val)}'";
        }
      }
    }
    $sql.=implode(',',$fields);
  }

  //----------------------------------------------------------------------------

  /**
   * Returns the object of type given by ClassType variable and dynamically
   * fills only properties that correspond to columns in given SQL query
   * @param string $sql SQL query
   * @param string $Class classname
   * @return Object
   * @return NotFoundException
   * @throws ReadException
   * @throws Exception
   */
  protected function getObject($sql,$Class)
  {
    if(class_exists($Class))
    {
      if($rst=$this->db->query($sql))
      {
        if($Object=$rst->fetch_object($Class))
        {
          $rst->free();
        }
        else
        {
          throw new NotFoundException($sql,$this->db->error,$this->db->errno);
        }
      }
      else
      {
        throw new ReadException($sql,$this->db->error,$this->db->errno);
      }
    }
    else
    {
      throw new Exception("Non-existent class: $Class");
    }
    return $Object;
  }

  /**
   * Returns the object of self type
   * @param $sql SQL query
   * @return self
   */
  protected function getSelfObject($sql)
  {
    return $this->getObject($sql,get_class($this));
  }

  /**
   * Returns the array of objects of type given by ClassType variable and
   * dynamically fills only properties that correspond to columns in given SQL query
   * @param string $sql SQL query
   * @param string $Class class (name)
   * @return Object[]
   * @throws ReadException
   * @throws Exception
   */
  protected function getObjects($sql,$Class)
  {
    $Objects=[];

    if(class_exists($Class))
    {
      if($rst=$this->db->query($sql))
      {
        while($Object=$rst->fetch_object($Class))
        {
          $Objects[]=$Object;
        }
        $rst->free();
      }
      else
      {
        throw new ReadException($sql,$this->db->error,$this->db->errno);
      }
    }
    else
    {
      throw new Exception("Non-existent class: {$Class}!");
    }
    return $Objects;
  }

  /**
   * Returns the array of self type objects
   * @param string $sql SQL query
   * @return self[]
   */
  protected function getSelfObjects($sql)
  {
    return $this->getObjects($sql,get_class($this));
  }

  //----------------------------------------------------------------------------

  /**
   * Returns the single number
   * @param string $sql
   * @return number
   */
  protected function getSingleNum($sql)
  {
    if($rst=$this->db->query($sql))
    {
      $row=$rst->fetch_row();
      $rst->free();
      return (int)$row[0];
    }
    else
    {
      return 0;
    }
  }

  //////////////////////////////////////////////////////////////////////////////

  /**
   * Returns the list of all objects
   * @param INTEGER $limit
   * @param INTEGER $offset
   * @return Object[]
   */
  public function getList($limit=100,$offset=0)
  {
    $sql="{$this->sql} LIMIT {$limit} OFFSET {$offset}";
    return $this->getSelfObjects($sql);
  }

  /**
   * Returns the simple array
   * @param string $sql SELECT query with one column ('value')
   * @return array ['value1','value2',...]
   * @throws ReadException
   */
  protected function getArray($sql)
  {
    $list=[];

    if($rst=$this->db->query($sql))
    {
      while($row=$rst->fetch_row())
      {
        $list[]=$row[0];
      }
      $rst->free();
      return $list;
    }
    else
    {
      throw new ReadException($sql,$this->db->error,$this->db->errno);
    }
  }

  /**
   * Returns the associative array
   * @param string $sql SELECT query with two columns ('id','value')
   * @return array ['id1'=>'value1','id2'=>'value2',...]
   * @throws ReadException
   */
  protected function getAssocArray($sql)
  {
    $list=[];

    if($rst=$this->db->query($sql))
    {
      while($row=$rst->fetch_row())
      {
        $list[$row[0]]=$row[1]; //@TODO multidimensional
      }
      $rst->free();
      return $list;
    }
    else
    {
      throw new ReadException($sql,$this->db->error,$this->db->errno);
    }
  }

  /**
   * Returns the total number of records
   * @return INTEGER
   */
  public function getTotalNo()
  {
    $sql="SELECT COUNT(1) FROM {$this->table} LIMIT 1";
    return $this->getSingleNum($sql);
  }

  // ID FUNCTIONS //////////////////////////////////////////////////////////////

  /**
   * Returns the list of IDs and there are three possibilities:
   * @param string $glue (can be comma or AND operator)
   * @param mixed $ids single PK value or associative array of PKs with values
   * @return string
   */
  private function getImplodedIds($glue,$ids)
  {
    $wheres=[];
    if($ids)
    {
      if(is_array($ids))
      {
        foreach($ids as $id=>$val)
        {
          $wheres[]="{$id}='{$val}'";
        }
        $where=implode($glue,$wheres);
      }
      else
      {
        $where=" {$this->primkeys[0]}='{$ids}'";
      }
    }
    else //if $ids was not given, use object's primary key(s)
    {
      if($this->primkeys)
      {
        foreach($this->primkeys as $pk)
        {
          if($this->$pk)
          {
            $wheres[]="{$pk}='{$this->$pk}'";
          }
          else
          {
            $wheres[]="{$pk}=NULL";
          }
        }
        $where=implode($glue,$wheres);
      }
      else
      {
        $where='1=1'; // no PK
      }
    }
    return $where;
  }

  // CRUDS FUNCTIONS ///////////////////////////////////////////////////////////

  /**
   * Reads database row from table and dynamically fills object's variables from row's fields
   * @param mixed $ids single PK value or array of PKs with values
   * @throws ReadException
   */
  public function read($ids=0)
  {
    $sql="{$this->sql} WHERE {$this->getImplodedIds(' AND ',$ids)} LIMIT 1";
    $this->fetch($sql);
  }

  /**
   * Reads given columns only from table and dynamically fills object's variables
   * @param array $columns Array of table columns
   * @param mixed $ids single PK value or array of PKs with values
   * @throws ReadException
   */
  public function readColumns(array $columns,$ids=0)
  {
    if(is_array($columns))
    {
      $sql='SELECT '.implode(',',$columns)." FROM {$this->table} WHERE {$this->getImplodedIds(' AND ',$ids)} LIMIT 1";
      $this->fetch($sql);
    }
    else
    {
      throw new Exception('No columns defined!');
    }
  }

  /**
   * Dynamically builds INSERT query and creates a record in database
   * @param mixed $ids single PK value or array of PKs with values
   * @throws NotUniqueException
   * @throws CreateException
   */
  public function create($ids=0)
  {
    $sql="INSERT INTO {$this->table} SET ";

    if($ids || count($this->primkeys)>1) //if id explicitly defined or compound id (PK)
    {
      $sql.=$this->getImplodedIds(',',$ids).',';
    }

    $this->addFields($sql);

    if($this->db->query($sql))
    {
      if(count($this->primkeys)==1) //single PK (could be auto_increment)
      {
        $pkfield=$this->primkeys[0];
        $this->$pkfield=$this->db->insert_id;
      }
    }
    else
    {
      if($this->db->errno===1062) //unique key violation
      {
        throw new NotUniqueException($sql,$this->db->error,$this->db->errno);
      }
      else
      {
        throw new CreateException($sql,$this->db->error,$this->db->errno);
      }
    }
  }

  /**
   * Dynamically builds UPDATE query and updates a record in database
   * @param mixed $ids single PK value or array of PKs with values
   * @throws NotUniqueException
   * @throws UpdateException
   */
  public function update($ids=0)
  {
    $sql="UPDATE {$this->table} SET ";

    $this->addFields($sql);

    if(count($this->primkeys)>0)
    {
      $sql.=" WHERE {$this->getImplodedIds(' AND ',$ids)} LIMIT 1";
    }

    if(!$this->db->query($sql))
    {
      if($this->db->errno===1062) //unique key violation
      {
        throw new NotUniqueException($sql,$this->db->error,$this->db->errno);
      }
      else
      {
        throw new UpdateException($sql,$this->db->error,$this->db->errno);
      }
    }
  }

  /**
   * Dynamically builds INSERT+UPDATE query and saves a record in database
   * if record does not exist it will be created, otherwise updated
   * @param mixed $ids single PK value or array of PKs with values
   * @throws SaveException
   */
  public function save($ids=0)
  {
    $sql="INSERT INTO {$this->table} SET ";

    $sql.=$this->getImplodedIds(',',$ids).',';

    $this->addFields($sql);

    $sql.=' ON DUPLICATE KEY UPDATE '; // MySQL specific

    $this->addFields($sql);

    if($this->db->query($sql))
    {
      if(count($this->primkeys)==1) //single PK (could be auto_increment)
      {
        $pkfield=$this->primkeys[0];
        $this->$pkfield=$this->db->insert_id;
      }
    }
    else
    {
      throw new SaveException($sql,$this->db->error,$this->db->errno);
    }
  }

  /**
   * Dynamically builds DELETE query and deletes a record in database
   * @param mixed $ids single PK value or array of PKs with values
   * @throws DeleteException
   */
  public function delete($ids=0)
  {
    $sql="DELETE FROM {$this->table} WHERE {$this->getImplodedIds(' AND ',$ids)} LIMIT 1";

    if(!$this->db->query($sql))
    {
      throw new DeleteException($sql,$this->db->error,$this->db->errno);
    }
  }

  /**
   * Finds rows from table according to given list of columns/values
   * @param array $conditions Array of table columns=>'vaule' pairs (conditions)
   * @throws Exception
   */
  public function find($conditions=0)
  {
    if(is_array($conditions))
    {
      $sql="SELECT * FROM {$this->table} WHERE {$this->getImplodedIds(' AND ',$conditions)} LIMIT 1";
      $this->fetch($sql);
    }
    else
    {
      throw new Exception('No columns defined!');
    }
  }

  //////////////////////////////////////////////////////////////////////////////

  public function beginTransaction()
  {
    $this->db->begin_transaction();
  }

  public function commitTransaction()
  {
    $this->db->commit();
  }

  public function rollbackTransaction()
  {
    $this->db->rollback();
  }

}

////////////////////////////////////////////////////////////////////////////////

// <editor-fold defaultstate="collapsed" desc="Exceptions">

abstract class DatabaseException extends Exception
{
  protected $query;

  public function __construct($query='',$message='',$code=0,$previous=NULL)
  {
    $this->query=$query;
    parent::__construct($message,$code,$previous);
  }

  final public function getHtmlMessage($class='alert alert-danger')
  {
    return "<div class=\"{$class}\">CODE = {$this->getCode()}<br>MESSAGE = {$this->getMessage()}<br>QUERY = {$this->query}</div>".PHP_EOL;
  }
  final public function getQuery()
  {
    return $this->query;
  }
}

//------------------------------------------------------------------------------

/**
 * Connection Exception
 */
class ConnectionException extends DatabaseException {}

/**
 * Create Exception
 */
class CreateException extends DatabaseException {}

/**
 * Read Exception
 */
class ReadException extends DatabaseException {}

/**
 * Update Exception
 */
class UpdateException extends DatabaseException {}

/**
 * Delete Exception
 */
class DeleteException extends DatabaseException {}

/**
 * Save Exception
 */
class SaveException extends DatabaseException {}

//------------------------------------------------------------------------------

/**
 * Not Unique Exception
 */
class NotUniqueException extends DatabaseException {}

/**
 * Not found exception
 */
class NotFoundException extends DatabaseException {}

// </editor-fold>

////////////////////////////////////////////////////////////////////////////////
