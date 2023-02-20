@extends('layouts.app')
@section('content')
<h2>Create collection</h2>
<form action="{{ route('store-collection') }}" method="POST" enctype="multipart/form-data">
    @csrf
    <div>
        <label for="name">Collection title</label>
        <input type="text" name="title" value="{{ old('title') }}">
        @error('title')
        <span>{{ $errors->first('title') }}</span>
        @enderror

    </div>
    <div>
        <label for="name">Description</label>
        <input id="drescription" type="hidden" name="drescription" value="{{ old('description') }}">
        <trix-editor input="drescription"></trix-editor>
        @error('drescription')
            <span>{{ $errors->first('drescription') }}</span>
        @enderror
    </div>
    <div>
        <label for="name">Collection featured Image</label>
        <input type="file" name="featured_image" id="featured_image">
        <span>* The image must has the dimensions 4472px by 4472px</span>
        @error('featured_image')
            <span>{{ $errors->first('featured_image') }}</span>
        @enderror
    </div>
    <div>
        <button type="submit">Create Collection</button>
    </div>
</form>
@endsection