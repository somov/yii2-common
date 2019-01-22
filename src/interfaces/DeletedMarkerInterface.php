<?php
/**
 * Created by PhpStorm.
 * User: develop
 * Date: 26.11.2018
 * Time: 20:35
 */

namespace somov\common\interfaces;

use yii\base\Component;


/**
 * Interface DeletedMarkerInterface
 * @package somov\common\interfaces
 *
 * @method deleteMarkedRelation(array $relations, callable $onMatch = null, callable $onUpdateGroup = null)
 * @method deleteMarked(array $models, callable $onMatch = null, callable $onUpdateGroup = null)
 * @method integer markDeleted(Component[] $items, array $search = null)
 * @method Component[] filterDeleted(array $items, $not = false)
 */
interface DeletedMarkerInterface
{

}