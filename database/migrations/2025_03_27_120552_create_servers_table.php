<?php

use App\Models\Network;
use App\Models\Organisation;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('servers', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(Organisation::class);
            $table->foreignIdFor(Network::class, 'external_network_id');
            $table->foreignIdFor(Network::class, 'internal_network_id');
            $table->string('name');
            $table->string('provider');
            $table->string('provider_id');
            $table->string('ipv4');
            $table->string('ipv6');
            $table->string('private_ip');
            $table->string('provider_status');
            $table->string('internal_ip');
            $table->integer('internal_ip_ending');
            $table->text('internal_public_key')->nullable();
            $table->string('status');
            $table->string('region');
            $table->string('os');
            $table->string('plan');
            $table->string('user');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('servers');
    }
};
