<?php

declare(strict_types=1);

namespace Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20240320161419 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        $table = $schema->createTable('stories');
        $table->addColumn('id', 'integer', [
            'autoincrement' => true,
            'unsigned' => true,
        ]);
        $table->addColumn('user_id', 'integer');
        $table->addColumn('slug', 'string', ['length' => 255]);
        $table->addColumn('active', 'boolean');
        $table->addColumn('title', 'string', ['length' => 255]);
        $table->addColumn('text', 'text');
        $table->addColumn('rating', 'integer');
        $table->addColumn('reads', 'integer');
        $table->addColumn('locked', 'boolean');
        $table->addColumn('created_at', 'datetime');
        $table->setPrimaryKey(['id']);
    }

    public function down(Schema $schema): void
    {
        $schema->dropTable('stories');
    }
}
