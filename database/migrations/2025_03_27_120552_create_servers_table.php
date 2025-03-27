<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('servers', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('provider');
            $table->string('provider_id');
            $table->string('ipv4');
            $table->string('ipv6');
            $table->string('provider_status');
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
