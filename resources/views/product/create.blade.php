@extends('layouts.app')
@section('content')
<h2>Create Product</h2>
<form action="{{ route('store-product') }}" method="POST" enctype="multipart/form-data">
    @csrf
    <input type="hidden" name="collectionID" value="{{ $collectionID }}">
    <div>
        <label for="title">Product title</label>
        <input type="text" name="title" value="{{ old('title') }}">
        @error('title')
        <span>{{ $errors->first('title') }}</span>
        @enderror

    </div>
    <div>
        <label for="product_type">Product Type</label>
        <input type="text" name="product_type" value="{{ old('product_type') }}">
        @error('product_type')
        <span>{{ $errors->first('product_type') }}</span>
        @enderror

    </div>
    <div>
        <label for="vendor">Vendor</label>
        <input type="text" name="vendor" value="{{ old('vendor') }}">
        @error('vendor')
        <span>{{ $errors->first('vendor') }}</span>
        @enderror

    </div>
    <div>
        <label for="tags">Tags</label>
        <input type="text" name="tags" value="{{ old('tags') }}">
        @error('tags')
        <span>{{ $errors->first('tags') }}</span>
        @enderror

    </div>
    <div>
        <label for="status">Status</label>
        <select name="status" id="status">
            <option value="ACTIVE">Active</option>
            <option value="ARCHIVED">Archived</option>
            <option value="DRAFT">Draft</option>
        </select>
        @error('status')
        <span>{{ $errors->first('status') }}</span>
        @enderror

    </div>
    <div>
        <label for="name">Description</label>
        <input id="description" type="hidden" name="description" value="{{ old('description') }}">
        <trix-editor input="description"></trix-editor>
        @error('description')
            <span>{{ $errors->first('description') }}</span>
        @enderror
    </div>
    <div><h3>Variant details</h3></div>
    <div>
        <label for="variant_title">Title</label>
        <input type="text" name="variant_title[]" value="{{ old('variant_title') }}">
        @error('variant_title')
        <span>{{ $errors->first('variant_title') }}</span>
        @enderror

    </div>
    <div>
        <label for="price">Price</label>
        <input type="text" name="price[]" value="{{ old('price') }}">
        @error('price')
        <span>{{ $errors->first('price') }}</span>
        @enderror

    </div>
    <div>
        <label for="compare_at_price">Compare at price</label>
        <input type="text" name="compare_at_price[]" value="{{ old('compare_at_price') }}">
        @error('compare_at_price')
        <span>{{ $errors->first('compare_at_price') }}</span>
        @enderror

    </div>
    <div>
        <label for="inventory_quantities">Inventory quantities</label>
        <input type="number" name="inventory_quantities[]" value="{{ old('inventory_quantities') }}">
        @error('inventory_quantities')
        <span>{{ $errors->first('inventory_quantities') }}</span>
        @enderror

    </div>
    <div>
        <button type="submit">Create Product</button>
    </div>
</form>
@endsection