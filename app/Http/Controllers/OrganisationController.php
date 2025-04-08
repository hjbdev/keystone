<?php

namespace App\Http\Controllers;

use App\Models\Organisation;
use App\Models\Provider;
use Illuminate\Http\Request;
use Inertia\Inertia;

class OrganisationController extends Controller
{
    public function show(Request $request)
    {
        return inertia('organisations/Show', [
            'providers' => Inertia::lazy(fn () => Provider::whereOrganisationId($request->route('organisation'))->get()),
            'organisation' => Organisation::withCount('servers', 'applications', 'members')->findOrFail($request->route('organisation')),
        ]);
    }
}
