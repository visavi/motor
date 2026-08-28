<?php

use App\Models\User;
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
        $migration = new Migration(new User());
        $migration
            ->create('id')
            ->create('login')
            ->create('password')
            ->create('email')
            ->create('role')
            ->create('name')
            ->create('picture')
            ->create('avatar')
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
        $migration = new Migration(new User());
        $migration->deleteTable();
    }
};
