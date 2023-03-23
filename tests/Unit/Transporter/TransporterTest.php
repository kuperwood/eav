<?php

namespace Tests\Unit\Transporter;

use Kuperwood\Dev\Transporter;
use PHPUnit\Framework\TestCase;
use stdClass;

class TransporterTest extends TestCase
{
    private Transporter $transporter;

    public function setUp() : void
    {
        parent::setUp();
        $this->transporter = new Transporter();
    }

    /** @test */
    public function setField()
    {
        // Test setting a new field with a string key and a scalar value.
        $this->transporter->setField('foo', 'bar');
        $this->assertEquals('bar', $this->transporter->getField('foo'));

        // Test setting a new field with a string key and an array value.
        $this->transporter->setField('baz', [1, 2, 3]);
        $this->assertEquals([1, 2, 3], $this->transporter->getField('baz'));

        // Test setting a new field with a string key and an object value.
        $object = new stdClass();
        $object->prop1 = 'value1';
        $this->transporter->setField('obj', $object);
        $this->assertEquals($object, $this->transporter->getField('obj'));

        // Test setting a new field with a non-string key and a scalar value.
        $this->transporter->setField(123, 'test');
        $this->assertEquals('test', $this->transporter->getField(123));

        // Test setting a new field with an empty string key and a scalar value.
        $this->transporter->setField('', 'empty');
        $this->assertEquals('empty', $this->transporter->getField(''));
    }

    /** @test */
    public function getField()
    {
        // Test getting an existing field with a string key.
        $this->transporter->setField('foo', 'bar');
        $this->assertEquals('bar', $this->transporter->getField('foo'));
        // Test getting a field with a non-string key.
        $this->transporter->setField(123, 'test');
        $this->assertEquals('test', $this->transporter->getField(123));
        // Test getting a field with a not existing key.
        $this->assertNull($this->transporter->getField('none'));
    }

    /** @test */
    public function getData()
    {
        // Test getting the data array when the array is empty.
        $data = $this->transporter->getData();
        $this->assertIsArray($data);
        $this->assertEmpty($data);

        // Test getting the data array when the array has one field.
        $this->transporter->setField('foo', 'bar');
        $data = $this->transporter->getData();
        $this->assertIsArray($data);
        $this->assertCount(1, $data);
        $this->assertArrayHasKey('foo', $data);
        $this->assertEquals('bar', $data['foo']);

        // Test getting the data array when the array has multiple fields.
        $this->transporter->setField('baz', [1, 2, 3]);
        $data = $this->transporter->getData();
        $this->assertIsArray($data);
        $this->assertCount(2, $data);
        $this->assertArrayHasKey('foo', $data);
        $this->assertEquals('bar', $data['foo']);
        $this->assertArrayHasKey('baz', $data);
        $this->assertEquals([1, 2, 3], $data['baz']);
    }

    /** @test */
    public function test_setData()
    {
        // Test setting an empty array.
        $this->transporter->setData([]);
        $data = $this->transporter->getData();
        $this->assertIsArray($data);
        $this->assertEmpty($data);

        // Test setting an array with one field.
        $this->transporter->setData(['foo' => 'bar']);
        $data = $this->transporter->getData();
        $this->assertIsArray($data);
        $this->assertCount(1, $data);
        $this->assertArrayHasKey('foo', $data);
        $this->assertEquals('bar', $data['foo']);

        // Test setting an array with multiple fields.
        $this->transporter->setData(['baz' => [1, 2, 3], 'qux' => (object)['a' => 1]]);
        $data = $this->transporter->getData();
        $this->assertIsArray($data);
        $this->assertCount(2, $data);
        $this->assertArrayHasKey('baz', $data);
        $this->assertEquals([1, 2, 3], $data['baz']);
        $this->assertArrayHasKey('qux', $data);
        $this->assertEquals((object)['a' => 1], $data['qux']);
    }

    /** @test */
    public function hasField()
    {
        // Test checking for an existing field with a string key.
        $this->transporter->setField('foo', 'bar');
        $this->assertTrue($this->transporter->hasField('foo'));

        // Test checking for a non-existing field with a string key.
        $this->assertFalse($this->transporter->hasField('non_existing_field'));

        // Test checking for a field with a non-string key.
        $this->assertFalse($this->transporter->hasField(123));
    }

