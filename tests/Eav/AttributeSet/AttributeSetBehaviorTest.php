<?php
/**
 * This file is part of the eav package.
 * @author    Alex Kuperwood <alexkuperwood@gmail.com>
 * @copyright 2025 Alex Kuperwood
 * @license   https://opensource.org/license/mit  The MIT License
 */
declare(strict_types=1);

namespace Tests\Eav\AttributeSet;

use Kuperwood\Eav\Enum\_ATTR;
use Kuperwood\Eav\AttributeContainer;
use Kuperwood\Eav\AttributeSet;
use Kuperwood\Eav\AttributeSetAction;
use Kuperwood\Eav\Model\AttributeSetModel;
use PHPUnit\Framework\TestCase;

class AttributeSetBehaviorTest extends TestCase
{
    /**
     * @test
     * @group behavior
     * @covers \Kuperwood\Eav\AttributeSet::fetchContainers
     */
    public function fetch_containers() {
        $key = 321;
        $collection = [[_ATTR::NAME => 'test', _ATTR::ID => 1]];
        $attrSetModel = $this->getMockBuilder(AttributeSetModel::class)
            ->onlyMethods(['findAttributes'])
            ->getMock();
        $attrSetModel->expects($this->once())
            ->method('findAttributes')
            ->with($key)
            ->willReturn($collection);
        $instance = $this->getMockBuilder(AttributeSet::class)
            ->onlyMethods(['makeAttributeContainer', 'makeAttributeSetModel', 'pushContainer', 'getKey', 'hasKey'])
            ->getMock();
        $instance->expects($this->once())
            ->method('hasKey')
            ->willReturn(true);
        $instance->expects($this->once())
            ->method('getKey')
            ->willReturn($key);
        $instance->expects($this->once())
            ->method('makeAttributeSetModel')
            ->willReturn($attrSetModel);
        $attrSetAction = $this->getMockBuilder(AttributeSetAction::class)
            ->onlyMethods(['initialize'])
            ->getMock();
        $container = $this->getMockBuilder(AttributeContainer::class)
            ->onlyMethods(['setAttributeSet', 'getAttributeSetAction'])
            ->getMock();
        $container->expects($this->once())
            ->method('getAttributeSetAction')
            ->willReturn($attrSetAction);
        $instance->expects($this->once())
            ->method('makeAttributeContainer')
            ->willReturn($container);
        $container->expects($this->once())
            ->method('setAttributeSet')
            ->with($instance);
        $instance->expects($this->once())
            ->method('pushContainer')
            ->with($container);
        $result = $instance->fetchContainers();
        $this->assertEquals($instance, $result);
    }
    /**
     * @test
     * @group behavior
     * @covers \Kuperwood\Eav\AttributeSet::fetchContainers
     */
    public function fetch_containers_no_key() {
        $instance = $this->getMockBuilder(AttributeSet::class)
            ->onlyMethods(['pushContainer'])
            ->getMock();
        $instance->expects($this->never())
            ->method('pushContainer');
        $result = $instance->fetchContainers();
        $this->assertEquals($instance, $result);
    }
    /**
     * @test
     * @group behavior
     * @covers \Kuperwood\Eav\AttributeSet::fetchContainers
     */
    public function fetch_containers_no_force() {
        $instance = $this->getMockBuilder(AttributeSet::class)
            ->onlyMethods(['hasKey', 'getKey', 'hasContainers'])
            ->getMock();
        $instance->expects($this->once())->method('hasKey')->willReturn(true);
        $instance->expects($this->once())->method('hasContainers')->willReturn(true);
        $instance->expects($this->never())->method('getKey');
        $instance->fetchContainers();
    }
    /**
     * @test
     * @group behavior
     * @covers \Kuperwood\Eav\AttributeSet::fetchContainers
     */
    public function fetch_containers_force() {
        $model = $this->getMockBuilder(AttributeSetModel::class)
            ->onlyMethods(['findAttributes'])
            ->getMock();
        $model->expects($this->once())->method('findAttributes')
            ->willReturn([]);
        $instance = $this->getMockBuilder(AttributeSet::class)
            ->onlyMethods(['makeAttributeSetModel', 'hasContainers', 'hasKey', 'getKey'])
            ->getMock();
        $instance->expects($this->once())->method('hasKey')->willReturn(true);
        $instance->expects($this->once())->method('getKey')->willReturn(1);
        $instance->expects($this->never())->method('hasContainers')->willReturn(true);
        $instance->expects($this->once())->method('makeAttributeSetModel')->willReturn($model);
        $instance->fetchContainers(true);
    }
}