@extends('layouts.app')
@section('content')
    <h1>Editing collection</h1>
    <h2>Title: {{ $collection['title'] }}</h2>
    <p>
        {!! $collection['descriptionHtml'] !!}
    </p>
    <a target="_blank" href="{{ env('SHOP_URL')."/collections/".$collection['handle'] }}">
        view collection in store
    </a>
    <p>last update: {{ $collection['updatedAt'] }}</p>
    <p>Total Products: {{ $collection['productsCount'] }}</p>
    @isset($collection['image'])
    <img width="250px" src="{{ $collection['image']['src'] }}" alt="">
    @endisset
    <h3>Products</h3>
    <a href="{{ route('create-product', ['collectionHandle' => $collection['handle']]) }}">Add new product</a>
    <table>
        <thead>
            <tr>
                <th>Title</th>
                <th>Product Type</th>
                <th>Total Inventory</th>
                <th>Tags</th>
                <th>Vendor</th>
                <th>Status</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($products as $product)
                <tr>
                    <td>{{ $product['node']['title'] }}</td>
                    <td>{{ $product['node']['productType'] }}</td>
                    <td>{{ $product['node']['totalInventory'] }}</td>
                    <td>{{ implode(",", $product['node']['tags']) }}</td>
                    <td>{{ $product['node']['vendor'] }}</td>
                    <td>{{ $product['node']['status'] }}</td>
                    <td>
                        <a target="_blank" href="{{ env('SHOP_URL')."/products".$product['node']['handle'] }}">view on store</a>
                        <a href="{{ route('edit-product', ['handle' => $product['node']['handle']]) }}">Edit Product</a>
                        
                        <form action="{{ route('delete-product') }}" method="POST">
                            @csrf
                            <input type="hidden" name="id" value="{{ $product['node']['id'] }}">
                            <button>Delete Product</button>
                        </form>
                        
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
    <a href="{{ route('home') }}">All collections</a>
@endsection