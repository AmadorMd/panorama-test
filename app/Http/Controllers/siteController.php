<?php

namespace App\Http\Controllers;
use Illuminate\Support\Facades\Http;
use Illuminate\Http\Request;

class siteController extends Controller
{
    
    public function index(){
        $query = '
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
        }';
        $collections = $this->graphqlSendRequest($query)['data']['collections']['edges'];
        return view('Home', compact('collections'));
    }
    public function createCollection(){
        return view('create-collection');
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
            
            $imageStageResponse = $this->graphqlSendRequest($imageStage);
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
        
        $responseCollectionMutation = $this->graphqlSendRequest($collectionMutationQuery);
    
        $recentCollectionId = $responseCollectionMutation['data']['collectionCreate']['collection']['id'];
        
        $publishCollectionResponse = $this->publish($recentCollectionId);
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
    public function showCollection($handle){
        $query = '
        {
            collectionByHandle(handle: "'.$handle.'") {
              description(truncateAt: 300)
              descriptionHtml
              handle
              updatedAt
              productsCount
              image {
                altText
                src
                transformedSrc(maxHeight: 1000, maxWidth: 2048, crop: CENTER)
              }
              title
              products(first: 24) {
                pageInfo {
                  hasNextPage
                }
                edges {
                  cursor
                  node {
                    handle
                    id
                    productType
                    tags
                    title
                    totalInventory
                    vendor
                  }
                }
              }
            }
          }';
        $response = $this->graphqlSendRequest($query);
        
        $collection = $response['data']['collectionByHandle'];
        
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
       
        $this->graphqlSendRequest($query);  
        return redirect()->back();
    
    }
}
