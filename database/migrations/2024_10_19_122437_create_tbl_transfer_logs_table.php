<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTransferLogsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tbl_transfer_logs', function (Blueprint $table) {
            $table->id();  // Auto-incrementing ID column (equivalent to int NOT NULL AUTO_INCREMENT)
            $table->string('reference_no', 50)->nullable();
            $table->string('reference_id', 20)->charset('latin1')->collation('latin1_danish_ci')->nullable();
            $table->enum('reference_type', [
                'sale', 'purchase', 'receipt', 'payment', 'credit_note', 'debit_note',
                'expenses', 'sale-return', 'purchase-return', 'Opening balance', 'transfer'
            ])->nullable();
            $table->enum('txn_type', ['debit', 'credit', 'other'])->nullable();
            $table->enum('txn_mode', ['cash', 'bank', 'other'])->nullable();
            $table->enum('txn_method', ['CASH', 'CHEQUE', 'NEFT', 'IMPS', 'UPI', 'other'])->nullable();
            $table->date('txn_date');
            $table->unsignedBigInteger('receiver_party_id')->nullable(); // Foreign Key
            $table->unsignedBigInteger('payer_party_id')->nullable(); // Foreign Key
            $table->double('txn_amount', 10, 2);
            $table->string('remark', 200)->nullable();
            $table->string('payment_referrence_no', 50)->nullable();
            $table->unsignedBigInteger('payment_bank_id')->nullable(); // Foreign Key
            $table->unsignedBigInteger('user_id')->nullable(); // Foreign Key
            $table->unsignedBigInteger('branch_id')->nullable(); // Foreign Key
            $table->timestamps(); // created_at and updated_at

            // Foreign key constraints
            $table->foreign('receiver_party_id')->references('id')->on('tbl_account')->onDelete('set null');
            $table->foreign('payer_party_id')->references('id')->on('tbl_account')->onDelete('set null');
            $table->foreign('payment_bank_id')->references('id')->on('tbl_Account')->onDelete('set null');
            $table->foreign('branch_id')->references('id')->on('branches')->onDelete('set null');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('tbl_transfer_logs');
    }
}

};
