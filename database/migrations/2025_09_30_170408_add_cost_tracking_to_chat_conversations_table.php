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
        Schema::table('chat_conversations', function (Blueprint $table) {
            $table->decimal('total_cost', 10, 6)->default(0)->after('raw_conversation');
            $table->integer('total_tokens')->default(0)->after('total_cost');
            $table->integer('input_tokens')->default(0)->after('total_tokens');
            $table->integer('output_tokens')->default(0)->after('input_tokens');
            $table->string('cost_currency', 3)->default('USD')->after('output_tokens');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('chat_conversations', function (Blueprint $table) {
            $table->dropColumn([
                'total_cost',
                'total_tokens', 
                'input_tokens',
                'output_tokens',
                'cost_currency'
            ]);
        });
    }
};
