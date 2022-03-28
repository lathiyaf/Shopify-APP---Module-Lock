<?php

namespace App\Http\Controllers\Lock;

use App\Http\Controllers\Controller;
use App\Models\Lock;
use App\Models\Setting;
use Illuminate\Http\Request;
use Osiset\BasicShopifyAPI\BasicShopifyAPI;
use Osiset\BasicShopifyAPI\Options;
use Osiset\BasicShopifyAPI\Session;

class LockController extends Controller
{
    public function index()
    {
        try {
            $shop = \Auth::user();
            $lockResult = Lock::where('user_id', $shop->id)->get();
            $lock = $lockResult->map(function ($name) {
                return [
                    'id' => $name->id,
                    'image' => $name->image,
                    'resource' => $name->resource,
                    'resource_id' => $name->resource_id,
                    'subtitle' => $name->subtitle,
                    'title' => $name->title,
                    'handle' => $name->handle,
                    'is_checked' => false,
                    'key' => json_decode($name->key),
                    'is_enable' => $name->is_enable,
                    'guest_user_content' => $name->guest_user_content,
                    'access_denied_content' => $name->access_denied_content,
                    'is_sh_deleted' => $name->is_sh_deleted,
                ];
            });
            $disableLockResult = Lock::where('user_id', $shop->id)->where('is_enable', 0)->count();

            $res['lock'] = $lock;
            $res['disableLock'] = $disableLockResult;

            return response()->json(['data' => $res], 200);
        } catch (\Exception $e) {
            return response()->json(['data' => $e->getMessage()], 422);
        }
    }

    public function search(Request $request)
    {
        try {
            $shop = \Auth::user();

            $resources = ['products', 'productVariants', 'collections'];
            $data = [];
            foreach ($resources as $rk => $rv) {
                $query = $this->$rv($request->s);

                $result = $this->request($query);

                $entity = [];
                if (!$result['errors']) {
                    $entity = $result['body']->container['data'][$rv]['edges'];
                    if (!empty($entity)) {
                        foreach ($entity as $key => $val) {
                            $p = $val['node'];
                            if ($rv == 'products') {
                                $d['_resource'] = 'product';
                                $d['_resourceId'] = $p['legacyResourceId'];
                                $d['_resourceTitle'] = $p['title'];
                                $d['_resourceHandle'] = $p['handle'];
                                $d['_resourceImage'] = ($p['featuredImage']) ? $p['featuredImage']['originalSrc'] : env('NO_IMAGE_PATH');
                                $d['_resourceDescription'] = 'A product, with the handle "'.$p['handle'].'"';
                            } else {
                                if ($rv == 'productVariants') {
                                    $d['_resource'] = 'variant';
                                    $selectedOption = $p['selectedOptions'];
                                    $selectedOption = $selectedOption[0];
                                    $d['_resourceId'] = $p['legacyResourceId'];
                                    $d['_resourceTitle'] = 'All variants where "'.$selectedOption['name'].'" equals "'.$selectedOption['value'].'"';
                                    $d['_resourceHandle'] = '';
                                    $d['_resourceImage'] = env('NO_IMAGE_PATH');
                                    $d['_resourceDescription'] = "This covers all matching variants, across all products";
                                } else {
                                    if ($rv == 'collections') {
                                        $d['_resourceId'] = basename($p['id']);
                                        $d['_resource'] = 'collection';
                                        $d['_resourceTitle'] = $p['title'];
                                        $d['_resourceHandle'] = $p['handle'];
                                        $d['_resourceImage'] = ($p['image']) ? $p['image']['originalSrc'] : env('NO_IMAGE_PATH');
                                        $d['_resourceDescription'] = 'A collection with the handle "'.$p['handle'].'"';
                                    }
                                }
                            }
                            $d['id'] = '';
                            $d['_is_enable'] = 0;
                            array_push($data, $d);
                        }
                    }
                }
            }
            $page = $this->page($request->s);
            $data = array_merge($data, $page);
            return response()->json(['data' => $data], 200);
        } catch (\Exception $e) {
            return response()->json(['data' => $e->getMessage()], 422);
        }
    }

    public function products($s)
    {
        try {
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
        } catch (\Exception $e) {
            dd($e);
        }
    }

