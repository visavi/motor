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
        $migration
            ->create('id')->before('code')
            ->changeTable();

       $stickers = file(storagePath('database/stickers.csv'));

       $temp = '';
        foreach ($stickers as $i => $iValue) {
            $temp .= $i === 0 ? $iValue : $i . $iValue;
        }

        file_put_contents(storagePath('database/stickers.csv'), $temp, LOCK_EX);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down(): void
    {
        $migration = new Migration(new Sticker());
        $migration
            ->delete('id')
            ->changeTable();
    }
};
