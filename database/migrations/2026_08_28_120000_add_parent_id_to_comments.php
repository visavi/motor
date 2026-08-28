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
            ->create('parent_id')->default(0)->after('story_id')
            ->changeTable();
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down(): void
    {
        $migration = new Migration(new Comment());
        $migration
            ->delete('parent_id')
            ->changeTable();
    }
};
