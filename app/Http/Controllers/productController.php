<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class productController extends Controller
{
    public function getCollectionByHandle($handle){
        $query = '
            query {
                collectionByHandle(handle: "'.$handle.'"){
                    id
                }
            }
        ';
        $response = $this->graphqlSendRequest($query);
        return $response['data']['collectionByHandle']['id'];
    }
    public function createView($collectionHandle){
        $collectionID = $this->getCollectionByHandle($collectionHandle);
        return view('product.create', compact('collectionID'));
    }
    public function createProduct(Request $request){
        dd($request->all());
       $query = '
        productCreate(input: {
            title: "Sweet new product", productType: "Snowboard", vendor: "JadedPixel", collectionsToJoin: "'.$collectionID.'"
        }){
            product {
                id
            }
            userErrors {
                field
                message
            }
        }
       ';
       $mutationQuery = $this->mutation($query);
       $createProductResponse = $this->graphqlSendRequest($mutationQuery);
       $productId = $createProductResponse['data']['productCreate']['product']['id'];
       $publisProduct = $this->publish($productId);
       dd($publisProduct);
    }
    
}
