<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\DB;
use App\Product;    //import Product model

class AdminProductsController extends Controller
{
    // display all products
    public function index() {
        // use another method to call database by DB but not eloguent
        // $products = DB::table('products')->get();
        // $products = Product::all();
        // use this otherwise {‌{$products->links()}} will have an error inside AdminProductsController.php
    
        $products = Product::paginate(3);
        // return view('allproducts', compact('products'));
        return view('admin.displayProducts', ['products'=>$products]);
    }

    public function editProductImageForm($id){
        $product = Product::find($id);
        //return view('admin.editProductImageForm', ['product'=>$product]);
        return view('admin.editProductImageForm', compact('product'));
    }

    // update product image - use Request for validation, file size
    public function updateProductImage(Request $request, $id){
        // image is coming from name="image"  max:5000 = 5Gbyte
        // <input type="file" class=""  name="image" id="image" placeholder="Image" value="{{$product->image}}" required>
        // create a validator instance => https://laravel.com/docs/5.8/validation
        Validator::make($request->all(),['image'=>"required|file|image|mimes:jpg,png,jpeg|max:5000"])->validate();


        if($request->hasFile("image")){
          $product = Product::find($id);
          $exists = Storage::disk('local')->exists("public/product_images/".$product->image);

          //delete old image
          if($exists){
             Storage::delete('public/product_images/'.$product->image);
          }

          //upload new image
          $ext = $request->file('image')->getClientOriginalExtension(); //jpg

          $request->image->storeAs("public/product_images/",$product->image);

          $arrayToUpdate = array('image'=>$product->image);
          DB::table('products')->where('id',$id)->update($arrayToUpdate);

          return redirect()->route("adminDisplayProducts");

        }else{

           $error = "NO Image was Selected";
           return $error;

        }

    }

    public function editProductForm($id){
        $product = Product::find($id);
        return view('admin.editProductForm', ['product'=>$product]);
    }

    //update product fields (name,description....)
    public function updateProduct(Request $request,$id){
        // get data from the form by Request object
        $name =  $request->input('name');
        $description =  $request->input('description');
        $type = $request->input('type');
        $price_with_sign = $request->input('price');
        $price = (float)str_replace("$","",$price_with_sign);

        $updateArray = array("name"=>$name, "description"=> $description,"type"=>$type,"price"=>$price);

        DB::table('products')->where('id',$id)->update($updateArray);

        /* Eloguent
        $product = Product::find($id);
        $product->$name =  $request->input('name');
        $product->$description =  $request->input('description');
        $product->$type = $request->input('type');
        $product->$price = $request->input('price');
        $product->save(); */
        return redirect()->route("adminDisplayProducts");
     }

    //display create product form
    public function createProductForm(){
        return view("admin.createProductForm");
    }


    //store new product to database
    public function sendCreateProductForm(Request $request){

        $message = [
            'size'    => 'The :attribute must be exactly :size.',
            'mimes'    => 'The :attribute can ONLY be of png and jpg extension'
        ];
        $name =  $request->input('name');
        $description =  $request->input('description');
        $type = $request->input('type');
        $price = $request->input('price');

        Validator::make($request->all(),['image'=>"required|file|image|mimes:jpg,png,jpeg|max:5000"], $message)->validate();
        $ext =  $request->file("image")->getClientOriginalExtension();
        $stringImageReFormat = str_replace(" ","",$request->input('name'));  // remove any space

        $imageName = $stringImageReFormat.".".$ext; //blackdress.jpg
        // retrieve the contents of a file
        $imageEncoded = File::get($request->image);
        // by default: /storage/app  -> Storage::disk('local')->put('file.txt', 'Contents');
        Storage::disk('local')->put('public/product_images/'.$imageName, $imageEncoded);

        $newProductArray = array("name"=>$name, "description"=> $description,"image"=> $imageName,"type"=>$type,"price"=>$price);

        $created = DB::table("products")->insert($newProductArray);


        if($created){
            return redirect()->route("adminDisplayProducts");
        }else{
           return "Product was not Created";

        }

    }

    //delete product
    public function deleteProduct($id){

        $product = Product::find($id);

        $exists =  Storage::disk("local")->exists("public/product_images/".$product->image);

        //if old image exists
        if($exists){
            //delete it
            Storage::delete('public/product_images/'.$product->image);
        }


        Product::destroy($id);

        return redirect()->route("adminDisplayProducts");

    }
    /*
    public function store(ContactValidation $request){
        // dd($request->all());
        Contact::create([
            'name'=>$request->get('name'),
            'address'=>$request->get('address'),
            'phone'=>$request->get('phone')
        ]);
        return redirect()->back();
    }

    public function edit($id){
        $contact = Contact::findOrFail($id);
        return view('contact.edit', compact('contact'));
    }

    public function update($id){
        $contact=Contact::findOrFail($id);
        $contact->name = request('name');
        $contact->address = request('address');
        $contact->phone = request('phone');
        $contact->save();
        return redirect()->to('/contacts');
    }

    public function show($id) {
        $contact=Contact::findOrFail($id);
        return view('contact.show',compact('contact'));
    }

    public function destroy($id) {
        $contact=Contact::findOrFail($id);
        $contact->delete();
        return redirect()->to('/contacts');
    }
    */
}

