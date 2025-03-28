<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class ApplicationController extends Controller
{
    public function show(Request $request)
    {
        return inertia('Applications/Show', [
            // 'application' => $request->route('application'),
        ]);
    }
}
