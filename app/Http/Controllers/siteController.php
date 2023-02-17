<?php

namespace App\Http\Controllers;

use GuzzleHttp\Psr7\Query;
use Illuminate\Support\Facades\Http;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

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
        
        $request->validate([
            'title' => 'required|string|min:5|max:255',
            'drescription' => 'nullable|string|min:5',
            'featured_image' => 'nullable|image|mimes:png,jpg|max:1500'
        ]);
        $temp = [];
        $temp[] = 
          'title: "'.$request->title.'"';
        if(isset($request->drescription) && $request->drescription !== null)
            $temp[] = ' descriptionHtml: "'.$request->drescription.'"';
        if($request->hasFile('featured_image')) {
            $tempImage = $request->file('featured_image');
            $imageStage = 'mutation {stagedUploadsCreate(input: [{
                fileSize: "'.$tempImage->getSize().'",
                filename: "'.$tempImage->getClientOriginalName().'",
                httpMethod: POST,
                mimeType: "'.$tempImage->getClientMimeType().'",
                resource: IMAGE
            }]){
                stagedTargets {
                    url
                    resourceUrl
                    parameters {
                      name
                      value
                    }
                }
                userErrors {
                    field
                    message
                  }
        }}';
            $imageStageResponse = Http::withHeaders([
                'X-Shopify-Access-Token' => env('SHOPIFY_API_KEY'),
            ])->post(env('GRAPHQL_URL'), [
                'query' => $imageStage
            ])->json();
            $imageStageUrl = $imageStageResponse['data']['stagedUploadsCreate']['stagedTargets'][0]['resourceUrl'];
            
            $temp[] = 'image: {altText: "'.$request->title.'", src: "'.$imageStageUrl.'"}';
        }
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
      $response = Http::withHeaders([
            'X-Shopify-Access-Token' => env('SHOPIFY_API_KEY'),
        ])->post(env('GRAPHQL_URL'), [
            'query' => $query
        ])->json();
        dd($response);  
        //$id = "gid://shopify/Collection/272494264362"; //temporal
        $id = $response['data']['collectionCreate']['collection']['id'];
        
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
