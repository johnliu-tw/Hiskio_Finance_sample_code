<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Http\Request;
// use Laravel Excel
use Maatwebsite\Excel\Facades\Excel;
use App\Imports\CreditCardsImport;
use App\Exports\CreditCardsExport;

class CreditCardController extends Controller
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;
    public function index()
    {
        return view('credit_cards.index');
    }

    public function export()
    {
        return Excel::download(new CreditCardsExport, 'creditCards.xlsx');
    }
    public function import(Request $request)
    {
        $tempPath = $request->all()['file']->store('temp');
        $path = storage_path('app').'/'.$tempPath;
        $import = new CreditCardsImport;

        return Excel::import($import, $path);
    }
}
