<?php

declare(strict_types=1);

namespace Kuperwood\Eav;

use Kuperwood\Eav\Enum\_ATTR;
use Kuperwood\Eav\Enum\ATTR_TYPE;
use Kuperwood\Eav\Exception\AttributeException;
use Kuperwood\Eav\Interface\StrategyInterface;
use Kuperwood\Eav\Model\ValueBase;
use Kuperwood\Eav\Trait\ContainerTrait;

class Attribute
{
    use ContainerTrait;
    private AttributeBag $bag;
    private AttributeSet $attributeSet;
    private StrategyInterface $strategy;

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

    public function getKey() : ?int
    {
        return $this->getBag()->getField(_ATTR::ID);
    }

    public function setKey(?int $key) : self
    {
        $this->getBag()->setField(_ATTR::ID, $key);
        return $this;
    }

    public function getDomainKey() : ?int
    {
        return $this->getBag()->getField(_ATTR::DOMAIN_ID);
    }

    public function setDomainKey(?int $key) : self
    {
        $this->getBag()->setField(_ATTR::DOMAIN_ID, $key);
        return $this;
    }

    public function getName() : ?string
    {
        return $this->getBag()->getField(_ATTR::NAME);
    }

    public function setName(?string $name) : self
    {
        $this->getBag()->setField(_ATTR::NAME, $name);
        return $this;
    }

    /**
     * @throws AttributeException
     */
    public function getType() : ATTR_TYPE
    {
        $type =  $this->getBag()->getField(_ATTR::TYPE);
        if (!ATTR_TYPE::isValid($type)) {
            AttributeException::unexpectedType($type);
        }
        return ATTR_TYPE::getCase($type);
    }

    public function setType(string $type) : self
    {
        $this->getBag()->setField(_ATTR::TYPE, $type);
        return $this;
    }

    public function getStrategy() : string
    {
        return  $this->getBag()->getField(_ATTR::STRATEGY);
    }

    public function setStrategy(string $strategy) : self
    {
        $this->getBag()->setField(_ATTR::STRATEGY, $strategy);
        return $this;
    }

    public function getSource() : ?string
    {
        return $this->getBag()->getField(_ATTR::SOURCE);
    }

    public function setSource(?string $source) : self
    {
        $this->getBag()->setField(_ATTR::SOURCE, $source);
        return $this;
    }

    public function getDefaultValue() : ?string
    {
        return $this->getBag()->getField(_ATTR::DEFAULT_VALUE);
    }
    public function setDefaultValue($value) : self
    {
        $this->getBag()->setField(_ATTR::DEFAULT_VALUE, $value);
        return $this;
    }

    public function getDescription() : string
    {
        return $this->getBag()->getField(_ATTR::DESCRIPTION);
    }

    public function setDescription($value) : self
    {
        $this->getBag()->setField(_ATTR::DESCRIPTION, $value);
        return $this;
    }

    public function getValueModel() : ValueBase
    {
        return $this->getType()->model();
    }
}