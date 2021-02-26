<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateNspService946Table extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('nsp_service_946', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('aid');
            $table->string('applicant_mobile');
            $table->longText('DATA');
            $table->text('attachment');
            $table->timestamp('created_at')->useCurrent();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('nsp_service_946');
    }
}
