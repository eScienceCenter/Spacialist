<?php

namespace Tests\Unit;

use App\AttributeValue;
use Tests\TestCase;

class UserTest extends TestCase
{
    /**
     * Test moderation trait
     *
     * @return void
     */
    public function testModerationTraitAccept()
    {
        $val = AttributeValue::find(36);
        $this->assertFalse($val->isModerated());

        $copyVal = $val->moderate('pending', false, true);
        $copyVal->int_val = 20;
        $copyVal->save();
        $this->assertFalse($val->isModerated());
        $this->assertTrue($copyVal->isModerated());
        $this->assertEquals(100, $val->int_val);
        $this->assertEquals(20, $copyVal->int_val);

        $vals = AttributeValue::where('entity_id', $val->entity_id)
            ->where('attribute_id', $val->attribute_id)
            ->withModerated()
            ->get();
        $this->assertEquals(2, count($vals));
        $vals = AttributeValue::where('entity_id', $val->entity_id)
            ->where('attribute_id', $val->attribute_id)
            ->withoutModerated()
            ->get();
        $this->assertEquals(1, count($vals));
        $vals = AttributeValue::where('entity_id', $val->entity_id)
            ->where('attribute_id', $val->attribute_id)
            ->onlyModerated()
            ->get();
        $this->assertEquals(1, count($vals));

        $copyVal->moderate();

        $vals = AttributeValue::where('entity_id', $val->entity_id)
            ->where('attribute_id', $val->attribute_id)
            ->withModerated()
            ->get();
        $this->assertEquals(1, count($vals));
        $valsWo = AttributeValue::where('entity_id', $val->entity_id)
            ->where('attribute_id', $val->attribute_id)
            ->withModerated(false)
            ->get();
        $vals = AttributeValue::where('entity_id', $val->entity_id)
            ->where('attribute_id', $val->attribute_id)
            ->withoutModerated()
            ->get();
        $this->assertEquals(1, count($vals));
        $this->assertEquals(1, count($valsWo));
        $this->assertEquals($vals[0]->id, $valsWo[0]->id);
        $this->assertEquals(20, $vals[0]->int_val);
        $vals = AttributeValue::where('entity_id', $val->entity_id)
            ->where('attribute_id', $val->attribute_id)
            ->onlyModerated()
            ->get();
        $this->assertEquals(0, count($vals));
    }

    /**
     * Test moderation trait deny
     *
     * @return void
     */
    public function testModerationTraitDeny()
    {
        $val = AttributeValue::find(36);
        $this->assertFalse($val->isModerated());

        $copyVal = $val->moderate('pending', false, true);
        $copyVal->int_val = 20;
        $copyVal->save();
        $this->assertFalse($val->isModerated());
        $this->assertTrue($copyVal->isModerated());
        $this->assertEquals(100, $val->int_val);
        $this->assertEquals(20, $copyVal->int_val);

        $copyVal->moderate('deny');

        $vals = AttributeValue::where('entity_id', $val->entity_id)
            ->where('attribute_id', $val->attribute_id)
            ->withModerated()
            ->get();
        $this->assertEquals(1, count($vals));
        $valsWo = AttributeValue::where('entity_id', $val->entity_id)
            ->where('attribute_id', $val->attribute_id)
            ->withModerated(false)
            ->get();
        $vals = AttributeValue::where('entity_id', $val->entity_id)
            ->where('attribute_id', $val->attribute_id)
            ->withoutModerated()
            ->get();
        $this->assertEquals(1, count($vals));
        $this->assertEquals(1, count($valsWo));
        $this->assertEquals($vals[0]->id, $valsWo[0]->id);
        $this->assertEquals(100, $vals[0]->int_val);
        $vals = AttributeValue::where('entity_id', $val->entity_id)
            ->where('attribute_id', $val->attribute_id)
            ->onlyModerated()
            ->get();
        $this->assertEquals(0, count($vals));
    }

    /**
     * Test moderation trait delete
     *
     * @return void
     */
    public function testModerationTraitDeleteAccept()
    {
        $val = AttributeValue::find(36);
        $this->assertFalse($val->isModerated());

        $val->moderate('pending-delete', true);
        $this->assertTrue($val->isModerated());
        $this->assertEquals(100, $val->int_val);

        $val->moderate('accept');

        $vals = AttributeValue::where('entity_id', $val->entity_id)
            ->where('attribute_id', $val->attribute_id)
            ->withModerated()
            ->get();
        $this->assertEquals(0, count($vals));
        $valsWo = AttributeValue::where('entity_id', $val->entity_id)
            ->where('attribute_id', $val->attribute_id)
            ->withModerated(false)
            ->get();
        $vals = AttributeValue::where('entity_id', $val->entity_id)
            ->where('attribute_id', $val->attribute_id)
            ->withoutModerated()
            ->get();
        $this->assertEquals(0, count($vals));
        $this->assertEquals(0, count($valsWo));
        $vals = AttributeValue::where('entity_id', $val->entity_id)
            ->where('attribute_id', $val->attribute_id)
            ->onlyModerated()
            ->get();
        $this->assertEquals(0, count($vals));
    }

    /**
     * Test moderation trait delete deny
     *
     * @return void
     */
    public function testModerationTraitDeleteDeny()
    {
        $val = AttributeValue::find(36);
        $this->assertFalse($val->isModerated());

        $val->moderate('pending-delete', true);
        $this->assertTrue($val->isModerated());
        $this->assertEquals(100, $val->int_val);

        $val->moderate('deny');

        $vals = AttributeValue::where('entity_id', $val->entity_id)
            ->where('attribute_id', $val->attribute_id)
            ->withModerated()
            ->get();
        $this->assertEquals(1, count($vals));
        $valsWo = AttributeValue::where('entity_id', $val->entity_id)
            ->where('attribute_id', $val->attribute_id)
            ->withModerated(false)
            ->get();
        $vals = AttributeValue::where('entity_id', $val->entity_id)
            ->where('attribute_id', $val->attribute_id)
            ->withoutModerated()
            ->get();
        $this->assertEquals(1, count($vals));
        $this->assertEquals(1, count($valsWo));
        $this->assertEquals($vals[0]->id, $valsWo[0]->id);
        $this->assertEquals(100, $vals[0]->int_val);
        $vals = AttributeValue::where('entity_id', $val->entity_id)
            ->where('attribute_id', $val->attribute_id)
            ->onlyModerated()
            ->get();
        $this->assertEquals(0, count($vals));
    }
}
