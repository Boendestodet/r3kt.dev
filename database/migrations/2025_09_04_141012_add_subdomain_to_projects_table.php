<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('projects', function (Blueprint $table) {
            $table->string('subdomain')->unique()->nullable()->after('name');
            $table->string('custom_domain')->nullable()->after('subdomain');
            $table->boolean('dns_configured')->default(false)->after('custom_domain');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('projects', function (Blueprint $table) {
            $table->dropColumn(['subdomain', 'custom_domain', 'dns_configured']);
        });
    }
};
