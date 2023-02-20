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
                <td>ID</td>
                <td>Title</td>
                <td>Shop url</td>
                <td>Actions</td>
            </tr>
        </thead>
        <tbody>
            
        </tbody>
    </table>
    <a href="{{ route('home') }}">All collections</a>
@endsection