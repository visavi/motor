<?php
/** @var string $message */
?>
<?php $this->layout('layout-simple') ?>

<?php $this->start('title') ?>BBCode<?php $this->stop() ?>

<?= bbCode($message) ?>
