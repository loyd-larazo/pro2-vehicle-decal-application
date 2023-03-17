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
        Schema::table('applicants', function (Blueprint $table) {
            $table->string('endorser')->nullable()->after('email_sent');
            $table->string('endorser_id')->nullable()->after('email_sent');
            $table->string('drivers_license')->nullable()->after('email_sent');
            $table->tinyInteger('own_vehicle')->default(0)->unsigned()->default(1)->after('email_sent');
            $table->string('deed_of_sale')->nullable()->after('email_sent');
        });

        Schema::table('users', function (Blueprint $table) {
            $table->string('endorser')->nullable()->after('pnp_id_picture');
            $table->string('endorser_id')->nullable()->after('pnp_id_picture');
            $table->string('drivers_license')->nullable()->after('pnp_id_picture');
            $table->tinyInteger('own_vehicle')->default(0)->unsigned()->default(1)->after('pnp_id_picture');
            $table->string('deed_of_sale')->nullable()->after('pnp_id_picture');
        });

        Schema::table('user_vehicles', function(Blueprint $table) {
            $table->string('cr')->nullable()->after('or_cr');
            $table->renameColumn('or_cr', 'or');
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
            $table->renameColumn('or', 'or_cr');
            $table->dropColumn('cr');
        });

        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('endorser');
            $table->dropColumn('endorser_id');
            $table->dropColumn('drivers_license');
            $table->dropColumn('own_vehicle');
            $table->dropColumn('deed_of_sale');
        });
        
        Schema::table('applicants', function (Blueprint $table) {
            $table->dropColumn('endorser');
            $table->dropColumn('endorser_id');
            $table->dropColumn('drivers_license');
            $table->dropColumn('own_vehicle');
            $table->dropColumn('deed_of_sale');
        });
    }
};
