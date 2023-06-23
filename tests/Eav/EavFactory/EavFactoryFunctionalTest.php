<?php
/**
 * This file is part of the eav package.
 *
 * @author    Aleksandr Drobotik <drobotiksbox@gmail.com>
 * @copyright 2023 Aleksandr Drobotik
 * @license   https://opensource.org/license/mit  The MIT License
 */
declare(strict_types=1);

namespace Tests\Eav\EavFactory;

use Drobotik\Eav\Database\Connection;
use Drobotik\Eav\Enum\_ATTR;
use Drobotik\Eav\Enum\_DOMAIN;
use Drobotik\Eav\Enum\_ENTITY;
use Drobotik\Eav\Enum\_GROUP;
use Drobotik\Eav\Enum\_RESULT;
use Drobotik\Eav\Enum\_SET;
use Drobotik\Eav\Enum\ATTR_FACTORY;
use Drobotik\Eav\Enum\ATTR_TYPE;
use Drobotik\Eav\Model\AttributeGroupModel;
use Drobotik\Eav\Model\AttributeModel;
use Drobotik\Eav\Model\AttributeSetModel;
use Drobotik\Eav\Model\PivotModel;
use Drobotik\Eav\Model\ValueDatetimeModel;
use Drobotik\Eav\Model\ValueDecimalModel;
use Drobotik\Eav\Model\ValueIntegerModel;
use Drobotik\Eav\Model\ValueStringModel;
use Drobotik\Eav\Model\ValueTextModel;
use Drobotik\Eav\Result\EntityFactoryResult;
use Drobotik\Eav\Result\Result;
use Tests\TestCase;

/**
 * @internal
 *
 * @coversNothing
 */
class EavFactoryFunctionalTest extends TestCase
{
    /**
     * @test
     *
     * @group functional
     *
     * @covers \Drobotik\Eav\Factory\EavFactory::createDomain
     */
    public function domainDefault()
    {
        $this->assertEquals(1, $this->eavFactory->createDomain());
        $this->assertEquals(2, $this->eavFactory->createDomain());
    }

    /**
     * @test
     *
     * @group functional
     *
     * @covers \Drobotik\Eav\Factory\EavFactory::createDomain
     */
    public function domainInputData()
    {
        $name = 'test';
        $key = $this->eavFactory->createDomain([
            _DOMAIN::NAME->column() => $name
        ]);

        $qb = Connection::get()->createQueryBuilder();
        $record = $qb->select('*')->from(_DOMAIN::table())
            ->executeQuery()
            ->fetchAssociative();

        $this->assertEquals([
            _DOMAIN::ID->column() => $key,
            _DOMAIN::NAME->column() => $name
        ], $record);
    }

    /**
     * @test
     *
     * @group functional
     *
     * @covers \Drobotik\Eav\Factory\EavFactory::createEntity
     */
    public function entityDefault()
    {
        $this->assertEquals(1, $this->eavFactory->createEntity());

        $qb = Connection::get()->createQueryBuilder();
        $record = $qb->select('*')->from(_ENTITY::table())
            ->executeQuery()
            ->fetchAssociative();

        $this->assertEquals([
            _ENTITY::ID->column() => 1,
            _ENTITY::DOMAIN_ID->column() => 1,
            _ENTITY::ATTR_SET_ID->column() => 1,
            _ENTITY::SERVICE_KEY->column() => null
        ], $record);

        // domain created
        $record = $qb->select('count(*) as c')->from(_DOMAIN::table())
            ->executeQuery()
            ->fetchAssociative();
        $this->assertEquals(1, $record['c']);

        // attribute set created
        $this->assertEquals(1, AttributeSetModel::query()->count());
    }

    /**
     * @test
     *
     * @group functional
     *
     * @covers \Drobotik\Eav\Factory\EavFactory::createAttributeSet
     */
    public function attributeSet()
    {
        $result = $this->eavFactory->createAttributeSet();
        $this->assertInstanceOf(AttributeSetModel::class, $result);
        $this->assertEquals(1, $result->getKey());
        $this->assertEquals(1, $result->getDomainKey());
        $data = $result->toArray();
        $this->assertArrayHasKey(_SET::NAME->column(), $data);
    }

    /**
     * @test
     *
     * @group functional
     *
     * @covers \Drobotik\Eav\Factory\EavFactory::createAttributeSet
     */
    public function attributeSetInput()
    {
        $input = [
            _SET::NAME->column() => 'test',
        ];
        $result = $this->eavFactory->createAttributeSet(null, $input);
        $this->assertInstanceOf(AttributeSetModel::class, $result);
        $this->assertEquals(1, $result->getKey());
        $this->assertEquals(1, $result->getDomainKey());
        $this->assertEquals($input[_SET::NAME->column()], $result->getName());
    }

