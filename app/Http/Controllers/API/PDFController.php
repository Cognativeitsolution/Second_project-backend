<?php

namespace App\Http\Controllers\API;

use Illuminate\Http\Request;
use App\Http\Controllers\API\BaseController as BaseController;
use App\Models\Product;
use App\Models\User;
use PDF;

use App\Exports\UsersExport;
use App\Imports\UsersImport;
use Maatwebsite\Excel\Facades\Excel;

class PDFController extends BaseController
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function generatePDF()
    {
        $users = User::get();
        $combinedDT = date('d-m-Y-H:i:s');

        $data = [
            'title' => 'Welcome to Employment Agency',
            'date' => date('m/d/Y'),
            'users' => $users
        ];

        $pdf = PDF::loadView('pdf_view', $data);

        return $pdf->download('employment_agency-'.$combinedDT.'.pdf');
    }
    /**
     * @return \Illuminate\Support\Collection
     */
    public function export()
    {
        return Excel::download(new UsersExport, 'users.xlsx');
    }

    /**
     * @return \Illuminate\Support\Collection
     */
    public function import()
    {
        Excel::import(new UsersImport,request()->file('file'));

        return $this->sendResponse("", 'Record import successfully.');
    }

}
