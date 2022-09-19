<?php

namespace App\Services;

use Carbon\Carbon;
use Illuminate\Support\Facades\Http;

class LinePayService
{
    private $channelId;
    private $channelSecretKey;
    private $nonce;
    private $baseUrl;


    // 初始化，設定 data, mac, signature 和加密用的 dataString
    public function __construct($channelId = null, $channelSecretKey = null)
    {
        $this->channelId = ($channelId != null ? $channelId : env('LINE_CHANNEL_ID'));
        $this->channelSecretKey = ($channelSecretKey != null ? $channelSecretKey : env('LINE_SECRETKEY'));
        $this->nonce = $this->genUuid();
        $this->baseUrl = env('LINE_URL');
    }

    public function send()
    {
        $body = $this->setBody();
        $url = '/v3/payments/request';
        $header = $this->setHeader($url, $body);
        $response = Http::withHeaders($header)->post($this->baseUrl.$url, $body);
        dump($response->body());
    }

    public function confirm($tranId)
    {
        $body = array('amount' => 3000, 'currency'=>'TWD');
        $url = '/v3/payments/'.$tranId.'/confirm';
        $header = $this->setHeader($url, $body);
        $response = Http::withHeaders($header)->post($this->baseUrl.$url, $body);
        dump($response->body());
    }


    public function setHeader($url, $body)
    {
        $signatureData = $this->channelSecretKey . $url . json_encode($body) . $this->nonce;
        $signature = base64_encode(hash_hmac('sha256', $signatureData, $this->channelSecretKey, true));
        return [
          'Content-Type' => 'application/json',
          'X-LINE-ChannelId' => $this->channelId,
          'X-LINE-Authorization-Nonce' => $this->nonce,
          'X-LINE-Authorization' => $signature
        ];
    }
    public function setBody()
    {
        return array(
        'amount' => 3000,
        'currency' => 'TWD',
        'orderId' => strval(Carbon::now()->timestamp),
        'packages' => [
          [
            'id' => '10',
            'amount' => 3000,
            'name' => 'test',
            'products' => [
              [
                'name' => 'testProduct',
                'quantity' => 2,
                'price' => 1500
              ]
            ]
          ]
              ],
              'redirectUrls' => [
                'confirmUrl' => env('LINE_CONFIRM_URL'),
                'cancelUrl' => env('LINE_CANCEL_URL')
              ]
              );
    }

    public function genUuid()
    {
        return sprintf(
            '%04x%04x-%04x-%04x-%04x-%04x%04x%04x',

      // 32 bits for "time_low"
      mt_rand(0, 0xffff),
            mt_rand(0, 0xffff),
  
      // 16 bits for "time_mid"
      mt_rand(0, 0xffff),
  
      // 16 bits for "time_hi_and_version",
      // four most significant bits holds version number 4
      mt_rand(0, 0x0fff) | 0x4000,
  
      // 16 bits, 8 bits for "clk_seq_hi_res",
      // 8 bits for "clk_seq_low",
      // two most significant bits holds zero and one for variant DCE1.1
      mt_rand(0, 0x3fff) | 0x8000,
  
      // 48 bits for "node"
      mt_rand(0, 0xffff),
            mt_rand(0, 0xffff),
            mt_rand(0, 0xffff)
        );
    }
}
