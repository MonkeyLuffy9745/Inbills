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
        Schema::create('invoices', function (Blueprint $table) {
            $table->id();
            $table->string('invoice_number')->unique();
            $table->date('invoice_date');
            $table->decimal('invoice_amount', 10, 2);
            $table->enum('invoice_status', ['pending', 'paid', 'overdue'])->default('pending');
            $table->date('invoice_due_date');
            $table->string('payement_method');
            $table->date('payement_date');
            $table->decimal('vat', 10, 2);
            $table->enum('currency', ['EUR', 'USD', 'GBP'])->default('EUR');
            $table->text('notes')->nullable();
            $table->foreignId('client_id')->constrained('clients')->onDelete('cascade');
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('invoices');
    }
};
