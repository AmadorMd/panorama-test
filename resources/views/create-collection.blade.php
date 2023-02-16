@extends('layouts.app')
@section('content')
<h2>Create collection</h2>
<form action="{{ route('store-collection') }}" method="POST" enctype="multipart/form-data">
    @csrf
    <div>
        <label for="name">Collection name</label>
        <input type="text" name="name">
    </div>
    <div>
        <label for="name">Description</label>
        <trix-editor></trix-editor>
    </div>
    <div>
        <label for="name">Collection featured Image</label>
        <input type="file" name="featured_image" id="featured_image">
    </div>
</form>
@endsection