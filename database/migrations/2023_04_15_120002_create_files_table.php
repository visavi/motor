<?php

use App\Models\File;
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
        $migration = new Migration(new File());
        $migration
            ->create('id')
            ->create('user_id')
            ->create('story_id')
            ->create('path')
            ->create('name')
            ->create('ext')
            ->create('size')
            ->create('created_at')
            ->createTable();
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down(): void
    {
        $migration = new Migration(new File());
        $migration->deleteTable();
    }
};
