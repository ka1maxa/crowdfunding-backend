<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('loans', function (Blueprint $table) {
            $table->id();
            $table->foreignId('borrower_id')->constrained('users')->onDelete('cascade');
            $table->string('title');
            $table->text('description');
            $table->decimal('amount_requested', 12, 2);
            $table->decimal('interest_rate', 5, 2)->default(10.00); // მაგ. 10.00%
            $table->decimal('total_amount_to_repay', 12, 2);
            $table->decimal('amount_raised', 12, 2)->default(0.00);
            $table->integer('term_months');
            $table->text('collateral_info')->nullable();
            $table->enum('status', ['draft', 'active', 'fully_funded', 'closed'])->default('active');
            $table->text('ai_summary')->nullable();
            $table->json('ai_risk_score')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('loans');
    }
};