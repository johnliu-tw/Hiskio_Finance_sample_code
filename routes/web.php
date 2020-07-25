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
Route::resource('invoices', 'InvoiceController');
Route::get('creditCards', 'CreditCardController@index');
Route::get('creditCards/export', 'CreditCardController@export');
Route::post('creditCards/import', 'CreditCardController@import');
Route::post('virtualAccounts/normal', 'VirtualAccountController@normal');
Route::post('virtualAccounts/advanced', 'VirtualAccountController@advanced');
Route::get('virtualAccounts', 'VirtualAccountController@index');
Route::get('purchases', 'PurchaseController@index');
Route::post('purchases', 'PurchaseController@purchase');
Route::get('purchases/success', 'PurchaseController@success');
Route::post('purchases/successRedirect', 'PurchaseController@successRedirect');
Route::post('purchases/success', 'PurchaseController@postSuccess');
Route::get('purchases/back', 'PurchaseController@back');

Route::get('purePurchases', 'PurePurchaseController@index');
Route::post('purePurchases', 'PurePurchaseController@purchase');
Route::get('purePurchases/success', 'PurePurchaseController@success');
Route::post('purePurchases/successRedirect', 'PurePurchaseController@successRedirect');
Route::post('purePurchases/success', 'PurePurchaseController@postSuccess');
Route::get('purePurchases/back', 'PurePurchaseController@back');
Route::post('purePurchases/logisticsPurchase', 'PurePurchaseController@logisticsPurchase');


Route::get('/', function () {
    return view('welcome');
});
