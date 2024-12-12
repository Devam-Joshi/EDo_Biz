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
        Schema::create('tbl_financial_logs', function (Blueprint $table) {
            $table->integer('id', true);
            $table->string('reference_no', 50)->nullable();
            $table->string('reference_id', 20)->nullable();
            $table->enum('reference_type', ['sale', 'purchase', 'receipt', 'payment', 'credit_note', 'debit_note', 'expenses', 'sale-return', 'purchase-return', 'Opening balance', 'transfer'])->nullable();
            $table->enum('txn_type', ['debit', 'credit', 'other'])->nullable();
            $table->enum('txn_mode', ['cash', 'bank', 'other'])->nullable();
            $table->enum('txn_method', ['CASH', 'CHEQUE', 'NEFT', 'IMPS', 'UPI', 'other'])->nullable();
            $table->date('txn_date');
            $table->integer('party_id')->nullable()->default(0);
            $table->double('party_prevBal')->nullable();
            $table->double('txn_amount');
            $table->double('party_currentBal')->nullable();
            $table->string('remark', 200)->nullable();
            $table->string('payment_referrence_no', 50)->nullable();
            $table->integer('payment_bank_id')->nullable();
            $table->dateTime('created_at')->nullable();
            $table->dateTime('updated_at')->nullable();
            $table->integer('status')->nullable()->default(1);
            $table->integer('user_id')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tbl_financial_logs');
    }
};
