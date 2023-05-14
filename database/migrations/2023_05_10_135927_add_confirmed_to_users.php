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
        $migration->changeTable(function (Migration $table) {
            $table->create('confirmed')->default(0)->before('created_at');
            $table->create('confirm_code')->after('confirmed');
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
        $migration->changeTable(function (Migration $table) {
            $table->delete('confirmed');
            $table->delete('confirm_code');
        });
    }
};
