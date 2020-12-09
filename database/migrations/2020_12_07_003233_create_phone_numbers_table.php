<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePhoneNumbersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('phone_numbers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('contact_id')->constrained('contacts')
                ->onUpdate('cascade')
                ->onDelete('cascade');

            $table->integer('index');
            $table->string('label',63);
            $table->jsonb('options')->default('{}');

            $table->string('phone_number', 63);
            $table->char('country_code', 2)->default('NL');

            $table->text('remarks')->nullable();

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
        Schema::dropIfExists('phone_numbers');
    }
}
