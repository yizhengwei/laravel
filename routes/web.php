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

Route::get('/test', function () {
    return view('test');
});


Route::any('/category', 'View\CategoryController@toCategory');
Route::any('/service/login', 'Service\LoginController@login');
Route::any('/upload', 'TestController@upload');
Route::any('/uploadExcel', 'TestController@uploadExcel');
Route::get('excel/export','ExcelController@export');
Route::get('text','ExcelController@text');
Route::get('excel/import','ExcelController@import');

Route::any('/export', 'TestController@export');


Route::group(['namespace' => 'V1', 'prefix' => 'v1'], function () {
    Route::any('/login', 'wxLoginController@login');
    Route::any('/save', 'wxLoginController@save');
    Route::any('/userLogin', 'wxLoginController@userLogin');
    Route::any('/sendSMS', 'ValidateController@sendSMS');
    Route::any('/register', 'ValidateController@register');
    Route::any('/getGoodsList', 'GoodsController@getGoodsList');
    Route::any('/detail', 'GoodsController@detail');
    Route::any('/getCategoryList', 'CategoryController@getCategoryList');
    Route::any('/cateGoods', 'GoodsController@cateGoods');
    Route::any('/shopCart', 'GoodsController@shopCart');
    Route::any('/getCartList', 'GoodsController@getCartList');
    Route::any('/updateCount', 'GoodsController@updateCount');
    Route::any('/delCart', 'GoodsController@delCart');
    Route::any('/saveOrder', 'GoodsController@saveOrder');

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
    Route::any('/delGoods', 'GoodsController@delGoods');
    Route::any('/getOrderList', 'OrderController@getOrderList');
    Route::any('/delete', 'GoodsController@delete');
    Route::any('/curve', 'GoodsController@curve');
    Route::any('/getMemberList', 'MemberController@getMemberList');
    Route::any('/orderDetail', 'OrderController@detail');
    Route::any('/test', 'CategoryController@test');
});
