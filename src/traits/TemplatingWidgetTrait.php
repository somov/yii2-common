<?php
/**
 * Created by PhpStorm.
 * User: develop
 * Date: 20.05.2020
 * Time: 19:02
 */

namespace somov\common\traits;


use somov\common\helpers\ArrayHelper;
use yii\base\Component;
use yii\base\Widget;
use yii\helpers\Html;

/**
 * Trait TemplatingWidgetTrait
 * @package somov\common\traits
 *
 * @property array contentOptions
 */
trait TemplatingWidgetTrait
{

    /**
     * @var array|null
     */
    private static $_cacheTemplate;


    /**
     * @param string|array $template
     * @return string
     */
    protected function renderTemplate($template = null)
    {

        $template = isset($template) ? $template : (isset($this->template) ? $this->template : '');

        if (empty($template)) {
            return '';
        }

        $meta = is_array($template) ? $template : $this->templateMeta($template);

        if (is_array($meta) && !empty($meta)) {
            return $this->fillContentTemplate($meta)->resolveContentTemplate($template, $meta);
        }

        return '';
    }

    /**
     * @param $template
     * @return array|null
     */
    protected function templateMeta($template)
    {
        $key = crc32($template . get_class($this));
        if (isset(self::$_cacheTemplate[$key])) {
            return self::$_cacheTemplate[$key];
        }
        if ($meta = $this->compileTemplate($template)) {
            self::$_cacheTemplate[$key] = $meta;
            return $meta;
        };
        return null;
    }


    /**
     * @param array $meta
     * @return TemplatingWidgetTrait
     */
    private function fillContentTemplate(array &$meta)
    {
        foreach ($meta as $name => &$item) {
            if (isset($item['children']) && is_array($item['children'])) {
                $this->fillContentTemplate($item['children']);
                $item['content'] = $this->getContentSection($name, null, $this->resolveContentTemplate($item['sub'], $item['children']));
            } else {
                $item['content'] = $this->getContentSection($name);
            }
        }
        return $this;
    }

    /**
     * @param string|array $template
     * @param array $meta
     * @return mixed
     */
    private function resolveContentTemplate($template, array $meta)
    {
        $content = $template;

        if (is_array($template)) {
            $content = implode('', ArrayHelper::getColumn($template, 'templatePart'));
        }

        foreach ($meta as $item) {
            $content = str_replace($item['templatePart'], $item['content'], $content);
        }

        return $content;
    }


    /**
     * @param string $name
     * @param callable|string $data
     * @param string $insert
     * @param string|boolean $methodPrefix
     * @return string|false
     * @throws \yii\base\InvalidConfigException
     */
    protected function getContentSection($name, $data = null, $insert = '', $methodPrefix = 'render')
    {

        $data = ((isset($data)) ? $data : (isset($this->$name) ? $this->$name : ''));

        if ($data === false) {
            return false;
        }

        $options = ArrayHelper::remove($this->getContentOptions(), $name, []);

        if ($methodPrefix) {
            $method = $methodPrefix . ucfirst($name);
            if (($this instanceof Component) ? $this->hasMethod($method) : method_exists($this, $method)) {
                $data = call_user_func_array([$this, $method], [&$insert, &$data]);
            }
        }

        if (is_callable($data)) {
            $data = call_user_func_array($data, [$this, &$insert, &$data]);
        }

        if (is_array($data)) {
            if ($sectionOptions = ArrayHelper::remove($options, 'sectionConfig')) {
                $data = ArrayHelper::merge($data, $sectionOptions);
            }

            if ($class = ArrayHelper::remove($data, 'class')) {
                if (is_subclass_of($class, Widget::class)) {
                    /** @var Widget|string $class */
                    $data = $class::widget($data);
                } else {
                    $data = (string)\Yii::createObject(array_merge(['class' => $class], $data));
                }
            } else {
                $data = '';
            }
        } elseif (is_string($data) && strpos($data, '@') === 0 && $this instanceof Widget) {
            /** @noinspection PhpUndefinedFieldInspection */
            $data = $this->render($data, array_merge(['widget' => $this],
                $this->hasProperty('viewParams') ? $this->viewParams : []));
        } elseif ($data === false) {
            return false;
        }

        $data = $data . $insert;

        if ($tag = ArrayHelper::remove($options, 'tag', false)) {
            return Html::tag($tag, $data, $options);
        }

        return $data;
    }

    /**
     * @param $template
     * @param int $offsetTotal
     * @return array|bool
     */
    private function compileTemplate($template, $offsetTotal = 0)
    {
        if (preg_match_all('/{(?:[^{}]+|(?R))*}/', $template, $matches, PREG_OFFSET_CAPTURE)) {
            $list = [];
            foreach (ArrayHelper::getValue($matches, '0') as $index => $match) {
                list($templatePart, $offset) = $match;
                $offsetTotal += $offset;
                $item = compact('templatePart', 'offsetTotal', 'offset');
                $templatePart = preg_replace('/}$/', '', ltrim($templatePart, '{'));
                preg_match('/^([A-z|0-9]+)(.*)$/', $templatePart, $match);
                $item['name'] = ArrayHelper::getValue($match, 1);
                if ($sub = ArrayHelper::getValue($match, 2, false)) {
                    $item['children'] = $this->compileTemplate($templatePart, $offsetTotal);
                    $item['sub'] = $sub;
                }
                $list[] = $item;
            }
            return ArrayHelper::index($list, 'name');
        }

        return false;
    }

    /**
     * @param array $options
     */
    public function setContentOptions(array $options)
    {
        $this->csOptions = ArrayHelper::merge($this->csOptions, $options);
    }

    /**
     * @return array
     */
    public function &getContentOptions()
    {
        if (isset($this->csOptions)) {
            return $this->csOptions;
        }
        $options = [];
        return $options;
    }
}