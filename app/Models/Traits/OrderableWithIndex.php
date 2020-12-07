<?php


namespace App\Models\Traits;

/**
 * @property integer $index
 */
trait OrderableWithIndex
{

    public static $ownerKey = 'individual_id';

    /**
     * Defines some event handlers.
     */
    public static function bootOrderableWithIndex() {

        static::creating(function($model) {
            if($model->index === null) {
                $model->index = static::query()->where(static::$ownerKey, $model->{static::$ownerKey})->count();
            }
        });

        static::deleted(function($model) {
            static::refreshModelIndexes($model->{static::$ownerKey});
        });
    }

    /**
     * Re-calculates the indexes off all the models that belong to the person with $person_id.
     *
     * @param $owner_id
     * @throws
     */
    public static function refreshModelIndexes($owner_id) {
        $models = static::query()->where(static::$ownerKey, $owner_id)->orderBy('index')->get();

        if(count($models) > 0) {
            \DB::transaction(function () use ($models) {
                foreach ($models->values() as $index => $model) {
                    $model->index = $index;
                    $model->saveOrFail();
                }
            });
        }
    }

    /**
     * Moves the model to the position of the given index.
     *
     * @param int $toIndex
     * @throws \Exception
     * @throws \Throwable
     */
    public function moveToIndex(int $toIndex) {

        // Get the models.
        $models = static::query()->where(static::$ownerKey, $this->{static::$ownerKey})->orderBy('index')->get();

        if($toIndex < 0) {
            $toIndex = 0;
        } elseif($toIndex >= count($models)) {
            $toIndex = count($models) - 1;
        }

        $models->splice($this->index, 1);
        $models->splice($toIndex, 0, [$this]);

        \DB::transaction(function() use ($models) {
            foreach ($models->values() as $index => $model) {
                $model->index = $index;
                $model->saveOrFail();
            }
        });

    }
}
