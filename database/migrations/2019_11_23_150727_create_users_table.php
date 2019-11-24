<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('users', function (Blueprint $table) {
            $table->bigIncrements('id');

            $table->unsignedBigInteger('vk_user_id')->unique();

            $table->boolean('notifications_are_enabled')->default(false);
            $table->boolean('messages_are_enabled')->default(false);

            $table->string('first_name')->nullable();
            $table->string('last_name')->nullable();
            $table->string('avatar_200')->nullable();

            $table->date('bdate')->nullable();
            $table->tinyInteger('sex')->default(0)->index();


            $table->tinyInteger('utc_offset')->nullable();

            $table->dateTime('visited_at')->nullable();

            $table->boolean('is_admin')->default(false);

            $table->timestamps();
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
}
