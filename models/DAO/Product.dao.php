<?php

require_once 'DAO.php';

/**
 * Class ProductDAO
*/
abstract class ProductDAO extends EntityBase
{

  /**
   * Protected variable
   * (PK)->Primary key
   * @var int $prodid
   */
  protected $prodid;

  public function getProdId() {return $this->prodid;}

  public function setProdId($prodid) {$this->prodid=$prodid;}

  /**
   * Protected variable
   * @var varchar $prodname
   */
  protected $prodname;

  public function getProdname() {return $this->prodname;}

  public function setProdname($prodname) {$this->prodname=$prodname;}

  /**
   * Protected variable
   * @var date $proddate
   */
  protected $proddate;

  public function getProdDate() {return $this->proddate;}

  public function setProdDate($proddate) {$this->proddate=$proddate;}

  /**
   * Constructor
   * @var mixed $id
   */
  public function __construct($id=0)
  {
    parent::__construct();
    $this->table='products';
    $this->primkeys=['prodid'];
    $this->fields=['prodname','proddate'];
    $this->sql="SELECT * FROM {$this->table}";
    if($id) $this->read($id);
  }

  /**
   * Usages - referred table
   * @returns object[]
   */
  public function getUsages()
  {
    $sql="SELECT * FROM usages WHERE prodid='{$this->prodid}'";
    return $this->getObjects($sql,'Usage');
  }

  /**
  /* Primary Key Finder
   * @return object
   */
  public function findByProdId($prodid)
  {
    $sql="SELECT * FROM products WHERE prodid='$prodid' LIMIT 1";
    return $this->getSelfObject($sql);
  }

  /**
   * Column prodname Finder
   * @return object[]
   */
  public function findByProdname($prodname)
  {
    $sql="SELECT * FROM products WHERE prodname='$prodname'";
    return $this->getSelfObjects($sql);
  }

  /**
   * Column proddate Finder
   * @return object[]
   */
  public function findByProdDate($proddate)
  {
    $sql="SELECT * FROM products WHERE proddate='$proddate'";
    return $this->getSelfObjects($sql);
  }


  /**
   * Composite Unique Constraint Finder
   * Columns: prodname,proddate
   * @return object
   */
  public function findByProdNameAndProdDate($prodname,$proddate)
  {
    $sql="SELECT * FROM products WHERE prodname='$prodname' AND proddate='$proddate' LIMIT 1";
    return $this->getSelfObject($sql);
  }

  // ==========!!!DO NOT PUT YOUR OWN CODE (BUSINESS LOGIC) HERE!!!========== //
  // EXTEND THIS DAO CLASS WITH YOUR OWN CLASS CONTAINING YOUR BUSINESS LOGIC //
  //  BECAUSE THIS CLASS FILE WILL BE OVERWRITTEN UPON EACH NEW PHPDAO BUILD  //
  // ======================================================================== //
}

