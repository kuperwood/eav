<?php

namespace Tests\Unit\Strategy;

use Kuperwood\Eav\Attribute;
use Kuperwood\Eav\AttributeSet;
use Kuperwood\Eav\Entity;
use Kuperwood\Eav\Enum\VALUE_RESULT;
use Kuperwood\Eav\Model\ValueStringModel;
use Kuperwood\Eav\Result\ValueResult;
use Kuperwood\Eav\Strategy;
use Kuperwood\Eav\ValueManager;
use Tests\TestCase;

class StrategyTest extends TestCase
{
    public function setUp(): void
    {
        parent::setUp();
        $this->strategy = new Strategy();
    }

    /** @test */
    public function attribute() {
        $attribute = new Attribute();
        $this->strategy->setAttribute($attribute);
        $this->assertSame($attribute, $this->strategy->getAttribute());
    }

    /** @test */
    public function value_manager() {
        $value = new ValueManager();
        $this->strategy->setValueManager($value);
        $this->assertSame($value, $this->strategy->getValueManager());
    }

    /** @test */
    public function create_value() {
        $entityKey = 1;
        $domainKey = 2;
        $attrKey = 3;
        $valueToSave = 'test';

        $entity = new Entity();
        $entity->setKey($entityKey);
        $entity->setDomainKey($domainKey);

        $attrSet = new AttributeSet();
        $attrSet->setEntity($entity);

        $attribute = new Attribute();
        $attribute->setKey($attrKey);
        $attribute->setAttributeSet($attrSet);

        $this->strategy->setAttribute($attribute);
        $value = new ValueManager();
        $value->setRuntime($valueToSave);
        $this->strategy->setValueManager($value);

        $result = $this->strategy->createValue();

        $this->assertEquals(1, ValueStringModel::query()->count());

        $record = ValueStringModel::query()->first();
        $this->assertNotNull($record);
        $this->assertEquals($domainKey, $record->getDomainKey());
        $this->assertEquals($entityKey, $record->getEntityKey());
        $this->assertEquals($attrKey, $record->getAttrKey());
        $this->assertEquals($valueToSave, $record->getVal());

        $this->assertNull($value->getRuntime());
        $this->assertEquals($valueToSave, $value->getStored());
        $this->assertEquals($record->getKey(), $value->getKey());

        $this->assertInstanceOf(ValueResult::class, $result);
        $this->assertEquals(VALUE_RESULT::CREATED->code(), $result->getCode());
        $this->assertEquals(VALUE_RESULT::CREATED->message(), $result->getMessage());
    }

    /** @test */
    public function create_value_no_runtime() {
        $entity = new Entity();
        $attrSet = new AttributeSet();
        $attrSet->setEntity($entity);
        $attribute = new Attribute();
        $attribute->setAttributeSet($attrSet);
        $this->strategy->setAttribute($attribute);
        $value = new ValueManager();
        $this->strategy->setValueManager($value);
        $result = $this->strategy->createValue();
        $this->assertEquals(0, ValueStringModel::query()->count());
        $this->assertInstanceOf(ValueResult::class, $result);
        $this->assertEquals(VALUE_RESULT::EMPTY->code(), $result->getCode());
        $this->assertEquals(VALUE_RESULT::EMPTY->message(), $result->getMessage());
    }

    /** @test */
    public function update_value() {
        $valueToSave = 'test';
        $valueKey = 1;

        $record = new ValueStringModel();
        $record->setDomainKey(1)
            ->setEntityKey(2)
            ->setAttrKey(3)
            ->setVal('old');
        $record->save();
        $record->refresh();

        $attribute = new Attribute();
        $this->strategy->setAttribute($attribute);

        $value = new ValueManager();
        $value->setKey($valueKey);
        $value->setRuntime($valueToSave);
        $this->strategy->setValueManager($value);

        $result = $this->strategy->updateValue();

        $record = ValueStringModel::query()->first();
        $this->assertEquals($valueToSave, $record->getVal());

        $this->assertNull($value->getRuntime());
        $this->assertEquals($valueToSave, $value->getStored());
        $this->assertEquals($record->getKey(), $value->getKey());

        $this->assertInstanceOf(ValueResult::class, $result);
        $this->assertEquals(VALUE_RESULT::UPDATED->code(), $result->getCode());
        $this->assertEquals(VALUE_RESULT::UPDATED->message(), $result->getMessage());
    }

