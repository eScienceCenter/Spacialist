<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use MStaack\LaravelPostgis\Eloquent\PostgisTrait;
use App\Traits\ModerationTrait;

class AttributeValue extends Model
{
    use PostgisTrait;
    use ModerationTrait;

    protected $table = 'attribute_values';
    /**
     * The attributes that are assignable.
     *
     * @var array
     */
    protected $fillable = [
        'entity_id',
        'attribute_id',
        'entity_val',
        'dbl_val',
        'dt_val',
        'geography_val',
        'int_val',
        'json_val',
        'str_val',
        'thesaurus_val',
        'certainty',
        'certainty_description',
        'lasteditor',
    ];

    // TODO always hide *_val in favor of (computed) value?
    protected $hidden = [
        'entity_val',
        'dbl_val',
        'dt_val',
        'geography_val',
        'int_val',
        'json_val',
        'str_val',
        'thesaurus_val'
    ];

    protected $postgisFields = [
        'geography_val',
    ];

    protected $copyOn = [
        'entity_id',
        'attribute_id',
    ];

    const patchRules = [
        'certainty' => 'integer|between:0,100',
        'certainty_description' => 'string|nullable'
    ];

    public function getValue() {
        return $this->str_val ??
               $this->int_val ??
               $this->dbl_val ??
               $this->entity_val ??
               $this->thesaurus_val ??
               json_decode($this->json_val) ??
               $this->geography_val ??
               $this->dt_val;
    }

    public static function getValueById($aid, $cid) {
        $av = self::where('attribute_id', $aid)
            ->where('entity_id', $cid)->first();
        if(!isset($av)) {
            return null;
        }
        return $av->getValue();
    }

    public function patch($values) {
        foreach($values as $k => $v) {
            $this->{$k} = $v;
        }
        $this->save();
    }

    public function entity() {
        return $this->belongsTo('App\Entity');
    }

    public function attribute() {
        return $this->belongsTo('App\Attribute');
    }

    public function entity_value() {
        return $this->belongsTo('App\Entity', 'entity_val');
    }

    public function concept() {
        return $this->belongsTo('App\ThConcept', 'thesaurus_val', 'concept_url');
    }
}
