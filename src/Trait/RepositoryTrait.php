<?php
/**
 * This file is part of the eav package.
 * @author    Aleksandr Drobotik <drobotiksbox@gmail.com>
 * @copyright 2023 Aleksandr Drobotik
 * @license   https://opensource.org/license/mit  The MIT License
 */
declare(strict_types=1);

namespace Drobotik\Eav\Trait;

use Drobotik\Eav\Repository\AttributeRepository;
use Drobotik\Eav\Repository\EntityRepository;
use Drobotik\Eav\Repository\GroupRepository;
use Drobotik\Eav\Repository\PivotRepository;
use Drobotik\Eav\Repository\ValueRepository;

trait RepositoryTrait
{
    public function makePivotRepository(): PivotRepository
    {
        return new PivotRepository();
    }

    public function makeAttributeRepository(): AttributeRepository
    {
        return new AttributeRepository();
    }

    public function makeValueRepository(): ValueRepository
    {
        return new ValueRepository();
    }

    public function makeEntityRepository(): EntityRepository
    {
        return new EntityRepository();
    }

    public function makeGroupRepository(): GroupRepository
    {
        return new GroupRepository();
    }
}