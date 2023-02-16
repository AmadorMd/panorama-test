@extends('layouts.app')
@section('content')
<h1>Administrador Panorama</h1>
    <h2>Shopify Collections</h2>
    <a href="{{ route('create-collection') }}">Create a Collection</a>
    <table>
        <thead>
            <tr>
                <td>ID</td>
                <td>Title</td>
                <td>Products Count</td>
                <td>Shop url</td>
                <td>Actions</td>
            </tr>
        </thead>
        <tbody>
            @foreach ($collections as $collection)
           
            <tr>
                <td>{{ $collection['node']['id'] }}</td>
                <td>{{ $collection['node']['title'] }}</td>
                <td>{{ $collection['node']['productsCount'] }}</td>
                <td>
                    <a target="_blank" href="{{ env('SHOP_URL')."/collections/".$collection['node']['handle'] }}">
                        view collection in store
                    </a>
                </td>
                <td>
                    <button>View details</button>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
@endsection