    /** @test */
    public function removeField()
    {
        // Test removing an existing field with a string key.
        $this->transporter->setField('foo', 'bar');
        $this->transporter->removeField('foo');
        $this->assertFalse($this->transporter->hasField('foo'));

        // Test removing a non-existing field with a string key.
        $this->transporter->removeField('non_existing_field');
        $this->assertFalse($this->transporter->hasField('non_existing_field'));

        // Test removing a field with a non-string key.
        $this->transporter->removeField(123);
        $this->assertFalse($this->transporter->hasField(123));
    }

    /** @test */
    public function clear()
    {
        // Test clearing an empty array.
        $this->transporter->clear();
        $this->assertSame([], $this->transporter->getData());

        // Test clearing an array with one field.
        $this->transporter->setField('foo', 'bar');
        $this->transporter->clear();
        $this->assertSame([], $this->transporter->getData());

        // Test clearing an array with multiple fields.
        $this->transporter->setData(['foo' => 'bar', 'baz' => 'qux']);
        $this->transporter->clear();
        $this->assertSame([], $this->transporter->getData());
    }

    /** @test */
    public function test__get()
    {
        // Test getting an existing field with a string key.
        $this->transporter->setField('foo', 'bar');
        $this->assertSame('bar', $this->transporter->foo);
    }

    /** @test */
    public function magicSetMethod()
    {
        // Test setting a new field with a string key and a scalar value.
        $this->transporter->test = 42;
        $this->assertEquals(42, $this->transporter->test);

        // Test setting a new field with a string key and an array value.
        $this->transporter->test = ['apple', 'banana'];
        $this->assertEquals(['apple', 'banana'], $this->transporter->test);

        // Test setting a new field with a string key and an object value.
        $obj = new stdClass();
        $obj->name = 'John';
        $obj->age = 30;
        $this->transporter->test = $obj;
        $this->assertEquals($obj, $this->transporter->test);

        // Test setting a new field with a non-string key and a scalar value.
        $this->transporter->{42} = 'hello';
        $this->assertEquals('hello', $this->transporter->{42});

        // Test setting a new field with an empty string key and a scalar value.
        $this->transporter->{''} = 'world';
        $this->assertEquals('world', $this->transporter->{''});
    }

    /** @test */
    public function test__isset()
    {
        $this->assertFalse(isset($this->transporter->foo));
        $this->transporter->setField('foo', 'bar');
        $this->assertTrue(isset($this->transporter->foo));
        $this->assertFalse(isset($this->transporter->non_existing_field));
        $this->assertFalse(isset($this->transporter->{123}));
    }

    /** @test */
    public function unsetRemovesFieldFromDataArray(): void
    {
        // Arrange
        $field = 'foo';
        $value = 'bar';
        $this->transporter->setField($field, $value);

        // Act
        unset($this->transporter->{$field});

        // Assert
        $this->assertFalse($this->transporter->hasField($field));
    }

    /** @test */
    public function toStringReturnsJsonEncodedDataArray(): void
    {
        // Arrange
        $data = [
            'foo' => 'bar',
            'baz' => 123,
            'qux' => true,
        ];
        $this->transporter->setData($data);

        // Act
        $result = (string) $this->transporter;

        // Assert
        $this->assertJson($result);
        $this->assertEquals($data, json_decode($result, true));
    }

    /** @test */
    public function toArrayReturnsArrayRepresentationOfDataArray(): void
    {
        // Arrange
        $data = [
            'foo' => 'bar',
            'baz' => 123,
            'qux' => true,
        ];
        $this->transporter->setData($data);

        // Act
        $result = $this->transporter->__toArray();

        // Assert
        $this->assertIsArray($result);
        $this->assertEquals($data, $result);
    }

    /** @test */
    public function toJsonReturnsJsonEncodedStringRepresentationOfDataArray(): void
    {
        // Arrange
        $data = [
            'foo' => 'bar',
            'baz' => 123,
            'qux' => true,
        ];
        $this->transporter->setData($data);

        // Act
        $result = $this->transporter->__toJson();

        // Assert
        $this->assertJson($result);
        $this->assertEquals(json_encode($data), $result);
    }

    /** @test */
    public function toObjectReturnsObject()
    {
        $this->transporter->setField("name", "John Doe");
        $this->transporter->setField("age", 30);
        $result = $this->transporter->__toObject();
        $this->assertIsObject($result);
    }

}