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
       
        $request->validate([
            'title' => 'required|string|min:5',
            'product_type' => 'required|string|min:5',
            'vendor' => 'required|string|min:5',
            'tags' => 'required|string|min:5',
            'status' => 'required',
            'description' => 'required|string|min:5',
        ]);
        $collectionID = $request->collectionID;
        $variants = $this->setVariantProductQuery($request);
        $query = '
            productCreate(input: {
                title: "'.$request->title.'", 
                productType: "'.$request->product_type.'", 
                vendor: "'.$request->vendor.'", 
                collectionsToJoin: "'.$collectionID.'",
                tags: "'.$request->tags.'",
                status: '.$request->status.',
                descriptionHtml: "'.addslashes($request->description).'",
                variants: variants: ['.$variants.']
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
        if(isset($createProductResponse['data'])){
            $productId = $createProductResponse['data']['productCreate']['product']['id'];
            $publisProduct = $this->publish($productId);
        }else{
            dd($createProductResponse);
        }
        return redirect()->route('home');
    }
    public function editProduct($handle){
        $query = 'query{
            productByHandle(handle: "'.$handle.'"){
                id
                title
                descriptionHtml
                productType
                vendor
                tags
                status
                variants(first:5){
                    edges{
                        node{
                            price
                            title
                            compareAtPrice
                            inventoryQuantity
                        }
                    }
                }
            }
        }';
        $response = $this->graphqlSendRequest($query);
        if(isset($response['data'])){
            $product = $response['data']['productByHandle'];
            $variants = $product['variants']['edges'];
            return view('product.edit', compact('product', 'variants', 'handle'));
        }else{
            dd($response['errors']);
        }
        
    }
    public function setVariantProductQuery($request){
        
        if(isset($request->variant_title[0])){
            $strQuery = [];
            foreach($request->variant_title as $key=>$varianTitle){
                $compareAtPrice = isset($request->compare_at_price[$key])?isset($request->compare_at_price[$key]): 0;
                $strQuery[] = '{
                    title: "'.$request->variant_title[$key].'",
                    price: "'.$request->price[$key].'",
                    compareAtPrice: "'.$compareAtPrice.'",
                    inventoryItem: {cost: "'.$request->price[$key].'", tracked: true},
                    taxable: false,
                    inventoryQuantities: {availableQuantity: '.$request->inventory_quantities[$key].', locationId: "gid://shopify/Location/11508449322"}
                    }';
            }
            return implode(",", $strQuery);
        }
    }
    public function updateProduct(Request $request, $handle){
        $request->validate([
            'title' => 'required|string|min:5',
            'product_type' => 'required|string|min:5',
            'vendor' => 'required|string|min:5',
            'tags' => 'required|string|min:5',
            'status' => 'required',
            'description' => 'required|string|min:5',
        ]);
        $id = $request->id;
        $variants = $this->setVariantProductQuery($request);
        $query = 'productUpdate(input: { 
            id: "'.$id.'",
            title: "'.$request->title.'", 
            productType: "'.$request->product_type.'", 
            vendor: "'.$request->vendor.'",
            tags: "'.$request->tags.'",
            status: '.$request->status.',
            descriptionHtml: "'.addslashes($request->description).'",
            variants: ['.$variants.']
        }){
            product {
                id
            }

        }';
        $queryMutation = $this->mutation($query);
        $response = $this->graphqlSendRequest($queryMutation);
        if(isset($response['data'])){
            return redirect()->back();
        }else{
            dd($response['errors']);
        }
        
    }
    public function deleteProduct(Request $request){
        $id = $request->id;
        $query = '
            productDelete(input: {id: "'.$id.'"}){
                deletedProductId
                userErrors {
                    field
                    message
                  }
            }
        ';
        $mutationQuery = $this->mutation($query);
        $response = $this->graphqlSendRequest($mutationQuery);
        if(empty($response['data']['productDelete']['userErrors'])){
            return redirect()->back();
        }else{
            return $response['data']['productDelete']['userErrors'];
        }
    }
    
}
