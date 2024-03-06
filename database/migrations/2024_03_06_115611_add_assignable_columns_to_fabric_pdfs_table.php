<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddAssignableColumnsToFabricPdfsTable extends Migration
{

    public function up(): void
    {
        Schema::table('fabric_pdfs', function (Blueprint $table) {
            $table->nullableMorphs('assignable');
        });
    }

    public function down(): void
    {
        Schema::table('fabric_pdfs', function (Blueprint $table) {
            $table->dropMorphs('assignable');
        });
    }
}
