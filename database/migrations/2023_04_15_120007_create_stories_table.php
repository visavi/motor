<?php

use App\Models\Story;
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
        $migration = new Migration(new Story());
        $migration
            ->create('id')
            ->create('user_id')
            ->create('slug')
            ->create('active')
            ->create('title')
            ->create('text')
            ->create('rating')
            ->create('reads')
            ->create('locked')
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
        $migration = new Migration(new Story());
        $migration->deleteTable();
    }
};
