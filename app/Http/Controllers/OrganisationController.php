<?php

namespace App\Http\Controllers;

class OrganisationController extends Controller
{
    public function show()
    {
        return inertia('organisations/Show');
    }
}
