@extends('layouts.app')
@section('content')
<h2>Create Product</h2>
<form action="{{ route('store-product') }}" method="POST" enctype="multipart/form-data">
    @csrf
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
        <input id="drescription" type="hidden" name="drescription" value="{{ old('description') }}">
        <trix-editor input="drescription"></trix-editor>
        @error('drescription')
            <span>{{ $errors->first('drescription') }}</span>
        @enderror
    </div>
    <div><h3>Variant details</h3></div>
    <div>
        <label for="variant-title">Title</label>
        <input type="text" name="variant-title[]" value="{{ old('variant-title') }}">
        @error('variant-title')
        <span>{{ $errors->first('variant-title') }}</span>
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
        <label for="compare-at-price">Compare at price</label>
        <input type="text" name="compare-at-price[]" value="{{ old('compare-at-price') }}">
        @error('compare-at-price')
        <span>{{ $errors->first('compare-at-price') }}</span>
        @enderror

    </div>
    <div>
        <label for="inventory-quantities">Inventory quantities</label>
        <input type="number" name="inventory-quantities[]" value="{{ old('inventory-quantities') }}">
        @error('inventory-quantities')
        <span>{{ $errors->first('inventory-quantities') }}</span>
        @enderror

    </div>
    <div>
        <button type="submit">Create Product</button>
    </div>
</form>
@endsection