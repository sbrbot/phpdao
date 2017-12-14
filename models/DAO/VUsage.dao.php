<?php

require_once 'DAO.php';

/**
 * Class VUsageDAO
 * COMMENT: VIEW
*/
abstract class VUsageDAO extends EntityBase
{

  /**
   * Protected variable
   * @var int $svcid
   */
  protected $svcid;

  public function getSvcId() {return $this->svcid;}

  /**
   * Protected variable
   * @var varchar $svcname
   */
  protected $svcname;

  public function getSvcname() {return $this->svcname;}

  /**
   * Protected variable
   * @var date $date1
   */
  protected $date1;

  public function getDate1() {return $this->date1;}

  /**
   * Protected variable
   * @var date $date2
   */
  protected $date2;

  public function getDate2() {return $this->date2;}

  /**
   * Protected variable
   * @var int $prodid
   */
  protected $prodid;

  public function getProdId() {return $this->prodid;}

  /**
   * Protected variable
   * @var varchar $prodname
   */
  protected $prodname;

  public function getProdname() {return $this->prodname;}

  /**
   * Constructor
   * @var mixed $id
   */
  public function __construct($id=0)
  {
    parent::__construct();
    $this->table='v_usages';
    $this->primkeys=[];
    $this->fields=['svcid','svcname','date1','date2','prodid','prodname'];
    $this->sql="SELECT * FROM {$this->table}";
    if($id) $this->read($id);
  }

  // ==========!!!DO NOT PUT YOUR OWN CODE (BUSINESS LOGIC) HERE!!!========== //
  // EXTEND THIS DAO CLASS WITH YOUR OWN CLASS CONTAINING YOUR BUSINESS LOGIC //
  //  BECAUSE THIS CLASS FILE WILL BE OVERWRITTEN UPON EACH NEW PHPDAO BUILD  //
  // ======================================================================== //
}

