<?php

require_once 'DAO.php';

/**
 * Class UsageDAO
*/
abstract class UsageDAO extends EntityBase
{

  /**
   * Protected variable
   * (PK)->Primary key
   * (FK)->products.prodid
   * @var int $prodid
   */
  protected $prodid;

  public function getProdId() {return $this->prodid;}

  public function setProdId($prodid) {$this->prodid=$prodid;}

  /**
   * Protected variable
   * (PK)->Primary key
   * (FK)->services.svcid
   * @var int $svcid
   */
  protected $svcid;

  public function getSvcId() {return $this->svcid;}

  public function setSvcId($svcid) {$this->svcid=$svcid;}

  /**
   * Protected variable
   * @var date $date1
   */
  protected $date1;

  public function getDate1() {return $this->date1;}

  public function setDate1($date1) {$this->date1=$date1;}

  /**
   * Protected variable
   * @var date $date2
   */
  protected $date2;

  public function getDate2() {return $this->date2;}

  public function setDate2($date2) {$this->date2=$date2;}

  /**
   * Constructor
   * @var mixed $id
   */
  public function __construct($id=0)
  {
    parent::__construct();
    $this->table='usages';
    $this->primkeys=['prodid','svcid'];
    $this->fields=['date1','date2'];
    $this->sql="SELECT * FROM {$this->table}";
    if($id) $this->read($id);
  }

  /**
   * Product - referenced table
   * @returns object
   */
  public function getProduct()
  {
    $sql="SELECT * FROM products WHERE prodid='{$this->prodid}' LIMIT 1";
    return $this->getObject($sql,'Product');
  }

  /**
   * Service - referenced table
   * @returns object
   */
  public function getService()
  {
    $sql="SELECT * FROM services WHERE svcid='{$this->svcid}' LIMIT 1";
    return $this->getObject($sql,'Service');
  }

  /**
   * Column prodid Finder
   * @return object[]
   */
  public function findByProdId($prodid)
  {
    $sql="SELECT * FROM usages WHERE prodid='$prodid'";
    return $this->getSelfObjects($sql);
  }

  /**
   * Column svcid Finder
   * @return object[]
   */
  public function findBySvcId($svcid)
  {
    $sql="SELECT * FROM usages WHERE svcid='$svcid'";
    return $this->getSelfObjects($sql);
  }

  /**
   * Composite Primary Key Finder
   * @return object
   */
  public function findByProdIdAndSvcId($prodid,$svcid)
  {
    $sql="SELECT * FROM usages WHERE prodid='$prodid' AND svcid='$svcid' LIMIT 1";
    return $this->getSelfObject($sql);
  }

  // ==========!!!DO NOT PUT YOUR OWN CODE (BUSINESS LOGIC) HERE!!!========== //
  // EXTEND THIS DAO CLASS WITH YOUR OWN CLASS CONTAINING YOUR BUSINESS LOGIC //
  //  BECAUSE THIS CLASS FILE WILL BE OVERWRITTEN UPON EACH NEW PHPDAO BUILD  //
  // ======================================================================== //
}

