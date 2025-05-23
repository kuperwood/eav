<?php
/**
 * This file is part of the eav package.
 *
 * @author    Alex Kuperwood <alexkuperwood@gmail.com>
 * @copyright 2025 Alex Kuperwood
 * @license   https://opensource.org/license/mit  The MIT License
 */
declare(strict_types=1);

namespace Tests\Eav\EntityFactory;

use Kuperwood\Eav\Database\Connection;
use Kuperwood\Eav\Enum\_ATTR;
use Kuperwood\Eav\Enum\_ENTITY;
use Kuperwood\Eav\Enum\_PIVOT;
use Kuperwood\Eav\Enum\_VALUE;
use Kuperwood\Eav\Enum\ATTR_FACTORY;
use Kuperwood\Eav\Enum\ATTR_TYPE;
use Kuperwood\Eav\Exception\AttributeException;
use Kuperwood\Eav\Exception\EntityFactoryException;
use Kuperwood\Eav\Factory\EntityFactory;
use Kuperwood\Eav\Model\AttributeModel;
use Kuperwood\Eav\Model\PivotModel;
use Kuperwood\Eav\Model\ValueBase;
use Kuperwood\Eav\Result\EntityFactoryResult;
use Kuperwood\Eav\Value\ValueParser;
use PDO;
use Tests\TestCase;

class EntityFactoryFunctionalTest extends TestCase
{
    private EntityFactory $factory;

    public function setUp(): void
    {
        parent::setUp();
        $this->factory = new EntityFactory();
    }

    protected function getFactoryDefaultConfig(): array
    {
        return [
            ATTR_TYPE::STRING => [
                ATTR_FACTORY::ATTRIBUTE => [
                    _ATTR::NAME => ATTR_TYPE::STRING,
                    _ATTR::TYPE => ATTR_TYPE::STRING,
                    _ATTR::DEFAULT_VALUE => ATTR_TYPE::randomValue(ATTR_TYPE::STRING),
                ]
            ],
            ATTR_TYPE::INTEGER => [
                ATTR_FACTORY::ATTRIBUTE => [
                    _ATTR::NAME => ATTR_TYPE::INTEGER,
                    _ATTR::TYPE => ATTR_TYPE::INTEGER,
                    _ATTR::DEFAULT_VALUE => ATTR_TYPE::randomValue(ATTR_TYPE::INTEGER),
                ]
            ],
            ATTR_TYPE::DECIMAL => [
                ATTR_FACTORY::ATTRIBUTE => [
                    _ATTR::NAME => ATTR_TYPE::DECIMAL,
                    _ATTR::TYPE => ATTR_TYPE::DECIMAL,
                    _ATTR::DEFAULT_VALUE => ATTR_TYPE::randomValue(ATTR_TYPE::DECIMAL),
                ]
            ],
            ATTR_TYPE::DATETIME => [
                ATTR_FACTORY::ATTRIBUTE => [
                    _ATTR::NAME => ATTR_TYPE::DATETIME,
                    _ATTR::TYPE => ATTR_TYPE::DATETIME,
                    _ATTR::DEFAULT_VALUE => ATTR_TYPE::randomValue(ATTR_TYPE::DATETIME),
                ]
            ],
            ATTR_TYPE::TEXT => [
                ATTR_FACTORY::ATTRIBUTE => [
                    _ATTR::NAME => ATTR_TYPE::TEXT,
                    _ATTR::TYPE => ATTR_TYPE::TEXT,
                    _ATTR::DEFAULT_VALUE => ATTR_TYPE::randomValue(ATTR_TYPE::TEXT),
                ]
            ]
        ];
    }

    /**
     * @test
     *
     * @group functional
     *
     * @covers \Kuperwood\Eav\Factory\EntityFactory::create
     * @covers \Kuperwood\Eav\Factory\EntityFactory::makeNewResult
     * @covers \Kuperwood\Eav\Factory\EntityFactory::getResult
     * @covers \Kuperwood\Eav\Factory\EntityFactory::validateFields
     * @covers \Kuperwood\Eav\Factory\EntityFactory::handleAttribute
     * @covers \Kuperwood\Eav\Factory\EntityFactory::handlePivot
     * @covers \Kuperwood\Eav\Factory\EntityFactory::handleValue
     * @covers \Kuperwood\Eav\Factory\EntityFactory::handleField
     */
    public function create_entity() {
        $domainKey = $this->eavFactory->createDomain();
        $setKey = $this->eavFactory->createAttributeSet($domainKey);
        $result = $this->factory->create([], $domainKey, $setKey);
        $this->assertInstanceOf(EntityFactoryResult::class, $result);

        $conn = Connection::get();
        $sql = sprintf(
            "SELECT * FROM %s WHERE %s = :setKey AND %s = :domainKey",
            _ENTITY::table(),
            _ENTITY::DOMAIN_ID,
            _ENTITY::ATTR_SET_ID
        );
        $stmt = $conn->prepare($sql);
        $stmt->bindValue(':setKey', $setKey);
        $stmt->bindValue(':domainKey', $domainKey);
        $stmt->execute();

        $result = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $this->assertEquals(1, count($result));

        $this->assertEquals([
            _ENTITY::ID => 1,
            _ENTITY::DOMAIN_ID => $domainKey,
            _ENTITY::ATTR_SET_ID => $setKey,
            _ENTITY::SERVICE_KEY => null
        ], $result[0]);
    }

