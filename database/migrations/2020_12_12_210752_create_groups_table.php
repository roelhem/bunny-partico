<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateGroupsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('group_categories', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->text('description');
            $table->text('remarks');
            $table->timestamps();
            $table->foreignId('created_by')->nullable();
            $table->foreignId('updated_by')->nullable();
            $table->foreignId('created_by_team')->nullable();
            $table->foreignId('updated_by_team')->nullable();
        });

        Schema::create('groups', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->text('description');
            $table->text('remarks');
            $table->foreignId('category_id')->constrained('group_categories')
                ->onDelete('cascade')->onUpdate('cascade');
            $table->timestamps();
            $table->foreignId('created_by')->nullable();
            $table->foreignId('updated_by')->nullable();
            $table->foreignId('created_by_team')->nullable();
            $table->foreignId('updated_by_team')->nullable();
        });

        Schema::create('group_contact', function (Blueprint $table) {
            $table->foreignId('group_id')->constrained('groups')
                ->onDelete('cascade')->onUpdate('cascade');
            $table->foreignId('contact_id')->constrained('contacts')
                ->onDelete('cascade')->onUpdate('cascade');
            $table->primary(['group_id', 'contact_id']);
            $table->timestamps();
            $table->foreignId('created_by')->nullable();
            $table->foreignId('updated_by')->nullable();
            $table->foreignId('created_by_team')->nullable();
            $table->foreignId('updated_by_team')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('group_contact');
        Schema::dropIfExists('groups');
        Schema::dropIfExists('group_category');
    }
}
