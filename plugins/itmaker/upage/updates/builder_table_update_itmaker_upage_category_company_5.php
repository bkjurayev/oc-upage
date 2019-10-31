<?php namespace Itmaker\Upage\Updates;

use Schema;
use October\Rain\Database\Updates\Migration;

class BuilderTableUpdateItmakerUpageCategoryCompany5 extends Migration
{
    public function up()
    {
        Schema::table('itmaker_upage_category_company', function($table)
        {
            $table->integer('category_id')->nullable(false)->change();
            $table->integer('company_id')->nullable(false)->change();
        });
    }
    
    public function down()
    {
        Schema::table('itmaker_upage_category_company', function($table)
        {
            $table->integer('category_id')->nullable()->change();
            $table->integer('company_id')->nullable()->change();
        });
    }
}
