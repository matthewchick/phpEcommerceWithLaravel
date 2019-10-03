<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', 'ProductsController@index')->name('allProducts');

//show all products
//Route::get('products', ["uses"=>"ProductsController@index", "as"=> "allProducts"]);
//Route::get('products/addToCart/{id}', ['uses'=>'ProductsController@addProductToCart', 'as'=> 'AddToCartProduct']);

// return redirect()->route('allProducts'); inside ProductsController
// Section 4-25 Get Data Dynamically from DB
Route::get('products', 'ProductsController@index')->name('allProducts');
// for men
Route::get('products/men', 'ProductsController@menProducts')->name('menProducts');
// for women
Route::get('products/women', 'ProductsController@womenProducts')->name('womenProducts');
// for search
Route::get('search', 'ProductsController@search')->name('searchProducts');
// Section 5 : The Cart
// add items to cart
Route::get('products/addToCart/{id}', 'ProductsController@addProductToCart')->name('AddToCartProduct');
// show cart items
Route::get('cart', 'ProductsController@showCart')->name('cartproducts');
// delete cart items
Route::get('products/deleteItemFromCart/{id}', 'ProductsController@deleteItemFromCart')->name('DeleteItemFromCart');
// Section 8-57 Increasing & Descreasing single product
// increase single product in cart
Route::get('products/increaseSingleProduct/{id}', 'ProductsController@increaseSingleProduct')->name('increaseSingleProduct');
// decrease single product in cart
Route::get('products/decreaseSingleProduct/{id}', 'ProductsController@decreaseSingleProduct')->name('decreaseSingleProduct');
// Section 9: Checkout form & Create Orders
// create an order
Route::get('products/createOrder', 'ProductsController@createOrder')->name('createOrder');
// create an new order
Route::get('products/createNewOrder', 'ProductsController@createNewOrder')->name('createNewOrder');

// checkout page
Route::get('products/checkoutProducts', 'ProductsController@checkoutProducts')->name('checkoutProducts');

// Seciton 10 Payment
Route::get('payment/paymentpage', 'Payment\paymentController@showPaymentPage')->name('showPaymentPage');

// Section 6: Authentication & Sharing Data by using php artisan make:auth
// user authentication
Auth::routes();

Route::get('/home', 'HomeController@index')->name('home');

// Section 7: Admin Panel
Route::get('admin/products', 'Admin\AdminProductsController@index')->name('adminDisplayProducts')->middleware('restrictToAdmin');
// display edit product form
Route::get('admin/editProductForm/{id}', 'Admin\AdminProductsController@editProductForm')->name('adminEditProductForm');
// display edit image form
Route::get('admin/editProductImageForm/{id}', 'Admin\AdminProductsController@editProductImageForm')->name('adminEditProductImageForm');
// update product image
Route::post('admin/updateProductImage/{id}', 'Admin\AdminProductsController@updateProductImage')->name('adminUpdateProductImage');
// update product after edit
Route::post('admin/updateProduct/{id}', 'Admin\AdminProductsController@updateProduct')->name('adminUpdateProduct');
// display create product form
Route::get('admin/createProductForm', 'Admin\AdminProductsController@createProductForm')->name('adminCreateProductForm');
// send new product data to database
Route::post('admin/sendCreateProductForm/', 'Admin\AdminProductsController@sendCreateProductForm')->name('adminSendCreateProductForm');
// delete product
Route::get('admin/deleteProduct/{id}', 'Admin\AdminProductsController@deleteProduct')->name('adminDeleteProduct');

// storage
Route::get('/testStorage',function(){

    // return "<img src=".Storage::url('product_images/jacket.jpg').">";
   // return Storage::disk('local')->url('product_images/jacket.jpg');
   // print_r(Storage::disk("local")->exists("public/product_images/jacket.jpg"));
  // Storage::delete('public/product_images/jacket.jpg');

});



