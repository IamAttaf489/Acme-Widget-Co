<?php

include 'Basket.php';

$productCatalog = [
    'R01' => ['name' => 'Red Widget', 'price' => 32.95],
    'G01' => ['name' => 'Green Widget', 'price' => 24.95],
    'B01' => ['name' => 'Blue Widget', 'price' => 7.95],
];

$deliveryChargesRules = [
    ['maxPrice' => 50, 'delivery' => 4.95],
    ['maxPrice' => 90, 'delivery' => 2.95],
    ['maxPrice' => PHP_INT_MAX, 'delivery' => 0]
];

$offers = [
    'R01' => [['code'=>'buyOneGetSecondHalfPrice']]
];

$basket = new Basket($productCatalog, $deliveryChargesRules, $offers);

$basket->add('B01');
$basket->add('G01');
echo "Total: $" .$basket->total(). "\n";

$basket = new Basket($productCatalog, $deliveryChargesRules, $offers);
$basket->add('R01');
$basket->add('R01');
echo "Total: $" .$basket->total(). "\n";

$basket = new Basket($productCatalog, $deliveryChargesRules, $offers);
$basket->add('R01');
$basket->add('G01');
echo "Total: $" .$basket->total(). "\n";;

$basket = new Basket($productCatalog, $deliveryChargesRules, $offers);
$basket->add('B01');
$basket->add('B01');
$basket->add('R01');
$basket->add('R01');
$basket->add('R01');
echo "Total: $" .$basket->total(). "\n";
