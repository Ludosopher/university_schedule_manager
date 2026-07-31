<?php

namespace App\Http\Controllers;

use Illuminate\Contracts\Support\Renderable;
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
     * Show the application home page.
     */
    public function index(Request $request): Renderable
    {
        if (isset($request->permission_error) && $request->permission_error) {
            return view('home')->with(['permission_error' => $request->permission_error]);
        }
        return view('home');
    }

    /**
     * Show the application presentation.
     */
    public function about(): Renderable
    {
        return view('about');
    }
}
