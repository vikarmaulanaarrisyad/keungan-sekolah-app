<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index()
    {
        $user = Auth::user();

        if ($user->hasRole('admin')) {
            return view('admin.dashboard.index');
        } else if ($user->hasRole('guru')) {
            return view('guru.dashboard.index');
        } else {
            return view('siswa.dashboard.index');
        }
    }
}
