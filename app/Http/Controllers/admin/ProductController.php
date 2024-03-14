<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Brand;
use App\Models\Product;
use App\Models\ProductImage;
use App\Models\TempImage;
use Illuminate\Auth\Events\Validated;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Nette\Utils\Image; // Import Image facade


class ProductController extends Controller
{
    public function index(Request $request){
         $products = Product::latest('id')->with('product_images');

            if($request->get('keyword') !=""){
                $products = $products->where('title','like','%'.$request->keyword.'%');
            }

         $products = $products->paginate();
         $data['products'] = $products;
         return view('admin.products.list',$data);
    }
    public function create(){
        $data = [];
        $categories = Category::orderBy('name', 'ASC')->select('id', 'name')->get();
        // $categories = Category::orderBy('name', 'ASC')->get();
        $brands = Brand::orderBy('name', 'ASC')->get();
        $data['categories'] = $categories;
        $data['brands'] = $brands;
        return view('admin.products.create', $data);
    }

    public function store(request $request){

       $rules = [
       'title' =>'required',
       'slug' =>'required|unique:products',
       'price' =>'required|numeric',
       'sku' =>'required',
       'track_qty' =>'required|in:Yes,No',
       'category' =>'required|numeric',
       'is_featured' =>'required|in:Yes,No',
       ];
        
       if(!empty($request->track_qty) && $request->track_qty == 'Yes'){
         $rules['qty'] = 'required|numeric';
       }
       
       $validator =  Validator::make($request->all(),$rules);
      if($validator->passes()){
        $product = new Product;
        $product->title = $request->title;
        $product->slug = $request->slug;
        $product->price = $request->price;
        $product->compare_price = $request->compare_price;
        $product->sku = $request->sku;
        $product->barcode = $request->barcode;
        $product->track_qty = $request->track_qty;
        $product->qty = $request->qty;
        $product->status = $request->status;
        $product->category_id = $request->category;
        $product->sub_category_id = $request->sub_category_id;
        $product->description = $request->description;
        $product->brand_id = $request->brand;
        $product->is_featured = $request->is_featured;
        $product->save();

        // Save Gallery Pics 
        if(!empty($request->image_array)){
            foreach($request->image_array as $temp_image_id){
               $tempImageInfo = TempImage::find($temp_image_id);
               $extArray = explode('.',$tempImageInfo->name);
               $ext = last($extArray);

                $productImage = new ProductImage();
                $productImage->image_id = $product->id;
                $productImage->image = 'Null';  
                $productImage->save();        

                $imageName = $product->id.'-'.$productImage->id.'-'.time().'.'.$ext;
                $productImage->image = $imageName;
                $productImage->save();

                // Generate Product Thumbnails 

                // Large image 
                $sourcePath = public_path().'/temp/'.$tempImageInfo->name;
                $destPath = public_path().'/uploads/product/large/'.$imageName;
                $image = Image::make($sourcePath);
                $image->resize(1400, null, function ($contraint){
                    $contraint->aspectRatio();
                });
                $image->save($destPath );

                // Small Image 
                $destPath = public_path().'/uploads/product/small/'.$imageName;
                $image = Image::make($sourcePath);
                $image->fit(300,300);
                $image->save($destPath );
              }
        }

        $request->session()->flush('success','Product added successfully');

        return response()->json([
            'status' => true,
            'message' => 'Product added successfully'
        ]);
    }else{
         return response()->json([
            'status' => false,
            'errors' => $validator->errors()
         ]);
    }
      
}
}
