<?php

use App\Models\Organisation;
use App\Models\User;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('organisation_user', function (Blueprint $table) {
            $table->foreignIdFor(Organisation::class);
            $table->foreignIdFor(User::class);
            $table->string('role')->default('member');
            $table->primary(['organisation_id', 'user_id']);
            $table->unique(['organisation_id', 'user_id']);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('organisation_user');
    }
};
