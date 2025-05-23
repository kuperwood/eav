<?php
/**
 * This file is part of the eav package.
 * @author    Alex Kuperwood <alexkuperwood@gmail.com>
 * @copyright 2025 Alex Kuperwood
 * @license   https://opensource.org/license/mit  The MIT License
 */
declare(strict_types=1);

namespace Tests\Eav\ValueManager;

use Kuperwood\Eav\Value\ValueManager;
use PHPUnit\Framework\TestCase;

class ValueManagerFunctionalTest extends TestCase
{
    protected ValueManager $value;

    public function setUp(): void
    {
        parent::setUp();
        $this->value = new ValueManager();
    }
    /**
     * @test
     * @group functional
     * @covers \Kuperwood\Eav\Value\ValueManager::hasKey
     * @covers \Kuperwood\Eav\Value\ValueManager::getKey
     * @covers \Kuperwood\Eav\Value\ValueManager::setKey
     */
    public function key() {
        $this->assertFalse($this->value->hasKey());
        $this->value->setKey(1);
        $this->assertEquals(1, $this->value->getKey());
        $this->assertTrue($this->value->hasKey());
    }
    /**
     * @test
     * @group functional
     * @covers \Kuperwood\Eav\Value\ValueManager::hasKey
     * @covers \Kuperwood\Eav\Value\ValueManager::getKey
     * @covers \Kuperwood\Eav\Value\ValueManager::setKey
     */
    public function zero_key() {
        $this->value->setKey(0);
        $this->assertFalse($this->value->hasKey());
        $this->assertEquals(0, $this->value->getKey());
    }
    /**
     * @test
     * @group functional
     * @covers \Kuperwood\Eav\Value\ValueManager::getRuntime
     * @covers \Kuperwood\Eav\Value\ValueManager::__construct
     */
    public function get_runtime_value() {
        $this->assertEquals(null, $this->value->getRuntime());
    }
    /**
     * @test
     * @group functional
     * @covers \Kuperwood\Eav\Value\ValueManager::setRuntime
     */
    public function set_runtime_value() {
        $this->value->setRuntime(1);
        $this->assertEquals(1, $this->value->getRuntime());
        $this->value->setRuntime('test');
        $this->assertEquals('test', $this->value->getRuntime());
    }
    /**
     * @test
     * @group functional
     * @covers \Kuperwood\Eav\Value\ValueManager::isRuntime
     */
    public function is_runtime_value() {
        $this->assertFalse($this->value->isRuntime());
        $this->value->setRuntime(1);
        $this->assertTrue($this->value->isRuntime());
    }
    /**
     * @test
     * @group functional
     * @covers \Kuperwood\Eav\Value\ValueManager::clearValue
     */
    public function clear_runtime_value() {
        $this->value->setRuntime(1);
        $this->value->clearRuntime();
        $this->assertFalse($this->value->isRuntime());
    }
    /**
     * @test
     * @group functional
     * @covers \Kuperwood\Eav\Value\ValueManager::getStored
     * @covers \Kuperwood\Eav\Value\ValueManager::__construct
     */
    public function get_stored_value() {
        $this->assertEquals(null, $this->value->getStored());
    }
    /**
     * @test
     * @group functional
     * @covers \Kuperwood\Eav\Value\ValueManager::setStored
     */
    public function set_stored_value() {
        $this->value->setStored(1);
        $this->assertEquals(1, $this->value->getStored());
        $this->value->setStored('test');
        $this->assertEquals('test', $this->value->getStored());
    }
    /**
     * @test
     * @group functional
     * @covers \Kuperwood\Eav\Value\ValueManager::isStored
     */
    public function is_stored_value() {
        $this->assertFalse($this->value->isStored());
        $this->value->setStored(1);
        $this->assertTrue($this->value->isStored());
    }
    /**
     * @test
     * @group functional
     * @covers \Kuperwood\Eav\Value\ValueManager::isRuntime
     */
    public function clear_stored_value() {
        $this->value->setStored(1);
        $this->value->clearStored();
        $this->assertFalse($this->value->isRuntime());
    }
    /**
     * @test
     * @group functional
     * @covers \Kuperwood\Eav\Value\ValueManager::getValue
     */
    public function get_value() {
        $this->assertEquals(null, $this->value->getValue());
        $this->value->setRuntime(123);
        $this->assertEquals(123, $this->value->getValue());
    }
    /**
     * @test
     * @group functional
     * @covers \Kuperwood\Eav\Value\ValueManager::setValue
     * @covers \Kuperwood\Eav\Value\ValueManager::clearStored
     * @covers \Kuperwood\Eav\Value\ValueManager::clearRuntime
     * @covers \Kuperwood\Eav\Value\ValueManager::clearValue
     */
    public function set_value() {
        $this->value->setValue(1);
        $this->assertEquals(1, $this->value->getValue());
        $this->assertEquals(1, $this->value->getRuntime());
        $this->assertEquals(null, $this->value->getStored());

        $this->value->clearValue();
        $this->assertEquals(null, $this->value->getValue());
        $this->assertEquals(null, $this->value->getRuntime());
        $this->assertEquals(null, $this->value->getStored());

        $this->value->setStored(1);
        $this->assertEquals(1, $this->value->getValue());
        $this->assertEquals(null, $this->value->getRuntime());
        $this->assertEquals(1, $this->value->getStored());

        $this->value->setValue(2);
        $this->assertEquals(2, $this->value->getValue());
        $this->assertEquals(2, $this->value->getRuntime());
        $this->assertEquals(1, $this->value->getStored());

        $this->value->clearValue();
        $this->assertEquals(1, $this->value->getValue());
        $this->assertEquals(null, $this->value->getRuntime());
        $this->assertEquals(1, $this->value->getStored());

        $this->value->setValue(1);
        $this->value->clearStored();
        $this->assertEquals(1, $this->value->getValue());
        $this->assertEquals(1, $this->value->getRuntime());
        $this->assertEquals(null, $this->value->getStored());
    }
    /**
     * @test
     * @group functional
     * @covers \Kuperwood\Eav\Value\ValueManager::isClean
     * @covers \Kuperwood\Eav\Value\ValueManager::isEquivalent
     */
    public function is_clean() {
        $this->assertTrue($this->value->isClean());
        $this->value->setValue(22);
        $this->assertFalse($this->value->isClean());
        $this->value->clearValue();
        $this->assertTrue($this->value->isClean());
        $this->value->setStored("220.02");
        $this->assertTrue($this->value->isClean());
        $this->value->setValue(220.03);
        $this->assertFalse($this->value->isClean());
    }
}