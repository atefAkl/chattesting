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
        Schema::table('conversations', function (Blueprint $table) {
            // تحويل deleted_at من boolean إلى timestamp
            $table->dropColumn('deleted_at');
            $table->softDeletes(); // يضيف حقل deleted_at بشكل صحيح

            // إضافة الحقول الجديدة
            $table->string('status')->default('active')->after('receiver');
            $table->boolean('is_open')->default(false)->after('status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('conversations', function (Blueprint $table) {
            $table->dropSoftDeletes();
            $table->boolean('deleted_at')->nullable();
            $table->dropColumn(['status', 'is_open']);
        });
    }
};
