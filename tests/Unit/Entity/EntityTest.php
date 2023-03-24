<?php

namespace Tests\Unit\Entity;

use Kuperwood\Eav\AttributeSet;
use Kuperwood\Eav\Entity;
use Tests\TestCase;

class EntityTest extends TestCase
{
    public function setUp(): void
    {
        parent::setUp();
        $this->entity = new Entity();
    }

    /** @test */
    public function key() {
        $this->entity->setKey(1);
        $this->assertSame(1, $this->entity->getKey());
    }

    /** @test */
    public function domain_key() {
        $this->entity->setDomainKey(1);
        $this->assertSame(1, $this->entity->getDomainKey());
    }

    /** @test */
    public function attribute_set() {
        $set = new AttributeSet();
        $this->entity->setAttributeSet($set);
        $this->assertSame($set, $this->entity->getAttributeSet());
    }
}