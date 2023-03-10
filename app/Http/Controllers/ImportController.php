<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\File;
use App\Models\LeadsModel;
use Illuminate\Support\Facades\Validator;

class ImportController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function importCsv() {
        return view('import');
    }

    /**
     * Method to save CSV data in DB
     * Show data from uploaded CSV file
     */
    public function saveFile ($name) {

        $csv = [];
        if (($file = fopen(public_path('files').'/'.$name, 'r')) === false) {
            throw new Exception('There was an error loading the CSV file.');
        } else { 
            $line = fgetcsv($file, 1000, ",");
            $i = 0;
            while (($line = fgetcsv($file, 1000)) !== false) {
                $csv[$i]['platform'] = $line[0];
                $csv[$i]['business_name'] = $line[1];
                $csv[$i]['full_name'] = $line[2];
                $csv[$i]['business_sector'] = $line[3];
                $csv[$i]['state'] = $line[4];
                $csv[$i]['city'] = $line[5];
                $csv[$i]['phone'] = $line[6];
                $csv[$i]['email'] = $line[7];
                $i++;
            }
            fclose($file);
        }

        $leads = new LeadsModel;
        $leads::insert($csv);

        return \redirect('import-csv')->with('status', 'Data saved successfully');
    }

    /**
     * Show data from uploaded CSV file
     * Saving uploaded file info in DB
     */
    public function showCsvData (Request $request) {
        $validate = $request->validate([
            'file' => 'required|mimes:csv|max:20000',
        ],[
            'file.required' => 'Please select the right filetype'
        ]);

        $path = $request->file('file')->store('public/files');
        $name = $request->file('file')->getClientOriginalName();
        $request->file('file')->move(public_path('files'), $name);

        $csv = [];

        if (($file = fopen(public_path('files').'/'.$name, 'r')) === false) {
            throw new Exception('There was an error loading the CSV file.');
        } else { 
            $line = fgetcsv($file, 1000, ",");
            $i = 0;
            while (($line = fgetcsv($file, 1000)) !== false) {
                $csv[$i]['platform'] = $line[0];
                $csv[$i]['business_name'] = $line[1];
                $csv[$i]['full_name'] = $line[2];
                $csv[$i]['business_sector'] = $line[3];
                $csv[$i]['state'] = $line[4];
                $csv[$i]['city'] = $line[5];
                $csv[$i]['phone'] = $line[6];
                $csv[$i]['email'] = $line[7];
                $i++;
            }
            fclose($file);
        }

        $save = new File;
        $save->name = $name;
        $save->path = $path;
        $save->save();

        return view('showcsv', ['csv' => $csv, 'name' => $name]);
    }
}
