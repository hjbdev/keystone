<?php

use App\Models\Organisation;
use App\Models\Provider;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('networks', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(Organisation::class);
            $table->foreignIdFor(Provider::class);
            $table->string('external_id')->nullable();
            $table->string('type');
            $table->string('name');
            $table->string('ip_range');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('networks');
    }
};
