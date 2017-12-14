<?php

require 'models/Category.php';
require 'models/Service.php';
require 'models/Product.php';
require 'models/Usage.php';
require 'models/VService.php';
require 'models/VUsage.php';

//

$Category=new Category();

$Category->setCatname('wash');
$Category->create();
$washCategoryId=$Category->getCatId();

//

$Service=new Service();

$Service->setCatId($washCategoryId);
$Service->setSvcname('Interier Wash');
$Service->create();
$interierWashId=$Service->getSvcId();

// Object Create/Update

$Product=new Product();

$Product->setProdname('Car');
$Product->setProdDate(date('Y-m-d'));
$Product->create();
$carProductId=$Product->getProdId();

$Product->setProdname('Truck');
$Product->setProdDate(date('Y-m-d'));
$Product->create();
$truckProductId=$Product->getProdId();

$Ps=$Product->findByProdname('Car'); //returns an array of objects
foreach($Ps as $P)
{
  $P->setProdname($P->getProdname().'2');
  $P->update();
}

$P=$Product->findByProdId($truckProductId); // returns one object
$P->setProdname($P->getProdname().'2');
$P->update();

// Composite Primary Key Example

$Usage=new Usage();

$Usage->setProdId($carProductId);
$Usage->setSvcId($interierWashId);
$Usage->setDate1(date('Y-m-d'));
$Usage->create();

$ids=$Usage->getIds();

print_r($ids);

// Delete

$U=new Usage();
$U->setProdId($carProductId);
$U->setSvcId($interierWashId);
$U->delete();

$P=new Product();
$P->setProdId($truckProductId);
$P->delete();
$P->setProdId($carProductId);
$P->delete();

$S=new Service();
$S->setSvcId($interierWashId);
$S->delete();

$C=new Category();
$C->setCatId($washCategoryId);
$C->delete();