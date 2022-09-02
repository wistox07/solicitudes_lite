<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('requests', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger("register_id");
            $table->unsignedBigInteger("updater_id");
            $table->unsignedBigInteger("petitioner_id");
            $table->unsignedBigInteger("attendant_id");
            $table->unsignedBigInteger("type_id");
            $table->unsignedBigInteger("priority_id")->nullable();
            $table->unsignedBigInteger("satisfaction_id")->nullable();
            $table->unsignedBigInteger("state_id");

            $table->foreign("register_id")->references("id")->on("users");
            $table->foreign("updater_id")->references("id")->on("users");

            $table->foreign("petitioner_id")->references("id")->on("users");
            $table->foreign("attendant_id")->references("id")->on("users");
            $table->foreign("type_id")->references("id")->on("type_requests");
            $table->foreign("priority_id")->references("id")->on("priority_requests");
            $table->foreign("satisfaction_id")->references("id")->on("satisfaction_requests");
            $table->foreign("state_id")->references("id")->on("state_requests");

            $table->string("title");
            $table->text("description");
            $table->dateTime("start_date")->useCurrent();
            $table->dateTime("closing_date");
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('requests');
    }
};
