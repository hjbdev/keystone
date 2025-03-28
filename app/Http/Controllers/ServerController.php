<?php

namespace App\Http\Controllers;

use App\Models\Organisation;
use Illuminate\Http\Request;

class ServerController extends Controller
{
    public function index(Request $request)
    {
        $organisation = Organisation::findOrFail($request->route('organisation'));
        return inertia('servers/Index', [
            'servers' => $organisation->servers()->paginate(30),
        ]);
    }
}
