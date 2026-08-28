<?php

use App\Models\Notification;
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
        $migration = new Migration(new Notification());
        $migration
            ->create('id')
            ->create('user_id')
            ->create('message')
            ->create('read')
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
        $migration = new Migration(new Notification());
        $migration->deleteTable();
    }
};
