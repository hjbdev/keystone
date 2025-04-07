<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class ProvisionScript extends Controller
{
    public function __invoke(Request $request)
    {
        $validated = $request->validate([
            'sudo_password' => ['required', 'string'],
            'hostname' => ['required', 'string'],
            'server_id' => ['required', 'integer', 'exists:servers,id'],
        ]);

        $script = file_get_contents(base_path('provision.sh'));

        $keystonePublicKey = file_get_contents(storage_path('app/private/ssh/id_ed25519.pub'));

        $script = str_replace('[!hostname!]', $validated['hostname'], $script);
        $script = str_replace('[!sudo_password!]', $validated['sudo_password'], $script);
        $script = str_replace('[!server_id!]', $validated['server_id'], $script);
        $script = str_replace('[!keystonepublickey!]', $keystonePublicKey, $script);
        $script = str_replace('[!callback!]', route('provision.callback'), $script);

        return response($script)
            ->header('Content-Type', 'text/plain');
    }
}