    public function productVariants($s)
    {
        try {
            return '{
                         productVariants(first: 250, query: "selectedOptions:value:\'*'.$s.'*\'") {
                            edges {
                                node {
                                    legacyResourceId
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
        } catch (\Exception $e) {
            dd($e);
        }
    }

    public function collections($s)
    {
        try {
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
        } catch (\Exception $e) {
            dd($e);
        }
    }

    public function editLock(Request $request)
    {
        try {
            $shop = \Auth::user();
            $id = $request->id;
            $lock = Lock::where('user_id', $shop->id)->where('id', $id)->first()->toArray();
            $lock['key'] = json_decode($lock['key']);
            $setting = Setting::where('user_id', $shop->id)->where('key', 'access_denied_content')->first()->toArray();
            $data['access_denied_content'] = $setting['value'];
            $data['lock'] = $lock;
            return response()->json(['data' => $data], 200);
        } catch (\Exception $e) {
            return response()->json(['data' => $e->getMessage()], 422);
        }
    }

    public function page($s)
    {
        try {
            $shop = \Auth::user();
            $endPoint = '/admin/api/'.env('SHOPIFY_API_VERSION').'/pages.json';
            $parameter = [];

            $data = [];
            $result = $shop->api()->rest('GET', $endPoint, $parameter);
            if (!$result['errors']) {
                $pages = $result['body']->container['pages'];
                foreach ($pages as $key => $val) {
                    if ((stripos($val['title'], $s) !== false) || (stripos($val['handle'], $s) !== false)) {
                        $d['id'] = '';
                        $d['_resourceId'] = $val['id'];
                        $d['_resource'] = 'page';
                        $d['_resourceTitle'] = $val['title'];
                        $d['_resourceHandle'] = $val['handle'];
                        $d['_resourceImage'] = env('NO_IMAGE_PATH');
                        $d['_resourceDescription'] = 'A page, with the handle "'.$val['handle'].'"';
                        $d['_is_enable'] = 0;
                        array_push($data, $d);
                    }
                }
            }
            return $data;
        } catch (\Exception $e) {
            dd($e);
        }
    }

    public function request($query)
    {
        $shop = \Auth::user();
        $parameter = [];
        $options = new Options();
        $options->setVersion('2020-07');
        $api = new BasicShopifyAPI($options);
        $api->setSession(new Session(
            $shop->name, $shop->password));
        return $api->graph($query, $parameter);
    }

    public function saveLock(Request $request)
    {
        try {
            $shop = \Auth::user();
            $data = $request->data;
            $setting = Setting::where('user_id', $shop->id)->where('key', 'access_denied_content')->first()->toArray();
            $lock = (($data['id'] == null) || ($data['id'] == '')) ? new Lock : Lock::where('user_id',
                $shop->id)->where('id', $data['id'])->first();

            if ($data['id'] == '') {
                $lock->user_id = $shop->id;
                $lock->resource_id = $data['_resourceId'];
                $lock->resource = $data['_resource'];
                $lock->image = $data['_resourceImage'];
                $lock->handle = $data['_resourceHandle'];
                $lock->title = $data['_resourceTitle'];
                $lock->subtitle = $data['_resourceDescription'];
                $lock->is_enable = $data['_is_enable'];
                $lock->key = json_encode([]);
            } else {
                $lock->is_enable = $data['is_enable'];
                $lock->key = json_encode($data['key']);

                $lock->access_denied_content = $data['access_denied_content'];
                $lock->guest_user_content = $data['guest_user_content'];
            }
            $lock->save();

            $res['id'] = $lock->id;
            $res['msg'] = 'Lock Saved!';

            $this->updateSnippet();
            return response()->json(['data' => $res], 200);
        } catch (\Exception $e) {
            return response()->json(['data' => $e->getMessage()], 422);
        }
    }

    public function changeLockStatus(Request $request)
    {
        try {
            $action = $request->action;
            $ids = $request->ids;
            foreach ($ids as $key => $val) {
                $lock = Lock::where('id', $val)->first();
                if ($action == 'delete') {
                    $lock->delete();
                } else {
                    $lock->is_enable = ($action == 'enable') ? 1 : 0;
                    $lock->save();
                }
            }
            $this->updateSnippet();
            return response()->json(['data' => 'Changes Saved!'], 200);
        } catch (\Exception $e) {
            return response()->json(['data' => $e->getMessage()], 422);
        }
    }

    public function updateSnippet()
    {
        try {
            $shop = \Auth::user();
            $theme_id = $this->getTheme();

            if ($theme_id) {
                $sv = $this->getSnipetValue();
                $value = <<<EOF
$sv
EOF;
                $parameter['asset']['key'] = 'snippets/module-lock.liquid';
                $parameter['asset']['value'] = $value;
                $asset = $shop->api()->rest('PUT', 'admin/themes/'.$theme_id.'/assets.json', $parameter);

                $this->updateThemeLiquid($theme_id, 'module-lock', $shop);
            }
        } catch (\Exception $e) {
            dd($e);
        }
    }

    public function getSnipetValue()
    {
        try {
            $shop = \Auth::user();
            $access_denied_content = Setting::where('user_id', $shop->id)->where('key', 'access_denied_content')->first();
            $guest_msg_content = Setting::where('user_id', $shop->id)->where('key', 'guest_user_content')->first();
            $locks = Lock::select('id', 'resource_id', 'resource', 'is_enable', 'handle', 'key',
                'access_denied_content', 'guest_user_content')->where('user_id', $shop->id)->where('is_enable', 1)->get();
            $value = '';


            foreach ($locks as $key => $val) {
                $guest_content = ( $val['guest_user_content'] == '') ? $guest_msg_content['value'] : $val['guest_user_content'];
                $access_content = ( $val['access_denied_content'] == '') ? $access_denied_content['value'] : $val['access_denied_content'];

                $path = '/' . $val->resource . 's/' . $val->handle;
                $value .= "{% if request.path == '$path' %}{% assign _locked = false %}";
                $value .= '{% assign _dc = "' . $access_content . '"%}';
                $keys = json_decode($val['key']);

                if( empty( $keys ) ){
                    $value .= "{% assign _locked = true %}";
                }else {
                    $value .= "{% assign _orcnd = '' %}{% assign _endcnd = '' %}{% assign _r = '' %}{% assign _pc = false %}";
                    $value .= "{% if customer %}{% assign _lI = false %}{% else %}{% assign _lI = true %}{% endif %}";

                    foreach ($keys as $mk => $mv) {
                        foreach ($mv as $sk => $sv) {
                            if ($sv == 'is signed in') {
                                $value .= "{% assign _r= _lI %}";
                                $value .= "{% if customer %}";
                                    $value .= '{% assign _dc = "' . $access_content . '"%}';
                                $value .= "{% else %}";
                                    $value .= '{% assign _dc = "' . $guest_content . '"%}';
                                $value .= "{% endif %}";
                            } else {
                                $srchkey = substr($sv, strpos($sv, '"') + 1);
                                $srchkey = str_replace('"', '', $srchkey);

                                $value .= "{% if customer.orders_count > 0 %}";
                                    $value .= "{% for order in customer.orders %}";
                                        $value .= "{% if order.cancelled %}";
                                            $value .= "{% assign _r = true %}";
                                        $value .= "{% else %}";
                                            $value .= "{% for line_item in order.line_items %}";
                                                $value .= "{% if line_item.product.tags contains '$srchkey' or line_item.sku contains '$srchkey' or  line_item.product.title contains '$srchkey' %}";
                                                    $value .= "{% assign _r = false %}";
                                                    $value .= "{% assign _pc = true %}";
                                                    $value .= "{% break %}";
                                                $value .= "{% else %}";
                                                    $value .= "{% assign _r = true %}";
                                                    $value .= "{% assign _pc = false %}";
                                                $value .= "{% endif %}";
                                            $value .= "{% endfor %}";
                                                $value .= "{% if _pc == true %}";
                                                    $value .= "{% break %}";
                                                $value .= "{% endif %}";
                                        $value .= "{% endif %}";
                                    $value .= "{% endfor %}";
                                $value .= "{% else %}";
                                        $value .= "{% assign _r = true %}";
//                                    $value .= "{% if _r == false %}{% assign _r= false %}{% else %}{% assign _r= true %}{% endif %}";
                                $value .= "{% endif %}";

                            }
                            $operator = ( $sk != 0 ) ? ' and ' : '';
                            if( $operator != '' ) {
                                $value .= "{% if _endcnd == false and _r == false %}";
                                    $value .= "{% assign _endcnd = false %}";
                                $value .= "{% elsif _endcnd == false and _r == true %}";
                                     $value .= "{% assign _endcnd = true %}";
                                $value .= "{% elsif  _endcnd == true and _r == false %}";
                                    $value .= "{% assign _endcnd = true %}";
                                $value .= "{% else %}";
                                    $value .= "{% if _endcnd and _r %}";
                                        $value .= "{% assign _endcnd = true %}";
                                    $value .= "{% else %}";
                                        $value .= "{% assign _endcnd = false %}";
                                    $value .= "{% endif %}";
                                $value .= "{% endif %}";
                            }else{
                                $value .= "{% assign _endcnd = _r %}";
                            }
                        }
                        $operator = ( $mk != 0 ) ? ' or ' : '';
                        if( $operator != '' ) {
//                            $value .= "{% if _orcnd == '' %}";
//                                $value .= "{% assign _orcnd = _endcnd %}";
//                            $value .= "{% endif %}";
                            $value .= "{% if _orcnd == true and _r == true %}";
                                $value .= "{% assign _orcnd = true %}";
                            $value .= "{% elsif _orcnd == false and _r == true %}";
                                $value .= "{% assign _orcnd = false %}";
                            $value .= "{% elsif _orcnd == true and _r == false %}";
                                $value .= "{% assign _orcnd = false %}";
                            $value .= "{% elsif _orcnd == false and _r == false %}";
                                $value .= "{% assign _orcnd = false %}";
                            $value .= "{% else %}";
                                $value .= "{% if _orcnd or _r %}";
                                    $value .= "{% assign _orcnd = true %}";
                                $value .= "{% else %}";
                                    $value .= "{% assign _orcnd = false %}";
                                $value .= "{% endif %}";
                            $value .= "{% endif %}";
                        }else{
                            $value .= "{% assign _orcnd = _endcnd %}";
                        }
                    }
                    $value .= "{% assign _locked = _orcnd %}";
                }
                    $value .= "{% if _locked == true %}";
                         $value .= "{% assign content_for_layout = _dc %}";
                    $value .= "{% endif %}";
                $value .= "{% endif %}";
            }
            return $value;
        } catch (\Exception $e) {
            dd($e);
        }
    }

    public function updateThemeLiquid($theme_id, $snippet_name, $shop)
    {
        try {
            \Log::info('-----------------------updateThemeLiquid-----------------------');
            $lock_cnt = Lock::where('user_id', $shop->id)->where('is_enable', 1)->count();

            $asset = $shop->api()->rest('GET', 'admin/themes/'.$theme_id.'/assets.json',
                ["asset[key]" => 'layout/theme.liquid']);
            if (@$asset['body']->container['asset']) {
                $asset = $asset['body']->container['asset']['value'];

                if( $lock_cnt > 0 ){
                    if (strpos($asset, "{% include '$snippet_name' %}") === false ) {
                        $asset = "{% include '$snippet_name' %}".$asset;
                    }
                }else{
                    $asset = str_replace("{% include '$snippet_name' %}", "", $asset);
                }
                $parameter['asset']['key'] = 'layout/theme.liquid';
                $parameter['asset']['value'] = $asset;
                $result = $shop->api()->rest('PUT', 'admin/themes/'.$theme_id.'/assets.json', $parameter);
            }
        } catch (\Exception $e) {
            \Log::info('------------ERROR :: updateThemeLiquid--------------');
            \Log::info(json_encode($e));
        }

    }

    public function getTheme()
    {
        try {
            $shop = \Auth::user();
            $parameter['role'] = 'main';
            $result = $shop->api()->rest('GET', '/admin/api/'.env('SHOPIFY_API_VERSION').'/themes.json', $parameter);
//            \Log::info($result);

            if (!$result['errors']) {
                return $result['body']->container['themes'][0]['id'];
            } else {
                return false;
            }
        } catch (\Exception $e) {
            dd($e);
        }
    }
}
