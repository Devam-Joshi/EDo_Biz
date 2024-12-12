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
        Schema::create('tbl_account_group', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('code', 10)->nullable();
            $table->string('name', 191);
            $table->timestamps();
            $table->integer('status')->nullable()->default(1);
            $table->integer('parent_id')->nullable()->default(0);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tbl_account_group');
    }
};
