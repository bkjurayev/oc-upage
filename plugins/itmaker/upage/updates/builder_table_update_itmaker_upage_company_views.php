<?php namespace Itmaker\Upage\Updates;

use Schema;
use October\Rain\Database\Updates\Migration;

class BuilderTableUpdateItmakerUpageCompanyViews extends Migration
{
    public function up()
    {
        Schema::table('itmaker_upage_company_views', function($table)
        {
            $table->integer('company_id')->nullable(false)->change();
            $table->integer('views')->nullable(false)->change();
        });
    }
    
    public function down()
    {
        Schema::table('itmaker_upage_company_views', function($table)
        {
            $table->integer('company_id')->nullable()->change();
            $table->integer('views')->nullable()->change();
        });
    }
}
