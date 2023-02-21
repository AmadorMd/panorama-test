@extends('layouts.app')
@section('content')
<div class="w-full bg-slate-400 py-2 text-center rounded-t-md">
    <h2 class="text-xl text-white font-bold uppercase tracking-wide">Create collection</h2>
</div>

<form class="mt-5 px-5" action="{{ route('store-collection') }}" method="POST" enctype="multipart/form-data">
    @csrf
    <div class="form-group">
        <label for="name">Collection title</label>
        <input class="form-input" type="text" name="title" value="{{ old('title') }}">
        @error('title')
        <span>{{ $errors->first('title') }}</span>
        @enderror

    </div>
    <div class="form-group">
        <label for="name">Description</label>
        <input id="drescription" type="hidden" name="drescription" value="{{ old('description') }}">
        <trix-editor input="drescription"></trix-editor>
        @error('drescription')
            <span>{{ $errors->first('drescription') }}</span>
        @enderror
    </div>
    <div class="form-group">
        <label for="name">Collection featured Image</label>
        <input class="form-input" type="file" name="featured_image" id="featured_image">
        <span class="text-slate-500 text-xs">* The image must has the dimensions 4472px by 4472px</span>
        @error('featured_image')
            <span>{{ $errors->first('featured_image') }}</span>
        @enderror
    </div>
    <div class="mt-5 text-right">
        <button class="btn btn-primary" type="submit">Create Collection</button>
    </div>
</form>
@endsection