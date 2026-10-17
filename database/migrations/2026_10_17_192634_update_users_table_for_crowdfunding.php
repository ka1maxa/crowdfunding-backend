<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('phone_number')->after('email');
            $table->string('id_number')->unique()->after('phone_number');
            $table->enum('role', ['investor', 'borrower', 'admin'])->default('investor')->after('password');
            $table->boolean('agreed_to_terms_and_privacy')->default(false)->after('role');
            $table->timestamp('terms_agreed_at')->nullable()->after('agreed_to_terms_and_privacy');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn([
                'phone_number',
                'id_number',
                'role',
                'agreed_to_terms_and_privacy',
                'terms_agreed_at'
            ]);
        });
    }
};