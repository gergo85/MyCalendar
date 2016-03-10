<?php namespace KurtJensen\MyCalendar\Updates;

use October\Rain\Database\Updates\Migration;
use Schema;

class AddEventLength extends Migration
{

    public function up()
    {
        Schema::table('kurtjensen_mycal_events', function ($table) {
            $table->time('length')->after('date');
            $table->string('pattern')->after('text');
        });
    }

    public function down()
    {
        Schema::table('kurtjensen_mycal_events', function ($table) {
            $table->dropColumn('length');
        });
    }

}
