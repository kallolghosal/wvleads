<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\LeadsModel;
use App\Models\CacModel;
use App\Models\WvCityModel;
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

    /**
     * Method to export Walmart leads
     * to csv file for download
     * @param $st $nd int range
     */
    public function exportToCsv ($st, $nd) {
        $uniqueval = [];
        $duplicates = [];

        // Get the list of cities to be included
        $cities = WvCityModel::pluck('city');
        //dd($cities);
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

        foreach ($rangeid as $x=>$y) {
            if (!in_array($y, $uniqueval)) {
                array_push($duplicates, $y);
            }
        }
        //dd($uniqueval);
        //dd(array_diff($rangeid, $uniqueval));
        //dd($duplicates);

        /**
         * Dataset for all unique leads in the given range
         */
        $data = LeadsModel::whereIn('id', $this->filterEmails($uniqueval))->whereIn('city', $cities)->get()->unique('phone');

        $dataid = LeadsModel::whereIn('id', $this->filterEmails($uniqueval))->whereIn('city', $cities)->pluck('id')->unique('phone');
        $dupids = array_diff($rangeid, $dataid);

        /**
         * Dataset of duplicate rows in the given range
         */
        //$dups = LeadsModel::whereIn('id', $duplicates)->get();
        $dups = LeadsModel::whereIn('id', $dupids)->get();
        //dd($data);

        if (count($data) == 0){
            return redirect()->back()->with('error', 'No unique row found in range');
        }

        $fileName = 'leads.csv';
        $headers = array(
            "Content-type"        => "text/csv;charset=UTF-8",
            "Content-Encoding"    => "UTF-8",
            "Content-Disposition" => "attachment; filename=$fileName",
            "Pragma"              => "no-cache",
            "Cache-Control"       => "must-revalidate, post-check=0, pre-check=0",
            "Expires"             => "0"
        );
        $columns = array('Platform', 'Business Name', 'Full Name', 'Business Sector', 'State', 'City', 'Phone', 'Email', 'Create Dt');

        $callback = function() use($data, $dups, $columns) {
            $file = fopen('php://output', 'w');
            fprintf($file, chr(0xEF).chr(0xBB).chr(0xBF));

            fwrite($file, "Enique values"."\n");
            fputcsv($file, $columns);
            foreach ($data as $task) {
                fputcsv($file, [
                    $task->platform,
                    $task->business_name,
                    $task->full_name,
                    $task->business_sector,
                    $task->state,
                    $task->city,
                    substr($task->phone, -10),
                    $task->email,
                    $task->created_at
                ]);
            }

            /**
             * Write duplicate entries to the file
             */
            fwrite($file, "\n"."Duplicates"."\n");
            fputcsv($file, $columns);
            foreach ($dups as $task) {
                fputcsv($file, [
                    $task->platform,
                    $task->business_name,
                    $task->full_name,
                    $task->business_sector,
                    $task->state,
                    $task->city,
                    substr($task->phone, -10),
                    $task->email,
                    $task->created_at
                ]);
            }
            fclose($file);
        };
        return response()->stream($callback, 200, $headers);
    }

    /**
     * Method to export data to csv file
     * from CAC table
     * with unique phone and email
     */
    public function exportCsvData ($st, $nd) {
        $uniqueval = [];
        $duplicates = [];
        $range = CacModel::where('id', '>=', $st)->where('id', '<=', $nd)->pluck('phone', 'id')->toArray();
        //dd($range);
        $rangeid = CacModel::where('id', '>=', $st)->where('id', '<=', $nd)->pluck('id')->toArray();
        $leads = CacModel::whereNotIn('id', $rangeid)->pluck('phone')->toArray();
        //dd($leads);
        foreach ($range as $k=>$v) {
            if (!in_array($v, $leads)) {
                array_push($uniqueval, $k);
            }
        }

        foreach ($rangeid as $x=>$y) {
            if (!in_array($y, $uniqueval)) {
                array_push($duplicates, $y);
            }
        }
        //dd($uniqueval);
        //dd(array_diff($rangeid, $uniqueval));
        //dd($duplicates);

        /**
         * Dataset for all unique leads in the given range
         */
        $data = CacModel::whereIn('id', $this->filterCacEmails($uniqueval))->get()->unique('phone');

        /**
         * Dataset of duplicate rows in the given range
         */
        $dups = CacModel::whereIn('id', $duplicates)->get();
        //dd($data);

        if (count($data) == 0){
            return redirect()->back()->with('error', 'No unique row found in range');
        }

        $fileName = 'leads.csv';
        $headers = array(
            "Content-type"        => "text/csv;charset=UTF-8",
            "Content-Encoding"    => "UTF-8",
            "Content-Disposition" => "attachment; filename=$fileName",
            "Pragma"              => "no-cache",
            "Cache-Control"       => "must-revalidate, post-check=0, pre-check=0",
            "Expires"             => "0"
        );
        $columns = array('Form', 'Platform', 'State', 'City', 'Full Name', 'Company', 'Phone', 'Email', 'Create Dt');

        $callback = function() use($data, $dups, $columns) {
            $file = fopen('php://output', 'w');
            fprintf($file, chr(0xEF).chr(0xBB).chr(0xBF));

            fwrite($file, "Enique values"."\n");
            fputcsv($file, $columns);
            foreach ($data as $task) {
                fputcsv($file, [
                    $task->form_name,
                    $task->platform,
                    $task->state,
                    $task->city,
                    $task->first_name.' '.$task->last_name,
                    $task->company_name,
                    substr($task->phone, -10),
                    $task->email,
                    $task->created_at
                ]);
            }

            /**
             * Write duplicate entries to the file
             */
            fwrite($file, "\n"."Duplicates"."\n");
            fputcsv($file, $columns);
            foreach ($dups as $task) {
                fputcsv($file, [
                    $task->form_name,
                    $task->platform,
                    $task->state,
                    $task->city,
                    $task->first_name.' '.$task->last_name,
                    $task->company_name,
                    substr($task->phone, -10),
                    $task->email,
                    $task->created_at
                ]);
            }
            fclose($file);
        };
        return response()->stream($callback, 200, $headers);
    }

    /**
     * Method to check for duplicate email
     * in rest of the database and 
     * return ids of unique email IDs
     */
    public function filterEmails($uniqueval) {
        $vals = [];
        $rangemail = LeadsModel::whereIn('id', $uniqueval)->pluck('email', 'id')->toArray();
        $rangeid = LeadsModel::whereIn('id', $uniqueval)->pluck('id')->toArray();
        $leads = LeadsModel::whereNotIn('id', $rangeid)->pluck('email')->toArray();
        foreach ($rangemail as $k=>$v) {
            if (!in_array($v, $leads)) {
                array_push($vals, $k);
            }
        }
        return $vals;
    }

    public function filterCacEmails($uniqueval) {
        $vals = [];
        $rangemail = CacModel::whereIn('id', $uniqueval)->pluck('email', 'id')->toArray();
        $rangeid = CacModel::whereIn('id', $uniqueval)->pluck('id')->toArray();
        $leads = CacModel::whereNotIn('id', $rangeid)->pluck('email')->toArray();
        foreach ($rangemail as $k=>$v) {
            if (!in_array($v, $leads)) {
                array_push($vals, $k);
            }
        }
        return $vals;
    }
}
