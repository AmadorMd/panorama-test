<?php

namespace App\Http\Controllers;
use Illuminate\Support\Facades\Http;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;

class Controller extends BaseController
{
    use AuthorizesRequests, ValidatesRequests;

    public function graphqlSendRequest($query){
        return Http::withHeaders([
            'X-Shopify-Access-Token' => env('SHOPIFY_API_KEY'),
        ])->post(env('GRAPHQL_URL'), [
            'query' => $query,
        ])->json();
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
    public function publish($id){
        $publishQuery = 'publishablePublish( 
            id: "'.$id.'", 
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
        $publisMutation = $this->mutation($publishQuery);
        return $this->graphqlSendRequest($publisMutation);
    }
    public function mutation($query){
        $mutation = 'mutation {
            '.$query.'
        }';
        return $mutation;
    }
}