    /**
     * @test
     * @group functional
     * @covers \Kuperwood\Eav\Factory\EntityFactory::create
     * @covers \Kuperwood\Eav\Factory\EntityFactory::makeNewResult
     * @covers \Kuperwood\Eav\Factory\EntityFactory::getResult
     * @covers \Kuperwood\Eav\Factory\EntityFactory::validateFields
     * @covers \Kuperwood\Eav\Factory\EntityFactory::handleAttribute
     * @covers \Kuperwood\Eav\Factory\EntityFactory::handlePivot
     * @covers \Kuperwood\Eav\Factory\EntityFactory::handleValue
     * @covers \Kuperwood\Eav\Factory\EntityFactory::handleField
     */
    public function create_attributes_no_group_exception() {
        $this->expectException(EntityFactoryException::class);
        $this->expectExceptionMessage("Group key must be provided!");
        $domainKey = $this->eavFactory->createDomain();
        $setKey = $this->eavFactory->createAttributeSet($domainKey);
        $config = $this->getFactoryDefaultConfig();
        $this->factory->create($config, $domainKey, $setKey);
    }

    /**
     * @test
     * @group functional
     * @covers \Kuperwood\Eav\Factory\EntityFactory::create
     * @covers \Kuperwood\Eav\Factory\EntityFactory::makeNewResult
     * @covers \Kuperwood\Eav\Factory\EntityFactory::getResult
     * @covers \Kuperwood\Eav\Factory\EntityFactory::validateFields
     * @covers \Kuperwood\Eav\Factory\EntityFactory::handleAttribute
     * @covers \Kuperwood\Eav\Factory\EntityFactory::handlePivot
     * @covers \Kuperwood\Eav\Factory\EntityFactory::handleValue
     * @covers \Kuperwood\Eav\Factory\EntityFactory::handleField
     */
    public function create_attributes_not_existing_group_exception() {
        $this->expectException(EntityFactoryException::class);
        $this->expectExceptionMessage('This group is not belongs to attribute set');
        $domainKey = $this->eavFactory->createDomain();
        $setKey = $this->eavFactory->createAttributeSet($domainKey);
        $config = $this->getFactoryDefaultConfig();
        $config[ATTR_TYPE::STRING][ATTR_FACTORY::GROUP] = 1;
        $config[ATTR_TYPE::INTEGER][ATTR_FACTORY::GROUP] = 2;
        $config[ATTR_TYPE::DECIMAL][ATTR_FACTORY::GROUP] = 3;
        $config[ATTR_TYPE::DATETIME][ATTR_FACTORY::GROUP] = 4;
        $config[ATTR_TYPE::TEXT][ATTR_FACTORY::GROUP] = 5;

        $this->factory->create($config, $domainKey, $setKey);
    }

