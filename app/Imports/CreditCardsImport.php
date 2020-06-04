<?php

namespace App\Imports;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithCustomCsvSettings;
use Maatwebsite\Excel\Concerns\Importable;

class CreditCardsImport implements ToCollection, WithCustomCsvSettings
{
    use Importable;

    public function __construct()
    {
        $this->data = collect();
        $this->header = '';
        $this->footer = '';
    }

    /**
     * @param Collection $collection
     */
    public function collection(Collection $rows)
    {
        // 整理匯入檔案的格式
        $rows = $rows->collapse();
        $header = $rows->shift();
        $footer = $rows->pop();
        // 匯入檔案
        $rows = $this->getFormatRows($rows);

        return true;
    }

    public function getFormatRows(Collection $rows)
    {
        // 整理檔案成特定的資料格式
        $data = collect();
        foreach ($rows as $row) {
            // substr 切割字串
            // ltrim 清除字串靠左所有特定字
            // rtrim 清除字串靠右所有特定字
            $row = [
                'char'             => substr($row, 0, 1),               // 識別字元 C 1 'T' 固定值
                'id'               => ltrim(substr($row, 1, 5), 0),     // 批次號碼 N 5 右靠前補0
                'number'           => rtrim(substr($row, 6, 19)),       // 信用卡號 C 19 必填，左靠後補空白
                'expiry_date'      => substr($row, 25, 4),              // 信用卡效期 C 4 MMYY，必填
                'amount'           => ltrim(substr($row, 29, 13), 0),   // 交易金額 N 13 兩位小數，必填，右靠前補0
                'status_code'      => substr($row, 42, 2),              // 回應碼 C 2 00 代表交易核准，其於則代表授權拒絕。此欄位於交易回應後主機填寫，上傳時填寫空白
                'order_id'         => rtrim(substr($row, 44, 15)),      // 訂單編號 C 15 必填，左靠後補空白。建議填寫唯一編號作為識別
                'read_id'          => substr($row, 59, 6),              // 調閱編號 C 6 此欄位於交易回應後主機填寫，上傳時填寫空白
                'type'             => substr($row, 65, 1),              // 交易類型 C 1 必填
                'approve_code'     => substr($row, 66, 6),              // 核准碼 C 6 此欄位於交易回應後主機填寫，上傳時填寫空白
                'last_status_code' => substr($row, 72, 2),              // 前次回應碼 C 2 進行Call Bank時會填寫第一次回應碼，此欄位於交易回應後主機填寫，上傳時填寫空白
                'cvv'              => substr($row, 74, 3),              // CVV2 C 3 卡片末三碼
                'identity_number'  => rtrim(substr($row, 77, 10)),      // 身份證字號 C 10 左靠後補空白
                'blank'            => substr($row, 87, 11),             // 保留欄位 C 11 空白
                'bps_status_code'  => substr($row, 98, 2),              // BPS錯誤回應碼 C 2 BPS Server檢核交易檔異常時填入回覆檔。上傳時填寫空白。
                'comment'          => rtrim(mb_substr($row, 100, 19)),  // 帳單描保留欄位述欄位 C 40 19個中文字，左靠後補空白
                'call_bank'        => rtrim(substr($row, 140, 38)),     // Call Bank資料欄位 C 38 填寫Call Bank資料，左靠後補空白
            ];

            $data->push(collect($row));
        }
        return $data;
    }

    public function getCsvSettings(): array
    {
        return [
            'input_encoding' => 'Big5'
        ];
    }
}
