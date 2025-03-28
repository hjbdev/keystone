<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class OrganisationController extends Controller
{
    public function show()
    {
        return inertia('organisations/Show');
    }
}
