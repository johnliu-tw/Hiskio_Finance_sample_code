<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Carbon\Carbon;

class LinePayService
{
    private $channelId;
    private $channelSecretKey;
    private $nonce;


    // 初始化，設定 data, mac, signature 和加密用的 dataString
    public function __construct($channelId = null, $channelSecretKey = null)
    {
        $this->channelId = ($channelId != null ? $channelId : env('LINE_CHANNEL_ID'));
        $this->channelSecretKey = ($channelSecretKey != null ? $channelSecretKey : env('LINE_SECRETKEY'));
        $this->nonce = $this->gen_uuid();
        $this->baseUrl = env('LINE_URL');
    }

    public function send()
    {
        $body = $this->setBody();
        $url = '/v3/payments/request';
        
        $header = $this->setHeader($url, $body);
        $response = Http::withHeaders($header)->post($this->baseUrl.$url, $body);
        dd($response->body());
    }

    public function setHeader($url, $body)
    {
        $signatureData = $this->channelSecretKey . $url . json_encode($body) . $this->nonce;
        $signature = base64_encode(hash_hmac('sha256', $signatureData, $this->channelSecretKey, true));
       
        return [
          'Content-Type' => 'application/json',
          'X-LINE-ChannelId' => $this->channelId,
          'X-LINE-Authorization-Nonce' => $this->nonce ,
          'X-LINE-Authorization' => $signature
        ];
    }

    public function setBody()
    {
        return array(
          "amount" => 3000,
          "currency" => "TWD",
          "orderId" => strval(Carbon::now()->timestamp),
            'packages'=> [
              [
                "id" => strval(10),
                "amount" => 3000,
                'products' => [[
                  "name" => "testProducts",
                  "quantity" => 2,
                  "price" => 1500
                ]
                ]
              ]
            ],
            'redirectUrls' =>
            [
              'confirmUrl' => env('LINE_CONFIRM_URL'),
              'cancelUrl' => env('LINE_CANCEL_URL')
            ]);
    }

    public function gen_uuid()
    {
        return sprintf(
            '%04x%04x-%04x-%04x-%04x-%04x%04x%04x',
            mt_rand(0, 0xffff),
            mt_rand(0, 0xffff),
            mt_rand(0, 0xffff),
            mt_rand(0, 0x0fff) | 0x4000,
            mt_rand(0, 0x3fff) | 0x8000,
            mt_rand(0, 0xffff),
            mt_rand(0, 0xffff),
            mt_rand(0, 0xffff)
        );
    }
}
