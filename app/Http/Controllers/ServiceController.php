<?php

namespace App\Http\Controllers;

use App\Actions\Services\CreateService;
use App\Enums\ServiceCategory;
use App\Enums\ServiceType;
use App\Models\Server;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class ServiceController extends Controller
{
    public function create(Request $request)
    {
        $server = Server::findOrFail($request->route('server'));

        return inertia('services/Create', [
            'server' => $server,
            'services' => config('keystone.services'),
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'category' => ['required', Rule::enum(ServiceCategory::class)],
            'type' => ['required', Rule::enum(ServiceType::class)],
            'version' => ['required', 'string', function ($key, $value, $fail) use ($request) {
                if (!isset(config('keystone.services')[$request->category][$request->type]['versions'][$value])) {
                    $fail('The selected version is invalid.');
                }
            }],
        ]);

        $server = Server::findOrFail($request->route('server'));

        $response = app(CreateService::class)->execute(
            server: $server,
            name: $request->name,
            category: $request->enum('category', ServiceCategory::class),
            type: $request->enum('type', ServiceType::class),
            version: $request->version,
        );

        $service = $response['service'];
        $defaultPassword = $response['defaultPassword'];

        return redirect()->route('servers.show', $server)->with([
            'success' => 'Service created successfully',
            'defaultPassword' => $defaultPassword,
            'service' => $service,
        ]);
    }
}
