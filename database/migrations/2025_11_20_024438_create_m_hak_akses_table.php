<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('m_hak_akses', function (Blueprint $table) {
            // Primary key sesuai generateId()
            $table->uuid('id')->primary();

            // user_id adalah UUID biasa, bukan primary key!
            $table->uuid('user_id');

            // akses berupa TEXT (list role yang dipisahkan koma)
            $table->text('akses');

            // timestamps mengikuti Sequelize (created_at & updated_at)
            $table->timestamp('created_at')->nullable();
            $table->timestamp('updated_at')->nullable();
        });
    }

    public function down()
    {
        Schema::dropIfExists('m_hak_akses');
    }
};