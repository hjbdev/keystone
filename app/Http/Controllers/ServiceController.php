<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class ServiceController extends Controller
{
    public function create(Request $request)
    {
        $server = $request->route('server');
        return inertia('services/Create', [
            'server' => $server,
        ]);
    }
}
