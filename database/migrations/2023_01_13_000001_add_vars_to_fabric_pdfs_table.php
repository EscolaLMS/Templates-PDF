<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddVarsToFabricPdfsTable extends Migration
{
    private string $table = 'fabric_pdfs';

    public function up()
    {
        Schema::table(
            $this->table,
            function (Blueprint $table) {
                $table->json('vars')->nullable();
            }
        );
    }

    public function down()
    {
        Schema::table(
            $this->table,
            function (Blueprint $table) {
                $table->dropColumn('vars');
            }
        );
    }
}
