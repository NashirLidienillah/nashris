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
        // 1. Indexes on attendances table
        Schema::table('attendances', function (Blueprint $table) {
            $table->index('date');
            $table->index('status');
            $table->index(['date', 'status']);
        });

        // 2. Indexes on leave_requests table
        Schema::table('leave_requests', function (Blueprint $table) {
            $table->index('status');
            $table->index('start_date');
            $table->index('end_date');
            $table->index(['status', 'start_date']);
        });

        // 3. Indexes on payrolls table
        Schema::table('payrolls', function (Blueprint $table) {
            $table->index('year');
            $table->index('status');
        });

        // 4. Indexes on employees table
        Schema::table('employees', function (Blueprint $table) {
            $table->index('contract_type');
            $table->index('contract_end_date');
            $table->index('join_date');
            $table->index(['contract_type', 'contract_end_date']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('attendances', function (Blueprint $table) {
            $table->dropIndex(['date']);
            $table->dropIndex(['status']);
            $table->dropIndex(['date', 'status']);
        });

        Schema::table('leave_requests', function (Blueprint $table) {
            $table->dropIndex(['status']);
            $table->dropIndex(['start_date']);
            $table->dropIndex(['end_date']);
            $table->dropIndex(['status', 'start_date']);
        });

        Schema::table('payrolls', function (Blueprint $table) {
            $table->dropIndex(['year']);
            $table->dropIndex(['status']);
        });

        Schema::table('employees', function (Blueprint $table) {
            $table->dropIndex(['contract_type']);
            $table->dropIndex(['contract_end_date']);
            $table->dropIndex(['join_date']);
            $table->dropIndex(['contract_type', 'contract_end_date']);
        });
    }
};
