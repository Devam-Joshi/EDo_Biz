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
        Schema::create('tbl_account', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('acCode', 10)->nullable();
            $table->string('name', 191);
            $table->string('email', 191)->nullable();
            $table->string('phone', 191)->nullable();
            $table->string('address', 191)->nullable();
            $table->integer('city_id')->nullable();
            $table->integer('state_id')->nullable();
            $table->string('city', 191)->nullable();
            $table->string('state', 100)->nullable();
            $table->string('country', 100)->nullable()->default('India');
            $table->string('pinCode', 10)->nullable();
            $table->string('type', 50)->nullable();
            $table->string('acGroup', 50)->nullable();
            $table->integer('priceGroup')->nullable()->default(1)->comment('1: Retail |2:wholesale');
            $table->string('shop_name', 191)->nullable();
            $table->string('contactPerson', 50)->nullable();
            $table->string('photo', 191)->nullable();
            $table->string('account_holder', 191)->nullable();
            $table->string('account_number', 191)->nullable();
            $table->string('bank_name', 191)->nullable();
            $table->string('bank_branch', 191)->nullable();
            $table->timestamps();
            $table->string('CSTN_No', 50)->nullable();
            $table->string('GSTN_No', 50)->nullable();
            $table->float('openingBalance')->nullable()->default(0);
            $table->string('opening_type', 10)->nullable();
            $table->integer('current_balance')->default(0);
            $table->integer('creditDays')->nullable()->default(0);
            $table->integer('creditAlertDays')->nullable();
            $table->integer('status')->nullable()->default(1);
            $table->integer('discount_rate')->nullable()->default(0);
            $table->integer('block_status')->nullable()->default(0);
            $table->string('block_remark', 100)->nullable();
            $table->integer('block_by')->nullable();
            $table->string('allow_login', 1)->nullable()->default('N');
            $table->integer('overdue_amount')->nullable();
            $table->date('overdue_asOnDate')->nullable();
            $table->string('overdue_note', 100)->nullable();
            $table->string('referred_by', 100)->nullable();
            $table->string('term_cond')->nullable();
            $table->string('transport', 100)->nullable();
            $table->integer('payby')->nullable();
            $table->integer('user_id')->nullable();
            $table->integer('visit_type')->nullable()->comment('0:offline | 1:Online');
            $table->foreignId('branch_id')->constrained('branches')->onUpdate('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tbl_account');
    }
};