    /**
     * @test
     *
     * @group functional
     *
     * @covers \Drobotik\Eav\Factory\EavFactory::createGroup
     */
    public function attributeGroup()
    {
        $result = $this->eavFactory->createGroup();
        $this->assertInstanceOf(AttributeGroupModel::class, $result);
        $this->assertEquals(1, $result->getKey());
        $this->assertEquals(1, $result->getAttrSetKey());
        $data = $result->toArray();
        $this->assertArrayHasKey(_GROUP::NAME->column(), $data);
    }

    /**
     * @test
     *
     * @group functional
     *
     * @covers \Drobotik\Eav\Factory\EavFactory::createGroup
     */
    public function attributeGroupInput()
    {
        $input = [
            _GROUP::NAME->column() => 'test',
        ];
        $result = $this->eavFactory->createGroup(null, $input);
        $this->assertInstanceOf(AttributeGroupModel::class, $result);
        $this->assertEquals(1, $result->getKey());
        $this->assertEquals(1, $result->getAttrSetKey());
        $this->assertEquals($input[_GROUP::NAME->column()], $result->getName());
    }

    /**
     * @test
     *
     * @group functional
     *
     * @covers \Drobotik\Eav\Factory\EavFactory::createAttribute
     */
    public function attribute()
    {
        $result = $this->eavFactory->createAttribute();
        $this->assertInstanceOf(AttributeModel::class, $result);
        $this->assertEquals(1, $result->getKey());
        $this->assertEquals(1, $result->getDomainKey());
        $this->assertEquals(_ATTR::TYPE->default(), $result->getType());
        $this->assertEquals(_ATTR::STRATEGY->default(), $result->getStrategy());
        $this->assertEquals(_ATTR::SOURCE->default(), $result->getSource());
        $this->assertEquals(_ATTR::DEFAULT_VALUE->default(), $result->getDefaultValue());
        $this->assertEquals(_ATTR::DESCRIPTION->default(), $result->getDescription());
        $data = $result->toArray();
        $this->assertArrayHasKey(_ATTR::NAME->column(), $data);
    }

    /**
     * @test
     *
     * @group functional
     *
     * @covers \Drobotik\Eav\Factory\EavFactory::createAttribute
     */
    public function attributeInput()
    {
        $input = [
            _ATTR::NAME->column() => 'test',
            _ATTR::TYPE->column() => 'test',
            _ATTR::STRATEGY->column() => 'test',
            _ATTR::SOURCE->column() => 'test',
            _ATTR::DEFAULT_VALUE->column() => 'test',
            _ATTR::DESCRIPTION->column() => 'test',
        ];
        $result = $this->eavFactory->createAttribute(null, $input);
        $this->assertInstanceOf(AttributeModel::class, $result);
        $this->assertEquals(1, $result->getKey());
        $this->assertEquals(1, $result->getDomainKey());
        $this->assertEquals($input[_ATTR::NAME->column()], $result->getType());
        $this->assertEquals($input[_ATTR::TYPE->column()], $result->getType());
        $this->assertEquals($input[_ATTR::STRATEGY->column()], $result->getStrategy());
        $this->assertEquals($input[_ATTR::SOURCE->column()], $result->getSource());
        $this->assertEquals($input[_ATTR::DEFAULT_VALUE->column()], $result->getDefaultValue());
        $this->assertEquals($input[_ATTR::DESCRIPTION->column()], $result->getDescription());
    }

    /**
     * @test
     *
     * @group functional
     *
     * @covers \Drobotik\Eav\Factory\EavFactory::createPivot
     */
    public function pivot()
    {
        $this->eavFactory->createDomain();
        $domainKey = $this->eavFactory->createDomain();
        $this->eavFactory->createAttributeSet($domainKey);
        $attrSet = $this->eavFactory->createAttributeSet($domainKey);
        $this->eavFactory->createGroup($attrSet->getKey());
        $group = $this->eavFactory->createGroup($attrSet->getKey());
        $this->eavFactory->createAttribute($domainKey);
        $attribute = $this->eavFactory->createAttribute($domainKey);

        $result = $this->eavFactory->createPivot($domainKey, $attrSet->getKey(), $group->getKey(), $attribute->getKey());
        $this->assertInstanceOf(PivotModel::class, $result);
        $this->assertEquals(1, $result->getKey());
        $this->assertEquals(2, $result->getDomainKey());
        $this->assertEquals(2, $result->getAttrKey());
        $this->assertEquals(2, $result->getGroupKey());
    }

    /**
     * @test
     *
     * @group functional
     *
     * @covers \Drobotik\Eav\Factory\EavFactory::createValue
     */
    public function valueString()
    {
        $domainKey = $this->eavFactory->createDomain();
        $entityKey = $this->eavFactory->createEntity($domainKey);
        $attribute = $this->eavFactory->createAttribute($domainKey);
        $result = $this->eavFactory->createValue(
            ATTR_TYPE::STRING,
            $domainKey,
            $entityKey,
            $attribute->getKey(),
            'test'
        );
        $this->assertInstanceOf(ValueStringModel::class, $result);
        $this->assertEquals(1, $result->getKey());
        $this->assertEquals(1, $result->getDomainKey());
        $this->assertEquals(1, $result->getEntityKey());
        $this->assertEquals(1, $result->getAttrKey());
        $this->assertEquals('test', $result->getValue());
    }

