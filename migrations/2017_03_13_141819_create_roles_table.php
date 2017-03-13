<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateRolesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create($this->getTableName(), function(Blueprint $table) {
            $table->increments('id');
            $table->string('label');
            $table->string('title');

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
        Schema::dropIfExists($this->getTableName());
    }

    protected function getTableName() {
        $model = Config::get('popcode-userrole.model', 'PopCode\\UserRole\\Models\\Role');
        if (class_exists($model)) {
            $instance = new $model;
            return $instance->table;
        }
        return 'roles';
    }
}
