<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    // Section 3 model which is used to insert data into Product table
    protected $fillable = [
        'name', 'description', 'image', 'price', 'type'
    ];

    //Section 3-21 Change the price format
    public function getPriceAttribute($value) {
        $newForm ="$".$value;
        return $newForm;
    }
}
