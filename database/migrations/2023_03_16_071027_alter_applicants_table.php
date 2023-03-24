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
            $table->tinyInteger('other_office')->default(0)->unsigned()->after('designation');
        });

        Schema::table('users', function (Blueprint $table) {
            $table->string('endorser')->nullable()->after('pnp_id_picture');
            $table->string('endorser_id')->nullable()->after('pnp_id_picture');
            $table->string('drivers_license')->nullable()->after('pnp_id_picture');
            $table->tinyInteger('other_office')->default(0)->unsigned()->after('designation');
        });

        Schema::table('user_vehicles', function(Blueprint $table) {
            $table->string('cr')->nullable()->after('or_cr');
            $table->bigInteger('applicant_id')->unsigned()->nullable()->after('id');
            $table->tinyInteger('own_vehicle')->default(0)->unsigned()->after('or_cr');
            $table->string('deed_of_sale')->nullable()->after('or_cr');
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
            $table->dropColumn('applicant_id');
            $table->dropColumn('own_vehicle');
            $table->dropColumn('deed_of_sale');
            $table->dropColumn('cr');
        });

        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('endorser');
            $table->dropColumn('endorser_id');
            $table->dropColumn('drivers_license');
            $table->dropColumn('other_office');
        });
        
        Schema::table('applicants', function (Blueprint $table) {
            $table->dropColumn('endorser');
            $table->dropColumn('endorser_id');
            $table->dropColumn('drivers_license');
            $table->dropColumn('other_office');
        });
    }
};
