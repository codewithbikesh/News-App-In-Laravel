<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\SubCategory;
use Illuminate\Auth\Events\Validated;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;

class SubCategoryController extends Controller
{
    public function create(){
        // get category name from category table 
        $categories = Category::orderBy('name', 'ASC')->get();
        $data['categories'] = $categories;
        return view("admin.sub_category.create",$data);
    }

    // store the sub-categories into database 
    // store the sub-categories into database 
    public function store(Request $request){
    $validator = Validator::make($request->all(),[
          'name' =>  'required',
          'slug' => 'required|unique:sub_categories',
          'category' => 'required',
          'status' => 'required'

    ]);
      if($validator->passes()){
      $subCategory = new SubCategory();
      $subCategory->name = $request->name;
      $subCategory->slug = $request->slug;
      $subCategory->status = $request->status;
      $subCategory->category_id = $request->category;
      $subCategory->save();

      $request->session()->flash('success', 'Sub Category Created successfully');

      return response([
        'status' => true,
        'message' => 'Sub Category Created successfully'
      ]);

    }else{
        return response()->json([
            'status' => false,
            'errors' => $validator->errors()
        ]);
    }
}
}