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

    public function getPayload($product, $method, $logistic = false)
    {
        $payload = $this->setPayload($product, $method, $logistic);
        $encryptedPayload = $this->aesEncrypt($payload->toArray(), $this->hashKey, $this->hashIV);
        $encodeString = "HashKey=$this->hashKey&$encryptedPayload&HashIV=$this->hashIV";
        return [
          'MerchantID' => $this->merchantID,
          'TradeInfo' => $encryptedPayload,
          'TradeSha' => strtoupper(hash("sha256", $encodeString)),
          'Version' => 1.5
        ];
    }

    public function parsePayload($params)
    {
        return json_decode($this->aesDecrypt($params, $this->hashKey, $this->hashIV));
    }

    private function setPayload($product, $method, $logistic)
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
        if ($logistic) {
            $payload = $payload->merge(["CVSCOM" => 2]);
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
    private function aesDecrypt($parameter, $key, $iv)
    {
        return $this->strippadding(openssl_decrypt(
            hex2bin($parameter),
            'AES-256-CBC',
            $key,
            OPENSSL_RAW_DATA|OPENSSL_ZERO_PADDING,
            $iv
        ));
    }

    private function strippadding($string)
    {
        $slast = ord(substr($string, -1));
        $slastc = chr($slast);
        $pcheck = substr($string, -$slast);
        if (preg_match("/$slastc{" . $slast . "}/", $string)) {
            $string = substr($string, 0, strlen($string) - $slast);
            return $string;
        } else {
            return false;
        }
    }
}
