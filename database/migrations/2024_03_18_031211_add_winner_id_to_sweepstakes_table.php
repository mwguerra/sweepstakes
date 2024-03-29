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
        Schema::table('sweepstakes', function (Blueprint $table) {
            $table->foreignId('winner_id')->after('is_winner_notified')->nullable()->constrained('participants')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('sweepstakes', function (Blueprint $table) {
            $table->dropConstrainedForeignId('winner_id');
        });
    }
};
