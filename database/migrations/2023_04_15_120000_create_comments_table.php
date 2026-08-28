<?php

use App\Models\Comment;
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
        $migration = new Migration(new Comment());
        $migration
            ->create('id')
            ->create('user_id')
            ->create('story_id')
            ->create('text')
            ->create('rating')
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
        $migration = new Migration(new Comment());
        $migration->deleteTable();
    }
};
