<?php
/**
 * Created by PhpStorm.
 * User: web
 * Date: 07.06.20
 * Time: 13:44
 */

namespace somov\common\traits;


use yii\bootstrap\BootstrapWidgetTrait;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\web\AssetBundle;

/**
 * Trait ScriptWidgetRegisterTrait
 * @package app\widgets\base
 *
 * @property-read string|null scriptDataAttribute
 * @property-read string|null scripPluginName
 * @property $options
 */
trait ScriptWidgetRegisterTrait
{
    use BootstrapWidgetTrait {
        registerPlugin as registerPluginBootstrap;
    }


    /**
     * @param string|bool $scriptAsset
     * @param string|bool $scriptDataAttribute
     * @param string|bool $scripPluginName
     */
    public function scriptRegister($scriptAsset = false, $scriptDataAttribute = false, $scripPluginName = false)
    {
        /** @var AssetBundle|string $scriptAsset */
        $scriptAsset = $scriptAsset ?: $this->scriptParam($scriptAsset);
        $scriptDataAttribute = $scriptDataAttribute ?: $this->scriptParam('scriptDataAttribute');
        $scripPluginName = $scripPluginName ?: $this->scriptParam('scripPluginName');

        if ($scriptAsset) {
            if ($scripPluginName) {
                if ($scriptAsset) {
                    $scriptAsset::register($this->getView());
                }
                $this->registerScriptPlugin($scripPluginName);
                $this->registerClientEvents();
            } else if ($scriptDataAttribute) {
                $this->registerScriptPluginViaDataAttr($scriptDataAttribute, $scriptAsset);
            }

        }
    }

    /**
     *
     */
    protected function idToOptions()
    {
        if (!isset($this->options['id'])) {
            $this->options['id'] = $this->getId();
        }
    }

    /**
     * @inheritdoc
     */
    public function registerScriptPlugin($name)
    {
        $this->idToOptions();
        $this->registerPluginBootstrap($name);
    }

    /**
     * @param string|boolean $dataAttributeName
     * @param string|AssetBundle|null $assetBundle
     * @param null $selector
     */
    public function registerScriptPluginViaDataAttr($dataAttributeName = 'widget', $assetBundle = null, $selector = null)
    {

        if ($this->clientOptions !== false) {
            if (isset($assetBundle)) {
                $assetBundle::register($this->getView());
            }
            if ($dataAttributeName) {
                ArrayHelper::setValue($this->options, 'data.' . $dataAttributeName, $this->clientOptions);
            }
            if (isset($selector)) {
                Html::addCssClass($this->options, $selector);
            }
            if ($this->clientEvents) {
                $this->idToOptions();
                $this->registerClientEvents();
            }
        }
    }

    /**
     * Sets the ID of the widget.
     * @param string $value id of the widget.
     */
    public function setId($value)
    {
        parent::setId($value);
        $this->idToOptions();
    }

    /**
     * @param $name
     * @param bool $default
     * @return bool
     */
    private function scriptParam($name, $default = false)
    {
        if ($this->hasProperty($name)) {
            return $this->{$name};
        }
        return $default;
    }

}