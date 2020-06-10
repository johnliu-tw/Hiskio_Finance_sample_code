<?php

namespace App\Responser;

class ReceivableResponser
{
    // 設定回傳的格式，和取代字串用的 object array
    private $template =
    '<PaySvcRs>'.
        '<StatusCode>$statusCode</StatusCode>'.
        '<StatusDesc>$message</StatusDesc>'.
        '<PmtAddRs>'.
            '<UserID></UserID>'.
            '<TxnSeq>$txSeq</TxnSeq>'.
            '<TxnDate>$txDate</TxnDate>'.
            '<TxnAmt>$amount</TxnAmt>'.
        '</PmtAddRs>'.
    '</PaySvcRs>';
    private $data = [
        '$statusCode' => '',
        '$message'    => '',
        '$txSeq'      => '',
        '$txDate'     => '',
        '$amount'     => '',
    ];

    // 成功的回傳結構
    public function success($xml, $msg = '')
    {
        $this->data['$txSeq']  = $xml->PmtAddRq->TDateSeqNo;
        $this->data['$txDate'] = $xml->PmtAddRq->TxnDate;
        $this->data['$amount'] = $xml->PmtAddRq->TxAmount;
        $this->data['$statusCode'] = 0;
        if (config('app.env') === 'production') {
            $this->data['$message'] = $msg;
        } else {
            $this->data['$message'] = 'PR_KEY='.$xml->PmtAddRq->PR_Key1;
        }

        return $this;
    }

    // 失敗的回傳結構
    public function error($xml, $code, $msg = '')
    {
        $this->data['$statusCode'] = $code;
        $this->data['$message'] = $msg;

        return $this;
    }

    // 當確定要取得結構時，把 template 中標記要轉換的字串，
    // 根據 object array 做轉換
    public function get()
    {
        return strtr($this->template, $this->data);
    }
}