    /** @test */
    public function update_value_no_runtime() {
        $attribute = new Attribute();
        $this->strategy->setAttribute($attribute);
        $value = new ValueManager();
        $value->setKey(1);
        $this->strategy->setValueManager($value);
        $result = $this->strategy->updateValue();
        $this->assertInstanceOf(ValueResult::class, $result);
        $this->assertEquals(VALUE_RESULT::EMPTY->code(), $result->getCode());
        $this->assertEquals(VALUE_RESULT::EMPTY->message(), $result->getMessage());
    }

    /** @test */
    public function destroy() {
        $record = new ValueStringModel();
        $record->setDomainKey(1)
            ->setEntityKey(2)
            ->setAttrKey(3)
            ->setVal('old')
            ->save();

        $attribute = new Attribute();
        $this->strategy->setAttribute($attribute);
        $value = new ValueManager();
        $value->setKey($record->getKey());
        $value->setStored($record->getVal());
        $value->setRuntime('new');
        $this->strategy->setValueManager($value);
        $result = $this->strategy->destroy();

        $this->assertEquals(0, ValueStringModel::query()->count());

        $this->assertNull($value->getRuntime());
        $this->assertNull($value->getStored());
        $this->assertNull($value->getKey());

        $this->assertInstanceOf(ValueResult::class, $result);
        $this->assertEquals(VALUE_RESULT::DELETED->code(), $result->getCode());
        $this->assertEquals(VALUE_RESULT::DELETED->message(), $result->getMessage());
    }

    /** @test */
    public function destroy_no_key() {
        $attribute = new Attribute();
        $this->strategy->setAttribute($attribute);
        $value = new ValueManager();
        $value->setRuntime('new');
        $this->strategy->setValueManager($value);
        $result = $this->strategy->destroy();
        $this->assertInstanceOf(ValueResult::class, $result);
        $this->assertEquals(VALUE_RESULT::EMPTY->code(), $result->getCode());
        $this->assertEquals(VALUE_RESULT::EMPTY->message(), $result->getMessage());
    }

    /** @test */
    public function find() {
        $record = new ValueStringModel();
        $record->setDomainKey(1)
            ->setEntityKey(2)
            ->setAttrKey(3)
            ->setVal('test')
            ->save();

        $attribute = new Attribute();
        $this->strategy->setAttribute($attribute);
        $value = new ValueManager();
        $value->setKey($record->getKey());
        $this->strategy->setValueManager($value);

        $result = $this->strategy->find();

        $this->assertNull($value->getRuntime());
        $this->assertEquals("test", $value->getStored());

        $this->assertInstanceOf(ValueResult::class, $result);
        $this->assertEquals(VALUE_RESULT::FOUND->code(), $result->getCode());
        $this->assertEquals(VALUE_RESULT::FOUND->message(), $result->getMessage());
    }

    /** @test */
    public function find_no_key() {
        $attribute = new Attribute();
        $this->strategy->setAttribute($attribute);
        $value = new ValueManager();
        $this->strategy->setValueManager($value);
        $result = $this->strategy->find();
        $this->assertNull($value->getRuntime());
        $this->assertNull($value->getStored());
        $this->assertInstanceOf(ValueResult::class, $result);
        $this->assertEquals(VALUE_RESULT::EMPTY->code(), $result->getCode());
        $this->assertEquals(VALUE_RESULT::EMPTY->message(), $result->getMessage());
    }

    /** @test */
    public function find_no_record() {
        $attribute = new Attribute();
        $this->strategy->setAttribute($attribute);
        $value = new ValueManager();
        $value->setKey(123);
        $this->strategy->setValueManager($value);
        $result = $this->strategy->find();
        $this->assertNull($value->getRuntime());
        $this->assertNull($value->getStored());
        $this->assertInstanceOf(ValueResult::class, $result);
        $this->assertEquals(VALUE_RESULT::NOT_FOUND->code(), $result->getCode());
        $this->assertEquals(VALUE_RESULT::NOT_FOUND->message(), $result->getMessage());
    }
}