    /**
     * @test
     * @group functional
     * @covers \Kuperwood\Eav\Factory\EntityFactory::create
     * @covers \Kuperwood\Eav\Factory\EntityFactory::makeNewResult
     * @covers \Kuperwood\Eav\Factory\EntityFactory::getResult
     * @covers \Kuperwood\Eav\Factory\EntityFactory::validateFields
     * @covers \Kuperwood\Eav\Factory\EntityFactory::handleAttribute
     * @covers \Kuperwood\Eav\Factory\EntityFactory::handlePivot
     * @covers \Kuperwood\Eav\Factory\EntityFactory::handleValue
     * @covers \Kuperwood\Eav\Factory\EntityFactory::handleField
     */
    public function create_attributes() {
        $domainKey = $this->eavFactory->createDomain();
        $setKey = $this->eavFactory->createAttributeSet($domainKey);
        $groupKey = $this->eavFactory->createGroup($setKey);

        $config = $this->getFactoryDefaultConfig();

        $config[ATTR_TYPE::STRING][ATTR_FACTORY::GROUP] = $groupKey;
        $config[ATTR_TYPE::INTEGER][ATTR_FACTORY::GROUP] = $groupKey;
        $config[ATTR_TYPE::DECIMAL][ATTR_FACTORY::GROUP] = $groupKey;
        $config[ATTR_TYPE::DATETIME][ATTR_FACTORY::GROUP] = $groupKey;
        $config[ATTR_TYPE::TEXT][ATTR_FACTORY::GROUP] = $groupKey;

        $stringConfig = $config[ATTR_TYPE::STRING][ATTR_FACTORY::ATTRIBUTE];
        $integerConfig = $config[ATTR_TYPE::INTEGER][ATTR_FACTORY::ATTRIBUTE];
        $decimalConfig = $config[ATTR_TYPE::DECIMAL][ATTR_FACTORY::ATTRIBUTE];
        $datetimeConfig = $config[ATTR_TYPE::DATETIME][ATTR_FACTORY::ATTRIBUTE];
        $textConfig = $config[ATTR_TYPE::TEXT][ATTR_FACTORY::ATTRIBUTE];

        $result = $this->factory->create($config, $domainKey, $setKey);

        // check attributes created
        $sql = sprintf(
            "SELECT * FROM %s WHERE %s = :domain AND %s = :type AND %s = :name",
            _ATTR::table(),
            _ATTR::DOMAIN_ID,
            _ATTR::TYPE,
            _ATTR::NAME
        );

        $stmt = Connection::get()->prepare($sql);

        $stmt->bindParam(':domain', $domainKey, PDO::PARAM_INT);

        // Fetch for string
        $typeString = ATTR_TYPE::STRING;
        $stmt->bindParam(':type', $typeString, PDO::PARAM_STR);
        $stmt->bindParam(':name', $typeString, PDO::PARAM_STR);
        $stmt->execute();
        $string = $stmt->fetch(PDO::FETCH_ASSOC);

        // Fetch for integer
        $typeInteger = ATTR_TYPE::INTEGER;
        $stmt->bindParam(':type', $typeInteger, PDO::PARAM_STR);
        $stmt->bindParam(':name', $typeInteger, PDO::PARAM_STR);
        $stmt->execute();
        $integer = $stmt->fetch(PDO::FETCH_ASSOC);

        // Fetch for decimal
        $typeDecimal = ATTR_TYPE::DECIMAL;
        $stmt->bindParam(':type', $typeDecimal, PDO::PARAM_STR);
        $stmt->bindParam(':name', $typeDecimal, PDO::PARAM_STR);
        $stmt->execute();
        $decimal = $stmt->fetch(PDO::FETCH_ASSOC);

        // Fetch for datetime
        $typeDatetime = ATTR_TYPE::DATETIME;
        $stmt->bindParam(':type', $typeDatetime, PDO::PARAM_STR);
        $stmt->bindParam(':name', $typeDatetime, PDO::PARAM_STR);
        $stmt->execute();
        $datetime = $stmt->fetch(PDO::FETCH_ASSOC);

        // Fetch for text
        $typeText = ATTR_TYPE::TEXT;
        $stmt->bindParam(':type', $typeText, PDO::PARAM_STR);
        $stmt->bindParam(':name', $typeText, PDO::PARAM_STR);
        $stmt->execute();
        $text = $stmt->fetch(PDO::FETCH_ASSOC);

        $this->assertIsArray($string);
        $this->assertIsArray($integer);
        $this->assertIsArray($decimal);
        $this->assertIsArray($datetime);
        $this->assertIsArray($text);

        $stringKey = $string[_ATTR::ID];
        $integerKey = $integer[_ATTR::ID];
        $decimalKey = $decimal[_ATTR::ID];
        $datetimeKey = $datetime[_ATTR::ID];
        $textKey = $text[_ATTR::ID];


        $expectedString = array_merge(
            _ATTR::bag(),
            $stringConfig,
            [_ATTR::ID => $stringKey, _ATTR::DOMAIN_ID => $domainKey]
        );
        $expectedInteger = array_merge(
            _ATTR::bag(),
            $integerConfig,
            [_ATTR::ID => $integerKey, _ATTR::DOMAIN_ID => $domainKey]
        );
        $expectedDecimal = array_merge(
            _ATTR::bag(),
            $decimalConfig,
            [_ATTR::ID => $decimalKey, _ATTR::DOMAIN_ID => $domainKey]
        );
        $expectedDatetime = array_merge(
            _ATTR::bag(),
            $datetimeConfig,
            [_ATTR::ID => $datetimeKey, _ATTR::DOMAIN_ID => $domainKey]
        );
        $expectedText = array_merge(
            _ATTR::bag(),
            $textConfig,
            [_ATTR::ID => $textKey, _ATTR::DOMAIN_ID => $domainKey]
        );

        $this->assertEquals($expectedString, $string);
        $this->assertEquals($expectedInteger, $integer);
        $this->assertEquals($expectedDecimal, $decimal);
        $this->assertEquals($expectedDatetime, $datetime);
        $this->assertEquals($expectedText, $text);

        $attributes = $result->getAttributes();
        $this->assertEquals([
            $string[_ATTR::NAME] => [
                _ATTR::ID => $string[_ATTR::ID],
                _ATTR::NAME => $string[_ATTR::NAME]
            ],
            $integer[_ATTR::NAME] => [
                _ATTR::ID => $integer[_ATTR::ID],
                _ATTR::NAME => $integer[_ATTR::NAME]
            ],
            $decimal[_ATTR::NAME] => [
                _ATTR::ID => $decimal[_ATTR::ID],
                _ATTR::NAME => $decimal[_ATTR::NAME]
            ],
            $datetime[_ATTR::NAME] => [
                _ATTR::ID => $datetime[_ATTR::ID],
                _ATTR::NAME => $datetime[_ATTR::NAME]
            ],
            $text[_ATTR::NAME] => [
                _ATTR::ID => $text[_ATTR::ID],
                _ATTR::NAME => $text[_ATTR::NAME]
            ]
        ], $attributes);
    }

