<?php

use App\Models\Story;
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
        $migration = new Migration(new Story());
        $migration->createTable(function (Migration $table) {
            $table->create('id');
            $table->create('user_id');
            $table->create('slug');
            $table->create('active');
            $table->create('title');
            $table->create('text');
            $table->create('rating');
            $table->create('reads');
            $table->create('locked');
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
        $migration = new Migration(new Story());
        $migration->deleteTable();
    }
};
