@extends('layouts.app')
@section('content')
<h2>Editing Product: {{ $product['title'] }}</h2>
<form action="{{ route('update-product', ['handle' => $handle]) }}" method="POST" enctype="multipart/form-data">
    @csrf
    @method('PUT')
    <input type="hidden" name="id" value="{{ $product['id'] }}">
    <div>
        <label for="title">Product title</label>
        <input type="text" name="title" value="{{ old('title', $product['title']) }}">
        @error('title')
        <span>{{ $errors->first('title') }}</span>
        @enderror

    </div>
    <div>
        <label for="product_type">Product Type</label>
        <input type="text" name="product_type" value="{{ old('product_type', $product['productType']) }}">
        @error('product_type')
        <span>{{ $errors->first('product_type') }}</span>
        @enderror

    </div>
    <div>
        <label for="vendor">Vendor</label>
        <input type="text" name="vendor" value="{{ old('vendor', $product['vendor']) }}">
        @error('vendor')
        <span>{{ $errors->first('vendor') }}</span>
        @enderror

    </div>
    <div>
        <label for="tags">Tags</label>
        <input type="text" name="tags" value="{{ old('tags', implode(',', $product['tags'])) }}">
        @error('tags')
        <span>{{ $errors->first('tags') }}</span>
        @enderror

    </div>
    <div>
        <label for="status">Status</label>
        <select name="status" id="status">
            <option value="ACTIVE" {{ $product['status'] === "ACTIVE"?'selected':'' }}>Active</option>
            <option value="ARCHIVED" {{ $product['status'] === "ARCHIVED"?'selected':'' }}>Archived</option>
            <option value="DRAFT" {{ $product['status'] === "DRAFT"?'selected':'' }}>Draft</option>
        </select>
        @error('status')
        <span>{{ $errors->first('status') }}</span>
        @enderror

    </div>
    <div>
        <label for="name">Description</label>
        <input id="description" type="hidden" name="description" value="{{ old('description', $product['descriptionHtml']) }}">
        <trix-editor input="description"></trix-editor>
        @error('description')
            <span>{{ $errors->first('description') }}</span>
        @enderror
    </div>
    <div><h3>Variant details</h3></div>
    @foreach ($variants as $variant)
        <div>
            <label for="variant_title">Title</label>
            <input type="text" name="variant_title[]" value="{{ old('variant_title', $variant['node']['title']) }}">
            @error('variant_title')
            <span>{{ $errors->first('variant_title') }}</span>
            @enderror
    
        </div>
        <div>
            <label for="price">Price</label>
            <input type="text" name="price[]" value="{{ old('price', $variant['node']['price']) }}">
            @error('price')
            <span>{{ $errors->first('price') }}</span>
            @enderror
    
        </div>
        <div>
            <label for="compare_at_price">Compare at price</label>
            <input type="text" name="compare_at_price[]" value="{{ old('compare_at_price', $variant['node']['compareAtPrice']) }}">
            @error('compare_at_price')
            <span>{{ $errors->first('compare_at_price') }}</span>
            @enderror
    
        </div>
        <div>
            <label for="inventory_quantities">Inventory quantities</label>
            <input type="number" name="inventory_quantities[]" value="{{ old('inventory_quantities', $variant['node']['inventoryQuantity']) }}">
            @error('inventory_quantities')
            <span>{{ $errors->first('inventory_quantities') }}</span>
            @enderror
    
        </div>
    @endforeach
    <div>
        <button type="submit">update Product</button>
    </div>
</form>
@endsection