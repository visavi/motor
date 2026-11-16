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
            ->create('online_at')->default(0)->after('created_at')
            ->changeTable();
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down(): void
    {
        $migration = new Migration(new User());
        $migration
            ->delete('online_at')
            ->changeTable();
    }
};
