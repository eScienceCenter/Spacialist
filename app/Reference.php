<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Reference extends Model
{
    protected $table = 'references';
    /**
     * The attributes that are assignable.
     *
     * @var array
     */
    protected $fillable = [
        'entity_id',
        'attribute_id',
        'bibliography_id',
        'description',
        'lasteditor',
    ];

    const rules = [
        'bibliography_id' => 'required|integer|exists:bibliography,id',
        'description' => 'string|nullable'
    ];

    const patchRules = [
        'description' => 'string|nullable'
    ];

    public static function add($values, $user) {
        $reference = new self();
        foreach($values as $k => $v) {
            // TODO remove after table/column renaming
            if($k == 'bibliography_id') {
                $reference->bibliography_id = $v;
            } else {
                $reference->{$k} = $v;
            }
        }
        $reference->lasteditor = $user->name;
        $reference->save();
        $reference->bibliography; // Retrieve bibliography relation

        return $reference;
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

    public function bibliography() {
        return $this->belongsTo('App\Bibliography', 'bibliography_id');
    }
}
