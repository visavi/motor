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
            ->create('active')->default(true)->before('created_at')
            ->changeTable();
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down(): void
    {
        $migration = new Migration(new Guestbook());
        $migration
            ->delete('active')
            ->changeTable();
    }
};
