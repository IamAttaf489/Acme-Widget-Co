# Basket Calculation System

## Assumptions

This solution assumes that inputs will be in the following format:

### Product Catalog

An associative array where:
- Key: Product code
- Value: Product details (i.e., name and price)

### Delivery Charge Rule

A multi-dimensional array where each entry is an associative array containing:
- Maximum price on which the delivery can be applied
- Delivery charges

### Offers

An associative array where:
- Key: Product code
- Value: Multi-dimensional array storing information regarding the offers applicable to a product

## Workflow

The `Basket` class implements an interface to ensure the basket always provides definitions of the `add` and `total` methods.

### `add` Function

This function accepts a product code and:
1. Checks if the given product code is in the product catalog.
2. If true, it adds the product to the items list. If not, the system will:
   - Send logs to AWS CloudWatch for monitoring purposes (Function not implemented).
   - Send a message to the client-side to ensure the client does not see any crash (Function not implemented).

### `total` Function

The role of this function is to return the total price of the products. This process requires the following steps:
1. Calculate the sub    total price.
2. Apply offers if any are applicable.
3. Calculate delivery charges.
4. Return the final price.

### `calculateSubtotal` Function

- Loops over all products from items.
- For each product, uses `productCatalog` to get its price and add that price to the total.

### `applyOffers` Function

- Loops over all items.
- Checks if there are any offers against the item.
- If found, then depending on the offer code:
  - Calculates the discount amount.
  - Deducts the discount amount from the total.
- Returns the final price.

### `calculateDeliveryCharges` Function

- Receives the subtotal (after application of offers, if any).
- Loops over the delivery rules array.
- Checks if the subtotal is less than the max price of the delivery rule.
- If true, applies that rule and returns the corresponding delivery charge.

## Example Usage

```php
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
    'R01' => [['code' => 'buyOneGetSecondHalfPrice']]
];

$basket = new Basket($productCatalog, $deliveryChargesRules, $offers);

$basket->add('B01');
$basket->add('G01');
echo "Total: $" . $basket->total() . "\n";

$basket = new Basket($productCatalog, $deliveryChargesRules, $offers);
$basket->add('R01');
$basket->add('R01');
echo "Total: $" . $basket->total() . "\n";

$basket = new Basket($productCatalog, $deliveryChargesRules, $offers);
$basket->add('R01');
$basket->add('G01');
echo "Total: $" . $basket->total() . "\n";

$basket = new Basket($productCatalog, $deliveryChargesRules, $offers);
$basket->add('B01');
$basket->add('B01');
$basket->add('R01');
$basket->add('R01');
$basket->add('R01');
echo "Total: $" . $basket->total() . "\n";
