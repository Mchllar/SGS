<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('religion', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->timestamps();
        });

        // Populate religion data
        $religionData = [
            [ 'Catholic' ],
            [ 'Protestant' ],
            [ 'Seventh Day Adventist' ],
            [ 'Jehovah Witness' ],
            [ 'Islam' ],
            [ 'Hinduism' ],
            [ 'Traditionalist' ]
        ];

        DB::table('religion')->insert($religionData);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('religion');
    }
};
