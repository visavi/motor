<?php

use App\Models\File;
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
        $migration = new Migration(new File());
        $migration->createTable(function (Migration $table) {
            $table->create('id');
            $table->create('user_id');
            $table->create('story_id');
            $table->create('path');
            $table->create('name');
            $table->create('ext');
            $table->create('size');
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
        $migration = new Migration(new File());
        $migration->deleteTable();
    }
};
