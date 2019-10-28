<?php
namespace Tracktik;

// set application-wide vars
define('APP_BASE', __DIR__);
define('CLASS_BASE', APP_BASE .'/classes');

// load classes (in a real application this would be done with composer)
require_once CLASS_BASE .'/electronic-item.class.php';
require_once CLASS_BASE .'/electronic-items.class.php';

$tv1 = ElectronicItem::factory('television');
$tv2 = ElectronicItem::factory('television', ['price' => 200]);
$console = ElectronicItem::factory('console');
$microwave = ElectronicItem::factory('microwave');

$console->addExtras([
    ElectronicItem::factory('controller'),
    ElectronicItem::factory('controller'),
    ElectronicItem::factory('controller_wireless'),
    ElectronicItem::factory('controller_wireless'),
]);

$tv1->addExtras([
    ElectronicItem::factory('controller_wireless'),
    ElectronicItem::factory('controller_wireless'),
]);

$tv2->addExtra(ElectronicItem::factory('controller'));

$cart = new ElectronicItems([
    $console,
    $tv1,
    $tv2,
    $microwave,
]);

echo $cart->printLines(['detailed' => true]);

echo "\n\nTotal price of console with extras: ". $console->getTotalPrice();
echo "\nDone :)\n";


