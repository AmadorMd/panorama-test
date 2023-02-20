<?php

namespace App\Http\Controllers;

use GuzzleHttp\Psr7\Query;
use Illuminate\Support\Facades\Http;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
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
            $imageOriginalPath = $tempImage->getRealPath()."/".$tempImage->getClientOriginalName();
            
            $imageStage = 'mutation {stagedUploadsCreate(input: [{
                fileSize: "'.$tempImage->getSize().'",
                filename: "'.$tempImage->getClientOriginalName().'",
                httpMethod: POST,
                mimeType: "'.$tempImage->getClientMimeType().'",
                resource: COLLECTION_IMAGE
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
            
            $target = $imageStageResponse['data']['stagedUploadsCreate']['stagedTargets'][0];
            $uploadUrl = $target['url'];
            $parameters = $target['parameters'];
            $resourceUrl = $target['resourceUrl'];
            foreach($parameters as $param){
                $postParameters[$param['name']] = $param['value'];
            }
            
            $mergePostParameters = array_merge($postParameters, ['file'=> file_get_contents($tempImage)]);
            //Upload Stage Image to Shopify
            //$result = Http::asForm()->post($uploadUrl, $mergePostParameters );
            $ch = curl_init();

            curl_setopt($ch, CURLOPT_URL, $uploadUrl);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
            curl_setopt($ch, CURLOPT_POST, 1);

            curl_setopt($ch, CURLOPT_POSTFIELDS, $mergePostParameters);

            $result = simplexml_load_string(curl_exec($ch));
            if (curl_errno($ch)) {
                dd("error");
            }
            curl_close($ch);
            $convertedResponse = json_decode(json_encode(($result)), true);
            $uploadedImageTempUrl = $convertedResponse['Location'];
            
            $temp[] = 'image: {altText: "'.$request->title.'", src: "'.$uploadedImageTempUrl.'"}';
        }
        // $temp[] = 'ruleSet: {
        //     appliedDisjunctively: true,
        //     rules: [
        //         {
        //         column: TITLE,
        //         condition: "T-Shirts",
        //         relation: CONTAINS
        //         }
        //     ]
        //     }';
        
        $productCreateMutation = 'collectionCreate (input: {'.implode(",", $temp).'}) { 
                collection { id title descriptionHtml handle sortOrder image { src } }
                userErrors { field message }
            }';
        
        $query = 'mutation { '.$productCreateMutation.' }';
        $response = Http::withHeaders([
            'X-Shopify-Access-Token' => env('SHOPIFY_API_KEY'),
        ])->post(env('GRAPHQL_URL'), [
            'query' => $query
        ])->json();
       
        //$id = "gid://shopify/Collection/272494264362"; //temporal
        $newCollectionId = $response['data']['collectionCreate']['collection']['id'];
        
        $publisCollectionMutation = 'mutation { 
            publishablePublish(id: "'.$newCollectionId.'", input: { publicationId: "gid://shopify/Publication/13775306794" })
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
        //update recente collection for add the image
        
        $updateCollectionMutation = 'mutation {
            collectionUpdate(input: {
                id: "'.$newCollectionId.'",
                image: {src: "'.$uploadedImageTempUrl.'"}
            }){
                collection {
                    id
                    title
                    image {
                        src
                        altText
                    }
                }
                userErrors {
                    field
                    message
                  }      
            }
        }';
        
        // $responseUpdateCollectionImage = Http::withHeaders([
        //     'X-Shopify-Access-Token' => env('SHOPIFY_API_KEY'),
        // ])->post(env('GRAPHQL_URL'), [
        //     'query' => $updateCollectionMutation
        // ])->json();
        //dd($responseUpdateCollectionImage);
    }
    public function showCollection(Request $request){
        $id = $request->id;
        $query = 'query {
            collection(id: "'.$id.'"){
                id
                title
                descriptionHtml
                handle
                updatedAt
                productsCount
                image {
                    src
                }
            }
        }';
        $response = Http::withHeaders([
            'X-Shopify-Access-Token' => env('SHOPIFY_API_KEY'),
        ])->post(env('GRAPHQL_URL'), [
            'query' => $query
        ])->json(); 
        $collection = $response['data']['collection'];
        
        return view('collection.details', compact('collection'));
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
       
        Http::withHeaders([
            'X-Shopify-Access-Token' => env('SHOPIFY_API_KEY'),
        ])->post(env('GRAPHQL_URL'), [
            'query' => $query
        ])->json();   
        
        return redirect()->back();
    
    }
}
