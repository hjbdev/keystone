<?php

use App\Enums\FirewallRuleStatus;
use App\Models\Server;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('firewall_rules', function (Blueprint $table) {
            $table->id();
            $table->string('status')->default(FirewallRuleStatus::NOT_INSTALLED->value);
            $table->foreignIdfor(Server::class);
            $table->string('type');
            $table->string('ports');
            $table->string('from')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('firewall_rules');
    }
};
