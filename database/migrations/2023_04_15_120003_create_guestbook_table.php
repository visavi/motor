<?php

use App\Models\Guestbook;
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
        $migration = new Migration(new Guestbook());
        $migration
            ->create('id')
            ->create('user_id')
            ->create('text')
            ->create('name')
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
        $migration = new Migration(new Guestbook());
        $migration->deleteTable();
    }
};
