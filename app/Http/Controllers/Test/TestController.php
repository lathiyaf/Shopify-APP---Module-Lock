<?php

namespace App\Http\Controllers\Test;

use App\Http\Controllers\Controller;
use App\Models\LineItem;
use App\Traits\GraphQLTrait;
use Illuminate\Http\Request;
use Osiset\BasicShopifyAPI\BasicShopifyAPI;
use Osiset\BasicShopifyAPI\Options;
use Osiset\BasicShopifyAPI\Session;

class TestController extends Controller
{
    use GraphQLTrait;
    public function index(Request $request){
        try{
            $shop = \Auth::user();
            $data = [];
            $endPoint = '/admin/api/' . env('SHOPIFY_API_VERSION') . '/orders.json';
            $parameter['fields'] = 'id,line_items';
            $result = $shop->api()->rest('GET', $endPoint, $parameter);
            if( !$result['errors'] ){
                $sh_orders = $result['body']->container['orders'];
                foreach ( $sh_orders as $key=>$val ){
                   $line_items = $val['line_items'];
                   if( !empty( $line_items ) ){
                       foreach( $line_items as $lkey=>$lval ){
                           $line_item = LineItem::where('id', $shop->id)->where('order_id', $val['id'])->where('variant_id', $val['id'])->first();

                           $endPoint = '/admin/api/' . env('SHOPIFY_API_VERSION') . '/products/'. $lval['product_id'] .'.json';
                           $parameter['fields'] = 'id,tags';
                           $result = $shop->api()->rest('GET', $endPoint, $parameter);
                           if( !$result['errors'] ){
                               $sh_product = $result['body']->container['product'];
                           }
                           $line_item = ( $line_item ) ? $line_item : new LineItem;
                           $line_item->user_id = $shop->id;
                           $line_item->order_id = $val['id'];
                           $line_item->variant_id = $lval['variant_id'];
                           $line_item->product_id = $lval['product_id'];
                           $line_item->title = $lval['title'];
                           $line_item->sku = $lval['sku'];
                           $line_item->tags = $sh_product['tags'];
                           $line_item->save();
                       }
                   }
                }
            }


                $resources = ['products', 'productVariants', 'collections'];

                foreach( $resources as $rk=>$rv ){
                    $query = $this->$rv($request->s);
                    $result = $this->request($query);

                    if( !$result['errors'] ){
                        $entity = $result['body']->container['data'][$rv]['edges'];
                    }
                    if( !empty( $entity ) ){
                        foreach ( $entity as $key=>$val ){
                            $p = $val['node'];
                            if( $rv == 'products' ){
                                $d['_resourceId'] = $p['legacyResourceId'];
                                $d['_resourceTitle'] = $p['title'];
                                $d['_resourceHandle'] = $p['handle'];
                                $d['_resourceImage'] = ( $p['featuredImage'] ) ? $p['featuredImage']['originalSrc'] : 'static_upload/no-image-box.png';
                                $d['_resourceDescription'] = 'A product, with the handle "' . $p['handle'] . '"';
                            }else if( $rv == 'productVariants' ){
                                $d['_resourceId'] = $p['legacyResourceId'];
                                $d['_resourceTitle'] = $p['title'];
                                $d['_resourceHandle'] = '';
                                $d['_resourceImage'] = 'static_upload/no-image-box.png';
                                $d['_resourceDescription'] = "This covers all matching variants, across all products";
                            }else if( $rv == 'collections' ){
                                $d['_resourceId'] = basename($p['id']);
                                $d['_resourceTitle'] = $p['title'];
                                $d['_resourceHandle'] = $p['handle'];
                                $d['_resourceImage'] = ( $p['image'] ) ? $p['image']['originalSrc'] : 'static_upload/no-image-box.png';
                                $d['_resourceDescription'] = 'A collection with the handle "'. $p['handle'] .'"';
                            }

                            array_push($data, $d);
                        }
                    }
                }

                dd($data);
        }catch (\Exception $e){
            dd($e);
        }
    }

    public function products($s){
        try{
                return '{
                          products(query: "title:*'.$s.'*", first: 250) {
                            edges {
                              node {
                                legacyResourceId
                                title
                                handle
                                featuredImage {
                                  originalSrc
                                }
                              }
                            }
                          }
                        }';
        }catch( \Exception $e ){
            dd($e);
        }
    }
    public function productVariants($s){
        try{
            return '{
                         productVariants(first: 250, query: "selectedOptions:value:\'*'. $s.'*\'") {
                            edges {
                                node {
                                        displayName
                                        legacyResourceId
                                        title
                                        image {
                                            src
                                        }
                                        selectedOptions {
                                          name
                                          value
                                        }
                                      }
                                    }
                                  }
                    }';
        }catch( \Exception $e ){
            dd($e);
        }
    }
    public function collections($s){
        try{
            return '{
                          collections(first: 250, query:"title:*'.$s.'*") {
                            edges {
                                node {
                                id
                                image {
                                  originalSrc
                                }
                                handle
                                title
                              }
                            }
                          }
                    }';
        }catch( \Exception $e ){
            dd($e);
        }
    }

    public function request($query){
        $shop = \Auth::user();
        $parameter = [];
        $options = new Options();
        $options->setVersion('2020-07');
        $api = new BasicShopifyAPI($options);
        $api->setSession(new Session(
            $shop->name, $shop->password));
        $result =  $api->graph($query, $parameter);
        return $result;
    }
}
