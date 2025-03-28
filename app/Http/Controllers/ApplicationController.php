<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class ApplicationController extends Controller
{
    public function show(Request $request)
    {
        $id = $request->route('application');
        return inertia('applications/Show');
    }
}
