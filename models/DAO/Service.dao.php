<?php

require_once 'DAO.php';

/**
 * Class ServiceDAO
*/
abstract class ServiceDAO extends EntityBase
{

  /**
   * Protected variable
   * (FK)->categories.catid
   * @var int $catid
   */
  protected $catid;

  public function getCatId() {return $this->catid;}

  public function setCatId($catid) {$this->catid=$catid;}

  /**
   * Protected variable
   * (PK)->Primary key
   * @var int $svcid
   */
  protected $svcid;

  public function getSvcId() {return $this->svcid;}

  public function setSvcId($svcid) {$this->svcid=$svcid;}

  /**
   * Protected variable
   * @var varchar $svcname
   */
  protected $svcname;

  public function getSvcname() {return $this->svcname;}

  public function setSvcname($svcname) {$this->svcname=$svcname;}

  /**
   * Constructor
   * @var mixed $id
   */
  public function __construct($id=0)
  {
    parent::__construct();
    $this->table='services';
    $this->primkeys=['svcid'];
    $this->fields=['catid','svcname'];
    $this->sql="SELECT * FROM {$this->table}";
    if($id) $this->read($id);
  }

  /**
   * Category - referenced table
   * @returns object
   */
  public function getCategory()
  {
    $sql="SELECT * FROM categories WHERE catid='{$this->catid}' LIMIT 1";
    return $this->getObject($sql,'Category');
  }

  /**
   * Usages - referred table
   * @returns object[]
   */
  public function getUsages()
  {
    $sql="SELECT * FROM usages WHERE svcid='{$this->svcid}'";
    return $this->getObjects($sql,'Usage');
  }

  /**
   * Column catid Finder
   * @return object[]
   */
  public function findByCatId($catid)
  {
    $sql="SELECT * FROM services WHERE catid='$catid'";
    return $this->getSelfObjects($sql);
  }

  /**
  /* Primary Key Finder
   * @return object
   */
  public function findBySvcId($svcid)
  {
    $sql="SELECT * FROM services WHERE svcid='$svcid' LIMIT 1";
    return $this->getSelfObject($sql);
  }

  // ==========!!!DO NOT PUT YOUR OWN CODE (BUSINESS LOGIC) HERE!!!========== //
  // EXTEND THIS DAO CLASS WITH YOUR OWN CLASS CONTAINING YOUR BUSINESS LOGIC //
  //  BECAUSE THIS CLASS FILE WILL BE OVERWRITTEN UPON EACH NEW PHPDAO BUILD  //
  // ======================================================================== //
}

