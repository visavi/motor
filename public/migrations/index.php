<?php

use App\Models\Comment;
use App\Models\File;
use App\Models\Guestbook;
use App\Models\Poll;
use App\Models\Read;
use App\Models\Story;
use App\Models\User;
use MotorORM\Migration;

error_reporting(E_ALL);
ini_set('display_errors', '1');
ini_set('display_startup_errors', '1');

require __DIR__ . '/../../vendor/autoload.php';

// Добавляет поля picture и avatar в users
$userHeaders = User::query()->headers();
if (! in_array('picture', $userHeaders, true)) {
    $migration = new Migration(new User());

    try {
        $migration->column('picture')->after('name')->create();
        $migration->column('avatar')->after('picture')->create();
    } catch (Exception $exception) {
        echo $exception->getMessage();
    }

    echo 'Добавлены поля picture и avatar в таблицу users<br>';
}

// Добавляет поле user_id и удаляет login
$guestbookHeaders = Guestbook::query()->headers();
if (! in_array('user_id', $guestbookHeaders, true)) {
    $migration = new Migration(new Guestbook());

    try {
        $migration->column('user_id')->after('id')->create();

        $messages = Guestbook::query()->get();
        foreach ($messages as $message) {
            $user = User::query()->where('login', $message->login)->first();
            $message->where('id', $message->id)->update([
                'user_id' => $user->id ?? '',
            ]);
        }

        $migration->column('login')->delete();
    } catch (Exception $exception) {
        echo $exception->getMessage();
    }

    echo 'Добавлено поле user_id в таблицу guestbook<br>';
}

// Переносит image из guestbook в files
$guestbookHeaders = Guestbook::query()->headers();
if (in_array('image', $guestbookHeaders, true)) {
    $migration = new Migration(new Guestbook());

    try {
        $messages = Guestbook::query()->get();
        foreach ($messages as $message) {
            if (! $message->image) {
                continue;
            }

            File::query()->insert([
                'user_id'    => $message->user_id,
                'post_id'    => $message->id,
                'path'       => $message->image,
                'created_at' => $message->created_at,
            ]);
        }

        $migration->column('image')->delete();
    } catch (Exception $exception) {
        echo $exception->getMessage();
    }

    echo 'Поле image перенесено из таблицы guestbook в таблицу files<br>';
}

// Добавляет поля name, ext и size в files
$fileHeaders = File::query()->headers();
if (! in_array('ext', $fileHeaders, true)) {
    $migration = new Migration(new File());

    try {
        $migration->column('name')->after('path')->create();
        $migration->column('ext')->after('name')->create();
        $migration->column('size')->after('ext')->create();

        $files = File::query()->get();
        foreach ($files as $file) {
            $file->where('id', $file->id)->update([
                'name' => basename($file->path),
                'ext'  => getExtension($file->path),
                'size' => filesize(publicPath($file->path)) ?? 0,
            ]);
        }

    } catch (Exception $exception) {
        echo $exception->getMessage();
    }

    echo 'Добавлены поля name, ext и size в таблицу files<br>';
}

// Добавляет поле name и удаляет title из guestbook
$guestbookHeaders = Guestbook::query()->headers();
if (! in_array('name', $guestbookHeaders, true)) {
    $migration = new Migration(new Guestbook());

    try {
        $migration->column('name')->after('text')->create();
        $migration->column('title')->delete();
    } catch (Exception $exception) {
        echo $exception->getMessage();
    }

    echo 'Добавлено поле name и удалено title из таблицы guestbook<br>';
}

// Добавляет поле rating в stories
$storyHeaders = Story::query()->headers();
if (! in_array('rating', $storyHeaders, true)) {
    $migration = new Migration(new Story());

    try {
        $migration->column('rating')->after('tags')->default(0)->create();
    } catch (Exception $exception) {
        echo $exception->getMessage();
    }

    echo 'Добавлено поле rating в таблицы stories<br>';
}

// Переименовывает поле views в reads в stories
$storyHeaders = Story::query()->headers();
if (in_array('views', $storyHeaders, true)) {
    $migration = new Migration(new Story());

    try {
        $migration->column('views')->to('reads')->rename();
    } catch (Exception $exception) {
        echo $exception->getMessage();
    }

    echo 'Переименовано поле views на reads в таблицы stories<br>';
}

// Переименовывает поле post_id в story_id в comments
$commentHeaders = Comment::query()->headers();
if (in_array('post_id', $commentHeaders, true)) {
    $migration = new Migration(new Comment());

    try {
        $migration->column('post_id')->to('story_id')->rename();
    } catch (Exception $exception) {
        echo $exception->getMessage();
    }

    echo 'Переименовано поле post_id в story_id в таблицы comments<br>';
}

// Переименовывает поле post_id в story_id в files
$fileHeaders = File::query()->headers();
if (in_array('post_id', $fileHeaders, true)) {
    $migration = new Migration(new File());

    try {
        $migration->column('post_id')->to('story_id')->rename();
    } catch (Exception $exception) {
        echo $exception->getMessage();
    }

    echo 'Переименовано поле post_id в story_id в таблицы files<br>';
}

// Переименовывает поле post_id в story_id в polls
$pollHeaders = Poll::query()->headers();
if (in_array('post_id', $pollHeaders, true)) {
    $migration = new Migration(new Poll());

    try {
        $migration->column('post_id')->to('story_id')->rename();
    } catch (Exception $exception) {
        echo $exception->getMessage();
    }

    echo 'Переименовано поле post_id в story_id в таблицы polls<br>';
}

// Переименовывает поле post_id в story_id в reads
$readHeaders = Read::query()->headers();
if (in_array('post_id', $readHeaders, true)) {
    $migration = new Migration(new Read());

    try {
        $migration->column('post_id')->to('story_id')->rename();
    } catch (Exception $exception) {
        echo $exception->getMessage();
    }

    echo 'Переименовано поле post_id в story_id в таблицы reads<br>';
}

// Переименовывает поле story_id в entity_id в polls
$pollHeaders = Poll::query()->headers();
if (in_array('story_id', $pollHeaders, true)) {
    $migration = new Migration(new Poll());

    try {
        $migration->column('story_id')->to('entity_id')->rename();
    } catch (Exception $exception) {
        echo $exception->getMessage();
    }

    echo 'Переименовано поле story_id в entity_id в таблицы polls<br>';
}

// Добавляет поле entity_name в polls
$pollHeaders = Poll::query()->headers();
if (! in_array('entity_name', $pollHeaders, true)) {
    $migration = new Migration(new Poll());

    try {
        $migration->column('entity_name')->after('entity_id')->default('story')->create();
    } catch (Exception $exception) {
        echo $exception->getMessage();
    }

    echo 'Добавлено поле entity_name в таблицы polls<br>';
}


echo 'Все миграции выполнены успешно!<br>';
