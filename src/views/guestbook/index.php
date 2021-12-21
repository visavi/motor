<?php if ($messages): ?>
    <?php foreach ($messages as $message): ?>
    <div class="border-bottom p-3">
        <?= $message->name ?> (<?= date('Y-m-d H:i', $message->time) ?>)<br>
        <?= $message->title ?><br>
        <?= nl2br(stripcslashes(htmlspecialchars($message->text))) ?>

        <a href="?action=edit&amp;id=<?= $message->id ?>">Edit</a>
        <a href="?action=delete&amp;id=<?= $message->id ?>">Del</a>
    </div>
    <?php endforeach; ?>

    <?= $paginator->links() ?>
<?php else: ?>
    echo 'Сообщений нет<br>
<?php endif; ?>

<?= (new \App\View())->render('guestbook/_form'); ?>
