<?php
/**
 * This file is part of the eav package.
 * @author    Alex Kuperwood <alexkuperwood@gmail.com>
 * @copyright 2025 Alex Kuperwood
 * @license   https://opensource.org/license/mit  The MIT License
 */
declare(strict_types=1);

namespace Tests\Eav\AttributeSetAction;

use Kuperwood\Eav\Attribute;
use Kuperwood\Eav\AttributeContainer;
use Kuperwood\Eav\AttributeSet;
use Kuperwood\Eav\AttributeSetAction;
use Kuperwood\Eav\Database\Connection;
use Kuperwood\Eav\Entity;
use Kuperwood\Eav\Enum\_ATTR;
use Kuperwood\Eav\Enum\ATTR_TYPE;
use Kuperwood\Eav\Model\ValueBase;
use Kuperwood\Eav\Strategy;
use PDO;
use Tests\TestCase;

class AttributeSetActionFunctionalTest extends TestCase
{
    private AttributeContainer $container;
    private AttributeSetAction $action;

    public function setUp(): void
    {
        parent::setUp();
        $this->container = new AttributeContainer();
        $this->container->makeAttributeSetAction();
        $this->action = $this->container->getAttributeSetAction();
    }
    /**
     * @test
     * @group functional
     * @covers \Kuperwood\Eav\AttributeSetAction::initializeAttribute
     */
    public function initialize_attribute() {
        $attribute = [
            _ATTR::ID => 1,
            _ATTR::DOMAIN_ID => 1,
            _ATTR::NAME => 'test',
            _ATTR::TYPE => ATTR_TYPE::STRING
        ];
        $result = $this->action->initializeAttribute($attribute);
        $this->assertInstanceOf(Attribute::class, $result);
        $this->assertEquals($attribute,  $result->getBag()->getFields());
        $this->assertSame($result, $this->container->getAttribute());
    }

    /**
     * @test
     * @group functional
     * @covers \Kuperwood\Eav\AttributeSetAction::initializeAttribute
     */
    public function initialize_attribute_with_empty_strategy() {
        $attribute = [
            _ATTR::STRATEGY => '',
        ];
        $this->action->initializeAttribute($attribute);
        $this->assertSame(Strategy::class, $this->container->getAttribute()->getStrategy());
    }
    /**
     * @test
     * @group functional
     * @covers \Kuperwood\Eav\AttributeSetAction::initializeAttribute
     */
    public function initialized_attribute_with_pivot() {
        $domainKey = $this->eavFactory->createDomain();
        $setKey = $this->eavFactory->createAttributeSet($domainKey);
        $groupKey = $this->eavFactory->createGroup($setKey);
        $attributeKey = $this->eavFactory->createAttribute($domainKey);
        $this->eavFactory->createPivot($domainKey, $setKey, $groupKey, $attributeKey);


        $sql = sprintf("SELECT * FROM %s", _ATTR::table());
        $stmt = Connection::get()->prepare($sql);
        $stmt->execute();
        $attributeRecord = $stmt->fetch(PDO::FETCH_ASSOC);

        $result = $this->action->initializeAttribute($attributeRecord);
        $this->assertEquals($attributeRecord, $result->getBag()->getFields());
        $this->assertSame($result, $this->container->getAttribute());
    }
    /**
     * @test
     * @group functional
     * @covers \Kuperwood\Eav\AttributeSetAction::initializeStrategy
     */
    public function initialized_strategy() {
        $attribute = new Attribute();
        $result = $this->action->initializeStrategy($attribute);
        $this->assertInstanceOf(Strategy::class, $result);
        $this->assertSame($result, $this->container->getStrategy());
    }
    /**
     * @test
     * @group functional
     * @covers \Kuperwood\Eav\AttributeSetAction::initialize
     */
    public function initialize() {
        $domainKey = $this->eavFactory->createDomain();
        $entityKey = $this->eavFactory->createEntity($domainKey);
        $setKey = $this->eavFactory->createAttributeSet($domainKey);
        $groupKey = $this->eavFactory->createGroup($setKey);
        $attKey = $this->eavFactory->createAttribute($domainKey);
        $this->eavFactory->createPivot($domainKey, $setKey, $groupKey, $attKey);

        $value = "test";
        $valueModel = new ValueBase();
        $valueKey = $valueModel->create(ATTR_TYPE::STRING, $domainKey, $entityKey, $attKey, $value);

        $entity = new Entity();
        $entity->setKey($entityKey);
        $entity->setDomainKey($domainKey);
        $attrSet = new AttributeSet();
        $attrSet->setEntity($entity);

        $this->container->setAttributeSet($attrSet);

        $sql = sprintf("SELECT * FROM %s", _ATTR::table());
        $stmt = Connection::get()->prepare($sql);
        $stmt->execute();
        $attributeRecord = $stmt->fetch(PDO::FETCH_ASSOC);

        $this->action->initialize($attributeRecord);

        // attribute
        $attribute = $this->container->getAttribute();
        $this->assertInstanceOf(Attribute::class, $attribute);
        $this->assertEquals($attributeRecord, $attribute->getBag()->getFields());
        // value
        $valueManager = $this->container->getValueManager();
        $this->assertEquals($valueKey, $valueManager->getKey());
        $this->assertEquals($value, $valueManager->getStored());
    }
}