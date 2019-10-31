<?php namespace Itmaker\Upage\Updates;

use Schema;
use October\Rain\Database\Updates\Migration;

class BuilderTableCreateItmakerUpageInteresting extends Migration
{
    public function up()
    {
        Schema::create('itmaker_upage_interesting', function($table)
        {
            $table->engine = 'InnoDB';
            $table->increments('id');
            $table->string('type');
            $table->text('description');
            $table->integer('views');
            $table->dateTime('date');
            $table->text('link');
            $table->timestamp('created_at')->nullable();
            $table->timestamp('updated_at')->nullable();
        });
    }
    
    public function down()
    {
        Schema::dropIfExists('itmaker_upage_interesting');
    }
}
