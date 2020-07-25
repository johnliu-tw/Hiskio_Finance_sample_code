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
        $payload = $service->getPayload($product, $params['method']);
        $result = '<form name="newebpay" id="order-form" method="post" action=' . env('CASE_URL') . ' >';
        foreach ($payload as $key => $value) {
            $result .= '<input type="hidden" name="' . $key . '" value="' . $value . '">';
        }
        $result .= '</form><script type="text/javascript">document.getElementById(\'order-form\').submit();</script>';

        return $result;
    }

    public function logisticsPurchase(Request $request)
    {
        $params = $request->all();
        $service = new PurchaseService(env('CASH_STORE_ID'), env('CASH_STORE_HASH_KEY'), env('CASH_STORE_HASH_IV'));
        $product = Product::find($params['productId']);
        $payload = $service->getPayload($product, '', true);
        $result = '<form name="newebpay" id="order-form" method="post" action=' . env('CASE_URL') . ' >';
        foreach ($payload as $key => $value) {
            $result .= '<input type="hidden" name="' . $key . '" value="' . $value . '">';
        }
        $result .= '</form><script type="text/javascript">document.getElementById(\'order-form\').submit();</script>';

        return $result;
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
        $params = $request->all();
        $service = new PurchaseService(env('CASH_STORE_ID'), env('CASH_STORE_HASH_KEY'), env('CASH_STORE_HASH_IV'));
        $result = $service->parsePayload($params['TradeInfo']);
        Log::info('app.requests', ['request' => $params, 'data' => $result]);
    }
    public function back()
    {
        return view('pure_purchases.back');
    }
}
