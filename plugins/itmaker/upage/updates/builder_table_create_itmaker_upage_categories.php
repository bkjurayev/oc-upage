<?php namespace Itmaker\Upage\Updates;

use Schema;
use October\Rain\Database\Updates\Migration;

class BuilderTableCreateItmakerUpageCategories extends Migration
{
    public function up()
    {
        Schema::create('itmaker_upage_categories', function($table)
        {
            $table->engine = 'InnoDB';
            $table->increments('id');
            $table->integer('parent_id')->nullable();
            $table->integer('nest_left')->nullable()->unsigned();
            $table->integer('nest_right')->nullable()->unsigned();
            $table->integer('nest_depth')->nullable()->unsigned();
            $table->boolean('is_active');
            $table->timestamp('created_at')->nullable();
            $table->timestamp('updated_at')->nullable();
            $table->string('title');
            $table->text('description');
        });
    }
    
    public function down()
    {
        Schema::dropIfExists('itmaker_upage_categories');
    }
}
