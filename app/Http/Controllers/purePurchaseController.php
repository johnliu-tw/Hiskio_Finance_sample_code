<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class PurePurchaseController extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;
    public function index()
    {
        return view('pure_purchases.index');
    }
  
    public function purchase(Request $request)
    {
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
