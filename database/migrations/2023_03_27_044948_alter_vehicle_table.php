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
        Schema::table('user_vehicles', function (Blueprint $table) {
            $table->tinyInteger('status')->default(1)->unsigned()->after('expiration_date');
            $table->string('year_model')->change();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('user_vehicles', function (Blueprint $table) {
            $table->dropColumn('status');
            $table->year('year_model')->change();
        });
    }
};
