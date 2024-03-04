<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\LeadsModel;
use App\Models\CacModel;
use Illuminate\Support\Facades\Validator;

class LeadsController extends Controller
{

    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $leads = LeadsModel::get();
        return view('home', ['leads' => $leads]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }

    public function removeLeads () {
        return view('remove');
    }

    /**
     * Method to remove leads
     * from CAC leads table
     * 
     * @param int $st $nd
     * 
     * @return confirmation msg
     */
    public function removecac ($st, $nd) {
        $range = CacModel::where('id', '>=', $st)->where('id', '<=', $nd)->get();
        if (count($range) == 0) {
            return \redirect('remove')->with('status', 'Range does not exist');
        } else {
            CacModel::where('id', '>=', $st)->where('id', '<=', $nd)->delete();
            return \redirect('remove')->with('status', 'Data deleted successfully');
        }
    }

    /**
     * Method to remove leads
     * from WV leads table
     * 
     * @param int $st $nd
     * 
     * @return confirmation msg
     */
    public function removewv ($st, $nd) {
        $range = LeadsModel::where('id', '>=', $st)->where('id', '<=', $nd)->get();
        dd($range);
        if (count($range) == 0) {
            return \redirect('remove')->with('status', 'Range does not exist');
        } else {
            LeadsModel::where('id', '>=', $st)->where('id', '<=', $nd)->delete();
            return \redirect('remove')->with('status', 'Data deleted successfully');
        }
    }
}
