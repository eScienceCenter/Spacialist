<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Phaza\LaravelPostgis\Eloquent\PostgisTrait;

class AttributeValue extends Model
{
    use PostgisTrait;

    protected $table = 'attribute_values';
    /**
     * The attributes that are assignable.
     *
     * @var array
     */
    protected $fillable = [
        'context_id',
        'attribute_id',
        'context_val',
        'dbl_val',
        'dt_val',
        'geography_val',
        'int_val',
        'json_val',
        'str_val',
        'thesaurus_val',
        'possibility',
        'possibility_description',
        'lasteditor',
    ];

    // TODO always hide *_val in favor of (computed) value?
    protected $hidden = [
        'context_val',
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

    const patchRules = [
        'certainty' => 'integer|between:0,100',
        'certainty_description' => 'string|nullable'
    ];

    public function getValue() {
        return $this->str_val ??
               $this->int_val ??
               $this->dbl_val ??
               $this->context_val ??
               $this->thesaurus_val ??
               json_decode($this->json_val) ??
               $this->geography_val ??
               $this->dt_val;
    }

    public function patch($values) {
        foreach($values as $k => $v) {
            if($k == 'certainty') {
                $this->possibility = $v;
            } else if($k == 'certainty_description') {
                $this->possibility_description = $v;
            } else {
                $this->{$k} = $v;
            }
        }
        $this->save();
    }

    public function context() {
        return $this->belongsTo('App\Context');
    }

    public function attribute() {
        return $this->belongsTo('App\Attribute');
    }

    public function context_val() {
        return $this->belongsTo('App\Context', 'context_val');
    }

    public function thesaurus_val() {
        return $this->belongsTo('App\ThConcept', 'thesaurus_val', 'concept_url');
    }
}
