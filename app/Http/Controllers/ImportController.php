<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\File;
use App\Models\LeadsModel;
use App\Models\CacModel;
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
    public function saveFile ($name, $owner) {
        //dd($owner);
        $file = fopen(public_path('files').'/'.$name, 'r');
        $csv = [];
        if ($file === false) {
            throw new Exception('There was an error loading the CSV file.');
        } else { 
            if ($owner === 'wv') {
                $line = fgetcsv($file, 1000, ",");
                while (($line = fgetcsv($file, 1000)) !== false) {
                    $csv [] = [
                    'platform' => $line[11],
                    'business_name' => (strlen($line[14]) > 50) ? 'self' : $line[14],
                    'full_name' => (strlen($line[16]) > 50) ? 'self' : $line[16],
                    'business_sector' => (strlen($line[15]) > 50) ? 'self' : $line[15],
                    'state' => $line[12],
                    'city' => str_replace('Bangalore','Bengaluru',$line[13]),
                    'phone' => substr($line[18], -10),
                    'email' => $line[17],
                    'remark' => 'None',
                    'created_at' => date('Y-m-d')
                    ];
                }
                fclose($file);
                LeadsModel::insert($csv);
            } else {
                $line = fgetcsv($file, 1000, ",");
                while (($line = fgetcsv($file, 1000)) !== false) {
                    $csv [] = [
                    'form_name' => $line[9],
                    'platform' => $line[11],
                    'state' => $line[12],
                    'city' => str_replace('Bangalore','Bengaluru',$line[13]),
                    'first_name' => $line[14],
                    'last_name' => $line[15],
                    'company_name' => $line[16],
                    'phone' => substr($line[17], -10),
                    'email' => $line[18],
                    'remark' => 'None',
                    'created_at' => date('Y-m-d')
                    ];
                }
                fclose($file);
                //dd($csv);
                CacModel::insert(mb_convert_encoding($csv, "UTF-8"));
            }
        }
        
        return \redirect('import-csv')->with('status', 'Data saved successfully');
    }

    /**
     * Show data from uploaded CSV file
     * Saving uploaded file info in DB
     */
    public function showCsvData (Request $request) {
        $validate = $request->validate([
            'file' => 'required|mimes:csv,xls|max:200000'
        ],[
            'file.required' => 'Please select the right filetype'
        ]);

        $path = $request->file('file')->store('public/files');
        $name = $request->file('file')->getClientOriginalName();
        $request->file('file')->move(public_path('files'), $name);

        $csv = [];
        //dd($request->owner);

        if (($file = fopen(public_path('files').'/'.$name, 'r')) === false) {
            throw new Exception('There was an error loading the CSV file.');
        } else { 
            if ($request->owner === 'wv') {
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
            } else {
                $line = fgetcsv($file, 1000, ",");
                $i = 0;
                //dd($line);
                while (($line = fgetcsv($file, 1000)) !== false) {
                    $csv[$i]['form'] = $line[9];
                    $csv[$i]['platform'] = $line[11];
                    $csv[$i]['state'] = $line[12];
                    $csv[$i]['city'] = str_replace('Bangalore','Bengaluru',$line[13]);
                    $csv[$i]['full_name'] = $line[14].' '.$line[15];
                    $csv[$i]['company'] = $line[16];
                    $csv[$i]['phone'] = substr($line[17], -10);
                    $csv[$i]['email'] = $line[18];
                    $i++;
                }
            }
            
            fclose($file);
        }

        $save = new File;
        $save->name = $name;
        $save->path = $path;
        $save->save();

        return view('showcsv', ['csv' => $csv, 'name' => $name, 'owner' => $request->owner]);
    }

}
