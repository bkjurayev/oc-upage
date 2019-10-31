<?php namespace Itmaker\Upage\Updates;

use Schema;
use October\Rain\Database\Updates\Migration;

class BuilderTableUpdateItmakerUpageInterestings3 extends Migration
{
    public function up()
    {
        Schema::table('itmaker_upage_interestings', function($table)
        {
            $table->integer('sort_order')->nullable();
        });
    }
    
    public function down()
    {
        Schema::table('itmaker_upage_interestings', function($table)
        {
            $table->dropColumn('sort_order');
        });
    }
}
