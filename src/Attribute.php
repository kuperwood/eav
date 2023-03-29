<?php

declare(strict_types=1);

namespace Kuperwood\Eav;

use Kuperwood\Eav\Enum\_ATTR;
use Kuperwood\Eav\Enum\ATTR_TYPE;
use Kuperwood\Eav\Exception\AttributeException;
use Kuperwood\Eav\Interface\StrategyInterface;
use Kuperwood\Eav\Model\ValueBase;
use Kuperwood\Eav\Trait\EavContainerTrait;

class Attribute
{
    use EavContainerTrait;
    private AttributeBag $bag;
    private AttributeSet $attributeSet;
    private StrategyInterface $strategy;

    private Source $source;

    public function __construct() {
        $this->setBag(new AttributeBag());
    }

    public function getBag(): AttributeBag
    {
        return $this->bag;
    }

    public function setBag(AttributeBag $bag): self
    {
        $this->bag = $bag;
        return $this;
    }

    public function getSource() : Source
    {
        return $this->source;
    }

    public function setSource(Source $source) : self
    {
        $this->source = $source;
        return $this;
    }

    public function getKey() : int
    {
        return $this->getBag()->getField(_ATTR::ID);
    }

    public function setKey(int $key) : self
    {
        $this->getBag()->setField(_ATTR::ID, $key);
        return $this;
    }

    public function getName() : string
    {
        return $this->getBag()->getField(_ATTR::NAME);
    }

    public function setName(string $name) : self
    {
        $this->getBag()->setField(_ATTR::NAME, $name);
        return $this;
    }

    public function getType() : ATTR_TYPE
    {
        return $this->getBag()->getField(_ATTR::TYPE);
    }

    /**
     * @throws AttributeException
     */
    public function setType(string $type) : self
    {
        if (!ATTR_TYPE::isValid($type)) {
            AttributeException::unexpectedType($type);
        }
        $case = ATTR_TYPE::getCase($type);
        $this->getBag()->setField(_ATTR::TYPE, $case);
        return $this;
    }

    public function getDomainKey() : int
    {
        return $this->getBag()->getField(_ATTR::DOMAIN_ID);
    }

    public function getDefaultValue() : string
    {
        return $this->getBag()->getField(_ATTR::DEFAULT_VALUE);
    }

    public function getDescription() : string
    {
        return $this->getBag()->getField(_ATTR::DESCRIPTION);
    }

    public function getValueModel() : ValueBase
    {
        return $this->getType()->model();
    }
}