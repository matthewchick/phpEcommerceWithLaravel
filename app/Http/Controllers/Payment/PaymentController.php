<?php

namespace App\Http\Controllers\Payment;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Product;
use App\Cart;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;

class PaymentController extends Controller
{
    public function index(){

        /*   $products = [0=> ["name"=>"Iphone","category"=>"smart phones","price"=>1000],
               1=> ["name"=>"Galaxy","category"=>"tablets","price"=>2000],
               2=> ["name"=>"Sony","category"=>"TV","price"=>3000]];*/

            $products = Product::paginate(3);

            return view("allproducts",compact("products"));

    }

    public function showPaymentPage(){

        $cart = Session::get('cart');

            //cart is not empty
            if($cart){
               return view('payment.paymentpage',['cartItems'=> $cart]);

            }else{
                return redirect()->route("allProducts");
            }
            Session::forget("cart");
            Session::flush();
   }


}
