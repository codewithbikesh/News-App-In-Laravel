@extends('admin.layouts.app')

@section('content')
<!-- Content Header (Page header) -->
<section class="content-header">
    <div class="container-fluid my-2">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1>Create News</h1>
            </div>
            <div class="col-sm-6 text-right">
                <a href="{{ route('news.index') }}" class="btn btn-primary">Back</a>
            </div>
        </div>
    </div>
    <!-- /.container-fluid -->
</section>

<!-- Main content -->
<section class="content">
    <!-- Default box -->
    <form action="" method="post" name="newsForm" id="newsForm" enctype="multipart/form-data">
        @csrf
        <div class="container-fluid">
            <div class="row">
                <div class="col-md-8">
                    <div class="card mb-3">
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="mb-3">
                                        <label for="title">Title</label>
                                        <input type="text" name="title" id="title" class="form-control" placeholder="Title" value="{{$news->title}}">
                                        <p class="error"></p>
                                    </div>
                                </div>
                                <div class="col-md-12">
                                    <div class="mb-3">
                                        <label for="slug">Slug</label>
                                        <input type="text" readonly name="slug" id="slug" class="form-control" placeholder="Slug" readonly value="{{$news->Slug}}">
                                        <p class="error"></p>
                                    </div>
                                </div>
                                <div class="col-md-12">
                                    <div class="mb-3">
                                        <label for="description">Description</label>
                                        <textarea name="description" id="description" cols="30" rows="10" class="summernote" placeholder="Description">{{ $news->description }}</textarea>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="card mb-3">
                        <div class="card-body">
                            <h2 class="h4 mb-3">Media</h2>
                            <input class="form-control" type="file" value="{{ $news->image }}" name="image" id="image">
                        </div>
                    </div>
                    <div class="row" id="news-gallery"></div>
                    <p class="errors"></p>
                </div>
                <div class="col-md-4">
                    <div class="card">
                        <div class="card-body">
                            <h2 class="h4 mb-3">News category</h2>
                            <div class="mb-3">
                                <label for="category">Category</label>
                                <select name="category" id="category" class="form-control">
                                    <option value="{{$news->category_id}}">{{$news->category_id}}</option>
                                    @if ($categories->isNotEmpty())
                                        @foreach ($categories as $item)
                                            <option value="{{ $item->id }}">{{ $item->name }}</option>
                                        @endforeach
                                    @endif
                                </select>
                                <p class="error"></p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="pb-5 pt-3">
                <button type="submit" class="btn btn-primary">Update</button>
                <a href="{{ route('news.index') }}" class="btn btn-outline-dark ml-3">Cancel</a>
            </div>
        </div>
    </form>
    <!-- /.card -->
</section>
<!-- /.content -->
@endsection

{{-- javascript code line from here  --}}
{{-- javascript code line from here  --}}
@section('costomJs')
<script>         
$("#newsForm").submit(function(event) {
event.preventDefault();
var element = $(this);
$("button[type=submit]").prop('disabled', true);
$.ajax({
url: '{{ route("news.update",$news->id) }}',
type: 'put',
data: element.serialize(), // Use serialize() instead of serializeArray()
dataType: 'json',
success: function(response) {
    $("button[type=submit]").prop('disabled', false);
    if(response["status"] == true){
        window.location.href="{{ route('news.index') }}";
        $("#name").removeClass('is-invalid').siblings('p').removeClass('invalid-feedback').html(""); 
        $("#slug").removeClass('is-invalid').siblings('p').removeClass('invalid-feedback').html("");
    }else{               
    var errors = response['errors'];
    if(errors['name']){
      $("#name").addClass('is-invalid').siblings('p').addClass('invalid-feedback').html(errors['name']);
    }else{
      $("#name").removeClass('is-invalid').siblings('p').removeClass('invalid-feedback').html(""); 
    }

    if(errors['slug']){
      $("#slug").addClass('is-invalid').siblings('p').addClass('invalid-feedback').html(errors['slug']);
    }else{
      $("#slug").removeClass('is-invalid').siblings('p').removeClass('invalid-feedback').html("");
    }
}
},
error: function(jqXHR, exception) {
    console.log("Something went wrong");
}
});
});

// change auto name to slug name 
// change auto name to slug name 
$("#name").change(function(){
element = $(this);
$("button[type=submit]").prop('disabled', true);
$.ajax({
url: '{{ route("getSlug") }}',
type: 'get',
data: {title: element.val()},
dataType: 'json',
success: function(response) {
    $("button[type=submit]").prop('disabled', false);
     if(response["status"] == true) {
        $("#slug").val(response["slug"]);
     }
}
});
});

 </script>
@endsection