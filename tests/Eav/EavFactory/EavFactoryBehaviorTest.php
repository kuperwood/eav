<?php
/**
 * This file is part of the eav package.
 * @author    Aleksandr Drobotik <drobotiksbox@gmail.com>
 * @copyright 2023 Aleksandr Drobotik
 * @license   https://opensource.org/license/mit  The MIT License
 */
declare(strict_types=1);

namespace Tests\Eav\EavFactory;

use Drobotik\Eav\Factory\EavFactory;
use Tests\TestCase;

class EavFactoryBehaviorTest extends TestCase
{
    /**
     * @test
     * @group behavior
     * @covers \Drobotik\Eav\Factory\EavFactory::createDomain
     */
    public function entity_domain() {
        $factory = $this->getMockBuilder(EavFactory::class)
            ->onlyMethods(['createDomain'])
            ->getMock();
        $factory->expects($this->never())
            ->method('createDomain');
        $domain = $this->eavFactory->createDomain();
        $factory->createEntity($domain->getKey());
    }
    /**
     * @test
     * @group behavior
     * @covers \Drobotik\Eav\Factory\EavFactory::createEntity
     */
    public function entity_attr_set() {
        $factory = $this->getMockBuilder(EavFactory::class)
            ->onlyMethods(['createAttributeSet'])
            ->getMock();
        $factory->expects($this->never())
            ->method('createAttributeSet');
        $domain = $this->eavFactory->createDomain();
        $attrSet = $this->eavFactory->createAttributeSet($domain->getKey());
        $factory->createEntity($domain->getKey(), $attrSet->getKey());
    }
    /**
     * @test
     * @group behavior
     * @covers \Drobotik\Eav\Factory\EavFactory::createAttributeSet
     */
    public function attribute_set_domain() {
        $factory = $this->getMockBuilder(EavFactory::class)
            ->onlyMethods(['createDomain'])
            ->getMock();
        $factory->expects($this->never())
            ->method('createDomain');
        $domain = $this->eavFactory->createDomain();
        $factory->createAttributeSet($domain->getKey());
    }
    /**
     * @test
     * @group behavior
     * @covers \Drobotik\Eav\Factory\EavFactory::createGroup
     */
    public function attribute_group_attribute_set() {
        $factory = $this->getMockBuilder(EavFactory::class)
            ->onlyMethods(['createAttributeSet'])
            ->getMock();
        $factory->expects($this->never())
            ->method('createAttributeSet');
        $set = $this->eavFactory->createAttributeSet();
        $factory->createGroup($set->getKey());
    }
    /**
     * @test
     * @group behavior
     * @covers \Drobotik\Eav\Factory\EavFactory::createAttribute
     */
    public function attribute_domain() {
        $factory = $this->getMockBuilder(EavFactory::class)
            ->onlyMethods(['createDomain'])
            ->getMock();
        $factory->expects($this->never())
            ->method('createDomain');
        $domain = $this->eavFactory->createDomain();
        $factory->createAttribute($domain->getKey());
    }
}