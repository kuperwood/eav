<?php

namespace Kuperwood\Eav\Result;

use Kuperwood\Eav\Model\AttributeModel;
use Kuperwood\Eav\Model\EntityModel;
use Kuperwood\Eav\Model\PivotModel;
use Kuperwood\Eav\Model\ValueBase;

class EntityFactoryResult
{
    private EntityModel $entityModel;
    /** @var PivotModel[] */
    private array $pivots = [];
    /** @var AttributeModel[] */
    private array $attributes = [];
    /** @var ValueBase[] */
    private array $values = [];

    public function setEntityModel(EntityModel $entityModel): void
    {
        $this->entityModel = $entityModel;
    }

    public function getEntityModel(): EntityModel
    {
        return $this->entityModel;
    }

    public function addAttribute(AttributeModel $attributeModel): void
    {
        $this->attributes[$attributeModel->getName()] = $attributeModel;
    }

    public function getAttributes(): array
    {
        return $this->attributes;
    }

    public function addValue(string $attrName, ValueBase $valueModel): void
    {
        $this->values[$attrName] = $valueModel;
    }

    public function getValues(): array
    {
        return $this->values;
    }

    public function addPivot(string $attrName, PivotModel $pivot): void
    {
        $this->pivots[$attrName] = $pivot;
    }

    public function getPivots() : array
    {
        return $this->pivots;
    }
}