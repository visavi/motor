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
        $migration->changeTable(function (Migration $table) {
            $table->create('active')->default(true)->before('created_at');
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
        $migration->changeTable(function (Migration $table) {
            $table->delete('active');
        });
    }
};
