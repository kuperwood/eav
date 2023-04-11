<?php

namespace Tests\Unit\ValueModel;

use Drobotik\Eav\Enum\ATTR_TYPE;
use Drobotik\Eav\Model\ValueBase;
use Drobotik\Eav\Model\ValueDatetimeModel;
use Drobotik\Eav\Model\ValueDecimalModel;
use Drobotik\Eav\Model\ValueIntegerModel;
use Drobotik\Eav\Model\ValueStringModel;
use Drobotik\Eav\Model\ValueTextModel;
use PHPUnit\Framework\TestCase;

class ValueModelFunctionalTest extends TestCase
{
    /**
     * @test
     * @group functional
     * @covers ValueBase::setDomainKey, ValueBase::getDomainKey
     */
    public function domainKey() {
        $model = new ValueBase();
        $model->setDomainKey(123);
        $this->assertEquals(123, $model->getDomainKey());
    }

    /**
     * @test
     * @group functional
     * @covers ValueBase::setEntityKey, ValueBase::getEntityKey
     */
    public function valueEntityKey() {
        $model = new ValueBase();
        $model->setEntityKey(456);
        $this->assertEquals(456, $model->getEntityKey());
    }
    /**
     * @test
     * @group functional
     * @covers ValueBase::setAttrKey,ValueBase::getAttrKey
     */
    public function attrKey() {
        $model = new ValueBase();
        $model->setAttrKey(789);
        $this->assertEquals(789, $model->getAttrKey());
    }
    /**
     * @test
     * @group functional
     * @covers ValueBase::getValue, ValueBase::getValue
     */
    public function value() {
        $model = new ValueBase();
        $model->setValue("test");
        $this->assertEquals("test", $model->getValue());
    }
    /**
     * @test
     * @group functional
     * @covers ValueStringModel::getTable
     */
    public function value_string() {
        $model = new ValueStringModel();
        $this->assertEquals(ATTR_TYPE::STRING->valueTable(), $model->getTable());
    }
    /**
     * @test
     * @group functional
     * @covers ValueTextModel::getTable
     */
    public function value_text() {
        $model = new ValueTextModel();
        $this->assertEquals(ATTR_TYPE::TEXT->valueTable(), $model->getTable());
    }
    /**
     * @test
     * @group functional
     * @covers ValueIntegerModel::getTable
     */
    public function value_integer() {
        $model = new ValueIntegerModel();
        $this->assertEquals(ATTR_TYPE::INTEGER->valueTable(), $model->getTable());
    }
    /**
     * @test
     * @group functional
     * @covers ValueDatetimeModel::getTable
     */
    public function value_datetime() {
        $model = new ValueDatetimeModel();
        $this->assertEquals(ATTR_TYPE::DATETIME->valueTable(), $model->getTable());
    }
    /**
     * @test
     * @group functional
     * @covers ValueDecimalModel::getTable
     */
    public function value_decimal() {
        $model = new ValueDecimalModel();
        $this->assertEquals(ATTR_TYPE::DECIMAL->valueTable(), $model->getTable());
    }
}