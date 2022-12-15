<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        // $this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index(Request $request)
    {
        if (isset($request->permission_error) && $request->permission_error) {
            return view('home')->with(['permission_error' => $request->permission_error]);
        }
        return view('home');
    }

    public function about()
    {
        return view('about');
    }
}
