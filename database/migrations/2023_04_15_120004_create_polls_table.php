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
        $migration->createTable(function (Migration $table) {
            $table->create('id');
            $table->create('user_id');
            $table->create('entity_id');
            $table->create('entity_name');
            $table->create('vote');
            $table->create('created_at');
        });
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
