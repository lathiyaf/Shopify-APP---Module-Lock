<?php

namespace App\Http\Controllers\Setting;

use App\Http\Controllers\Controller;
use App\Models\Lock;
use App\Models\Setting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SettingController extends Controller
{
    public function index(){
        try{
            $shop = Auth::user();
            $setting = Setting::where('user_id', $shop->id)->get()->toArray();
            $data = [];
            foreach ( $setting as $key=>$val ){
                if( $val['key'] == 'is_enable'){
                    $data[$val['key']] = (int)$val['value'];
                }else{
                    $data[$val['key']] = $val['value'];
                }
            }
            return response()->json(['data' => $data], 200);
        }catch( \Exception $e ){
            return response()->json(['data' => $e->getMessage()], 422);
        }
    }

    public function store(Request $request){
        try{
            $shop = Auth::user();
            $data = $request->data;
            foreach( $data as $key=>$val ){
                $setting = Setting::where('key', $key)->where('user_id', $shop->id)->first();
                $setting->value = $val;
                $setting->save();
            }
            return response()->json(['data' => $data], 200);
        }catch( \Exception $e ){
            return response()->json(['data' => $e->getMessage()], 422);
        }
    }

    public function changeAppStatus(Request $request){
        try{
            $shop = Auth::user();
            $setting = Setting::where('key', 'is_enable')->where('user_id', $shop->id)->first();
            $setting->value = $request->status;
            $setting->save();

            $msg = ( $request->status == 0 ) ? 'App Disabled!': 'App Enabled!';
            return response()->json(['data' => $msg], 200);
        }catch( \Exception $e ){
            return response()->json(['data' => $e->getMessage()], 422);
        }
    }
}
