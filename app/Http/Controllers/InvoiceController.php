<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;
use App\Payment;
use Carbon\Carbon;
use SoapWrapper;
use Spatie\ArrayToXml\ArrayToXml;

class InvoiceController extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;
    public function __construct(
        SoapWrapper $soapWrapper
    ) {
        $this->soapWrapper = $soapWrapper;
    }
    public function index()
    {
        $payment = Payment::first();
        // 設定基本資料結構
        $orderDate = Carbon::now()->format('Y/m/d');
        $data = array(
            'OrderId' => $payment->order_number,
            'OrderDate' => $orderDate,
            'BuyerIdentifier' => $payment->is_company ? $payment->identifier : '',
            'BuyerName' => $payment->user_name,
            'BuyerEmailAddress' => $payment->email,
            'DonateMark' => $payment->donate,
            'InvoiceType' => "07" ,
            'TaxType' => "1" ,
            'PayWay' => "3" ,
            'Details' => array(
                'ProductItem' => array(
                    'ProductionCode' => "賣錢的商品",
                    'Description' => '商品A',
                    'Quantity' => 1,
                    'UnitPrice' => $payment->price,
                ),
            ),
        );

        // 設定紙本結構
        if ($payment->donate == 2) {
            array_merge($data, array(
                'BuyerAddress' => $payment->user_address,
            ));
        }

        // 設定配合捐贈碼的資料結構
        if ($payment->donate == 1) {
            array_merge($data, array(
                'NPOBAN' => $payment->donate_code,
            ));
        }


        // 注意文件自然人條碼和手機條碼格式
        if ($payment->carrier_type) {
            $carry = array(
              'CarrierType' => $payment->carrier_type,
              'CarrierId1' => $payment->carrier_code
            );
            array_merge($data, $carry);
        }

        // soap wrapper https://github.com/artisaninweb/laravel-soap
        // 設定 Soap wrapper 讀取的網址
        $this->soapWrapper::add('Invoice', function ($service) {
            $service
              ->wsdl(env('INVOICE_API_URL')) // http://invoice.cetustek.com.tw/InvoiceMultiWeb/InvoiceAPI?wsdl
              ->trace(true);
        });

        // 改變資料格式，從陣列轉為 xml
        $xml_data = ArrayToXml::convert($data);

        // 呼叫 CreateInvoiceV3 函式，並設定必須資料
        $response = $this->soapWrapper::call(
            'Invoice.CreateInvoiceV3',
            array( 'message' => [
                'invoicexml'  => $xml_data,
                'hastax'      => '1',
                'rentid'      => env('INVOICE_API_ID'),
                'source'      => env('INVOICE_API_KEY'),
            ])
        );
    }
}
