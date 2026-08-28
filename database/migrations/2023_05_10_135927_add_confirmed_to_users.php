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
            ->create('confirmed')->default(0)->before('created_at')
            ->create('confirm_code')->after('confirmed')
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
            ->delete('confirmed')
            ->delete('confirm_code')
            ->changeTable();
    }
};