    /**
     * @test
     *
     * @group functional
     *
     * @covers \Kuperwood\Eav\Factory\EntityFactory::create
     * @covers \Kuperwood\Eav\Factory\EntityFactory::makeNewResult
     * @covers \Kuperwood\Eav\Factory\EntityFactory::getResult
     * @covers \Kuperwood\Eav\Factory\EntityFactory::validateFields
     * @covers \Kuperwood\Eav\Factory\EntityFactory::handleAttribute
     * @covers \Kuperwood\Eav\Factory\EntityFactory::handlePivot
     * @covers \Kuperwood\Eav\Factory\EntityFactory::handleValue
     * @covers \Kuperwood\Eav\Factory\EntityFactory::handleField
     */
    public function create_attribute_array_not_provided() {
        $this->expectException(EntityFactoryException::class);
        $this->expectExceptionMessage(EntityFactoryException::UNDEFINED_ATTRIBUTE_ARRAY);
        $domainKey = $this->eavFactory->createDomain();
        $setKey = $this->eavFactory->createAttributeSet($domainKey);
        $groupKey = $this->eavFactory->createGroup($setKey);

        $config = $this->getFactoryDefaultConfig();
        $field = $config[ATTR_TYPE::STRING];
        $field[ATTR_FACTORY::GROUP] = $groupKey;
        unset($field[ATTR_FACTORY::ATTRIBUTE]);

        $this->factory->create([$field], $domainKey, $setKey);
    }
    /**
     * @test
     *
     * @group functional
     *
     * @covers \Kuperwood\Eav\Factory\EntityFactory::create
     * @covers \Kuperwood\Eav\Factory\EntityFactory::makeNewResult
     * @covers \Kuperwood\Eav\Factory\EntityFactory::getResult
     * @covers \Kuperwood\Eav\Factory\EntityFactory::validateFields
     * @covers \Kuperwood\Eav\Factory\EntityFactory::handleAttribute
     * @covers \Kuperwood\Eav\Factory\EntityFactory::handlePivot
     * @covers \Kuperwood\Eav\Factory\EntityFactory::handleValue
     * @covers \Kuperwood\Eav\Factory\EntityFactory::handleField
     */
    public function create_attribute_name_not_provided() {
        $this->expectException(AttributeException::class);
        $this->expectExceptionMessage(sprintf(AttributeException::UNDEFINED_NAME));
        $domainKey = $this->eavFactory->createDomain();
        $setKey = $this->eavFactory->createAttributeSet($domainKey);
        $groupKey = $this->eavFactory->createGroup($setKey);

        $config = $this->getFactoryDefaultConfig();
        $field = $config[ATTR_TYPE::STRING];
        $field[ATTR_FACTORY::GROUP] = $groupKey;
        unset($field[ATTR_FACTORY::ATTRIBUTE][_ATTR::NAME]);

        $this->factory->create([$field], $domainKey, $setKey);
    }

    /**
     * @test
     *
     * @group functional
     *
     * @covers \Kuperwood\Eav\Factory\EntityFactory::create
     * @covers \Kuperwood\Eav\Factory\EntityFactory::makeNewResult
     * @covers \Kuperwood\Eav\Factory\EntityFactory::getResult
     * @covers \Kuperwood\Eav\Factory\EntityFactory::validateFields
     * @covers \Kuperwood\Eav\Factory\EntityFactory::handleAttribute
     * @covers \Kuperwood\Eav\Factory\EntityFactory::handlePivot
     * @covers \Kuperwood\Eav\Factory\EntityFactory::handleValue
     * @covers \Kuperwood\Eav\Factory\EntityFactory::handleField
     */
    public function create_attribute_type_not_provided() {
        $this->expectException(AttributeException::class);
        $this->expectExceptionMessage(sprintf(AttributeException::UNDEFINED_TYPE));
        $domainKey = $this->eavFactory->createDomain();
        $setKey = $this->eavFactory->createAttributeSet($domainKey);
        $groupKey = $this->eavFactory->createGroup($setKey);

        $config = $this->getFactoryDefaultConfig();
        $field = $config[ATTR_TYPE::STRING];
        $field[ATTR_FACTORY::GROUP] = $groupKey;
        unset($field[ATTR_FACTORY::ATTRIBUTE][_ATTR::TYPE]);

        $this->factory->create([$field], $domainKey, $setKey);
    }

