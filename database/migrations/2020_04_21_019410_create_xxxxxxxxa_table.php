<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAmbassadorsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('xxxxxxxxa', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('title')->nullable();
            $table->string('first_name');
            $table->string('last_name');
            $table->string('email');
            $table->date('DOB')->nullable();
            $table->unsignedBigInteger('legal_status_id');
            $table->boolean('vat_registered')->default(0);
            $table->boolean('accepted_terms_and_conditions')->default(0);
            $table->string('picture_path')->nullable();
            $table->unsignedBigInteger('home_address')->nullable()->index();
            $table->unsignedBigInteger('delivery_address')->nullable()->index();
            $table->string('telephone')->nullable();
            $table->string('mobile')->nullable();
            $table->boolean('active')->default(0);

            // Customers.RankID
            $table->unsignedInteger('rank_id')->nullable()->index();
            // Customers.CustomerTypeID
            $table->unsignedInteger('type_id')->nullable()->index();
            // Customers.CustomerStatusID
            $table->unsignedInteger('status_id')->nullable()->index();
            // Customers.SponsorID
            $table->unsignedInteger('sponsor_id')->nullable()->index();
            // Customers.CustomerId
            $table->unsignedInteger('exigo_id')->nullable()->index();
            $table->unsignedInteger('parent_id')->nullable()->index();

            $table->timestamp('deleted_at')->nullable();
            $table->timestamps();

            $table->foreign('legal_status_id')->references('id')->on('ambassador_legal_statuses');
            $table->foreign('home_address')->references('id')->on('addresses');
            $table->foreign('delivery_address')->references('id')->on('addresses');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('xxxxxxxxa');
    }
}
