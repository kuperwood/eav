<?php

declare(strict_types=1);

namespace Drobotik\Dev\Migration;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\DBAL\Types\Types;
use Doctrine\Migrations\AbstractMigration;
use Drobotik\Eav\Enum\_SET;

final class AttributeSetMigration extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Attribute set table';
    }

    public function up(Schema $schema): void
    {
        $table = $schema->createTable(_SET::table());
        $table->addColumn(_SET::ID->column(), Types::INTEGER , ['Autoincrement' => true,'unsigned' => true]);
        $table->addColumn(_SET::DOMAIN_ID->column(), Types::INTEGER, ['unsigned' => true]);
        $table->addColumn(_SET::NAME->column(), Types::INTEGER);
        $table->setPrimaryKey([_SET::ID->column()]);
        $table->addIndex([_SET::DOMAIN_ID->column()]);
    }

    public function down(Schema $schema): void
    {
        $schema->dropTable(_SET::table());
    }
}
