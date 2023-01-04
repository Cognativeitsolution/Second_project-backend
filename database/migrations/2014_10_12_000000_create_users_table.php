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
            $table->string('uuid', 100)->nullable();
            $table->string('name');
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
