<?php

use App\Models\Tag;
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
        $migration = new Migration(new Tag());
        $migration->createTable(function (Migration $table) {
            $table->create('id');
            $table->create('story_id');
            $table->create('tag');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down(): void
    {
        $migration = new Migration(new Tag());
        $migration->deleteTable();
    }
};
