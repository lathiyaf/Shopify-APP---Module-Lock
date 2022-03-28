<?php

use Illuminate\Support\Facades\Route;
/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/
Route::get('/', function () {
    return view('layouts.app');
})->middleware(['auth.shopify'])->name('home');

Route::group(['middleware' => ['auth.shopify']], function () {
//setting
    Route::get('app-status', 'Setting\SettingController@changeAppStatus')->name('app-status');
    Route::get('settings', 'Setting\SettingController@index')->name('settings');
    Route::post('settings', 'Setting\SettingController@store')->name('settings');

// locks
    Route::get('search', 'Lock\LockController@search')->name('search');
    Route::get('lock', 'Lock\LockController@index')->name('lock');
    Route::get('edit-lock', 'Lock\LockController@editLock')->name('edit-lock');
    Route::post('save-lock', 'Lock\LockController@saveLock')->name('save-lock');
    Route::post('lock-status', 'Lock\LockController@changeLockStatus')->name('lock-status');

//test
    Route::get('test', 'Test\TestController@index')->name('test');
});

Route::get('flush', function(){
    request()->session()->flush();
});
