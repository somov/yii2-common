<?php
/**
 *
 * User: develop
 * Date: 26.11.2018
 */

namespace somov\common\behaviors;


use somov\common\interfaces\DeletedMarkerInterface;
use somov\common\interfaces\RelatedInterface;
use yii\base\Behavior;
use yii\base\Component;
use yii\base\Event;
use yii\db\ActiveRecord;
use yii\helpers\ArrayHelper;

/**
 * Class Related
 * @package somov\common\behaviors
 *
 * @method  deleteMarkedRelation(array $relations, callable $onMatch = null, callable $onUpdateGroup = null)
 * @method  deleteMarked(array $models, callable $onMatch = null, callable $onUpdateGroup = null)
 * @method mixed markDeleted(array $items, array $search = null)
 * @method Component[] filterDeleted(array $items, $not = false)
 */
class Related extends Behavior implements DeletedMarkerInterface, RelatedInterface
{

    /** @var Component|DeletedMarkerInterface */
    public $owner;

    public function attach($owner)
    {
        if (!in_array(DeletedMarker::class,
            ArrayHelper::getColumn($owner->behaviors(), 'class'))) {
            $this->owner->attachBehavior('deletedMarker', DeletedMarker::class);
        }
        parent::attach($owner);
    }

    /**
     * @param ActiveRecord $record
     * @param string $name
     * @param ActiveRecord[] $data
     * @param bool $deleteLinkedRelation
     * @return null|\yii\base\Component|ActiveRecord
     */
    public function setRelation(ActiveRecord $record, $name, array $data, $deleteLinkedRelation = true)
    {
        if (empty($data)) {
            $this->owner;
        }

        //Проверка на удаленные сущности
        $unlinked = $this->owner->filterDeleted($data);

        if ($unlinked && count($unlinked) > 0) {
            $handler = [$this, '_unLinkRelated'];
            $eventData = [
                'relName' => $name,
                'relData' => [$name => $unlinked],
                'handler' => $handler,
                'deleteLinkedRelation' => $deleteLinkedRelation
            ];
            $record->on(ActiveRecord::EVENT_BEFORE_UPDATE, $handler, $eventData);
            $this->owner->deleteMarked($data, null, function ($models) use (&$data) {
                $data = $models;
            });
        }

        if (count($data) == 0) {
            return $this->owner;
        }

        $handler = [$this, '_linkRelated'];

        $eventData = [
            'relName' => $name,
            'relData' => $data,
            'handler' => $handler
        ];

        if ($record->isNewRecord) {
            $record->on(ActiveRecord::EVENT_AFTER_INSERT, $handler, $eventData);
        } else {
            $record->on(ActiveRecord::EVENT_AFTER_UPDATE, $handler, $eventData);
        }

        return $this->owner;
    }


    /**
     * @param string $relatedName
     * @param ActiveRecord $object
     * @param null ActiveRecord $record
     * @return bool
     */
    public function isLinked($relatedName, $object, $record = null)
    {
        $record = (isset($record)) ? $record : $this->owner;
        /** @var ActiveRecord $model */
        foreach ($record->$relatedName as $model) {
            if ($model->primaryKey === $object->primaryKey) {
                return true;
            }
        }
        return false;
    }


    /**
     * @param Event $event
     * @throws \Exception
     * @private
     */
    public function _linkRelated($event)
    {
        /** @var ActiveRecord $record */
        $record = $event->sender;

        if ($record->isNewRecord) {
            return;
        }
        $relationName = $event->data['relName'];
        $relations = $event->data['relData'];

        /** @var ActiveRecord $obj */
        if (is_array($relations)) {
            foreach ($relations as $index => $obj) {
                //если уже добавлен - сохраняем и пропускаем
                if ($this->isLinked($relationName, $obj, $record)) {
                    if (!$obj->isNewRecord) {
                        $obj->save(false);
                    }
                    continue;
                }

                //если есть отметка об удалении пропускаем
                if (isset($obj->deleteMarked) && $obj->deleteMarked) {
                    continue;
                }

                if ($obj->isNewRecord) {
                    $obj->save();
                } else {
                    $obj->validate();
                }

                $record->link($relationName, $obj);
            }
        } else {
            $record->link($relationName, $relations);
        }

        $record->off($event->name, $event->data['handler']);
    }

    /**
     * @param Event $event
     * @throws \Exception
     * @private
     */
    public function _unLinkRelated($event)
    {

        $record = $event->sender;

        /**
         * @var string $relationName
         * @var ActiveRecord[] $relations
         */
        foreach ($event->data['relData'] as $relationName => $relations) {
            foreach ($relations as $obj) {
                if ($obj->isNewRecord) {
                    continue;
                }
                if ($this->isLinked($relationName, $obj, $record)) {
                    $record->unlink($relationName, $obj, $event->data['deleteLinkedRelation']);
                }
            }
        }
        $record->off($event->name, $event->data['handler']);
    }

}