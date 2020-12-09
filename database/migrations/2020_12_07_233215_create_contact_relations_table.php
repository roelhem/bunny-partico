<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateContactRelationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('contact_relations', function (Blueprint $table) {
            $table->id();

            $table->foreignId('contact_id')->constrained('contacts')
                ->onUpdate('cascade')
                ->onDelete('cascade');

            $table->foreignId('related_contact_id')->constrained('contacts')
                ->onUpdate('cascade')
                ->onDelete('cascade');

            $table->integer('index');
            $table->string('label',63);
            $table->jsonb('options')->default('{}');
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
        Schema::dropIfExists('contact_relations');
    }
}
