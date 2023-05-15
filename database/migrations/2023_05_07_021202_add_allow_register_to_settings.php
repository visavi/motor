<?php

use App\Models\Setting;

return new class
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up(): void
    {
        Setting::query()->create([
            'name'  => 'main.allow_register',
            'value' => 1,
        ]);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down(): void
    {
        Setting::query()
            ->where('name', 'main.allow_register')
            ->delete();
    }
};
