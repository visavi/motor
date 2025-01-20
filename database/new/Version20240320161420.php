<?php

declare(strict_types=1);

namespace Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20240320161420 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        $table = $schema->createTable('users');
        $table->addColumn('id', 'integer', [
            'autoincrement' => true,
            'unsigned' => true,
        ]);

        $table->addColumn('name', 'string', ['length' => 50]);
        $table->addColumn('created_at', 'datetime');
        $table->setPrimaryKey(['id']);
    }

    public function down(Schema $schema): void
    {
        $schema->dropTable('users');
    }
}
