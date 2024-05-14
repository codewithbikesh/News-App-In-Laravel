<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\News;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class NewsController extends Controller
{
    public function index(Request $request)
    {
        $news = News::query(); 

        if ($request->get('keyword') != "") {
            $news = $news->where('title', 'like', '%' . $request->keyword . '%');
        }

        $news = $news->paginate();
        $data['news'] = $news;
        return view('admin.news.list', $data);
    }

    public function create()
    {
        $data = [];
        $categories = Category::orderBy('name', 'ASC')->select('id', 'name')->get();
        $data['categories'] = $categories;
        return view('admin.news.create', $data);
    }

    public function store(Request $request)
    {
        $rules = [
            'title' => 'required',
            'slug' => 'required|unique:news',
            'category' => 'required|numeric',
            'image' => 'required|image|mimes:jpeg,png,jpg,gif,svg|max:2048'
        ];

        $validator = Validator::make($request->all(), $rules);

        if ($validator->passes()) {
            $news = new News();
            $news->title = $request->title;
            $news->slug = $request->slug;
            $news->category_id = $request->category;
            $news->description = $request->description;

            if ($request->hasFile('image')) {
                $file = $request->file('image');
                $extension = $file->getClientOriginalExtension();
                $filename = time() . '.' . $extension;
                $file->move(public_path('uploads/news'), $filename);
                $news->image = $filename;
            }

            $news->save();

            $request->session()->flash('success', 'News added successfully');

            return response()->json([
                'status' => true,
                'message' => 'News added successfully'
            ]);
        } else {
            return response()->json([
                'status' => false,
                'errors' => $validator->errors()
            ]);
        }
    }

    public function edit($newsID)
    {
        $news = News::find($newsID);
        $categories = Category::orderBy('name', 'ASC')->select('id', 'name')->get();
        if (empty($news)) {
            return redirect()->route('news.index');
        }
        return view('admin.news.edit', compact('news', 'categories'));
    }

    public function update($newsID, Request $request)
    {
        $news = News::find($newsID);
        if (empty($news)) {
            $request->session()->flash('error', 'News not found');
            return response()->json([
                'status' => false,
                'notFound' => true,
                'message' => 'News not found'
            ]);
        }

        $validator = Validator::make($request->all(), [
            'title' => 'required',
            'slug' => 'required|unique:news,slug,' . $news->id,
            'category' => 'required|numeric',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048'
        ]);

        if ($validator->passes()) {
            $news->title = $request->title;
            $news->slug = $request->slug;
            $news->category_id = $request->category;
            $news->description = $request->description;

            if ($request->hasFile('image')) {
                // Retrieve the current image path from the database
                $currentImage = $news->image;

                // Handle image upload
                $image = $request->file('image');
                $imageName = time() . '.' . $image->getClientOriginalExtension();
                $image->move(public_path('uploads/news/'), $imageName);
                $news->image = $imageName;

                // Delete the old image
                if ($currentImage) {
                    $imagePath = public_path('uploads/news/') . $currentImage;
                    if (file_exists($imagePath)) {
                        unlink($imagePath);
                    }
                }
            }

            $news->save();

            $request->session()->flash('success', 'News updated successfully');

            return response()->json([
                'status' => true,
                'message' => 'News updated successfully'
            ]);
        } else {
            return response()->json([
                'status' => false,
                'errors' => $validator->errors()
            ]);
        }
    }


    //  Delete the category data from database code line from over here 
  //  Delete the category data from database code line from over here 
   public function destory($newsId, Request $request){
    $news = News::find($newsId);
    if(empty($news)){
      return redirect()->route('news-index');
    }

    // Check if the student exists 
    if($news){
    //  Delete the associated image file 
    $imagePath = public_path('uploads/news/' .$news->image);
    if(file_exists($imagePath)){
      unlink($imagePath);
    }

    $news->delete();

    $request->session()->flash('success','News deleted successfully');

     return response()->json([
      'status'=> true,
      'message' => 'News deleted successfully'
     ]);
   }
}
}
