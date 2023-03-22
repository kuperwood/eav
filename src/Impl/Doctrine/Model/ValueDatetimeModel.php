<?php
declare(strict_types=1);
namespace Kuperwood\Eav\Impl\Doctrine\Model;
use Doctrine\ORM\Mapping\ClassMetadata;
use Kuperwood\Eav\Enum\AttributeTypeEnum;

class ValueDatetimeModel extends  ValueGenericModel
{
    private ?string $value;
    public static function loadMetadata(ClassMetadata $metadata)
    {
        AttributeTypeEnum::DATETIME->loadMetadata($metadata);
    }

    public function getValue(): ?string
    {
        return $this->value;
    }

    public function setValue(string $value): self
    {
        $this->value = $value;
        return $this;
    }

}