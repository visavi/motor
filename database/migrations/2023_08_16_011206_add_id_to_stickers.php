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
        $migration->changeTable(function (Migration $table) {
            $table->create('id')->before('code');
        });

       $stickers = file(storagePath('database/stickers.csv'));

       $temp = '';
       for ($i = 0; $i < count($stickers); $i++) {
           $temp .= $i === 0 ? $stickers[$i] : $i . $stickers[$i];
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
        $migration->changeTable(function (Migration $table) {
            $table->delete('id');
        });
    }
};
