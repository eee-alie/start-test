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
        Schema::create('successful_transactions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('transaction_id')->constrained();
            $table->string('inquiry_type');
            $table->string('inquiry_ref_code');
            $table->string('inquiry_date');
            $table->string('inquiry_time');
            $table->string('inquiry_sequence');
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
        Schema::dropIfExists('successful_transactions');
    }
};
