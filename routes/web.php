<?php

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

use App\Model\Category;

Route::get('/', function () {
    return view('login');
});

//Route::get('/index', function () {
//    return view('index');
//});

Route::any('/category', 'View\CategoryController@toCategory');

Route::any('/service/login', 'Service\LoginController@login');


Route::get('excel/export','ExcelController@export');
Route::get('excel/import','ExcelController@import');
////Route::get('category', 'View\CategoryController@toCategory');
//Route::get('category', 'View\CategoryController@saveCategory');
//Route::get('category', 'View\CategoryController@delCategory');

Route::group(['namespace' => 'V1', 'prefix' => 'v1'], function () {
    Route::any('/login', 'wxLoginController@test');
});

Route::group(['namespace' => 'Service', 'prefix' => 'service'], function () {
    Route::any('/getMenuList', 'MenuController@getMenuList');
    Route::any('/saveCategory', 'CategoryController@saveCategory');
    Route::any('/delCategory', 'CategoryController@delCategory');
    Route::any('/getCategoryList', 'CategoryController@getCategoryList');
    Route::any('/getAdminList', 'AdminController@getAdminList');
    Route::any('/changeStatus', 'AdminController@changeStatus');
    Route::any('/addAdmin', 'AdminController@addAdmin');
    Route::any('/getAdminInfo', 'AdminController@getAdminInfo');
    Route::any('/delAdmin', 'AdminController@delAdmin');
    Route::any('/sortCategory', 'CategoryController@sortCategory');
    Route::any('/getCateInfo', 'CategoryController@getCateInfo');
    Route::any('/getSpecList', 'CategoryController@getSpecList');
    Route::any('/saveSpec', 'CategoryController@saveSpec');
    Route::any('/delSpec', 'CategoryController@delSpec');
    Route::any('/sortSpec', 'CategoryController@sortSpec');
    Route::any('/getGoodsList', 'GoodsController@getGoodsList');
    Route::any('/saveGoods', 'GoodsController@saveGoods');
    Route::any('/onSale', 'GoodsController@onSale');
    Route::any('/getBrandList', 'GoodsController@getBrandList');

    Route::any('/getRoleList', 'PowerController@getRoleList');

    Route::any('/editView', 'GoodsController@editView');
    Route::any('/getGoodsSpec', 'GoodsController@getGoodsSpec');

});
