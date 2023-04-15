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
        $migration->createTable(function (Migration $table) {
            $table->create('id');
            $table->create('user_id');
            $table->create('text');
            $table->create('name');
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
        $migration = new Migration(new Guestbook());
        $migration->deleteTable();
    }
};
