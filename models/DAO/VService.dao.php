<?php

require_once 'DAO.php';

/**
 * Class VServiceDAO
 * COMMENT: VIEW
*/
abstract class VServiceDAO extends EntityBase
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
   * @var int $catid
   */
  protected $catid;

  public function getCatId() {return $this->catid;}

  /**
   * Protected variable
   * @var varchar $catname
   */
  protected $catname;

  public function getCatname() {return $this->catname;}

  /**
   * Constructor
   * @var mixed $id
   */
  public function __construct($id=0)
  {
    parent::__construct();
    $this->table='v_services';
    $this->primkeys=[];
    $this->fields=['svcid','svcname','catid','catname'];
    $this->sql="SELECT * FROM {$this->table}";
    if($id) $this->read($id);
  }

  // ==========!!!DO NOT PUT YOUR OWN CODE (BUSINESS LOGIC) HERE!!!========== //
  // EXTEND THIS DAO CLASS WITH YOUR OWN CLASS CONTAINING YOUR BUSINESS LOGIC //
  //  BECAUSE THIS CLASS FILE WILL BE OVERWRITTEN UPON EACH NEW PHPDAO BUILD  //
  // ======================================================================== //
}

