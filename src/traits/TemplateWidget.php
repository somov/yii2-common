<?php
/**
 * Created by PhpStorm.
 * User: develop
 * Date: 08.12.2018
 * Time: 19:02
 */

namespace somov\common\traits;


use yii\base\Widget;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\helpers\Inflector;

trait TemplateWidget
{

    /** генерирует секции по шаблону
     * @param string $template
     * @return mixed
     */
    public function renderTemplate($template = null)
    {

        if (empty($template) && empty($this->template)) {
            return '';
        }

        $template = isset($template) ? $template : $this->template;

        return preg_replace_callback("/\{([a-zA-z-]+)\}/", [$this, 'processSection'], $template);
    }

    /** обрабатывает секцию
     * @param array $match
     * @return string
     */
    private function processSection($match)
    {
        $content = '';
        if (is_array($match)) {
            $sections = array_filter(explode('-', $match[1]));
            $section = array_shift($sections);
        } else {
            $section = $match;
            $sections = null;
        }

        if (!empty($sections)) {
            $content = implode('', array_map([$this, 'processSection'], $sections));
        }

        $method = 'render' . ucfirst(Inflector::id2camel($section));

        if ($this->hasMethod($method)) {
            return call_user_func([$this, $method], $sections, $content);
        } else {
            return $this->getContentSection($section, null, $content);
        }
    }


    /**
     * @param string $name
     * @param callable|string $data
     * @param string $content
     * @return string
     */
    protected function getContentSection($name, $data = null, $content = '')
    {

        if (!isset($this->$name) || $this->$name === false) {
            return '';
        }

        $data = (!empty($data)) ? $data : $this->$name;

        $options = ArrayHelper::remove($this->options, $name, []);
        /** @var Widget|string $class */
        if (is_array($data) && !empty($data)) {
            if ($class = ArrayHelper::remove($options, 'class')) {
                $data = $class::widget($options);
            }
        }

        if (is_callable($data)) {
            $data = call_user_func_array($data, [$this]);
        } elseif (is_string($data) && strpos($data, '@') === 0) {
            $data = $this->render($data, array_merge(['widget' => $this],
                $this->hasProperty('viewParams') ? $this->viewParams : []));
        }

        if ($tag = ArrayHelper::remove($options, 'tag', false)) {
            return Html::tag($tag, $data . $content, $options);
        }

        return $data . $content;

    }

    /**
     * @param array|string $group
     * @param array|string $classes
     * @param array|string $styles
     * @return $this
     */
    public function initGroupOptions($group, $classes = null, $styles = null)
    {

        $groupName = (is_array($group)) ? key($group) : $group;

        if (!isset($this->options) || !isset($this->$groupName) || $this->$groupName === false) {
            return $this;
        }

        if (empty($this->options[$groupName])) {
            $this->options[$groupName] = [];
        }

        if (is_array($group)) {
            $this->options[$groupName] = reset($group);
        }

        if (isset($this->options[$groupName]['options'])) {
            $options = &$this->options[$groupName]['options'];
        } else {
            $options = &$this->options[$groupName];
        }

        if (isset($classes)) {
            foreach ((array)$classes as $class) {
                Html::addCssClass($options, $class);
            }
        }

        if (isset($styles)) {
            foreach ((array)$styles as $property => $value) {
                Html::addCssStyle($options, [$property => $value]);
            }
        }

        return $this;
    }


    /** Сеттер слияни настроек
     * @param array $options
     * @param string $optionsProperty
     */
    public function setMergeOptions(array $options, $optionsProperty = 'options')
    {
        $this->$optionsProperty = array_merge($this->$optionsProperty, $options);
    }
}