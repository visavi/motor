<?php

use App\Models\Notification;
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
        $migration = new Migration(new Notification());
        $migration->createTable(function (Migration $table) {
            $table->create('id');
            $table->create('user_id');
            $table->create('message');
            $table->create('read');
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
        $migration = new Migration(new Notification());
        $migration->deleteTable();
    }
};
