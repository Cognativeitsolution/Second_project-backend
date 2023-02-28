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
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('uuid', 100)->nullable();
            $table->string('email')->unique();
            $table->timestamp('email_verified_at')->nullable();
            $table->tinyInteger('status')->default(1); // If admin want to inactive this account ?? 0
            $table->string('verify_code')->nullable();
            $table->string('password')->nullable();
            $table->string('contact', 25)->nullable();
            $table->string('sin_number', 25)->nullable();
            $table->tinyInteger('is_super_admin')->default(0)->index(); // Super Admin

            $table->tinyInteger('is_agency')->default(0)->index(); // Agency Admin
                $table->tinyInteger('is_company')->default(0)->index(); // Company Admin
                $table->tinyInteger('is_worker')->default(1)->index(); // Company Worker
                    $table->tinyInteger('parent_id')->default(0);

            $table->datetime('last_login_at')->nullable();
            $table->string('last_login_ip')->nullable();

            $table->string('mobile_number', 30)->nullable();
            $table->string('fax', 30)->nullable();
            $table->string('street_number', 30)->nullable();
            $table->string('street_name', 30)->nullable();
            $table->string('unit_no', 30)->nullable();

            $table->bigInteger('country_id')->nullable();
            $table->bigInteger('state_id')->nullable();
            $table->bigInteger('city_id')->nullable();

            $table->string('postal_code', 15)->nullable();
            $table->string('contact_person_name', 30)->nullable();
            $table->string('designation', 20)->nullable();
            $table->string('cell_number', 20)->nullable();

            $table->rememberToken();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('users');
    }
};
