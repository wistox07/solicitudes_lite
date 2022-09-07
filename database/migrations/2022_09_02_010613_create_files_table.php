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
        Schema::create('files', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger("user_id");
            $table->unsignedBigInteger("request_id");
            $table->unsignedBigInteger("state_file_id");
            $table->string("name");
            $table->string("path");
            $table->integer("size");
            $table->string("extension");
            $table->foreign("user_id")->references("id")->on("users");
            $table->foreign("request_id")->references("id")->on("requests");
            $table->foreign("state_file_id")->references("id")->on("state_files");
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
        Schema::dropIfExists('files');
    }
};
