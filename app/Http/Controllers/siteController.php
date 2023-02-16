<?php

namespace App\Http\Controllers;
use Illuminate\Support\Facades\Http;

use Illuminate\Http\Request;

class siteController extends Controller
{
    public function requestShopifyData($query){
        return Http::withHeaders([
            'X-Shopify-Access-Token' => env('SHOPIFY_API_KEY'),
        ])->post(env('GRAPHQL_URL'), [
            'query' => $query,
        ])->json();
    }
    public function index(){
        $query = <<<QUERY
        query {
        collections(first: 5) {
            edges {
            node {
                id
                title
                handle
                updatedAt
                productsCount
                sortOrder
            }
            }
        }
        }
        QUERY;
        $collections = $this->requestShopifyData($query)['data']['collections']['edges'];
        return view('Home', compact('collections'));
    }
    public function createCollection(){
        return view('create-collection');
    }
}
