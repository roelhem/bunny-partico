<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddTeamStamps extends Migration
{
    protected $tables = [
        'contacts',
        'contact_languages',
        'contact_relations',
        'email_addresses',
        'phone_numbers',
        'postal_addresses'
    ];

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        foreach ($this->tables as $table) {
            Schema::table($table, function (Blueprint $table) {
                $table->foreignId('created_by_team')->nullable();
                $table->foreignId('updated_by_team')->nullable();
            });
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        foreach ($this->tables as $table) {
            Schema::table($table, function (Blueprint $table) {
                $table->dropColumn('created_by_team');
                $table->dropColumn('updated_by_team');
            });
        }
    }
}
