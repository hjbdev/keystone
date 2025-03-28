<?php

namespace App\Http\Controllers;

use App\Models\Environment;
use Illuminate\Http\Request;

class EnvironmentController extends Controller
{
    public function show(Request $request)
    {
        $id = $request->route('environment');
        return inertia('environments/Show', [
            'environment' => Environment::findOrFail($id),
        ]);
    }
}
