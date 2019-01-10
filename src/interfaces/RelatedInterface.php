<?php
/**
 * Created by PhpStorm.
 * User: develop
 * Date: 26.11.2018
 * Time: 20:48
 */

namespace somov\common\interfaces;

use yii\db\ActiveRecord;

/**
 * Interface RelatedInterface
 * @package somov\common\interfaces
 *
 * @method $this setRelation(ActiveRecord $record, $name, array $data, $deleteLinkedRelation = true)
 * @method bool isLinked($relatedName, $object, $record = null)
 */
interface RelatedInterface
{

}