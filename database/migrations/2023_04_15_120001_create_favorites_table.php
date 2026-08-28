<?php

use App\Models\Favorite;
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
        $migration = new Migration(new Favorite());
        $migration
            ->create('id')
            ->create('user_id')
            ->create('story_id')
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
        $migration = new Migration(new Favorite());
        $migration->deleteTable();
    }
};
