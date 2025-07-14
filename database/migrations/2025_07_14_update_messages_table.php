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
        Schema::table('messages', function (Blueprint $table) {
            // تحويل deleted_at من boolean إلى timestamp
            $table->dropColumn('deleted_at');
            $table->softDeletes(); // يضيف حقل deleted_at بشكل صحيح

            // إضافة حقل receiver
            $table->unsignedBigInteger('receiver')->after('sender')->constrained('users');

            // إضافة حقول لحالة القراءة
            $table->boolean('is_read')->default(false)->after('message');
            $table->timestamp('read_at')->nullable()->after('is_read');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('messages', function (Blueprint $table) {
            $table->dropSoftDeletes();
            $table->boolean('deleted_at')->nullable();
            $table->dropColumn(['receiver', 'is_read', 'read_at']);
        });
    }
};
