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
            ['name' => 'app.name', 'value' => 'Motorcms.ru'],
            ['name' => 'app.url', 'value' => 'https://motorcms.ru'],
            ['name' => 'main.confirm_email', 'value' => false],
            ['name' => 'mailer.dsn', 'value' => 'smtps://login:password@smtp.yandex.ru:465'],
            ['name' => 'mailer.from_email', 'value' => 'my@email.ru'],
            ['name' => 'mailer.from_name', 'value' => 'Администратор'],
        ];
    }
};
