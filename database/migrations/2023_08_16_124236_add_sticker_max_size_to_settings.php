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
        foreach ($this->seed() as $seed) {
            Setting::query()->create($seed);
        }

    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down(): void
    {
        foreach ($this->seed() as $seed) {
            Setting::query()
                ->where('name', $seed['name'])
                ->delete();
        }
    }

    /**
     * Seed
     *
     * @return array[]
     */
    private function seed(): array
    {
        return [
            ['name' => 'sticker.size_max', 'value' => 10240],
            ['name' => 'sticker.weight_min', 'value' => 16],
            ['name' => 'sticker.weight_max', 'value' => 128],
            ['name' => 'sticker.per_page', 'value' => 20],
        ];
    }
};
