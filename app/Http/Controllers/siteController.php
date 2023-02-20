<?php

namespace App\Http\Controllers;

use GuzzleHttp\Psr7\Query;
use Illuminate\Support\Facades\Http;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Storage;

class siteController extends Controller
{
    public function graphlqlSendRequest($query){
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
        $collections = $this->graphlqlSendRequest($query)['data']['collections']['edges'];
        return view('Home', compact('collections'));
    }
    public function createCollection(){
        return view('create-collection');
    }
    public function stagedUploadsCreate($fileSize, $filename, $httpMethod, $mimeType, $resource){
        $query = 'stagedUploadsCreate(input: [{
            fileSize: "'.$fileSize.'",
            filename: "'.$filename.'",
            httpMethod: '.$httpMethod.',
            mimeType: "'.$mimeType.'",
            resource: '.$resource.'
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
        }';
        return $this->mutation($query);
    }
    public function mutation($query){
        $mutation = 'mutation {
            '.$query.'
        }';
        return $mutation;
    }
    public function storeCollection(Request $request){
        
        $request->validate([
            'title' => 'required|string|min:5|max:255',
            'drescription' => 'required|string|min:5',
            'featured_image' => 'nullable|image|mimes:png,jpg|max:1500'
        ]);
        $temp = [];
        $temp[] = 'title: "'.$request->title.'"';
        if(isset($request->drescription) && $request->drescription !== null)
            $temp[] = ' descriptionHtml: "'.$request->drescription.'"';
        if($request->hasFile('featured_image')) {
            $tempImage = $request->file('featured_image');
            $imageSize = $tempImage->getSize();
            $imageName = $tempImage->getClientOriginalName();
            $imageMime = $tempImage->getClientMimeType();
            $imageStage = $this->stagedUploadsCreate($imageSize, $imageName, 'POST', $imageMime, 'COLLECTION_IMAGE');
            
            $imageStageResponse = $this->graphlqlSendRequest($imageStage);
            $target = $imageStageResponse['data']['stagedUploadsCreate']['stagedTargets'][0];
            $uploadUrl = $target['url'];
            $parameters = $target['parameters'];

            foreach($parameters as $param){
                $postParameters[$param['name']] = $param['value'];
            }
            
            $mergePostParameters = array_merge($postParameters, ['file'=> file_get_contents($tempImage)]);
            //Upload Stage Image to Shopify
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
        
        $createCollectionQuery = 'collectionCreate (input: {'.implode(",", $temp).'}) { 
                collection { id title descriptionHtml handle sortOrder image { src } }
                userErrors { field message }
            }';
        
        $collectionMutationQuery = $this->mutation($createCollectionQuery);
        
        $responseCollectionMutation = $this->graphlqlSendRequest($collectionMutationQuery);
    
        $recentCollectionId = $responseCollectionMutation['data']['collectionCreate']['collection']['id'];
        
        $publishCollectionQuery = 'publishablePublish( 
            id: "'.$recentCollectionId.'", 
            input: { publicationId: "gid://shopify/Publication/13775306794" })
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
          }';
        $publishCollectionMutation = $this->mutation($publishCollectionQuery);
        $publishCollectionResponse = $this->graphlqlSendRequest($publishCollectionMutation);
        $userErrors = $publishCollectionResponse['data']['publishablePublish']['userErrors'];
        if(empty($userErrors)){
            return redirect()->route('home');
        }
    }
    public function updateCollection(){
        $updateCollectionMutation = 'mutation {
            collectionUpdate(input: {
                id: "",
                image: {src: ""}
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
        $response = $this->graphlqlSendRequest($query);
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
        $query = $this->mutation($vars);
       
        $this->graphlqlSendRequest($query);  
        return redirect()->back();
    
    }
}
