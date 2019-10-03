<?php

namespace App;

class Cart
{
    public $items;   // items is an array, [ ['id' => ['quantity' => , 'price' => , 'data' =>],....]
    public $totalQuantity;
    public $totalPrice;

    public function __construct($prevCart){  // $prevCart is an object
        if ($prevCart != null) {
            $this->items = $prevCart->items;
            $this->totalQuantity = $prevCart->totalQuantity;
            $this->totalPrice = $prevCart->totalPrice;
        } else {
            // if empty
            $this->items = [];
            $this->totalQuantity = 0;
            $this->totalPrice = 0;
        }
    }

    public function addItem($id, $product){

        $price = (int)str_replace("$","",$product->price); // extract $ sign and convert it into an integer

        if (array_key_exists($id,$this->items)){
            // the item already exists
            $productToAdd = $this->items[$id];
            $productToAdd['quantity']++;
            $productToAdd['totalSinglePrice'] = $productToAdd['quantity'] *  $price;

        } else {
            // first time to add this product to cart
            $productToAdd = ['quantity'=> 1, 'totalSinglePrice'=> $price, 'data'=>$product];
        }
        $this->items[$id] = $productToAdd;    // array of object
        $this->totalQuantity++;
        $this->totalPrice= $this->totalPrice + $price;
    }

    public function updatePriceAndQuantity(){

        $totalPrice = 0;
        $totalQuantity = 0;

        foreach($this->items as $item){

            $totalQuanity = $totalQuantity + $item['quantity'];
            $totalPrice = $totalPrice + $item['totalSinglePrice'];

        }

        $this->totalQuantity = $totalQuantity;
        $this->totalPrice =  $totalPrice;

   }
}
