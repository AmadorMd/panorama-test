@extends('layouts.app')
@section('content')
<h2>Create collection</h2>
<form action="{{ route('store-collection') }}" method="POST" enctype="multipart/form-data">
    @csrf
    <div>
        <label for="name">Collection name</label>
        <input type="text" name="title">
    </div>
    <div>
        <label for="name">Description</label>
        <input id="drescription" type="hidden" name="drescription">
        <trix-editor input="drescription"></trix-editor>
    </div>
    <div>
        <label for="name">Collection featured Image</label>
        <input type="file" name="featured_image" id="featured_image">
    </div>
    <div>
        <button type="submit">Create Collection</button>
    </div>
</form>
@endsection