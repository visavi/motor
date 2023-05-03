<?php

use App\Models\Setting;
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
        $migration = new Migration(new Setting());
        $migration->createTable(function (Migration $table) {
            $table->create('name');
            $table->create('value');
        });

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
        $migration = new Migration(new Setting());
        $migration->deleteTable();
    }

    /**
     * Seed
     *
     * @return array[]
     */
    private function seed(): array
    {
        return [
            ['name' => 'main.title', 'value' => 'Добро пожаловать'],
            ['name' => 'main.guest_name', 'value' => 'Гость'],
            ['name' => 'main.delete_name', 'value' => 'Удаленный'],
            ['name' => 'story.active', 'value' => 1],
            ['name' => 'story.allow_posting', 'value' => 1],
            ['name' => 'story.per_page', 'value' => 10],
            ['name' => 'story.title_min_length', 'value' => 5],
            ['name' => 'story.title_max_length', 'value' => 50],
            ['name' => 'story.text_min_length', 'value' => 5],
            ['name' => 'story.text_max_length', 'value' => 5000],
            ['name' => 'story.short_words', 'value' => 100],
            ['name' => 'story.tags_max', 'value' => 5],
            ['name' => 'story.tags_min_length', 'value' => 2],
            ['name' => 'story.tags_max_length', 'value' => 20],
            ['name' => 'comment.text_min_length', 'value' => 5],
            ['name' => 'comment.text_max_length', 'value' => 1000],
            ['name' => 'guestbook.per_page', 'value' => 10],
            ['name' => 'guestbook.text_min_length', 'value' => 5],
            ['name' => 'guestbook.text_max_length', 'value' => 1000],
            ['name' => 'guestbook.name_min_length', 'value' => 3],
            ['name' => 'guestbook.name_max_length', 'value' => 20],
            ['name' => 'guestbook.allow_guests', 'value' => 1],
            ['name' => 'file.size_max', 'value' => 5242880],
            ['name' => 'file.total_max', 'value' => 5],
            ['name' => 'file.extensions', 'value' => 'jpg,jpeg,gif,png,bmp,webp'],
            ['name' => 'image.resize', 'value' => 1000],
            ['name' => 'image.weight_max', 'value' => null],
            ['name' => 'image.weight_min', 'value' => 100],
            ['name' => 'captcha.length', 'value' => 5],
            ['name' => 'captcha.symbols', 'value' => 1234567890],
            ['name' => 'user.per_page', 'value' => 10],
        ];
    }
};
