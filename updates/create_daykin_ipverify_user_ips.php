<?php

namespace Daykin\Ipverify\Updates;

use October\Rain\Database\Schema\Blueprint;
use October\Rain\Database\Updates\Migration;
use October\Rain\Support\Facades\Schema;

class CreateDaykinIpverifyUserIps extends Migration
{
    public function up()
    {
        Schema::create('daykin_ipverify_user_ips', function (Blueprint $table)
        {
            $table->engine = 'InnoDB';
            $table->increments('id')->unsigned();
            $table->integer('backend_user_id')->unsigned();
            $table->foreign('backend_user_id')->references('id')->on('backend_users')->onDelete('cascade');
            $table->string('ip');
            $table->string('token')->nullable();
            $table->dateTime('verified_at')->nullable();
            $table->unique(['backend_user_id', 'ip']);
        });
    }
    
    public function down()
    {
        Schema::table('daykin_ipverify_user_ips', function (Blueprint $table) {
            $table->dropForeign(['backend_user_id']);
        });

        Schema::dropIfExists('daykin_ipverify_user_ips');
    }
}