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
            $table->unsignedBigInteger("updater_id")->nullable();
            $table->unsignedBigInteger("petitioner_id");
            $table->unsignedBigInteger("agent_id");
            $table->unsignedBigInteger("type_request_id");
            $table->unsignedBigInteger("priority_request_id")->nullable();
            $table->unsignedBigInteger("satisfaction_request_id")->nullable();
            $table->unsignedBigInteger("state_request_id");

            $table->foreign("register_id")->references("id")->on("users");
            $table->foreign("updater_id")->references("id")->on("users");

            $table->foreign("petitioner_id")->references("id")->on("users");
            $table->foreign("agent_id")->references("id")->on("users");
            $table->foreign("type_request_id")->references("id")->on("type_requests");
            $table->foreign("priority_request_id")->references("id")->on("priority_requests");
            $table->foreign("satisfaction_request_id")->references("id")->on("satisfaction_requests");
            $table->foreign("state_request_id")->references("id")->on("state_requests");

            $table->string("title");
            $table->text("description");
            $table->dateTime("register_date")->useCurrent();
            $table->dateTime("start_date")->nullable();
            $table->dateTime("tentative_end_date");
            $table->dateTime("end_date")->nullable();
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
