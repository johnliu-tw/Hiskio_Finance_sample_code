<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;
use NewebPay;

class PurchaseController extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;
    public function index()
    {
        return view('purchases.index');
    }
  
    public function purchase(Request $request)
    {
        $newebpay = new NewebPay();
        return $newebpay->payment(
            Carbon::now()->timestamp,
            10,
            '測試訂單',
            'johntest@gmail.com'
        )->submit();
    }

    public function successRedirect()
    {
        return redirect('/purchases/success');
    }

    public function success()
    {
        return view('purchases.success');
    }
    public function postSuccess(Request $request)
    {
        $newebpay = new NewebPay();
        Log::info('app.requests', ['request' => $request->all(), 'data' => $newebpay->decodeCallback($request->all()['TradeInfo'])]);
    }
    public function back()
    {
        return view('purchases.back');
    }
}
