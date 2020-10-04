<?php

namespace App\Http\Controllers;

use Illuminate\Routing\Controller as BaseController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\Services\LinePayService;
use Carbon\Carbon;

class LinePayController extends BaseController
{
    public function index()
    {
        return view('linepays.index');
    }
  
    public function request(Request $request)
    {
        $service = new LinePayService();
        $result = $service->payment();
        return response()->json($result);
    }

    public function confirm(Request $request)
    {
        $service = new LinePayService();
        $result = $service->confirm($request->all()['transactionId']);
        return response()->json($result);
    }
}
