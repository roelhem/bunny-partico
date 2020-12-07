<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateIndividualsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('individuals', function (Blueprint $table) {
            $table->id();

            $table->string('title')->nullable();

            $table->string('name_full')->nullable();
            $table->string('name_prefix')->nullable();
            $table->string('name_initials')->nullable();
            $table->string('name_first')->nullable();
            $table->string('name_middle')->nullable();
            $table->string('name_last')->nullable();
            $table->string('name_suffix')->nullable();

            $table->date('birth_date')->nullable();

            $table->string('nickname')->nullable();
            $table->string('remarks')->nullable();

            $table->timestamps();
            $table->foreignId('created_by')->nullable();
            $table->foreignId('updated_by')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('individuals');
    }
}