    /**
     * @test
     *
     * @group functional
     *
     * @covers \Kuperwood\Eav\Factory\EntityFactory::create
     * @covers \Kuperwood\Eav\Factory\EntityFactory::makeNewResult
     * @covers \Kuperwood\Eav\Factory\EntityFactory::getResult
     * @covers \Kuperwood\Eav\Factory\EntityFactory::validateFields
     * @covers \Kuperwood\Eav\Factory\EntityFactory::handleAttribute
     * @covers \Kuperwood\Eav\Factory\EntityFactory::handlePivot
     * @covers \Kuperwood\Eav\Factory\EntityFactory::handleValue
     * @covers \Kuperwood\Eav\Factory\EntityFactory::handleField
     */
    public function create_attribute_type_not_supported() {
        $this->expectException(AttributeException::class);
        $this->expectExceptionMessage(sprintf(AttributeException::UNSUPPORTED_TYPE, 'test'));
        $domainKey = $this->eavFactory->createDomain();
        $setKey = $this->eavFactory->createAttributeSet($domainKey);
        $groupKey = $this->eavFactory->createGroup($setKey);

        $config = $this->getFactoryDefaultConfig();
        $field = $config[ATTR_TYPE::STRING];
        $field[ATTR_FACTORY::GROUP] = $groupKey;
        $field[ATTR_FACTORY::ATTRIBUTE][_ATTR::TYPE] = "test";

        $this->factory->create([$field], $domainKey, $setKey);
    }

    /**
     * @test
     *
     * @group functional
     *
     * @covers \Kuperwood\Eav\Factory\EntityFactory::create
     * @covers \Kuperwood\Eav\Factory\EntityFactory::makeNewResult
     * @covers \Kuperwood\Eav\Factory\EntityFactory::getResult
     * @covers \Kuperwood\Eav\Factory\EntityFactory::validateFields
     * @covers \Kuperwood\Eav\Factory\EntityFactory::handleAttribute
     * @covers \Kuperwood\Eav\Factory\EntityFactory::handlePivot
     * @covers \Kuperwood\Eav\Factory\EntityFactory::handleValue
     * @covers \Kuperwood\Eav\Factory\EntityFactory::handleField
     */
    public function create_pivot_table_rows() {
        $domainKey = $this->eavFactory->createDomain();
        $setKey = $this->eavFactory->createAttributeSet($domainKey);
        $groupOneKey = $this->eavFactory->createGroup($setKey);
        $groupTwoKey = $this->eavFactory->createGroup($setKey);

        $config = $this->getFactoryDefaultConfig();
        $config[ATTR_TYPE::STRING][ATTR_FACTORY::GROUP] = $groupOneKey;
        $config[ATTR_TYPE::INTEGER][ATTR_FACTORY::GROUP] = $groupOneKey;
        $config[ATTR_TYPE::DECIMAL][ATTR_FACTORY::GROUP] = $groupOneKey;
        $config[ATTR_TYPE::DATETIME][ATTR_FACTORY::GROUP] = $groupTwoKey;
        $config[ATTR_TYPE::TEXT][ATTR_FACTORY::GROUP] = $groupTwoKey;

        $result = $this->factory->create($config, $domainKey, $setKey);

        $sql = sprintf(
            "SELECT * FROM %s WHERE %s = :domain AND %s = :type AND %s = :name",
            _ATTR::table(),
            _ATTR::DOMAIN_ID,
            _ATTR::TYPE,
            _ATTR::NAME
        );

        $stmt = Connection::get()->prepare($sql);
        $stmt->bindParam(':domain', $domainKey, PDO::PARAM_INT);

        // Define the attribute types as variables
        $stringType = ATTR_TYPE::STRING;
        $integerType = ATTR_TYPE::INTEGER;
        $decimalType = ATTR_TYPE::DECIMAL;
        $datetimeType = ATTR_TYPE::DATETIME;
        $textType = ATTR_TYPE::TEXT;

        // Fetch for string
        $stmt->bindParam(':type', $stringType, PDO::PARAM_STR);
        $stmt->bindParam(':name', $stringType, PDO::PARAM_STR);
        $stmt->execute();
        $string = $stmt->fetch(PDO::FETCH_ASSOC);
        $stringKey = $string[_ATTR::ID];

        // Fetch for integer
        $stmt->bindParam(':type', $integerType, PDO::PARAM_STR);
        $stmt->bindParam(':name', $integerType, PDO::PARAM_STR);
        $stmt->execute();
        $integer = $stmt->fetch(PDO::FETCH_ASSOC);
        $integerKey = $integer[_ATTR::ID];

        // Fetch for decimal
        $stmt->bindParam(':type', $decimalType, PDO::PARAM_STR);
        $stmt->bindParam(':name', $decimalType, PDO::PARAM_STR);
        $stmt->execute();
        $decimal = $stmt->fetch(PDO::FETCH_ASSOC);
        $decimalKey = $decimal[_ATTR::ID];

        // Fetch for datetime
        $stmt->bindParam(':type', $datetimeType, PDO::PARAM_STR);
        $stmt->bindParam(':name', $datetimeType, PDO::PARAM_STR);
        $stmt->execute();
        $datetime = $stmt->fetch(PDO::FETCH_ASSOC);
        $datetimeKey = $datetime[_ATTR::ID];

        // Fetch for text
        $stmt->bindParam(':type', $textType, PDO::PARAM_STR);
        $stmt->bindParam(':name', $textType, PDO::PARAM_STR);
        $stmt->execute();
        $text = $stmt->fetch(PDO::FETCH_ASSOC);
        $textKey = $text[_ATTR::ID];

        $this->assertIsArray($string);
        $this->assertIsArray($integer);
        $this->assertIsArray($decimal);
        $this->assertIsArray($datetime);
        $this->assertIsArray($text);

        $pivotModel = new PivotModel();
        $stringPivot = $pivotModel->findOne($domainKey, $setKey, $groupOneKey, $string[_ATTR::ID]);
        $integerPivot = $pivotModel->findOne($domainKey, $setKey, $groupOneKey, $integer[_ATTR::ID]);
        $decimalPivot = $pivotModel->findOne($domainKey, $setKey, $groupOneKey, $decimal[_ATTR::ID]);
        $datetimePivot = $pivotModel->findOne($domainKey, $setKey, $groupTwoKey, $datetime[_ATTR::ID]);
        $textPivot = $pivotModel->findOne($domainKey, $setKey, $groupTwoKey, $text[_ATTR::ID]);

        $this->assertIsArray($stringPivot);
        $this->assertIsArray($integerPivot);
        $this->assertIsArray($decimalPivot);
        $this->assertIsArray($datetimePivot);
        $this->assertIsArray($textPivot);

        $pivots = $result->getPivots();
        $this->assertCount(5, $pivots);

        $this->assertEquals($stringPivot[_PIVOT::ID], $pivots[$stringKey]);
        $this->assertEquals($integerPivot[_PIVOT::ID], $pivots[$integerKey]);
        $this->assertEquals($decimalPivot[_PIVOT::ID], $pivots[$decimalKey]);
        $this->assertEquals($datetimePivot[_PIVOT::ID], $pivots[$datetimeKey]);
        $this->assertEquals($textPivot[_PIVOT::ID], $pivots[$textKey]);
    }