    /**
     * @test
     *
     * @group functional
     *
     * @covers \Drobotik\Eav\Factory\EavFactory::createValue
     */
    public function valueText()
    {
        $domainKey = $this->eavFactory->createDomain();
        $entityKey = $this->eavFactory->createEntity($domainKey);
        $attribute = $this->eavFactory->createAttribute($domainKey);
        $result = $this->eavFactory->createValue(
            ATTR_TYPE::TEXT,
            $domainKey,
            $entityKey,
            $attribute->getKey(),
            'test'
        );
        $this->assertInstanceOf(ValueTextModel::class, $result);
        $this->assertEquals(1, $result->getKey());
        $this->assertEquals(1, $result->getDomainKey());
        $this->assertEquals(1, $result->getEntityKey());
        $this->assertEquals(1, $result->getAttrKey());
        $this->assertEquals('test', $result->getValue());
    }

    /**
     * @test
     *
     * @group functional
     *
     * @covers \Drobotik\Eav\Factory\EavFactory::createValue
     */
    public function valueInteger()
    {
        $domainKey = $this->eavFactory->createDomain();
        $entityKey = $this->eavFactory->createEntity($domainKey);
        $attribute = $this->eavFactory->createAttribute($domainKey);
        $result = $this->eavFactory->createValue(
            ATTR_TYPE::INTEGER,
            $domainKey,
            $entityKey,
            $attribute->getKey(),
            123
        );
        $this->assertInstanceOf(ValueIntegerModel::class, $result);
        $this->assertEquals(1, $result->getKey());
        $this->assertEquals(1, $result->getDomainKey());
        $this->assertEquals(1, $result->getEntityKey());
        $this->assertEquals(1, $result->getAttrKey());
        $this->assertEquals(123, $result->getValue());
    }

    /**
     * @test
     *
     * @group functional
     *
     * @covers \Drobotik\Eav\Factory\EavFactory::createValue
     */
    public function valueDecimal()
    {
        $domainKey = $this->eavFactory->createDomain();
        $entityKey = $this->eavFactory->createEntity($domainKey);
        $attribute = $this->eavFactory->createAttribute($domainKey);
        $result = $this->eavFactory->createValue(
            ATTR_TYPE::DECIMAL,
            $domainKey,
            $entityKey,
            $attribute->getKey(),
            123.123
        );
        $this->assertInstanceOf(ValueDecimalModel::class, $result);
        $this->assertEquals(1, $result->getKey());
        $this->assertEquals(1, $result->getDomainKey());
        $this->assertEquals(1, $result->getEntityKey());
        $this->assertEquals(1, $result->getAttrKey());
        $this->assertEquals(123.123, $result->getValue());
    }

    /**
     * @test
     *
     * @group functional
     *
     * @covers \Drobotik\Eav\Factory\EavFactory::createValue
     */
    public function valueDatetime()
    {
        $domainKey = $this->eavFactory->createDomain();
        $entityKey = $this->eavFactory->createEntity($domainKey);
        $attribute = $this->eavFactory->createAttribute($domainKey);
        $datetime = (new \DateTime())->format('Y-m-d H:i:s');
        $result = $this->eavFactory->createValue(
            ATTR_TYPE::DATETIME,
            $domainKey,
            $entityKey,
            $attribute->getKey(),
            $datetime
        );
        $this->assertInstanceOf(ValueDatetimeModel::class, $result);
        $this->assertEquals(1, $result->getKey());
        $this->assertEquals(1, $result->getDomainKey());
        $this->assertEquals(1, $result->getEntityKey());
        $this->assertEquals(1, $result->getAttrKey());
        $this->assertEquals($datetime, $result->getValue());
    }
    /**
     * @test
     *
     * @group functional
     *
     * @covers \Drobotik\Eav\Factory\EavFactory::createEavEntity
     */
    public function createEavEntity()
    {
        $domainKey = $this->eavFactory->createDomain();
        $set = $this->eavFactory->createAttributeSet($domainKey);
        $group = $this->eavFactory->createGroup($set->getKey());
        $config = [
            [
                ATTR_FACTORY::ATTRIBUTE->field() => [
                    _ATTR::NAME->column() => ATTR_TYPE::STRING->value(),
                    _ATTR::TYPE->column() => ATTR_TYPE::STRING->value(),
                    _ATTR::DEFAULT_VALUE->column() => ATTR_TYPE::STRING->randomValue(),
                ],
                ATTR_FACTORY::GROUP->field() => $group->getKey()
            ]
        ];
        $result = $this->eavFactory->createEavEntity($config, $domainKey, $set->getKey());
        $this->assertInstanceOf(Result::class, $result);
        $this->assertEquals(_RESULT::CREATED->code(), $result->getCode());
        $this->assertEquals(_RESULT::CREATED->message(), $result->getMessage());
        $this->assertInstanceOf(EntityFactoryResult::class, $result->getData());
    }
}
