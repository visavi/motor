<?php

use App\Models\Favorite;
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
        $migration = new Migration(new Favorite());
        $migration->createTable(function (Migration $table) {
            $table->create('id');
            $table->create('user_id');
            $table->create('story_id');
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
        $migration = new Migration(new Favorite());
        $migration->deleteTable();
    }
};
