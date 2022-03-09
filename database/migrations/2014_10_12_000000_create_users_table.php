<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->boolean('status')->default(true);
            $table->string('identifier')->default('12345');
            $table->string('mobile')->unique();
            $table->string('password');
            $table->string('first_name')->nullable();
            $table->string('last_name')->nullable();
            $table->string('bank_account_number')->nullable();
            $table->string('cash')->default(0);
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
        Schema::dropIfExists('users');
    }
};
