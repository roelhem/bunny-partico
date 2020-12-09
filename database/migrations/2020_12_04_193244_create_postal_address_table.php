<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePostalAddressTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('postal_addresses', function (Blueprint $table) {
            $table->id();
            $table->foreignId('contact_id')->constrained('contacts')
                ->onUpdate('cascade')
                ->onDelete('cascade');

            $table->integer('index');
            $table->string('label', 63);
            $table->jsonb('options')->default('{}');

            $table->char('country_code', 2)->default('NL');
            $table->string('administrative_area')->nullable();
            $table->string('locality')->nullable();
            $table->string('dependent_locality')->nullable();
            $table->string('postal_code')->nullable();
            $table->string('sorting_code')->nullable();
            $table->string('address_line_1')->nullable();
            $table->string('address_line_2')->nullable();
            $table->string('organisation')->nullable();
            $table->string('locale', 15)->default('und');

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
        Schema::dropIfExists('postal_addresses');
    }
}
