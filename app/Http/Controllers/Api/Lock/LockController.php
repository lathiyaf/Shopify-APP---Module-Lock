<?php

namespace App\Http\Controllers\Api\Lock;

use App\Http\Controllers\Controller;
use App\Models\LineItem;
use App\Models\Lock;
use App\Models\Setting;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LockController extends Controller
{
    public function checkLock(Request $request){
        try{
            $shopDomain = $request->shop;
            $template = $request->template;
            $resource = $request->resource;
            $is_logged_in = $request->is_logged_in;

            $shop = User::where('name', $shopDomain)->first();
            $access_denied_content = Setting::where('user_id', $shop->id)->where('key', 'access_denied_content')->first();
            $guest_msg_content = Setting::where('user_id', $shop->id)->where('key', 'guest_user_content')->first();

            $lockData = $this->getLock($resource, $template, $shop->id);
            $lock = $lockData['lock'];

            if( !empty($lock) ){
                $condition = '';
                if( $lockData['template'] == 'variant' ){
                    foreach ( $lock as $lkey=>$lval ){
                        $keys = json_decode($lval['key']);

                        if( !empty($keys) ){
                            $resDataPre[] = $this->getCondition($keys, $lval, $guest_msg_content, $is_logged_in, 'variant');
                        }else{
                            $resDataPre[$lkey]['resource'] = $lval['resource_id'];
                            $resDataPre[$lkey]['is_hide'] = true;
                        }
                    }
                    $resData['template'] = 'variant';
                    $resData['data'] = $resDataPre;
                }else{
                    $keys = json_decode($lock['key']);

                    $resData = $this->getCondition($keys, $lock, $guest_msg_content, $access_denied_content, $is_logged_in, $template, $shop->id);
                }

            }else{
                $resData['is_hide'] = false;
                $resData['message'] = '';
                $resData['template'] = $template;
            }

            return Response()->json(['data' => $resData], 200);
        }catch(\Exception $e){
            return Response()->json(['data' => $e], 422);
        }
    }

    public function getLock($resource, $template, $shop_id){
        try{
            $lockData['template'] = $template;
            $lockData['lock'] = [];
            $shop = User::where('id', $shop_id)->first();
            if( $template == 'product' ) {
                $lock = Lock::where('user_id', $shop->id)->where('resource', $template)->where('is_enable', 1)->where('handle', $resource['handle'])->first();

                if( !$lock ){
                    $variants = $resource['variants'];
                    foreach($variants as $key=>$val){
                        $variant_ids[] = $val['id'];
                    }
                    $lock = Lock::where('user_id', $shop->id)->where('resource', 'variant')->where('is_enable', 1)->whereIn('resource_id', $variant_ids)->get()->toArray();

                    $lockData['template'] = 'variant';
                }
            }elseif ( $template == 'page' ){
                $lock = Lock::where('user_id', $shop->id)->where('resource', $template)->where('is_enable', 1)->where('handle', $resource)->first();
            }

            $lockData['lock'] = (@$lock) ? $lock : '';
            return $lockData;
        }catch(\Exception $e){
            dd($e);
            return false;
        }
    }

    public function getCondition($keys, $lock, $guest_msg_content,$access_denied_content, $is_logged_in, $template, $shop_id){
        try{
            $shop = User::where('id', $shop_id)->first();
            $condition = '';
            $data['is_hide'] = false;
            $data['message'] = ( $lock['guest_user_content'] == '') ? $guest_msg_content['value'] : $lock['guest_user_content'];
            $data['template'] = $template;
            $data['resource_id'] = $lock['resource_id'];

            if( empty( $keys ) ){
                $data['is_hide'] = true;

            } else{
                $msg = '';
                $orcondition = '';
                $endcondition = '';
                foreach( $keys as $k=>$val ){
                    $condition .= ( $k != 0 ) ? ' || ' : '';
                    foreach( $val as $sk=>$sval ){
                        $condition .= ( $sk != 0 ) ? ' && ' : '';
                        if( $sval == 'is signed in' ){
                            $condition .= ($is_logged_in == 1);
                            $r = ($is_logged_in == 0);
                            if( $is_logged_in == 0 ){
                                $condition .= $is_logged_in == 1;
                                $msg = $data['message'];
                            }else{
                                $msg = ( $lock['access_denied_content'] == '') ? $access_denied_content['value'] : $lock['access_denied_content'];
                               $condition .= $is_logged_in == 0;
                            }
                        }else{
                            $srchkey = substr($sval, strpos($sval, '"') + 1);
                            $srchkey = str_replace('"', '', $srchkey);
                            $srchlock = LineItem::where('user_id', $shop['id'])->whereRaw('json_contains(search_words, \'["' . $srchkey . '"]\')')->first();
                            $condition .= ( $srchlock ) ? true : 0;
                            $r = ( $srchlock ) ? false : true;
                        }
                        $operator = ( $sk != 0 ) ? ' && ' : '';

                        if( $operator != '' ) {
                            if ($endcondition == false && $r == false) {
                                $endcondition = false;
                            } elseif (($endcondition == false && $r == true) || ($endcondition == true && $r == false)){
                                $endcondition = true;
                            } else {
                                $endcondition = ($endcondition && $r);
                            }
                        }else{
                            $endcondition = $r;
                        }
//                        $cndres = ( $operator != '' ) ? ($cndres && $r) : $r;
                    }

                    $operator = ( $k != 0 ) ? ' || ' : '';
                    if( $operator != '' ){
                        if( $orcondition == true && $r == true ){
                            $orcondition = true;
                        }elseif ( ($orcondition == true && $r == false) || ($orcondition == false && $r == true) || ( $orcondition == false && $r == false ) ){
                            $orcondition = false;
                        }else{
                            $orcondition = ($orcondition || $r);
                        }
//                        $cndres = ( $cndres == true || $r == true ) ? false : ($cndres || $r);
                    }else{
                        $orcondition = $endcondition;
                    }
                }
                $data['template'] = $template;
                $data['is_hide'] = $orcondition;

                if( !$data['is_hide'] ){
                    $data['message'] = '';
                }else{
                    if( $msg == '' ){
                        $data['message'] = ( $lock['access_denied_content'] == '') ? $access_denied_content['value'] : $lock['access_denied_content'];
                    }else{
                        $data['message'] = $msg;
                    }
                }
            }
            return $data;
        }catch( \Exception $e ){
            dd($e);
            return $e->getMessage();
        }
    }
}
