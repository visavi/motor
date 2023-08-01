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
            ['name' => 'guestbook.active', 'value' => true],
        ];
    }
};
