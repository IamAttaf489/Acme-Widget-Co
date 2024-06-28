<?php

include 'BasketInterface.php';

class Basket implements BasketInterface
{
    private $productCatalogue;
    private $deliveryChargeRules;
    private $offers;
    private $items = [];

    public function __construct($productCatalogue,$deliveryChargeRules,$offers){
        $this->productCatalogue = $productCatalogue;
        $this->deliveryChargeRules = $deliveryChargeRules;
        $this->offers = $offers;
    }

    public function add($productCode) {
        if (isset($this->productCatalogue[$productCode])) {
            $this->items[] = $productCode;
        } else {
            //TODO Send logs to AWS Cloud Watch
            //TODO Send Simple message like "Something went wrong. Please comeback latter" so end users don't see any crash
        }
    }

    public function total() {
        $subtotal = $this->calculateSubtotal();
        $subtotal = $this->applyOffers($subtotal);
        $delivery = $this->calculateDeliveryCharges($subtotal);
        return $subtotal + $delivery;
    }

    public function calculateSubtotal() {
        $subtotal = 0;
        foreach ($this->items as $item) {
            $subtotal  = $subtotal + $this->productCatalogue[$item]['price'];
        }
        return $subtotal;
    }

    private function applyOffers($subtotal) {
        $itemsOfferApplied = [];
        foreach ($this->items as $item){
            if(isset($this->offers[$item])){
                foreach ($this->offers[$item] as $offer){
                    if($offer['code'] == 'buyOneGetSecondHalfPrice'){
                        $itemCounts = array_count_values($this->items);
                        if (isset($itemCounts[$item]) && $itemCounts[$item] > 1 && !in_array($item,$itemsOfferApplied)) {
                            $price = $this->productCatalogue[$item]['price'];
                            $discounts = floor(($itemCounts[$item] / 2)) * ($price / 2);
                            $subtotal = $subtotal - $discounts;
                            $itemsOfferApplied[] = $item;
                        }
                    }
                    //TODO Apply conditions for rest of offers
                }
            }
        }
        return $subtotal;
    }

    private function calculateDeliveryCharges($subtotal) {
        foreach ($this->deliveryChargeRules as $chargeRule){
            if($subtotal < $chargeRule['maxPrice']){
                return $chargeRule['delivery'];
            }
        }
    }
}
