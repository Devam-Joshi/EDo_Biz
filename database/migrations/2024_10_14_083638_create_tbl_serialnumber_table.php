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
        Schema::create('tbl_serialnumber', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('name', 200);
            $table->string('prefix', 5);
            $table->string('length', 10)->nullable();
            $table->string('financialYear', 8)->nullable();
            $table->integer('next_number')->nullable()->default(0);
            $table->enum('type', ['master', 'transaction'])->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tbl_serialnumber');
    }
};
