<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\Services\PurchaseService;
use App\Product;

class PurePurchaseController extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;
    public function index()
    {
        $products = Product::all();
        return view('pure_purchases.index')->with('products', $products);
        ;
    }
  
    public function purchase(Request $request)
    {
        $params = $request->all();
        $service = new PurchaseService(env('CASH_STORE_ID'), env('CASH_STORE_HASH_KEY'), env('CASH_STORE_HASH_IV'));
        $product = Product::find($params['productId']);
        $result = $service->getPayload($product, $params['method']);
        dd($result);
    }

    public function successRedirect()
    {
        return redirect('/purePurchases/success');
    }

    public function success()
    {
        return view('pure_purchases.success');
    }
    public function postSuccess(Request $request)
    {
    }
    public function back()
    {
        return view('pure_purchases.back');
    }
}
