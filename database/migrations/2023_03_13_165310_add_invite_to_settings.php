<?php

use App\Models\Test;
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
        $migration = new Migration(new Test());
        $migration->createTable(function (Migration $table) {
            $table->create('id');
            $table->create('title');
            $table->create('text');
            $table->create('user_id');
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
        $migration = new Migration(new Test());
        $migration->deleteTable();
    }
};
