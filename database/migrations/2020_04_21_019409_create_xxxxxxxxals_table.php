<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAmbassadorLegalStatusesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('xxxxxxxxals', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->timestamp('deleted_at')->nullable();
            $table->timestamps();
        });

        $seeder = new AmbassadorLegalStatusSeeder();
        $seeder->run();
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('xxxxxxxxals');
    }
}
