<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Product;
use App\Cart;
use Illuminate\Support\Facades\Session;

class ProductsController extends Controller
{
    // Section 4-26; add image to Project & Database with product.sql
    public function index() {
        // use another method to call database by DB but not eloguent
        // $products = DB::table('products')->get();
        // $products = Product::all();
        $products = Product::paginate(3);
        return view('allproducts', compact('products'));
    }
    // Section 8 Query building & features
    // https://laravel.com/docs/4.2/queries
    public function menProducts() {
        $products = DB::table('products')->where('type', 'men')->get();
        return view('menProducts', compact('products'));
    }

    public function womenProducts() {
        $products = DB::table('products')->where('type', 'women')->get();
        return view('womenProducts', compact('products'));
    }

    public function search(Request $request) {
        $searchText = $request->get('searchText');
        // $products = DB::table('products')->where('name', 'Like', $searchText."%")->get();
        // add pagination and search together
        $products = Product::where('name', 'Like', $searchText."%")->paginate(3);
        return view('allProducts', compact('products'));
    }

    public function increaseSingleProduct(Request $request, $id){
        // print_r($id);  for debugging
        $prevCart = $request->session()->get('cart');  // get information from the cart
        $cart = new cart($prevCart);

        $product = Product::find($id);
        $cart->addItem($id,$product);

        $request->session()->put('cart', $cart); //stores it into session
        //dump($cart); for debugging
        return redirect()->route('cartproducts');  // go to show cart
    }

    public function decreaseSingleProduct(Request $request, $id){
        // print_r($id);  for debugging
        $prevCart = $request->session()->get('cart');  // get information from the cart
        $cart = new cart($prevCart);

        if ( $cart->items[$id]['quantity'] > 1) {
            $product = Product::find($id);
            $price=(int) str_replace("$","",$product['price']);
            $cart->items[$id]['quantity'] = $cart->items[$id]['quantity']-1;
            $cart->items[$id]['totalSinglePrice'] = $cart->items[$id]['quantity'] * $price;
            $cart->updatePriceAndQuantity();
            $request->session()->put('cart', $cart); //stores it into session
        }

        //dump($cart); for debugging
        return redirect()->route('cartproducts');  // go to show cart
    }
    // Section 5: The Cart
    public function addProductToCart(Request $request, $id){
        // print_r($id);  // for debugging
        $prevCart = $request->session()->get('cart');  // get null cart at the first time from the cart
        // dump($prevCart);
        $cart = new cart($prevCart);

        $product = Product::find($id);
        $cart->addItem($id,$product);

        $request->session()->put('cart', $cart); //stores it into session
        //dump($cart); for debugging
        return redirect()->route('allProducts');
    }

    public function showCart(){
        $cartItems = Session::get('cart');

        // cart is not empty
        if ($cartItems) {
            //dump($cart);
            //return view('cartproducts',['cartItems'=>$cart]);
            return view('cartproducts',compact('cartItems'));
        } else {
            // if cart is empty
            // echo "The cart is empty";
            return redirect()->route('allProducts');
        }
    }

    public function deleteItemFromCart(Request $request, $id){

        $cart = $request->session()->get('cart');

        if (array_key_exists($id, $cart->items)){
            unset($cart->items[$id]);

        }

        $prevCart = $request->session()->get('cart');
        $updatedCart = new Cart($prevCart);
        $updatedCart->updatePriceAndQuantity();

        $request->session()->put('cart', $updatedCart);

        //return redirect()->route('cartproducts');
        return redirect()->back();
    }

    public function createOrder(){
        $cart = Session::get('cart');

        //cart is not empty
        if($cart) {
        // dump($cart);
            $date = date('Y-m-d H:i:s');
            $newOrderArray = array("status"=>"on_hold","date"=>$date,"del_date"=>$date,"price"=>$cart->totalPrice);
            $created_order = DB::table("orders")->insert($newOrderArray);
            $order_id = DB::getPdo()->lastInsertId();;

            foreach ($cart->items as $cart_item){
                $item_id = $cart_item['data']['id'];
                $item_name = $cart_item['data']['name'];
                $item_price_sign = $cart_item['data']['price'];
                $item_price = str_replace("$","",$item_price_sign);
                $newItemsInCurrentOrder = array("item_id"=>$item_id,"order_id"=>$order_id,"item_name"=>$item_name,"item_price"=>$item_price);
                $created_order_items = DB::table("orders_items")->insert($newItemsInCurrentOrder);
            }

            //delete cart
            Session::forget("cart");
            Session::flush();
            // redirecting to a new UrL and flashing data to the session are usually done at the same time
            return redirect()->route("allProducts")->withsuccess("Thanks For Choosing Us");

        }else{
            return redirect()->route("allProducts");
        }
    }

    public function checkoutProducts(){
        return view('checkoutproducts');

     }

     public function createNewOrder(Request $request){

        $cart = Session::get('cart');

        $first_name = $request->input('first_name');
        $address = $request->input('address');
        $last_name = $request->input('last_name');
        $zip = $request->input('zip');
        $phone = $request->input('phone');
        $email = $request->input('email');

        /*
        //check if user is logged in or not
        $isUserLoggedIn = Auth::check();

        if($isUserLoggedIn){
           //get user id
          $user_id = Auth::id();  //OR $user_id = Auth:user()->id;

        }else{
           //user is guest (not logged in OR Does not have account)
         $user_id = 0;

        }
        */
        //cart is not empty
        if($cart) {
        // dump($cart);
             $date = date('Y-m-d H:i:s');
             $newOrderArray = array("user_id" => $user_id, "status"=>"on_hold","date"=>$date,"del_date"=>$date,"price"=>$cart->totalPrice,
             "first_name"=>$first_name, "address"=> $address, 'last_name'=>$last_name, 'zip'=>$zip,'email'=>$email,'phone'=>$phone);

             $created_order = DB::table("orders")->insert($newOrderArray);
             $order_id = DB::getPdo()->lastInsertId();;

             foreach ($cart->items as $cart_item){
                 $item_id = $cart_item['data']['id'];
                 $item_name = $cart_item['data']['name'];
                 $item_price = $cart_item['data']['price'];
                 $newItemsInCurrentOrder = array("item_id"=>$item_id,"order_id"=>$order_id,"item_name"=>$item_name,"item_price"=>$item_price);
                 $created_order_items = DB::table("order_items")->insert($newItemsInCurrentOrder);
             }


            //send the email

            //delete cart
            ession::forget("cart");
            Session::flush();
            /* $payment_info =  $newOrderArray;
            $payment_info['order_id'] = $order_id;
            $request->session()->put('payment_info',$payment_info);
            */
            print_r($newOrderArray);

            // return redirect()->route("showPaymentPage");

        }else{
            return redirect()->route("allProducts");
        }
    }

}
