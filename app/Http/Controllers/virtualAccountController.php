<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Http\Request;
use App\OriginLog;
use App\Responser\ReceivableResponser;
use Carbon\Carbon;
use App\Services\AdvancedService;

class VirtualAccountController extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

    public function index()
    {
        return view('virtual_accounts.index');
    }

    public function normal(Request $request)
    {
        // 解析 xml 資料
        libxml_use_internal_errors(true);
        $xml = simplexml_load_string($request->getContent());

        // 檢查 xml
        if ($xml === false) {
            return response('Invalid XML format');
        }
      
        // 針對回傳格式，設定一個 Class 物件處理
        $responser = new ReceivableResponser();

        try {
            // 儲存原始 log 資料
            $log = OriginLog::create([
              'method'      => $request->getMethod(),
              'params'      => [$request->getContent()],
              'uri'         => $request->getPathInfo(),
              'remote_addr' => $request->getClientIp(),
              'user_agent'  => $request->userAgent()
            ]);



            // 設定客製化 model 欄位資料
            $virtualAccount = trim($xml->PmtAddRq->PR_Key1);
            $txTime = Carbon::parse($xml->PmtAddRq->TxnDate.$xml->PmtAddRq->TxnTime);
            $depositTime = Carbon::parse($xml->PmtAddRq->ValueDate);
            $amount      = trim($xml->PmtAddRq->TxAmount);
            $fromBank    = trim($xml->PmtAddRq->BankID);
            $fromAccount = trim($xml->PmtAddRq->ActNo);
            $log->fill([
              'data'   => [
                  'virtual_account' => $virtualAccount,
                  'txTime'          => $txTime,
                  'depositTime'     => $depositTime,
                  'amount'          => $amount,
                  'from_bank'       => $fromBank,
                  'from_account'    => $fromAccount
              ]
            ])->save();

            return response($responser->success($xml, 'SuccessM')->get())
                  ->header('Content-Type', 'text/xml');
        } catch (\Throwable $th) {
            return response($responser->error($xml, 1, $th->getMessage())->get())
                ->header('Content-Type', 'text/xml');
        }
    }

    public function advanced(Request $request)
    {
        try {
            $requestData = json_decode($request->getContent(), true);
            $log = OriginLog::create([
                'method'      => $request->getMethod(),
                'params'      => $request->all(),
                'uri'         => $request->getPathInfo(),
                'remote_addr' => $request->getClientIp(),
                'user_agent'  => $request->userAgent(),
            ]);

            $service = new AdvancedService($requestData);
            $data = $service->data();
            
            $virtualAccount = $data['ecacc'];
            $time           = Carbon::parse($data['date'].$data['time']);
            $amount         = intval($data['amt']) / 100;
            $fromBank       = $data['wdbank'];
            $fromAccount    = $data['wdacc'];

            $log->fill([
                'data'   => [
                    'virtual_account' => $virtualAccount,
                    'txTime'          => $time,
                    'depositTime'     => $time,
                    'amount'          => $amount,
                    'from_bank'       => $fromBank,
                    'from_account'    => $fromAccount
                ],
            ])->save();

            if (! $service->validate()) {
                return response()->json(['txseq' => $data['txseq'], 'ubnotify' => 'record', 'resmsg' => 'failed']);
            }
            return response()->json(['txseq' => $data['txseq'], 'ubnotify' => 'record', 'resmsg' => 'succes']);
        } catch (\Throwable $th) {
            return response()->json(['txseq' => $data['txseq'], 'ubnotify' => 'record', 'resmsg' => 'failed']);
        }
    }
}
