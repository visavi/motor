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
        $migration
            ->create('id')
            ->create('story_id')
            ->create('tag')
            ->createTable();
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
