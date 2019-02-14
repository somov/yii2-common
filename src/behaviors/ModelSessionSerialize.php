<?php
/**
 * Created by PhpStorm.
 * User: web
 * Date: 14.02.19
 * Time: 11:44
 */

namespace somov\common\behaviors;


use Yii;
use yii\base\Behavior;
use yii\base\Model;

/**
 *
 * Стерилизация и восстановление артритов  модели в сессию
 * Class ModelSessionSerialize
 * @package somov\common\behaviors
 *
 *
 */
class ModelSessionSerialize extends Behavior
{
    /**
     * @var boolean
     */
    public $enabled = true;

    /** @var Model */
    public $owner;

    /**
     * @var bool
     */
    public $safeOnly = false;


    /**
     * @param \yii\base\Component $owner
     */
    public function attach($owner)
    {
        parent::attach($owner);

        if ($this->enabled) {
            $this->loadFromSession();
        }
    }

    /**
     *
     */
    public function __destruct()
    {
        if ($this->enabled) {
            $this->saveToSession();
        }
    }

    /**
     * @return string
     */
    protected function getKey()
    {
        return get_class($this->owner);
    }

    /**
     *
     */
    protected function loadFromSession()
    {
        $this->owner->setAttributes(Yii::$app->session->get($this->getKey(), []), $this->safeOnly);
    }

    /**
     *
     */
    protected function saveToSession()
    {
        Yii::$app->session->set($this->getKey(), $this->owner->attributes);
    }

}