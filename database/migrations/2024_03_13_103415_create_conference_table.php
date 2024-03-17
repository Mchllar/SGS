<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateConferenceTable extends Migration
{
    public function up()
    {
        Schema::create('conferences', function (Blueprint $table) {
            $table->id();
            $table->string('conference_title');
            $table->string('title_of_paper');
            $table->string('status');
            $table->string('file_upload');
           //$table->boolean('approved_by_director_graduate_studies')->default(false);
            //$table->boolean('approved_by_principal_supervisor')->default(false);
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
        Schema::dropIfExists('conference');
    }
};