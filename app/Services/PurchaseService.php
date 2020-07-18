<?php

namespace App\Services;

use Carbon\Carbon;

class PurchaseService
{
    private $merchantID;
    private $hashKey;
    private $hashIV;


    // 初始化，設定 data, mac, signature 和加密用的 dataString
    public function __construct($merchantID = null, $hashKey = null, $hashIV = null)
    {
        $this->merchantID = ($merchantID != null ? $merchantID : env('CASH_STORE_ID'));
        $this->hashKey = ($hashKey != null ? $hashKey : env('CASH_STORE_HASH_KEY'));
        $this->hashIV = ($hashIV != null ? $hashIV : env('CASH_STORE_HASH_IV'));
    }

    public function getPayload($product, $method)
    {
        $payload = $this->setPayload($product, $method);
        $encryptedPayload = $this->aesEncrypt($payload->toArray(), $this->hashKey, $this->hashIV);
        $encodeString = "HashKey=$this->hashKey&$encryptedPayload&HashIV=$this->hashIV";
        return [
          'MerchantID' => $this->merchantID,
          'TradeInfo' => $encryptedPayload,
          'TradeSha' => strtoupper(hash("sha256", $encodeString)),
          'Version' => 1.5
        ];
    }

    private function setPayload($product, $method)
    {
        $payload = collect([
            'MerchantID' => $this->merchantID,
            'RespondType' => 'JSON',
            'TimeStamp'=> Carbon::now()->timestamp,
            'Version'=> '1.5',
            'MerchantOrderNo' => Carbon::now()->timestamp,
            'Amt' => $product->price,
            'ItemDesc' => $product->name,
            'ReturnURL' => env('CASH_RETURN_URL'),
            'NotifyURL' => env('CASH_NOTIFY_URL'),
            'ClientBackURL' => env('CASH_CLIENT_BACK_URL')
        ]);
        if ($method == 'atm') {
            $payload = $payload->merge(["VACC" => 1, 'CustomerURL' => env('CASH_CLIENT_CUSTOMER_URL')]);
        }
        if ($method == 'card') {
            $payload = $payload->merge(["CREDIT" => 1 ]);
        }

        return $payload;
    }

    private function aesEncrypt($parameter = "", $key = "", $iv = "")
    {
        $returnStr = '';
        if (!empty($parameter)) {
            $returnStr = http_build_query($parameter);
        }

        return trim(bin2hex(openssl_encrypt($this->addpadding($returnStr), 'aes-256-cbc', $key, OPENSSL_RAW_DATA|OPENSSL_ZERO_PADDING, $iv)));
    }

    private function addpadding($string, $blocksize = 32)
    {
        $len = strlen($string);
        $pad = $blocksize - ($len % $blocksize);
        $string .= str_repeat(chr($pad), $pad);
        return $string;
    }
}
