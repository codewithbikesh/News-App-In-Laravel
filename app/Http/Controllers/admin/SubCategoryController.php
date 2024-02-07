<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Category;
use App\Models\SubCategory;
use Illuminate\Auth\Events\Validated;
use Illuminate\Support\Facades\Validator;

class SubCategoryController extends Controller
{
  // show entire data from database in list page 
  // show entire data from database in list page 
  public function index(Request $request){
    $sabCategories = SubCategory::select('sub_categories.*','categories.name as categoryName')
    ->latest('sub_categories.id')
    ->leftJoin('categories','categories.id', 'sub_categories.category_id');

    if(!empty($request->get('keyword'))){
      $sabCategories = $sabCategories->where('sub_categories.name', 'like','%'.$request->get('keyword').'%');
      $sabCategories = $sabCategories->orWhere('categories.name', 'like','%'.$request->get('keyword').'%');
    }

    $sabCategories =  $sabCategories->paginate(10);
    // dd($categories);
    return view('admin.sub_category.list',compact('sabCategories'));
  }

  
  // get category name from category table 
  // get category name from category table 
  public function create(){
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


// edit the sub category form cotroller code over here 
// edit the sub category form cotroller code over here 
public function edit($id, Request $request){
  $subCategory = SubCategory::find($id);
  if(empty($subCategory)){
    $request->session()->flash('error', 'Record not found');
    return redirect()->route('sub-categories.index');
  }
        $categories = Category::orderBy('name', 'ASC')->get();
        $data['categories'] = $categories;
        $data['subCategory'] = $subCategory;
        return view("admin.sub_category.edit",$data);
}

// update the sub category form through controller code over here
// update the sub category form through controller code over here
public function update($id, Request $request){
  $subCategory = SubCategory::find($id);
  if(empty($subCategory)){
    $request->session()->flash('error', 'Record not found');
    return response([
      'status' => false,
      'notFound' => true
    ]);
  }

  $validator = Validator::make($request->all(),[
    'name' =>  'required',
    // 'slug' => 'required|unique:sub_categories',
    'slug' => 'required|unique:sub_categories,slug,'.$subCategory->id.',id',
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

$request->session()->flash('success', 'Sub Category Update successfully');

return response([
  'status' => true,
  'message' => 'Sub Category Update successfully'
]);

}else{
  return response()->json([
      'status' => false,
      'errors' => $validator->errors()
  ]);
}
}

 //  Delete the category data from database code line from over here 
  //  Delete the category data from database code line from over here 
  public function destory($id, Request $request){
    $subCategory = SubCategory::find($id);
    if(empty($subCategory)){
      $request->session()->flash('error', 'Record not found');
      return redirect()->route('sub-categories.index');
    }

    $subCategory->delete();

    $request->session()->flash('success','Sub Category deleted successfully');

     return response()->json([
      'status'=> true,
      'message' => 'Sub Category deleted successfully'
     ]);
     
   }
}