    /**
     * @test
     *
     * @group functional
     *
     * @covers \Kuperwood\Eav\Factory\EntityFactory::create
     * @covers \Kuperwood\Eav\Factory\EntityFactory::makeNewResult
     * @covers \Kuperwood\Eav\Factory\EntityFactory::getResult
     * @covers \Kuperwood\Eav\Factory\EntityFactory::validateFields
     * @covers \Kuperwood\Eav\Factory\EntityFactory::handleAttribute
     * @covers \Kuperwood\Eav\Factory\EntityFactory::handlePivot
     * @covers \Kuperwood\Eav\Factory\EntityFactory::handleValue
     * @covers \Kuperwood\Eav\Factory\EntityFactory::handleField
     */
    public function create_values() {
        $domainKey = $this->eavFactory->createDomain();
        $setKey = $this->eavFactory->createAttributeSet($domainKey);
        $groupOneKey = $this->eavFactory->createGroup($setKey);
        $groupTwoKey = $this->eavFactory->createGroup($setKey);

        $config = $this->getFactoryDefaultConfig();
        $config[ATTR_TYPE::STRING][ATTR_FACTORY::GROUP] = $groupOneKey;
        $config[ATTR_TYPE::INTEGER][ATTR_FACTORY::GROUP] = $groupOneKey;
        $config[ATTR_TYPE::DECIMAL][ATTR_FACTORY::GROUP] = $groupOneKey;
        $config[ATTR_TYPE::DATETIME][ATTR_FACTORY::GROUP] = $groupTwoKey;
        $config[ATTR_TYPE::TEXT][ATTR_FACTORY::GROUP] = $groupTwoKey;

        $stringValue = ATTR_TYPE::randomValue(ATTR_TYPE::STRING);
        $integerValue = ATTR_TYPE::randomValue(ATTR_TYPE::INTEGER);
        $decimalValue = ATTR_TYPE::randomValue(ATTR_TYPE::DECIMAL);
        $datetimeValue = ATTR_TYPE::randomValue(ATTR_TYPE::DATETIME);
        $textValue = ATTR_TYPE::randomValue(ATTR_TYPE::TEXT);

        $config[ATTR_TYPE::STRING][ATTR_FACTORY::VALUE] = $stringValue;
        $config[ATTR_TYPE::INTEGER][ATTR_FACTORY::VALUE] = $integerValue;
        $config[ATTR_TYPE::DECIMAL][ATTR_FACTORY::VALUE] = $decimalValue;
        $config[ATTR_TYPE::DATETIME][ATTR_FACTORY::VALUE] = $datetimeValue;
        $config[ATTR_TYPE::TEXT][ATTR_FACTORY::VALUE] = $textValue;

        $result = $this->factory->create($config, $domainKey, $setKey);
        $entityKey = $result->getEntityKey();
        $attributes = $result->getAttributes();
        $valueModel = $this->makeValueModel();

        // check values created
        $stringKey = $attributes[ATTR_TYPE::STRING][_ATTR::ID];
        $string = $valueModel->find(
            ATTR_TYPE::STRING,
            $domainKey,
            $entityKey,
            $stringKey
        );


        $integerKey = $attributes[ATTR_TYPE::INTEGER][_ATTR::ID];
        $integer = $valueModel->find(
            ATTR_TYPE::INTEGER,
            $domainKey,
            $entityKey,
            $integerKey
        );

        $decimalKey = $attributes[ATTR_TYPE::DECIMAL][_ATTR::ID];
        $decimal = $valueModel->find(
            ATTR_TYPE::DECIMAL,
            $domainKey,
            $entityKey,
            $decimalKey
        );

        $datetimeKey = $attributes[ATTR_TYPE::DATETIME][_ATTR::ID];
        $datetime = $valueModel->find(
            ATTR_TYPE::DATETIME,
            $domainKey,
            $entityKey,
            $datetimeKey
        );

        $textKey = $attributes[ATTR_TYPE::TEXT][_ATTR::ID];
        $text = $valueModel->find(
            ATTR_TYPE::TEXT,
            $domainKey,
            $entityKey,
            $textKey
        );

        $this->assertIsArray($string);
        $this->assertIsArray($integer);
        $this->assertIsArray($decimal);
        $this->assertIsArray($datetime);
        $this->assertIsArray($text);

        $parser = $this->makeValueParser();

        $this->assertEquals($parser->parse(ATTR_TYPE::STRING, $stringValue), $string[_VALUE::VALUE]);
        $this->assertEquals($parser->parse(ATTR_TYPE::INTEGER, $integerValue), $integer[_VALUE::VALUE]);
        $this->assertEquals($parser->parse(ATTR_TYPE::DECIMAL, $decimalValue), $decimal[_VALUE::VALUE]);
        $this->assertEquals($parser->parse(ATTR_TYPE::DATETIME, $datetimeValue), $datetime[_VALUE::VALUE]);
        $this->assertEquals($parser->parse(ATTR_TYPE::TEXT, $textValue), $text[_VALUE::VALUE]);

        $values = $result->getValues();
        $this->assertCount(5, $values);
        $this->assertEquals($string[_VALUE::ID], $values[$stringKey]);
        $this->assertEquals($integer[_VALUE::ID], $values[$integerKey]);
        $this->assertEquals($decimal[_VALUE::ID], $values[$decimalKey]);
        $this->assertEquals($datetime[_VALUE::ID], $values[$datetimeKey]);
        $this->assertEquals($text[_VALUE::ID], $values[$textKey]);
    }

