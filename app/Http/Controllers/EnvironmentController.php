<?php

namespace App\Http\Controllers;

use App\Enums\ServerStatus;
use App\Models\Environment;
use Illuminate\Http\Request;

class EnvironmentController extends Controller
{
    public function show(Request $request)
    {
        $id = $request->route('environment');
        $environment = Environment::with('application')->findOrFail($id);

        return inertia('environments/Show', [
            'environment' => $environment,
            'servers' => inertia()->optional(function () use ($environment) {
                return $environment
                    ->application
                    ?->organisation
                    ?->servers()
                    ->where('status', ServerStatus::ACTIVE)
                    ->with('services')
                    ->get() ?? [];
            }),
        ]);
    }
}
