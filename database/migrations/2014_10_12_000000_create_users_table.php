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
    Schema::create('applicants', function (Blueprint $table) {
      $table->id();
      $table->string('email')->unique();
      $table->string('password');
      $table->string('firstname');
      $table->string('middlename')->nullable();
      $table->string('lastname');
      $table->string('rank');
      $table->text('address');
      $table->string('designation');
      $table->text('office');
      $table->string('mobile');
      $table->string('telephone')->nullable();
      $table->string('pnp_id_picture');
      $table->bigInteger('verified_by')->unsigned()->nullable();
      $table->dateTime('verified_date')->nullable();
      $table->enum('status', ['pending', 'approved', 'rejected', 'request_change'])->default('pending');
      $table->text('remarks')->nullable();
      $table->tinyInteger('email_sent')->default(0)->unsigned();
      $table->timestamps();
    });

    Schema::create('users', function (Blueprint $table) {
      $table->id();
      $table->enum('type', ['user', 'issuer', 'admin'])->default('user');
      $table->string('email')->unique();
      $table->string('password');
      $table->string('firstname');
      $table->string('middlename')->nullable();
      $table->string('lastname');
      $table->string('rank');
      $table->text('address');
      $table->string('designation');
      $table->text('office');
      $table->string('mobile');
      $table->string('telephone')->nullable();
      $table->string('pnp_id_picture')->nullable();
      $table->tinyInteger('status')->default(0)->unsigned()->default(1);
      $table->timestamps();
    });

    Schema::create('user_vehicles', function (Blueprint $table) {
      $table->id();
      $table->bigInteger('user_id')->unsigned();
      $table->string('make');
      $table->string('plate_number');
      $table->string('model');
      $table->year('year_model');
      $table->string('color');
      $table->string('engine_number');
      $table->string('chassis_number');
      $table->enum('type', ['motor', 'car']);
      $table->string('or_cr');
      $table->bigInteger('verified_by')->unsigned()->nullable();
      $table->dateTime('verified_date')->nullable();
      $table->enum('verified_status', ['approved', 'rejected', 'pending'])->default('pending');
      $table->string('code')->nullable();
      $table->text('qr_code')->nullable();
      $table->bigInteger('issued_by')->unsigned()->nullable();
      $table->dateTime('issued_date')->nullable();
      $table->enum('issued_status', ['pending', 'issued', 'rejected', 'renewal', 'expired'])->default('pending');
      $table->date('expiration_date')->nullable();
      $table->timestamps();

      $table->foreign('verified_by')->references('id')->on('users')->onDelete('cascade');
      $table->foreign('issued_by')->references('id')->on('users')->onDelete('cascade');
    });

    Schema::create('vehicle_images', function (Blueprint $table) {
      $table->id();
      $table->bigInteger('user_vehicle_id')->unsigned();
      $table->string('image');
      $table->timestamps();

      $table->foreign('user_vehicle_id')->references('id')->on('user_vehicles')->onDelete('cascade');
    });
  }

  /**
   * Reverse the migrations.
   *
   * @return void
   */
  public function down()
  {
    Schema::dropIfExists('vehicle_images');
    Schema::dropIfExists('user_vehicles');
    Schema::dropIfExists('users');
    Schema::dropIfExists('applicants');
  }
};
