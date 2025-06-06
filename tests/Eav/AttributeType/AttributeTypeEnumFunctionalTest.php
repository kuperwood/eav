<?php
/**
 * This file is part of the eav package.
 * @author    Alex Kuperwood <alexkuperwood@gmail.com>
 * @copyright 2025 Alex Kuperwood
 * @license   https://opensource.org/license/mit  The MIT License
 */
declare(strict_types=1);

namespace Tests\Eav\AttributeType;

use Kuperwood\Eav\Enum\_VALUE;
use Kuperwood\Eav\Enum\ATTR_TYPE;
use Kuperwood\Eav\Exception\AttributeException;
use Kuperwood\Eav\Validation\Constraints\DateConstraint;
use Kuperwood\Eav\Validation\Constraints\LengthConstraint;
use Kuperwood\Eav\Validation\Constraints\NumericConstraint;
use Kuperwood\Eav\Validation\Constraints\RegexConstraint;
use PHPUnit\Framework\TestCase;

class AttributeTypeEnumFunctionalTest extends TestCase
{
    /**
     * @test
     * @group functional
     * @covers \Kuperwood\Eav\Enum\ATTR_TYPE::INTEGER
     * @covers \Kuperwood\Eav\Enum\ATTR_TYPE::DATETIME
     * @covers \Kuperwood\Eav\Enum\ATTR_TYPE::DECIMAL
     * @covers \Kuperwood\Eav\Enum\ATTR_TYPE::STRING
     * @covers \Kuperwood\Eav\Enum\ATTR_TYPE::TEXT
     * @covers \Kuperwood\Eav\Enum\ATTR_TYPE::MANUAL
     */
    public function value() {
        $this->assertEquals("int", ATTR_TYPE::INTEGER);
        $this->assertEquals("datetime", ATTR_TYPE::DATETIME);
        $this->assertEquals("decimal", ATTR_TYPE::DECIMAL);
        $this->assertEquals("varchar", ATTR_TYPE::STRING);
        $this->assertEquals("text", ATTR_TYPE::TEXT);
        $this->assertEquals("manual", ATTR_TYPE::MANUAL);
    }
    /**
     * @test
     * @group functional
     * @covers \Kuperwood\Eav\Enum\ATTR_TYPE::isValid
     */
    public function is_valid() {
        $this->assertTrue(ATTR_TYPE::isValid(ATTR_TYPE::INTEGER));
        $this->assertTrue(ATTR_TYPE::isValid(ATTR_TYPE::DATETIME));
        $this->assertTrue(ATTR_TYPE::isValid(ATTR_TYPE::DECIMAL));
        $this->assertTrue(ATTR_TYPE::isValid(ATTR_TYPE::STRING));
        $this->assertTrue(ATTR_TYPE::isValid(ATTR_TYPE::TEXT));
        $this->assertFalse(ATTR_TYPE::isValid(ATTR_TYPE::MANUAL));
        $this->assertFalse(ATTR_TYPE::isValid("test"));
    }
    /**
     * @test
     * @group functional
     * @covers \Kuperwood\Eav\Enum\ATTR_TYPE::valueTable
     */
    public function value_table() {
        $this->assertEquals(sprintf(_VALUE::table(), ATTR_TYPE::INTEGER), ATTR_TYPE::valueTable(ATTR_TYPE::INTEGER));
        $this->assertEquals(sprintf(_VALUE::table(), ATTR_TYPE::DATETIME), ATTR_TYPE::valueTable(ATTR_TYPE::DATETIME));
        $this->assertEquals(sprintf(_VALUE::table(), ATTR_TYPE::DECIMAL), ATTR_TYPE::valueTable(ATTR_TYPE::DECIMAL));
        $this->assertEquals(sprintf(_VALUE::table(), ATTR_TYPE::STRING), ATTR_TYPE::valueTable(ATTR_TYPE::STRING));
        $this->assertEquals(sprintf(_VALUE::table(), ATTR_TYPE::TEXT), ATTR_TYPE::valueTable(ATTR_TYPE::TEXT));
    }
    /**
     * @test
     * @group functional
     * @covers \Kuperwood\Eav\Enum\ATTR_TYPE::validationRule
     */
    public function validation_rule() {
        $this->assertEquals([new NumericConstraint()], ATTR_TYPE::validationRule(ATTR_TYPE::INTEGER));
        $this->assertEquals([new DateConstraint('Y-m-d H:i:s')], ATTR_TYPE::validationRule(ATTR_TYPE::DATETIME));
        $this->assertEquals([new RegexConstraint('/^[0-9]{1,21}(?:\.[0-9]{1,6})?$/')], ATTR_TYPE::validationRule(ATTR_TYPE::DECIMAL));
        $this->assertEquals([new LengthConstraint( 1,191)], ATTR_TYPE::validationRule(ATTR_TYPE::STRING));
        $this->assertEquals([new LengthConstraint( 1,10000)], ATTR_TYPE::validationRule(ATTR_TYPE::TEXT));
    }
    /**
     * @test
     * @group functional
     * @covers \Kuperwood\Eav\Enum\ATTR_TYPE::getCase
     */
    public function get_case() {
        $this->assertEquals(ATTR_TYPE::INTEGER, ATTR_TYPE::getCase(ATTR_TYPE::INTEGER));
        $this->assertEquals(ATTR_TYPE::DATETIME, ATTR_TYPE::getCase(ATTR_TYPE::DATETIME));
        $this->assertEquals(ATTR_TYPE::DECIMAL, ATTR_TYPE::getCase(ATTR_TYPE::DECIMAL));
        $this->assertEquals(ATTR_TYPE::STRING, ATTR_TYPE::getCase(ATTR_TYPE::STRING));
        $this->assertEquals(ATTR_TYPE::TEXT, ATTR_TYPE::getCase(ATTR_TYPE::TEXT));
    }
    /**
     * @test
     * @group functional
     * @covers \Kuperwood\Eav\Enum\ATTR_TYPE::getCase
     */
    public function get_case_exception() {
        $this->expectException(AttributeException::class);
        $this->expectExceptionMessage(sprintf(AttributeException::UNSUPPORTED_TYPE, 'test'));
        ATTR_TYPE::getCase("test");
    }

    /**
     * @test
     * @group functional
     * @covers \Kuperwood\Eav\Enum\ATTR_TYPE::randomValue
     */
    public function random_value() {
        $this->assertTrue(is_string(ATTR_TYPE::randomValue(ATTR_TYPE::STRING)));
        $this->assertTrue(is_int(ATTR_TYPE::randomValue(ATTR_TYPE::INTEGER)));
        $this->assertTrue(is_float(ATTR_TYPE::randomValue(ATTR_TYPE::DECIMAL)));
        $this->assertTrue(preg_match('/^\d{4}-\d{2}-\d{2} \d{2}:\d{2}:\d{2}\.\d{6}$/', ATTR_TYPE::randomValue(ATTR_TYPE::DATETIME)) === 1);
        $this->assertTrue(is_string(ATTR_TYPE::randomValue(ATTR_TYPE::TEXT)));
    }
}
