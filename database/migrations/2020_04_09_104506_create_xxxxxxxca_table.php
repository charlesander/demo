<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAddressesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('xxxxxxxca', function (Blueprint $table) {
            $table->id();
            $table->string('name_on')->nullable();
            $table->string('first_name_on')->nullable();
            $table->string('last_name_on')->nullable();
            $table->string('address_line1')->nullable();
            $table->string('address_line2')->nullable();
            $table->string('address_line3')->nullable();
            $table->string('town')->nullable();
            $table->string('county')->nullable();
            $table->string('postcode', 10)->nullable();
            $table->string('phone')->nullable();
            $table->string('country_id')->nullable();
            $table->string('province_code')->nullable();
            $table->string('latitude')->nullable();
            $table->string('longitude')->nullable();
            $table->timestamp('deleted_at')->nullable();
            $table->timestamps();

            $table->foreign('country_id')->references('id')->on('countries');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('xxxxxxxca');
    }
}
