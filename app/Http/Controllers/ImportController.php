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

    public function saveFile (Request $request) {
        $validate = $request->validate([
            'file' => 'required|mimes:csv|max:20000',
        ],[
            'file.required' => 'Please select the right filetype'
        ]);

        $path = $request->file('file')->store('public/files');
        $name = $request->file('file')->getClientOriginalName();
        $request->file('file')->move(public_path('files'), $name);

        // if (($handle = fopen(public_path('files').'/'.$name, "r")) !== FALSE) {
        //     $row = 1;
        //     $cols = fgetcsv($handle, 1000, ",");
        //     print_r($cols);
        //     $csvdata = [];
        //     while (($data = fgetcsv($handle, 1000, ",")) !== FALSE) {
        //         $num = count($data);
        //         //echo "<p> $num fields in line $row: </p>\n";
        //         $row++;
        //         for ($c=0; $c < $num; $c++) {
        //             $csvdata[] = $data[$c];
        //         }
        //     }
        //     echo 'Total no of rows = ' . count($csvdata);
        //     fclose($handle);
        // }

        // $handle = fopen(public_path('files').'/'.$name, "r");
        // $cols = fgetcsv($handle, 1000, ",");
        // echo 'Column names are <br >';
        // foreach($cols as $col){
        //     echo $col . ',';
        // }
        // echo '<br>';
        // while (!feof($handle)) {
        //     $rows[] = fgetcsv($handle, 1000, ",");
        // }

        // fclose($handle);
        // echo 'Total no of rows - ' . count($rows);

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

        $save = new File;
        $save->name = $name;
        $save->path = $path;
        $save->save();

        return \redirect('import-csv')->with('status', 'File Has been uploaded successfully in WV Leads');
    }
}
