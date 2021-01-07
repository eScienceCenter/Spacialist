<?php

namespace Tests\Unit;

use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

use App\AttributeValue;

class AttributeValueTest extends TestCase
{
    /**
     * Test relations of an attribute value (id=60)
     *
     * @return void
     */
    public function testRelations()
    {
        $value = AttributeValue::with(['entity', 'attribute', 'entity_value', 'concept'])->find(60);

        $this->assertEquals(2, $value->entity->id);
        $this->assertEquals(14, $value->attribute->id);
        $this->assertEquals(53, $value->concept->id);
        $this->assertNull($value->entity_value);

        $value = AttributeValue::with(['entity', 'attribute', 'entity_value', 'concept'])->find(35);

        $this->assertEquals(5, $value->entity->id);
        $this->assertEquals(18, $value->attribute->id);
        $this->assertNull($value->concept);
        $this->assertEquals(6, $value->entity_value->id);
        $this->assertEquals('Aufschluss', $value->entity_value->name);
    }

    /**
     * Test get value of an attribute value (eid=2, aid=16)
     *
     * @return void
     */
    public function testGetAttributeValueById()
    {
        $value = AttributeValue::getValueById(16, 2);
        $this->assertEquals('POINT(8.92 48.45)', $value);

        $value = AttributeValue::getValueById(99, 2);
        $this->assertNull($value);
    }

    /**
     * Test get last editor of first attribute value
     *
     * @return void
     */
    public function testGetAttributeValueLasteditor()
    {
        $value = AttributeValue::first();
        $this->assertEquals(1, $value->user->id);
    }
}
