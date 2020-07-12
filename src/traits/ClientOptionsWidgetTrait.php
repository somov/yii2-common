<?php
/**
 * Created by PhpStorm.
 * User: web
 * Date: 16.11.19
 * Time: 16:53
 */

namespace somov\common\traits;


use yii\helpers\ArrayHelper;
use yii\helpers\Json;

/**
 * Trait ClientOptionsWidgetTrait
 * @package somov\common\traits
 *
 * @property-write array $options
 */
trait ClientOptionsWidgetTrait
{
    /**
     * @var array
     */
    public $clientOptions = [];


    /**
     * @var array
     */
    public $clientEvents = [];


    /**
     * Объединяет настройки виджета с предустановленными из зависимостей
     * @param array $options
     */
    public function setOptions(array $options)
    {
        foreach (['clientOptions', 'clientEvents'] as $category) {
            if ($settings = ArrayHelper::remove($options, $category)) {
                $this->{$category} = ArrayHelper::merge($this->{$category}, $settings);
            }
        }
    }


    /**
     * @param string $id
     * @param string $jsObjectName
     * @return string
     */
    public function getClientScriptJs($id, $jsObjectName)
    {

        $js = [];

        if (!empty($this->clientEvents)) {
            foreach ($this->clientEvents as $event => $handler) {
                $js[] = "jQuery('#$id').on('$event.$jsObjectName', $handler);";
            }
        }

        if ($this->clientOptions !== false) {
            $options = empty($this->clientOptions) ? '' : Json::htmlEncode($this->clientOptions);
            $js[] = "$jsObjectName.init($options);";
        }

        return implode("\n", $js);
    }

}