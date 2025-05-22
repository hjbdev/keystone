<?php

use App\Models\Deployment;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('steps', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(Deployment::class);
            $table->string('name');
            $table->integer('order');
            $table->string('status');
            $table->longText('script');
            $table->longText('logs')->nullable();
            $table->longText('error_logs')->nullable();
            $table->text('secrets')->nullable();
            $table->dateTime('started_at')->nullable();
            $table->dateTime('finished_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('steps');
    }
};
