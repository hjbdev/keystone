<?php

namespace App\Http\Controllers;

use App\Models\Organisation;
use Illuminate\Http\Request;

class ApplicationController extends Controller
{
    public function index(Request $request)
    {
        $organisation = Organisation::with('applications')->findOrFail($request->route('organisation'));

        return inertia('applications/Index', [
            'applications' => $organisation->applications,
        ]);
    }

    public function show(Request $request)
    {
        $id = $request->route('application');

        return inertia('applications/Show');
    }
}
