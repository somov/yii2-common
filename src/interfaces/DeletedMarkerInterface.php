<?php
/**
 * Created by PhpStorm.
 * User: develop
 * Date: 26.11.2018
 * Time: 20:35
 */

namespace somov\common\interfaces;

use yii\db\ActiveRecord;


/**
 * Interface DeletedMarkerInterface
 * @package somov\common\interfaces
 *
 * @method deleteMarkedRelation(array $relations, callable $onMatch = null, callable $onUpdateGroup = null)
 * @method deleteMarked(array $models, callable $onMatch = null, callable $onUpdateGroup = null)
 * @method integer markDeleted(ActiveRecord[] $items, array $search = null)
 * @method ActiveRecord[] filterDeleted(array $items, $not = false)
 * @property bool $markedDeleted
 */
interface DeletedMarkerInterface
{

}