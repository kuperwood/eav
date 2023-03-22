<?php

namespace Kuperwood\Eav\Migration;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\DBAL\Schema\Table;
use Doctrine\DBAL\Types\Types;
use Kuperwood\Eav\Enum\_VALUE;
use Kuperwood\Eav\Enum\AttributeTypeEnum;

class ValueMigration
{
    public static function runUp(Schema $schema, AttributeTypeEnum $type) : Table
    {
        $table = $schema->createTable(sprintf(_VALUE::table(), $type->value()));
        $table->addColumn(_VALUE::ID->column(), Types::INTEGER , ['Autoincrement' => true, 'unsigned' => true]);
        $table->addColumn(_VALUE::DOMAIN_ID->column(), Types::INTEGER, ['unsigned' => true]);
        $table->addColumn(_VALUE::ENTITY_ID->column(), Types::INTEGER, ['unsigned' => true]);
        $table->addColumn(_VALUE::ATTRIBUTE_ID->column(), Types::INTEGER, ['unsigned' => true]);
        $table->addColumn(_VALUE::VALUE->column(), $type->doctrineType());
        $table->setPrimaryKey([_VALUE::ID->column()]);
        $table->addIndex([_VALUE::DOMAIN_ID->column()]);
        $table->addIndex([_VALUE::ENTITY_ID->column()]);
        $table->addIndex([_VALUE::ATTRIBUTE_ID->column()]);
        $table->addUniqueIndex([
            _VALUE::DOMAIN_ID->column(),
            _VALUE::ENTITY_ID->column(),
            _VALUE::ATTRIBUTE_ID->column()
        ]);
        return $table;
    }

    public static function runDown(Schema $schema, AttributeTypeEnum $type) : void
    {
        $schema->dropTable(sprintf(_VALUE::table(), $type->value()));
    }
}