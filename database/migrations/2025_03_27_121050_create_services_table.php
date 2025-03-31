<?php

use App\Models\Server;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('services', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(Server::class);
            $table->string('name');
            $table->string('status');
            $table->string('category'); // database / cache / webserver
            $table->string('type'); // postgres / redis / caddy
            $table->string('version'); // 17 / 7 / 2
            $table->string('driver_name');
            $table->string('container_name')->nullable();
            $table->string('container_id')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('services');
    }
};
