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
    <form action="{{ route('news.store') }}" method="post" name="newsForm" id="newsForm" enctype="multipart/form-data">
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
                                        <input type="text" name="title" id="title" class="form-control" placeholder="Title">
                                        <p class="error"></p>
                                    </div>
                                </div>
                                <div class="col-md-12">
                                    <div class="mb-3">
                                        <label for="slug">Slug</label>
                                        <input type="text" readonly name="slug" id="slug" class="form-control" placeholder="Slug">
                                        <p class="error"></p>
                                    </div>
                                </div>
                                <div class="col-md-12">
                                    <div class="mb-3">
                                        <label for="description">Description</label>
                                        <textarea name="description" id="description" cols="30" rows="10" class="summernote" placeholder="Description"></textarea>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="card mb-3">
                        <div class="card-body">
                            <h2 class="h4 mb-3">Media</h2>
                            <input class="form-control" type="file" name="image" id="image">
                        </div>
                    </div>
                    <div class="row" id="product-gallery"></div>
                    <p class="errors"></p>
                </div>
                <div class="col-md-4">
                    <div class="card">
                        <div class="card-body">
                            <h2 class="h4 mb-3">News category</h2>
                            <div class="mb-3">
                                <label for="category">Category</label>
                                <select name="category" id="category" class="form-control">
                                    <option value="">Select a News</option>
                                    @if ($categories->isNotEmpty())
                                        @foreach ($categories as $item)
                                            <option value="{{ $item->id}}">{{ $item->name }}</option>
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
                <button type="submit" class="btn btn-primary">Create</button>
                <a href="{{ route('news.index') }}" class="btn btn-outline-dark ml-3">Cancel</a>
            </div>
        </div>
    </form>
    <!-- /.card -->
</section>
<!-- /.content -->
@endsection

@section('costomJs')
<script>
    $("#title").change(function(){
        var element = $(this);
        $("button[type=submit]").prop('disabled', true);
        $.ajax({
            url: '{{ route("getSlug") }}',
            type: 'GET',
            data: {title: element.val()},
            dataType: 'json',
            success: function(response) {
                $("button[type=submit]").prop('disabled', false);
                if(response.status) {
                    $("#slug").val(response.slug);
                }
            }
        });
    });

    $("#newsForm").submit(function(event) {
        event.preventDefault();
        var formData = new FormData(this);
        $.ajax({
            url: '{{ route("news.store") }}',
            type: 'POST',
            data: formData,
            dataType: 'json',
            processData: false,
            contentType: false,
            success: function(response) {
                if(response.status) {
                    $(".error").removeClass('invalid-feedback').html('');
                    $("input, select").removeClass('is-invalid');
                    window.location.href = "{{ route('news.index') }}";
                } else {
                    var errors = response.errors;
                    $(".error").removeClass('invalid-feedback').html('');
                    $("input, select").removeClass('is-invalid');
                    $.each(errors, function(key, value) {
                        $('#' + key).addClass('is-invalid').siblings('p').addClass('invalid-feedback').html(value);
                    });
                }
            },
            error: function() {
                console.log("Something went wrong");
            }
        });
    });
</script>
@endsection
