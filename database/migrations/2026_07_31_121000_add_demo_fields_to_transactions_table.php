<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('transactions', function (Blueprint $table) {
            $table->string('coupon_code')->nullable()->after('customer_phone');
            $table->unsignedInteger('discount_amount')->default(0)->after('coupon_code');
            $table->timestamp('checked_in_at')->nullable()->after('snap_token');
            $table->foreignId('checked_in_by')->nullable()->after('checked_in_at')->constrained('users')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('transactions', function (Blueprint $table) {
            $table->dropConstrainedForeignId('checked_in_by');
            $table->dropColumn(['coupon_code', 'discount_amount', 'checked_in_at']);
        });
    }
};
