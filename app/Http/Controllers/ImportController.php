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
            //dd($line);
            $i = 0;
            while (($line = fgetcsv($file, 1000)) !== false) {
                $csv[$i]['platform'] = $line[11];
                $csv[$i]['business_name'] = $line[14];
                $csv[$i]['full_name'] = $line[16];
                $csv[$i]['business_sector'] = $line[15];
                $csv[$i]['state'] = $line[12];
                $csv[$i]['city'] = str_replace('Bangalore','Bengaluru',$line[13]);
                $csv[$i]['phone'] = substr($line[18], -10);
                $csv[$i]['email'] = $line[17];
                $i++;
            }
            fclose($file);
        }
        //dd($csv);
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
            'file' => 'required|mimes:csv|max:200000',
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
                $csv[$i]['platform'] = $line[11];
                $csv[$i]['business_name'] = $line[14];
                $csv[$i]['full_name'] = $line[16];
                $csv[$i]['business_sector'] = $line[15];
                $csv[$i]['state'] = $line[12];
                $csv[$i]['city'] = str_replace('Bangalore','Bengaluru',$line[13]);
                $csv[$i]['phone'] = substr($line[18], -10);
                $csv[$i]['email'] = $line[17];
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
