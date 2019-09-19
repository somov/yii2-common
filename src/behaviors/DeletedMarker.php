<?php
/**
 *
 * User: develop
 * Date: 16.11.2018
 */

namespace somov\common\behaviors;


use somov\common\interfaces\DeletedMarkerInterface;
use yii\base\Behavior;
use yii\base\Component;
use yii\db\ActiveRecord;

class DeletedMarker extends Behavior implements DeletedMarkerInterface
{

    public $markedDeleted = false;


    /**
     * @param array $relations
     * @param callable $onMatch
     * @param callable $onUpdateGroup
     */
    public function deleteMarkedRelation(array $relations, callable $onMatch = null, callable $onUpdateGroup = null)
    {
        foreach ($relations as $relation) {

            $models = $this->owner->$relation;
            /**
             * @var  $index
             * @var ActiveRecord $model
             */
            foreach ($models as $index => $model) {
                if (!$model->hasProperty('markedDeleted')) {
                    continue;
                }
                if ($model->markedDeleted) {
                    unset($models[$index]);
                    if (isset($onMatch)) {
                        call_user_func($onMatch, $model, $relation);
                    }
                }
            }

            if (isset($onUpdateGroup) && count($models) <> count($this->owner->$relation)) {
                call_user_func($onUpdateGroup, $models, $relation);
            }
        }
    }

    /**
     * @param array $models
     * @param callable|null $onMatch
     * @param callable|null $onUpdateGroup
     */
    public function deleteMarked(array $models, callable $onMatch = null, callable $onUpdateGroup = null)
    {
        $count = count($models);
        foreach ($models as $index => $model) {
            if (!$model->hasProperty('markedDeleted')) {
                continue;
            }
            if ($model->markedDeleted) {
                unset($models[$index]);
                if (isset($onMatch)) {
                    call_user_func($onMatch, $model);
                }
            }
        }

        if (isset($onUpdateGroup) && count($models) <> $count) {
            call_user_func($onUpdateGroup, $models);
        }
    }

    /**
     * @param Component[] $items
     * @param array|null $search
     * @return int
     */
    public function markDeleted(array $items, array $search = null)
    {
        $count = 0;
        if (isset($search)) {
            $attribute = key($search);
            $value = reset($search);
        }

        foreach ($items as $model) {

            if (!$model->hasProperty('markedDeleted')) {
                $model->attachBehavior('deletedMarker', self::class);
            }

            if (isset($attribute) && isset($value)) {
                if ($model->$attribute === $value) {
                    $model->markedDeleted = true;
                    $count++;
                }
            } else {
                $model->markedDeleted = true;
                $count++;
            }
        }
        return $count;
    }

    /**
     * @param array $items
     * @param bool $not
     * @return Component[]
     */
    public function filterDeleted(array $items, $not = false)
    {
        return array_filter($items, function ($model) use ($not) {
            /**
             * @var ActiveRecord|DeletedMarkerInterface $model
             */
            if (!$model->hasProperty('markedDeleted')) {
                return $not;
            }

            return ($not) ? !$model->markedDeleted : $model->markedDeleted;
        });
    }

}