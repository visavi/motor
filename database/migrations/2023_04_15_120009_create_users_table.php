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
        $migration->createTable(function (Migration $table) {
            $table->create('id');
            $table->create('login');
            $table->create('password');
            $table->create('email');
            $table->create('role');
            $table->create('name');
            $table->create('picture');
            $table->create('avatar');
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
        $migration = new Migration(new User());
        $migration->deleteTable();
    }
};
