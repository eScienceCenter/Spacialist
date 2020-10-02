<?php

namespace App\Traits;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Gate;
use Illuminate\Database\Eloquent\Scope;
use App\AccessRule;
use App\Group;

class ModerationScope implements Scope
{
    /**
     * All of the extensions to be added to the builder.
     *
     * @var array
     */
    protected $extensions = ['WithModerated', 'WithoutModerated', 'OnlyModerated'];

    /**
     * Apply the scope to a given Eloquent query builder.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $builder
     * @param  \Illuminate\Database\Eloquent\Model  $model
     * @return void
     */
    public function apply(Builder $builder, Model $model)
    {
        $builder->whereNull($model->getQualifiedModerationStateColumn());
    }

    /**
     * Extend the query builder with the needed functions.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $builder
     * @return void
     */
    public function extend(Builder $builder)
    {
        foreach ($this->extensions as $extension) {
            $this->{"add{$extension}"}($builder);
        }
    }

    /**
     * Get the "deleted at" column for the builder.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $builder
     * @return string
     */
    protected function getModerationStateColumn(Builder $builder)
    {
        if (count((array) $builder->getQuery()->joins) > 0) {
            return $builder->getModel()->getQualifiedModerationStateColumn();
        }

        return $builder->getModel()->getModerationStateColumn();
    }

    /**
     * Add the with-trashed extension to the builder.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $builder
     * @return void
     */
    protected function addWithModerated(Builder $builder)
    {
        $builder->macro('withModerated', function (Builder $builder, $withModerated = true) {
            if (! $withModerated) {
                return $builder->withoutModerated();
            }

            return $builder->withoutGlobalScope($this);
        });
    }

    /**
     * Add the without-trashed extension to the builder.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $builder
     * @return void
     */
    protected function addWithoutModerated(Builder $builder)
    {
        $builder->macro('withoutModerated', function (Builder $builder) {
            $model = $builder->getModel();

            $builder->withoutGlobalScope($this)->whereNull(
                $model->getQualifiedModerationStateColumn()
            );

            return $builder;
        });
    }

    /**
     * Add the only-trashed extension to the builder.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $builder
     * @return void
     */
    protected function addOnlyModerated(Builder $builder)
    {
        $builder->macro('onlyModerated', function (Builder $builder) {
            $model = $builder->getModel();

            $builder->withoutGlobalScope($this)->whereNotNull(
                $model->getQualifiedModerationStateColumn()
            );

            return $builder;
        });
    }
}

trait ModerationTrait
{
    protected static function boot()
    {
        parent::boot();

        static::addGlobalScope(new ModerationScope);
    }

    /**
     * Restore a soft-deleted model instance.
     *
     * @return bool|null
     */
    public function moderate($action = 'accept', $ignoreCopyOn = false, $returnDirty = false)
    {
        // If the restoring event does not return false, we will proceed with this
        // restore operation. Otherwise, we bail out so the developer will stop
        // the restore totally. We will clear the deleted timestamp and save.
        if ($this->fireModelEvent('moderating') === false) {
            return false;
        }

        $pendingDelete = $this->{$this->getModerationStateColumn()} == 'pending-delete';

        if($action == 'accept') {
            if($pendingDelete) {
                $this->delete();
                $result = null;
            } else {
                // if copyOn is set, delete entries with same values
                // in columns defined in copyOn
                if(isset($this->copyOn) && count($this->copyOn) > 0) {
                    $query = self::whereNull($this->getModerationStateColumn());

                    foreach($this->copyOn as $col) {
                        $query->where($col, $this->{$col});
                    }

                    $query->delete();
                }

                $this->{$this->getModerationStateColumn()} = null;
                // Once we have saved the model, we will fire the "restored" event so this
                // developer will do anything they need to after a restore operation is
                // totally finished. Then we will return the result of the save call.
                $this->exists = true;

                $result = $this->save();
            }

            $this->fireModelEvent('moderated', false);

        } else if($action == 'deny' || $pendingDelete) {
            if($pendingDelete) {
                $this->{$this->getModerationStateColumn()} = null;
                $result = $this->save();
            } else {
                $this->delete();
                $result = null;
            }

        } else {
            if(!$ignoreCopyOn && isset($this->copyOn) && count($this->copyOn) > 0) {
                $copy = $this->replicate();
                $copy->{$this->getModerationStateColumn()} = $action;

                if($returnDirty) {
                    $result = $copy;
                } else {
                    $result = $copy->save();
                }
            } else {
                $this->{$this->getModerationStateColumn()} = $action;

                if($returnDirty) {
                    $result = $this;
                } else {
                    $result = $this->save();
                }
            }
        }

        return $result;
    }

    /**
     * Determine if the model instance is currently moderated.
     *
     * @return bool
     */
    public function isModerated()
    {
        return ! is_null($this->{$this->getModerationStateColumn()});
    }

    /**
     * Register a moderating model event with the dispatcher.
     *
     * @param  \Closure|string  $callback
     * @return void
     */
    public static function moderating($callback)
    {
        static::registerModelEvent('moderating', $callback);
    }

    /**
     * Register a moderated model event with the dispatcher.
     *
     * @param  \Closure|string  $callback
     * @return void
     */
    public static function moderated($callback)
    {
        static::registerModelEvent('moderated', $callback);
    }

    /**
     * Get the name of the "deleted at" column.
     *
     * @return string
     */
    public function getModerationStateColumn()
    {
        return defined('static::MODERATION_STATE') ? static::MODERATION_STATE : 'moderation_state';
    }

    /**
     * Get the fully qualified "moderation state" column.
     *
     * @return string
     */
    public function getQualifiedModerationStateColumn()
    {
        return $this->qualifyColumn($this->getModerationStateColumn());
    }
}
