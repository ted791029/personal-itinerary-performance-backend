<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMemberTokensTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('member_tokens', function (Blueprint $table) {
            $table->char('token', 50);
            $table->primary('token');
            $table->unsignedBigInteger('memberId');
            $table->foreign('memberId')->references('id')->on('members');
            $table->timestamp('expiryTime');
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
        Schema::dropIfExists('member_tokens');
    }
}
