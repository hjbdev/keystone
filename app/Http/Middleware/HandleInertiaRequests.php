<?php

namespace App\Http\Middleware;

use App\Models\Application;
use App\Models\Organisation;
use Illuminate\Http\Request;
use Inertia\Middleware;
use Tighten\Ziggy\Ziggy;

class HandleInertiaRequests extends Middleware
{
    protected $rootView = 'app';

    public function version(Request $request): ?string
    {
        return parent::version($request);
    }

    /**
     * Define the props that are shared by default.
     *
     * @see https://inertiajs.com/shared-data
     *
     * @return array<string, mixed>
     */
    public function share(Request $request): array
    {
        return [
            ...parent::share($request),
            'name' => config('app.name'),
            'organisation' => $request->route('organisation') ? Organisation::with('applications')->findOrFail($request->route('organisation')) : null,
            'application' => $request->route('application') ? Application::with('environments')->findOrFail($request->route('application')) : null,
            'flash' => [
                'server_credentials' => $request->session()->has('sudo_password') ? [
                    'sudo_password' => $request->session()->get('sudo_password'),
                ] : null,
            ],
            'auth' => [
                'user' => $request->user()?->load('organisations'),
            ],
            'ziggy' => [
                ...(new Ziggy)->toArray(),
                'location' => $request->url(),
            ],
        ];
    }
}
