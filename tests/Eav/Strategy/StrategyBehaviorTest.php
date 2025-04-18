<?php
/**
 * This file is part of the eav package.
 * @author    Alex Kuperwood <alexkuperwood@gmail.com>
 * @copyright 2025 Alex Kuperwood
 * @license   https://opensource.org/license/mit  The MIT License
 */
declare(strict_types=1);

namespace Tests\Eav\Strategy;

use Kuperwood\Eav\AttributeContainer;
use Kuperwood\Eav\AttributeSet;
use Kuperwood\Eav\Entity;
use Kuperwood\Eav\Enum\_RESULT;
use Kuperwood\Eav\Result\Result;
use Kuperwood\Eav\Strategy;
use Kuperwood\Eav\Validation\Constraints\NumericConstraint;
use Kuperwood\Eav\Validation\Constraints\RequiredConstraint;
use Kuperwood\Eav\Validation\Validator;
use Kuperwood\Eav\Value\ValueAction;
use Kuperwood\Eav\Value\ValueManager;
use Kuperwood\Eav\Value\ValueValidator;
use PHPUnit\Framework\TestCase;


class StrategyBehaviorTest extends TestCase
{
    private Strategy $strategy;
    public function setUp(): void
    {
        parent::setUp();
        $this->strategy = new Strategy();
    }
    /**
     * @test
     * @group behavior
     * @covers \Kuperwood\Eav\Strategy::create
     */
    public function create_action() {
        $valueAction = $this->getMockBuilder(ValueAction::class)
            ->onlyMethods(['create'])
            ->getMock();
        $valueAction->expects($this->once())
            ->method('create')
            ->willReturn((new Result())->created());
        $container = new AttributeContainer();
        $container->setValueAction($valueAction);
        $this->strategy->setAttributeContainer($container);

        $result = $this->strategy->create();

        $this->assertInstanceOf(Result::class, $result);
        $this->assertEquals(_RESULT::CREATED, $result->getCode());
        $this->assertEquals(_RESULT::message(_RESULT::CREATED), $result->getMessage());
    }
    /**
     * @test
     * @group behavior
     * @covers \Kuperwood\Eav\Strategy::find
     */
    public function find_action() {
        $valueAction = $this->getMockBuilder(ValueAction::class)
            ->onlyMethods(['find'])
            ->getMock();
        $valueAction->expects($this->once())
            ->method('find')
            ->willReturn((new Result())->found());
        $container = new AttributeContainer();
        $container->setValueAction($valueAction);
        $this->strategy->setAttributeContainer($container);

        $result = $this->strategy->find();

        $this->assertInstanceOf(Result::class, $result);
        $this->assertEquals(_RESULT::FOUND, $result->getCode());
        $this->assertEquals(_RESULT::message(_RESULT::FOUND), $result->getMessage());
    }
    /**
     * @test
     * @group behavior
     * @covers \Kuperwood\Eav\Strategy::update
     */
    public function update_action_updated() {
        $valueAction = $this->getMockBuilder(ValueAction::class)
            ->onlyMethods(['update'])
            ->getMock();
        $valueAction->expects($this->once())
            ->method('update')
            ->willReturn((new Result())->updated());
        $valueManager = $this->getMockBuilder(ValueManager::class)
            ->onlyMethods(['hasKey'])
            ->getMock();
        $valueManager->expects($this->once())
            ->method('hasKey')
            ->willReturn(true);
        $container = new AttributeContainer();
        $container->setValueAction($valueAction)
            ->setValueManager($valueManager);
        $this->strategy->setAttributeContainer($container);
        $result = $this->strategy->update();
        $this->assertInstanceOf(Result::class, $result);
        $this->assertEquals(_RESULT::UPDATED, $result->getCode());
        $this->assertEquals(_RESULT::message(_RESULT::UPDATED), $result->getMessage());
    }
    /**
     * @test
     * @group behavior
     * @covers \Kuperwood\Eav\Strategy::update
     */
    public function update_action_created() {
        $valueAction = $this->getMockBuilder(ValueAction::class)
            ->onlyMethods(['create'])
            ->getMock();
        $valueAction->expects($this->once())
            ->method('create')
            ->willReturn((new Result())->created());
        $container = new AttributeContainer();
        $container->setValueAction($valueAction)
            ->makeValueManager();
        $this->strategy->setAttributeContainer($container);
        $result = $this->strategy->update();
        $this->assertInstanceOf(Result::class, $result);
        $this->assertEquals(_RESULT::CREATED, $result->getCode());
        $this->assertEquals(_RESULT::message(_RESULT::CREATED), $result->getMessage());
    }
    /**
     * @test
     * @group behavior
     * @covers \Kuperwood\Eav\Strategy::delete
     */
    public function delete_action() {
        $strategy = $this->getMockBuilder(ValueAction::class)
            ->onlyMethods(['delete'])
            ->getMock();
        $strategy->expects($this->once())
            ->method('delete')
            ->willReturn((new Result())->deleted());
        $container = new AttributeContainer();
        $container->setValueAction($strategy);
        $this->strategy->setAttributeContainer($container);

        $result = $this->strategy->delete();

        $this->assertInstanceOf(Result::class, $result);
        $this->assertEquals(_RESULT::DELETED, $result->getCode());
        $this->assertEquals(_RESULT::message(_RESULT::DELETED), $result->getMessage());
    }
    /**
     * @test
     * @group behavior
     * @covers \Kuperwood\Eav\Strategy::save
     */
    public function save_action_create()
    {
        $entity = $this->getMockBuilder(Entity::class)
            ->onlyMethods(['getKey'])
            ->getMock();
        $attrSet = $this->getMockBuilder(AttributeSet::class)
            ->onlyMethods(['getEntity'])
            ->getMock();
        $attrSet->expects($this->once())
            ->method('getEntity')
            ->willReturn($entity);
        $container = $this->getMockBuilder(AttributeContainer::class)
            ->onlyMethods(['getAttributeSet'])
            ->getMock();
        $container->expects($this->once())
            ->method('getAttributeSet')
            ->willReturn($attrSet);
        $strategy = $this->getMockBuilder(Strategy::class)
            ->onlyMethods(['create'])
            ->getMock();
        $strategy->expects($this->once())
            ->method('create')
            ->willReturn((new Result())->created());
        $strategy->setAttributeContainer($container);
        $result = $strategy->save();
        $this->assertInstanceOf(Result::class, $result);
        $this->assertEquals(_RESULT::CREATED, $result->getCode());
        $this->assertEquals(_RESULT::message(_RESULT::CREATED), $result->getMessage());
    }
    /**
     * @test
     * @group behavior
     * @covers \Kuperwood\Eav\Strategy::save
     */
    public function save_action_update()
    {
        $entity = $this->getMockBuilder(Entity::class)
            ->onlyMethods(['getKey'])
            ->getMock();
        $entity->expects($this->once())
            ->method('getKey')
            ->willReturn(1);
        $attrSet = $this->getMockBuilder(AttributeSet::class)
            ->onlyMethods(['getEntity'])
            ->getMock();
        $attrSet->expects($this->once())
            ->method('getEntity')
            ->willReturn($entity);
        $container = $this->getMockBuilder(AttributeContainer::class)
            ->onlyMethods(['getAttributeSet'])
            ->getMock();
        $container->expects($this->once())
            ->method('getAttributeSet')
            ->willReturn($attrSet);
        $strategy = $this->getMockBuilder(Strategy::class)
            ->onlyMethods(['update'])
            ->getMock();
        $strategy->expects($this->once())
            ->method('update')
            ->willReturn((new Result())->updated());
        $strategy->setAttributeContainer($container);
        $result = $strategy->save();
        $this->assertInstanceOf(Result::class, $result);
        $this->assertEquals(_RESULT::UPDATED, $result->getCode());
        $this->assertEquals(_RESULT::message(_RESULT::UPDATED), $result->getMessage());
    }
    /**
     * @test
     * @group behavior
     * @covers \Kuperwood\Eav\Strategy::validate
     */
    public function validate_fails_action() {

        $validator = new Validator();
        $valueValidator = $this->getMockBuilder(ValueValidator::class)
            ->onlyMethods(['getValidator', 'getValidatedData'])
            ->getMock();
        $valueValidator->expects($this->once())
            ->method('getValidatedData')
            ->willReturn([]);
        $valueValidator->expects($this->once())
            ->method('getValidator')
            ->willReturn($validator);
        $container = new AttributeContainer();
        $strategy = $this->getMockBuilder(Strategy::class)
        ->onlyMethods(['rules'])->getMock();
        $strategy->expects($this->once())
            ->method('rules')->willReturn([new RequiredConstraint(), new NumericConstraint()]);
        $container->setStrategy($strategy);
        $container->setValueValidator($valueValidator);
        $strategy = $this->getMockBuilder(Strategy::class)
            ->onlyMethods(['getAttributeContainer'])
            ->getMock();
        $strategy->expects($this->once())
            ->method('getAttributeContainer')
            ->willReturn($container);
        $result = $strategy->validate();
        $this->assertInstanceOf(Result::class, $result);
        $this->assertEquals(_RESULT::VALIDATION_FAILS, $result->getCode());
        $this->assertEquals(_RESULT::message(_RESULT::VALIDATION_FAILS), $result->getMessage());
        $data = $result->getData();
        $this->assertEquals(4, count($data));
    }
    /**
     * @test
     * @group behavior
     * @covers \Kuperwood\Eav\Strategy::validate
     */
    public function validate_passed_action() {
        $valueValidator = $this->getMockBuilder(ValueValidator::class)
            ->onlyMethods(['getValidator', 'getValidatedData'])
            ->getMock();
        $valueValidator->expects($this->once())
            ->method('getValidatedData')
            ->willReturn([]);
        $container = new AttributeContainer();
        $strategy = $this->getMockBuilder(Strategy::class)
            ->onlyMethods(['rules'])->getMock();
        $strategy->expects($this->once())
            ->method('rules')->willReturn([]);
        $container->setStrategy($strategy);
        $container->setValueValidator($valueValidator);
        $result = $strategy->validate();
        $this->assertInstanceOf(Result::class, $result);
        $this->assertEquals(_RESULT::VALIDATION_PASSED, $result->getCode());
        $this->assertEquals(_RESULT::message(_RESULT::VALIDATION_PASSED), $result->getMessage());
        $this->assertNull($result->getData());
    }
}