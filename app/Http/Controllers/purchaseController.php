<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;
use Carbon\Carbon;
use NewebPay;

class PurchaseController extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;
    public function index()
    {
        return view('purchases.index');
    }
  
    public function purchase()
    {
        $newebpay = new NewebPay();
        return $newebpay->payment(
            '123',
            10,
            '測試訂單',
            'john831118@gmail.com'
        )->submit();
    }
}
