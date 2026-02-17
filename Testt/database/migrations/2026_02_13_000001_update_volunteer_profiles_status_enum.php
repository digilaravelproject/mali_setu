<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UpdateVolunteerProfilesStatusEnum extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('volunteer_profiles', function (Blueprint $table) {
            // Change the enum to include 'approved' and 'rejected' statuses
            $table->enum('status', ['active', 'inactive', 'pending', 'approved', 'rejected'])
                ->default('pending')
                ->change();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('volunteer_profiles', function (Blueprint $table) {
            // Revert back to original enum
            $table->enum('status', ['active', 'inactive', 'pending'])
                ->default('pending')
                ->change();
        });
    }
}
