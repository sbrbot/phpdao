<?php

require_once 'DAO.php';

/**
 * Class CategoryDAO
*/
abstract class CategoryDAO extends EntityBase
{

  /**
   * Protected variable
   * (PK)->Primary key
   * @var int $catid
   */
  protected $catid;

  public function getCatId() {return $this->catid;}

  public function setCatId($catid) {$this->catid=$catid;}

  /**
   * Protected variable
   * @var varchar $catname
   */
  protected $catname;

  public function getCatname() {return $this->catname;}

  public function setCatname($catname) {$this->catname=$catname;}

  /**
   * Constructor
   * @var mixed $id
   */
  public function __construct($id=0)
  {
    parent::__construct();
    $this->table='categories';
    $this->primkeys=['catid'];
    $this->fields=['catname'];
    $this->sql="SELECT * FROM {$this->table}";
    if($id) $this->read($id);
  }

  /**
   * Services - referred table
   * @returns object[]
   */
  public function getServices()
  {
    $sql="SELECT * FROM services WHERE catid='{$this->catid}'";
    return $this->getObjects($sql,'Service');
  }

  /**
  /* Primary Key Finder
   * @return object
   */
  public function findByCatId($catid)
  {
    $sql="SELECT * FROM categories WHERE catid='$catid' LIMIT 1";
    return $this->getSelfObject($sql);
  }

  // ==========!!!DO NOT PUT YOUR OWN CODE (BUSINESS LOGIC) HERE!!!========== //
  // EXTEND THIS DAO CLASS WITH YOUR OWN CLASS CONTAINING YOUR BUSINESS LOGIC //
  //  BECAUSE THIS CLASS FILE WILL BE OVERWRITTEN UPON EACH NEW PHPDAO BUILD  //
  // ======================================================================== //
}

