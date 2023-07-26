<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\LeadsModel;
use Illuminate\Support\Facades\Response;

class ExportController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function exportLeads() {
        return view('export');
    }

    public function exportToCsv ($st, $nd) {
        $uniqueval = [];
        $range = LeadsModel::where('id', '>=', $st)->where('id', '<=', $nd)->pluck('phone', 'id')->toArray();
        //dd($range);
        $rangeid = LeadsModel::where('id', '>=', $st)->where('id', '<=', $nd)->pluck('id')->toArray();
        $leads = LeadsModel::whereNotIn('id', $rangeid)->pluck('phone')->toArray();
        //dd($leads);
        foreach ($range as $k=>$v) {
            if (!in_array($v, $leads)) {
                array_push($uniqueval, $k);
            }
        }
        $data = LeadsModel::whereIn('id', $uniqueval)->get()->unique('phone');

        if (count($data) == 0){
            return redirect()->back()->with('error', 'No unique row found in range');
        }

        $fileName = 'leads.csv';
        $headers = array(
            "Content-type"        => "text/csv",
            "Content-Disposition" => "attachment; filename=$fileName",
            "Pragma"              => "no-cache",
            "Cache-Control"       => "must-revalidate, post-check=0, pre-check=0",
            "Expires"             => "0"
        );
        $columns = array('Platform', 'Business Name', 'Full Name', 'Business Sector', 'State', 'City', 'Phone', 'Email');

        $callback = function() use($data, $columns) {
            $file = fopen('php://output', 'w');
            fputcsv($file, $columns);

            foreach ($data as $task) {
                fputcsv($file, [
                    $task->platform,
                    $task->business_name,
                    $task->full_name,
                    $task->business_sector,
                    $task->state,
                    $task->city,
                    $task->phone,
                    $task->email
                ]);
            }
            fclose($file);
        };
        return response()->stream($callback, 200, $headers);
    }
}
