<?php

namespace App\Services;

use Carbon\Carbon;

class AdvancedService
{
    private $dataString;
    private $data;
    private $mac;
    private $signature;


    // 初始化，設定 data, mac, signature 和加密用的 dataString
    public function __construct($requestData)
    {
        $this->dataString = $requestData['data'];
        $this->data = json_decode($this->dataString, true);
        $this->mac = $requestData['mac'];
        $this->signature = $requestData['signature'];
    }

    public function data()
    {
        return $this->data;
    }
    // 資料防偽驗證
    public function validate()
    {
        return $this->validateMac() && $this->validateSignature();
    }

    // 驗證 mac 值
    private function validateMac()
    {
        $generatedMac = $this->calculateMac();

        return $generatedMac == $this->mac;
    }

    // 利用公鑰，驗證 base64 decode 後的 signature( SHA256 with RSA 2048 )
    private function validateSignature()
    {
        $publicKey = $this->getPublicKey();
        // ! ! openssl_verify($this->mac, base64_decode($this->signature), $publicKey, OPENSSL_ALGO_SHA256);
        return ! ! openssl_verify('ubot2cchoseRSASign', base64_decode($this->signature), $publicKey, OPENSSL_ALGO_SHA256);
    }

    // 計算 Mac 值
    private function calculateMac()
    {
        $aesKey = $this->getAESKey();
        $iv = $this->getUBotAESIV();
        $sha256HashedData = base64_encode(hash('sha256', $this->dataString, true));
        $aesEncryptedData = base64_encode(openssl_encrypt($sha256HashedData, 'AES-128-CBC', base64_decode($aesKey), OPENSSL_RAW_DATA, $iv));

        return $aesEncryptedData;
    }
    // 獲取經過解密後的 AES Key
    private function getAESKey()
    {
        $encryptKey = $this->getEncryptKey();
        $publicKey = $this->getPublicKey();

        $decrypted = null;
        $result = openssl_public_decrypt($encryptKey, $decrypted, $publicKey, OPENSSL_PKCS1_PADDING);

        if ($result == false) {
            echo 'Decrypt error';

            return;
        }

        return $decrypted;
    }

    // 獲取經過 base64 decode 後的 private key
    private function getEncryptKey()
    {
        return base64_decode(base64_decode($this->getSecretKeyContent()));
    }
    // 獲取 public key
    private function getPublicKey()
    {
        return openssl_pkey_get_public(config('bank.public_key'));
    }
    // 獲取 private key
    private function getSecretKeyContent()
    {
        $lines = explode("\n", config('bank.client_secret'));
        array_shift($lines); // remove first line
        array_pop($lines);   // remove end line

        return join('', $lines);
    }

    // 獲取 IV
    private function getUBotAESIV()
    {
        return config('bank.iv');
    }
}
