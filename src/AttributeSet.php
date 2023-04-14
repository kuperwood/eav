<?php
/**
 * This file is part of the eav package.
 * @author    Aleksandr Drobotik <drobotiksbox@gmail.com>
 * @copyright 2023 Aleksandr Drobotik
 * @license   https://opensource.org/license/mit  The MIT License
 */
declare(strict_types=1);

namespace Drobotik\Eav;

use Drobotik\Eav\Trait\ContainerTrait;
use Drobotik\Eav\Trait\SingletonsTrait;

class AttributeSet
{
    use SingletonsTrait;
    use ContainerTrait;

    private int $key;
    private string $name;
    /** @var AttributeContainer[] */
    private array $containers = [];
    private Entity $entity;

    public function getKey(): int
    {
        return $this->key;
    }

    public function setKey(int $key) : self
    {
        $this->key = $key;
        return $this;
    }

    public function hasKey() : bool
    {
        return isset($this->key);
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function setName(string $name) : self
    {
        $this->name = $name;
        return $this;
    }

    public function getEntity(): Entity
    {
        return $this->entity;
    }

    public function setEntity(Entity $entity) : self
    {
        $this->entity = $entity;
        return $this;
    }

    public function fetchContainers(bool $force = false) : self
    {
        if(!$this->hasKey()) return $this;
        if(!$force && $this->hasContainers()) return $this;
        $model = $this->makeAttributeSetModel();
        foreach ($model->findAttributes($this->getKey()) as $attribute) {
            $container = $this->makeAttributeContainer();
            $container->setAttributeSet($this);
            $container->makeAttributeSetAction();
            $action = $container->getAttributeSetAction();
            $action->initialize($attribute);
            $this->pushContainer($container);
        }
        return $this;
    }

    public function pushContainer(AttributeContainer $container) : self
    {
        $this->containers[$container->getAttribute()->getName()] = $container;
        return $this;
    }

    public function resetContainers() : self
    {
        $this->containers = [];
        return $this;
    }

    public function getContainers() : array
    {
        return $this->containers;
    }

    public function getContainer(string $name) : ?AttributeContainer
    {
        if(!$this->hasContainer($name)) return null;
        return $this->containers[$name];
    }

    public function hasContainer(string $name) : bool
    {
        return array_key_exists($name, $this->containers);
    }

    public function hasContainers() : bool
    {
        return !empty($this->containers);
    }

}