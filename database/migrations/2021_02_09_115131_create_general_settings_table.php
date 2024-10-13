<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateGeneralSettingsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('general_settings', function (Blueprint $table) {
            $table->id();
            $table->string('label');
            $table->string('unique_name');
            $table->string('type')->default('text');
            $table->string('value')->nullable();
            $table->string('options')->nullable();
            $table->string('class')->default('form-control');
            $table->string('extra')->nullable();
            $table->string('hint')->nullable();
            $table->enum('status', ['active', 'inactive'])->default('active');
            $table->integer('order_number')->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('general_settings');
    }
}
