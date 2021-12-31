<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateFabricPdfsTable extends Migration
{
    private string $table = 'fabric_pdfs';

    public function up()
    {
        Schema::create(
            $this->table,
            function (Blueprint $table) {
                $table->id('id');
                $table->foreignId('user_id')->index();
                $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
                $table->foreignId('template_id')->index();
                $table->foreign('template_id')->references('id')->on('templates')->onDelete('cascade');
                $table->string('path')->nullable();
                $table->json('content');
                $table->timestamps();
            }
        );
    }

    public function down()
    {
        Schema::dropIfExists($this->table);
    }
}
