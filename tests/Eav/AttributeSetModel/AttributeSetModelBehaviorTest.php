<?php
/**
 * This file is part of the eav package.
 * @author    Aleksandr Drobotik <drobotiksbox@gmail.com>
 * @copyright 2023 Aleksandr Drobotik
 * @license   https://opensource.org/license/mit  The MIT License
 */
declare(strict_types=1);

namespace Tests\Eav\AttributeSetModel;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Drobotik\Eav\Model\AttributeSetModel;
use PHPUnit\Framework\TestCase;

class AttributeSetModelBehaviorTest extends TestCase
{
    /**
     * @test
     * @group behavior
     * @covers AttributeSetModel::findAttributes
     */
    public function find_attributes() {
        $collection = new Collection(123);
        $belongsToMany = $this->getMockBuilder(BelongsToMany::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['get'])
            ->getMock();
        $belongsToMany->expects($this->once())
            ->method('get')
            ->willReturn($collection);
        $record = $this->getMockBuilder(AttributeSetModel::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['attributes'])
            ->getMock();
        $record->expects($this->once())
            ->method('attributes')
            ->willReturn($belongsToMany);
        $model = $this->getMockBuilder(AttributeSetModel::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['firstOrFail'])
            ->getMock();
        $model->expects($this->once())
            ->method('firstOrFail')
            ->willReturn($record);
        $result = $model->findAttributes(1);
        $this->assertSame($collection, $result);
    }
}