    /**
     * @test
     *
     * @group functional
     *
     * @covers \Kuperwood\Eav\Factory\EntityFactory::create
     */
    public function create_skip_creating_values() {
        $domainKey = $this->eavFactory->createDomain();
        $setKey = $this->eavFactory->createAttributeSet($domainKey);
        $groupKey = $this->eavFactory->createGroup($setKey);

        $config = $this->getFactoryDefaultConfig();
        $config[ATTR_TYPE::STRING][ATTR_FACTORY::GROUP] = $groupKey;
        $config[ATTR_TYPE::INTEGER][ATTR_FACTORY::GROUP] = $groupKey;
        $config[ATTR_TYPE::DECIMAL][ATTR_FACTORY::GROUP] = $groupKey;
        $config[ATTR_TYPE::DATETIME][ATTR_FACTORY::GROUP] = $groupKey;
        $config[ATTR_TYPE::TEXT][ATTR_FACTORY::GROUP] = $groupKey;

        $result = $this->factory->create($config, $domainKey, $setKey);
        $entityKey = $result->getEntityKey();
        $attributes = $result->getAttributes();
        $valueModel = $this->makeValueModel();
        // check values created
        $string = $valueModel->find(
            ATTR_TYPE::STRING,
            $domainKey,
            $entityKey,
            $attributes[ATTR_TYPE::STRING][_ATTR::ID]
        );
        $integer = $valueModel->find(
            ATTR_TYPE::INTEGER,
            $domainKey,
            $entityKey,
            $attributes[ATTR_TYPE::INTEGER][_ATTR::ID]
        );
        $decimal = $valueModel->find(
            ATTR_TYPE::DECIMAL,
            $domainKey,
            $entityKey,
            $attributes[ATTR_TYPE::DECIMAL][_ATTR::ID]
        );
        $datetime = $valueModel->find(
            ATTR_TYPE::DATETIME,
            $domainKey,
            $entityKey,
            $attributes[ATTR_TYPE::DATETIME][_ATTR::ID]
        );
        $text = $valueModel->find(
            ATTR_TYPE::TEXT,
            $domainKey,
            $entityKey,
            $attributes[ATTR_TYPE::TEXT][_ATTR::ID]
        );

        $this->assertFalse($string);
        $this->assertFalse($integer);
        $this->assertFalse($decimal);
        $this->assertFalse($datetime);
        $this->assertFalse($text);

        $this->assertEquals([], $result->getValues());
    }

