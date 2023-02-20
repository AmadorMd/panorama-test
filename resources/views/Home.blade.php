@extends('layouts.app')
@section('content')
<h1>Panorama Shopify Admin</h1>
    <h2>Collections</h2>
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
                <td>{{ $loop->index + 1 }}</td>
                <td>{{ $collection['node']['id'] }}</td>
                <td>{{ $collection['node']['title'] }}</td>
                <td>{{ $collection['node']['productsCount'] }}</td>
                <td>
                    <a target="_blank" href="{{ env('SHOP_URL')."/collections/".$collection['node']['handle'] }}">
                        view collection in store
                    </a>
                </td>
                <td>
                    <a href="{{ route('show-collection', ['handle' => $collection['node']['handle']]) }}">
                        View details
                    </a>
                    
                    
                    <form action="{{ route('delete-collection') }}" method="POST">
                        @csrf
                        <input type="hidden" name="id" value="{{ $collection['node']['id'] }}">
                        <button type="submit">Delete Collection</button>
                    </form>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
@endsection