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
        Schema::table('transactions', function (Blueprint $table) {
            if (!Schema::hasColumn('transactions', 'borrow_date')) {
                $table->dateTime('borrow_date')->after('status');
            }
            if (!Schema::hasColumn('transactions', 'return_date')) {
                $table->dateTime('return_date')->nullable()->after('due_date');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('transactions', function (Blueprint $table) {
            $table->dropColumn(['borrow_date', 'return_date']);
        });
    }
}; 