    /**
     * @test
     * @group functional
     * @covers \Kuperwood\Eav\Factory\EntityFactory::handleAttribute
     */
    public function handleAttribute_update()
    {
        $domainKey = 11;
        $attrName = 'test1';
        $attrKey = 22;
        $config = [
            _ATTR::NAME => $attrName,
            'not_exist' => 'field',
            _ATTR::TYPE => ATTR_TYPE::INTEGER,
            _ATTR::STRATEGY => 'test_STRATEGY',
            _ATTR::SOURCE => 'test_SOURCE',
            _ATTR::DEFAULT_VALUE => 'test_DEFAULT_VALUE',
            _ATTR::DESCRIPTION => 'test_DESCRIPTION',
        ];
        $record = [
            _ATTR::ID => $attrKey,
            _ATTR::NAME => $attrName
        ];
        $attrModel = $this->getMockBuilder(AttributeModel::class)
            ->onlyMethods(['findByName', 'updateByArray'])->getMock();
        $attrModel->method('findByName')->with($attrName, $domainKey)->willReturn($record);
        $attrModel->expects($this->once())->method('updateByArray')
            ->with($attrKey, [
                _ATTR::NAME => $attrName,
                _ATTR::TYPE => ATTR_TYPE::INTEGER,
                _ATTR::STRATEGY => 'test_STRATEGY',
                _ATTR::SOURCE => 'test_SOURCE',
                _ATTR::DEFAULT_VALUE => 'test_DEFAULT_VALUE',
                _ATTR::DESCRIPTION => 'test_DESCRIPTION',
            ]);

        $entityResult = $this->getMockBuilder(EntityFactoryResult::class)
            ->onlyMethods(['getDomainKey'])->getMock();
        $entityResult->method('getDomainKey')->willReturn($domainKey);

        $entityFactory = $this->getMockBuilder(EntityFactory::class)
            ->onlyMethods(['makeAttributeModel', 'getResult'])->getMock();
        $entityFactory->method('getResult')->willReturn($entityResult);
        $entityFactory->method('makeAttributeModel')->willReturn($attrModel);

        $entityFactory->handleAttribute($config);
    }

    /**
     * @test
     * @group functional
     * @covers \Kuperwood\Eav\Factory\EntityFactory::handlePivot
     */
    public function handlePivot_using_old_pivot_record()
    {
        $pivotKey = 33;
        $domainKey = 1;
        $setKey = 2;
        $groupKey = 3;
        $attrKey = 4;
        $pivotRecord = [
            _PIVOT::ID => $pivotKey,
            _PIVOT::DOMAIN_ID => $domainKey,
            _PIVOT::SET_ID => $setKey,
            _PIVOT::GROUP_ID => $groupKey,
            _PIVOT::ATTR_ID => $attrKey
        ];
        $entityResult = $this->getMockBuilder(EntityFactoryResult::class)
            ->onlyMethods(['getDomainKey', 'getSetKey'])->getMock();
        $entityResult->method('getDomainKey')->willReturn($domainKey);
        $entityResult->method('getSetKey')->willReturn($setKey);
        $pivotModel = $this->getMockBuilder(PivotModel::class)
            ->onlyMethods(['findOne'])->getMock();
        $pivotModel->method('findOne')->willReturn($pivotRecord);

        $entityFactory = $this->getMockBuilder(EntityFactory::class)
            ->onlyMethods(['makePivotModel', 'getResult'])->getMock();
        $entityFactory->method('makePivotModel')->willReturn($pivotModel);
        $entityFactory->method('getResult')->willReturn($entityResult);

        $this->assertEquals($pivotKey, $entityFactory->handlePivot($attrKey, $groupKey));
    }
    /**
     * @test
     * @group functional
     * @covers \Kuperwood\Eav\Factory\EntityFactory::handleValue
     */
    public function handleValue_update()
    {
        $domainKey = 1;
        $entityKey = 2;
        $attrKey = 4;
        $attrType = ATTR_TYPE::INTEGER;
        $valueKey = 5;
        $record = [
            _VALUE::ID => $valueKey
        ];
        $value = 432;
        $parsedValue = 432;

        $entityResult = $this->getMockBuilder(EntityFactoryResult::class)
            ->onlyMethods(['getDomainKey', 'getSetKey'])->getMock();
        $entityResult->method('getDomainKey')->willReturn($domainKey);

        $valueModel = $this->getMockBuilder(ValueBase::class)
            ->onlyMethods(['find', 'update'])->getMock();
        $valueModel->method('find')->willReturn($record);
        $valueModel->expects($this->once())->method('update')
            ->with($attrType, $domainKey, $entityKey, $attrKey, $parsedValue);

        $entityFactory = $this->getMockBuilder(EntityFactory::class)
            ->onlyMethods(['makeValueModel', 'getResult'])->getMock();
        $entityFactory->method('makeValueModel')->willReturn($valueModel);
        $entityFactory->method('getResult')->willReturn($entityResult);

        $this->assertEquals($valueKey, $entityFactory->handleValue($attrType, $entityKey, $attrKey, $value));
    }
}
