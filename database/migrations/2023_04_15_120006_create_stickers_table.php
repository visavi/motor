<?php

use App\Models\Sticker;
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
        $migration = new Migration(new Sticker());
        $migration->createTable(function (Migration $table) {
            $table->create('code');
            $table->create('path');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down(): void
    {
        $migration = new Migration(new Sticker());
        $migration->deleteTable();
    }
};
