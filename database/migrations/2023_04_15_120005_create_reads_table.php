<?php

use App\Models\Read;
use MotorORM\Migration;

return new class
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up(): void
    {
        $migration = new Migration(new Read());
        $migration->createTable(function (Migration $table) {
            $table->create('id');
            $table->create('story_id');
            $table->create('ip');
            $table->create('created_at');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down(): void
    {
        $migration = new Migration(new Read());
        $migration->deleteTable();
    }
};
