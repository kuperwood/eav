<?php
/**
 * This file is part of the eav package.
 * @author    Alex Kuperwood <alexkuperwood@gmail.com>
 * @copyright 2025 Alex Kuperwood
 * @license   https://opensource.org/license/mit  The MIT License
 */
declare(strict_types=1);

namespace Tests\Eav\ValueModel;

use Kuperwood\Eav\Database\Connection;
use Kuperwood\Eav\Enum\_VALUE;
use Kuperwood\Eav\Enum\ATTR_TYPE;
use Kuperwood\Eav\Model\ValueBase;
use Kuperwood\Eav\Value\ValueParser;
use PDO;
use Tests\TestCase;

class ValueModelFunctionalTest extends TestCase
{
    private ValueBase $model;

    public function setUp(): void
    {
        parent::setUp();
        $this->model = new ValueBase();
    }
    /**
     * @test
     * @group functional
     * @covers \Kuperwood\Eav\Model\ValueBase::__construct
     */
    public function defaults() {
        $this->assertEquals(_VALUE::ID, $this->model->getPrimaryKey());
    }

    /**
     * @return array
     */
    private function typesList(): array
    {
        return [ATTR_TYPE::STRING, ATTR_TYPE::INTEGER, ATTR_TYPE::DECIMAL, ATTR_TYPE::DATETIME, ATTR_TYPE::TEXT];
    }
    /**
     * @test
     * @group functional
     * @covers \Kuperwood\Eav\Model\ValueBase::create
     */
    public function create_record()
    {
        $domainKey = 1;
        $entityKey = 2;
        $attributeKey = 3;
        $parser = new ValueParser();
        foreach($this->typesList() as $case)
        {
            $value = ATTR_TYPE::randomValue($case);
            $valueKey = $this->model->create($case, $domainKey, $entityKey, $attributeKey, $value);
            $pdo = Connection::get();
            $table = ATTR_TYPE::valueTable($case);

            $sql = "SELECT * FROM `$table` WHERE " . _VALUE::DOMAIN_ID . " = ? 
        AND " . _VALUE::ENTITY_ID . " = ? 
        AND " . _VALUE::ATTRIBUTE_ID . " = ? LIMIT 1";

            $stmt = $pdo->prepare($sql);
            $stmt->execute([$domainKey, $entityKey, $attributeKey]);
            $valueRecord = $stmt->fetch(PDO::FETCH_ASSOC);
            $this->assertEquals($valueKey, $valueRecord[_VALUE::ID], "Iteration:".$case);
        }
    }

    /**
     * @test
     * @group functional
     * @covers \Kuperwood\Eav\Model\ValueBase::find
     */
    public function find_record()
    {
        $domainKey = 1;
        $entityKey = 2;
        $attributeKey = 3;
        $parser = new ValueParser();
        foreach($this->typesList() as $case)
        {
            $valueKey = $this->model->create($case, $domainKey, $entityKey, $attributeKey, ATTR_TYPE::randomValue($case));

            $pdo = Connection::get();
            $table = ATTR_TYPE::valueTable($case);
            $sql = "SELECT * FROM `$table` WHERE " . _VALUE::DOMAIN_ID . " = ? 
        AND " . _VALUE::ENTITY_ID . " = ? 
        AND " . _VALUE::ATTRIBUTE_ID . " = ? LIMIT 1";
            $stmt = $pdo->prepare($sql);
            $stmt->execute([$domainKey, $entityKey, $attributeKey]);
            $valueRecord = $stmt->fetch(PDO::FETCH_ASSOC);

            $record = $this->model->find($case, $domainKey, $entityKey, $attributeKey);
            $this->assertEquals($valueKey, $record[_VALUE::ID]);
            $this->assertSame($parser->parse($case,$valueRecord[_VALUE::VALUE]), $record[_VALUE::VALUE], "Iteration:".$case);
        }
    }

    /**
     * @test
     * @group functional
     * @covers \Kuperwood\Eav\Model\ValueBase::update
     */
    public function update_record()
    {
        $domainKey = 1;
        $entityKey = 2;
        $attributeKey = 3;
        $parser = new ValueParser();
        foreach($this->typesList() as $case)
        {
            $oldValue = ATTR_TYPE::randomValue($case);
            $newValue = ATTR_TYPE::randomValue($case);
            $this->model->create(ATTR_TYPE::getCase($case), $domainKey, $entityKey, $attributeKey, $oldValue);
            $result = $this->model->update(ATTR_TYPE::getCase($case), $domainKey, $entityKey, $attributeKey, $newValue);
            $this->assertEquals(1, $result, ATTR_TYPE::valueTable($case));
            $record = $this->model->find(ATTR_TYPE::getCase($case), $domainKey, $entityKey, $attributeKey);
            $this->assertIsArray($record);
            $this->assertSame($parser->parse($case, $newValue), $record[_VALUE::VALUE]);
        }
    }

    /**
     * @test
     * @group functional
     * @covers \Kuperwood\Eav\Model\ValueBase::destroy
     */
    public function destroy_record()
    {
        $domainKey = 1;
        $entityKey = 2;
        $attributeKey = 3;

        foreach($this->typesList() as $case)
        {
            $this->model->create($case, $domainKey, $entityKey, $attributeKey, ATTR_TYPE::randomValue($case));
            $result = $this->model->destroy($case, $domainKey, $entityKey, $attributeKey);
            $this->assertEquals(1, $result);
            $test = $this->model->find($case, $domainKey, $entityKey, $attributeKey);
            $this->assertFalse($test);
        }
    }
}