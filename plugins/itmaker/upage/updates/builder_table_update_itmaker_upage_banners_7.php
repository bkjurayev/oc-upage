<?php namespace Itmaker\Upage\Updates;

use Schema;
use October\Rain\Database\Updates\Migration;

class BuilderTableUpdateItmakerUpageBanners7 extends Migration
{
    public function up()
    {
        Schema::table('itmaker_upage_banners', function($table)
        {
            $table->renameColumn('show_time', 'deadline');
        });
    }
    
    public function down()
    {
        Schema::table('itmaker_upage_banners', function($table)
        {
            $table->renameColumn('deadline', 'show_time');
        });
    }
}
