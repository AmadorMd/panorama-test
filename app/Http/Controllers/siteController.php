<?php

namespace App\Http\Controllers;

use GuzzleHttp\Psr7\Query;
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
        collections(first: 15) {
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
    public function storeCollection(Request $request){
        $temp = [];
        $temp[] = 
          'title: "'.$request['title'].'"';
        if(isset($request['drescription']) && $request['drescription'] !== null)
            $temp[] = ' descriptionHtml: "'.$request['drescription'].'"';
        if(isset($request['product_type'])) 
            $temp[] = ' productType: "'.$request['product_type'].'"';
        if(isset($request['tags'])) 
            $temp[] = ' tags: ['.$this->returnTags($request['tags']).']';
        
            $temp[] = 'ruleSet: {
                appliedDisjunctively: true,
                rules: [
                  {
                    column: TITLE,
                    condition: "T-Shirts",
                    relation: CONTAINS
                  }
                ]
              }';
      
        $productCreateMutation = 'collectionCreate (input: {'.implode(",", $temp).'}) { 
                collection { id title descriptionHtml handle sortOrder }
                userErrors { field message }
            }';
        
        $query = 'mutation { '.$productCreateMutation.' }';

    //   $response = Http::withHeaders([
    //         'X-Shopify-Access-Token' => env('SHOPIFY_API_KEY'),
    //     ])->post(env('GRAPHQL_URL'), [
    //         'query' => $query
    //     ])->json();   
        $id = "gid://shopify/Collection/272494264362"; //temporal
        //$id = $response['data']['collectionCreate']['collection']['id'];
        
        $publisCollectionMutation = 'mutation { 
            publishablePublish(id: "'.$id.'", input: { publicationId: "gid://shopify/Publication/13775306794" })
            {
                publishable {
                  availablePublicationCount
                  publicationCount
                }
                shop {
                  publicationCount
                }
                userErrors {
                  field
                  message
                }
              }
        }';
        $getPublications = 'query {
            publications(first:10){
              edges{
                node{
                  id
                  name
                }
              }
            }}';
        $response = Http::withHeaders([
                'X-Shopify-Access-Token' => env('SHOPIFY_API_KEY'),
            ])->post(env('GRAPHQL_URL'), [
                'query' => $publisCollectionMutation
            ])->json(); 
        dd($response);
    }
    public function DeleteCollectionByID(Request $request){
        $id = $request->id;
        $vars = 'collectionDelete(input: {id: "'.$id.'"}) {
              deletedCollectionId
              shop {
                id
                name
              }
              userErrors {
                field
                message
              }
          }';
        $query = ' mutation { '.$vars.' }';
       
        $response = Http::withHeaders([
            'X-Shopify-Access-Token' => env('SHOPIFY_API_KEY'),
        ])->post(env('GRAPHQL_URL'), [
            'query' => $query
        ])->json();   
        dd($response);
    
    }
}
