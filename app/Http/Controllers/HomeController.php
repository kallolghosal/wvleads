<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\LeadsModel;

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index()
    {
        $leads = LeadsModel::select('*')->distinct('phone')->paginate(12);
        $count = LeadsModel::count();
        $unique = LeadsModel::select('phone')->groupBy('phone')->get();
        return view('home', ['leads' => $leads, 'count' => $count, 'unique' => $unique]);
    }
}
