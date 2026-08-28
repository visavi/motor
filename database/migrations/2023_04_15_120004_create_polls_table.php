<?php

use App\Models\Poll;
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
        $migration = new Migration(new Poll());
        $migration
            ->create('id')
            ->create('user_id')
            ->create('entity_id')
            ->create('entity_name')
            ->create('vote')
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
        $migration = new Migration(new Poll());
        $migration->deleteTable();
    }
};
