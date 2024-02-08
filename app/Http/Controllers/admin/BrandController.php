<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Brand;
use Illuminate\Support\facades\Validator;
class BrandController extends Controller
{
    // show data on list page
    // show data on list page
public function index(Request $request){
    $brands = Brand::latest('id');
    if(!empty($request->get('keyword'))){
        $brands = $brands->where('brands.name', 'like','%'.$request->get('keyword').'%');
      }
    $brands = $brands->paginate(10);
    return view('admin.brands.list', compact('brands'));

}

    public function create(){
         return view("admin.brands.create");
    }

    // store brand data into database 
    // store brand data into database 
    public function store(Request $request){
        $validator = Validator::make($request->all(),[
            'name' => 'required',
            'slug' => 'required|unique:brands',
        ]);

        if($validator->passes()){
            $brand = new Brand();
            $brand->name = $request->name;
            $brand->slug = $request->slug;
            $brand->status = $request->status;
            $brand->save();

            $request->session()->flash('success', 'Brand added successfully');
             

            return response()->json([
                'status' => true,
                'message' => 'Brand added successfully'
            ]);

    }else{
        return response()->json([
            'status' => false,
            'errors' => $validator->errors()
        ]);
    }
}

// get the brand data for update on the page 
// get the brand data for update on the page 
public function edit($id, Request $request){
    $brand = Brand::find($id);
    if(empty($brand)){
        return redirect()->route('brands.index');
        $request->session()->flash('error', 'Brand record not found');
        return response()->json([
            'status'=> false,
            'notFound' => true,
            'message' => 'Brand not found'
        ]);
    }
    return view('admin.brands.edit', compact('brand'));
}

// update the brand data controller code line from over here 
// update the brand data controller code line from over here 
public function update($id, Request $request){
    $brand = Brand::find($id);
    if(empty($brand)){
        return redirect()->route('brands.index');
        $request->session()->flash('error', 'Brand not found');
        return response()->json([
            'status'=> false,
            'notFound' => true,
            'message' => 'Brand not found'
        ]);
    }

    $validator =  Validator::make($request->all(), [
        'name' => 'required',
        'slug' => 'required|unique:brands,slug,'.$brand->id.',id',
        'status' => 'required'
    ]);

    if($validator->passes()){
        $brand = new Brand();
        $brand->name = $request->name;
        $brand->slug = $request->slug;
        $brand->status = $request->status;
        $brand->save();

        $request->session()->flash('success','Brand updated successfully');
        return response()->json([
            'status' => true,
            'message' => 'Brand updated successfully'
        ]);
    }else{
          return response()->json([
            'status'=> false,
            'message' => $validator->errors()
          ]);
    }
}

// Delete the brand data from database 
// Delete the brand data from database 
public function destroy($id, Request $request){
    $brand = Brand::find($id);

    if(empty($brand)){
        $request->session()->flash('erorr', 'Record not found');
        return redirect()->route('brands.index');
    }

    $brand->delete();

    $request->session()->flash('success','Brands deleted successfully');

    return response()->json([
        'status'=> true,
        'message'=> 'Brands deleted successfully'
    ]);

}


}
