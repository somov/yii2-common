<?php
/**
 * Created by PhpStorm.
 * User: web
 * Date: 07.02.20
 * Time: 21:47
 */

namespace somov\common\traits;


use DOMDocument;
use DOMXPath;
use tidy;

trait HtmlDomPathTrait
{
    /**
     * @var string|null
     */
    public $domPageEncoding;

    /**
     * @inheritdoc
     */
    public function init()
    {
        $this->initDom();
        parent::init();
    }

    protected function initDom()
    {
        if (!extension_loaded('tidy')) {
            throw new \RuntimeException('Tidy ext not loaded');
        }
    }


    /**
     * @param $html
     * @return DOMDocument
     */
    public function createDomDocument($html)
    {
        $dom = new DOMDocument();

        $html = $this->clearWhiteSpace($html);

        if (isset($this->domPageEncoding)) {
            $html = str_replace($this->domPageEncoding, "UTF-8", $html);
            $html = iconv($this->domPageEncoding, 'UTF-8', $html);
        }
        libxml_use_internal_errors(true);
        $dom->loadHTML(mb_convert_encoding((new tidy())->repairString($html, ['wrap' => 0], 'utf8'), 'HTML-ENTITIES', 'UTF-8'));
        libxml_use_internal_errors(false);

        return $dom;

    }

    /**
     * @param $dom
     * @return DOMXPath
     */
    public function createXpath($dom)
    {
        if (!$dom instanceof DOMDocument && is_string($dom)) {
            $dom = $this->createDomDocument($dom);
        }

        return new DomXPath($dom);
    }

    /**
     * @param \DOMNodeList|null $list
     * @param integer $index
     * @param  string|null $attribute
     * @param  mixed $default
     * @return string|mixed
     */
    public function getNodeText($list, $index, $attribute = null, $default = '')
    {
        if (empty($list) || $list->count() - 1 < $index) {
            return $default;
        }
        $value = (isset($attribute)) ? $list->item($index)->getAttribute($attribute) : $list->item($index)->textContent;
        $value = trim($value);
        if (empty($value)) {
            return $default;
        }
        return $value;

    }

    /**
     * @param \DOMNodeList|null $list
     * @param  string|null $attribute
     * @param string|mixed $default
     * @return array
     */
    public function getNodesText($list, $attribute = null, $default = '')
    {
        $data = [];
        if (empty($list)) {
            return $data;
        }

        foreach ($list as $index => $node) {
            $data[] = $this->getNodeText($list, $index, $attribute, $default);
        }

        return $data;
    }


    /**
     * @param $string
     * @return string
     */
    protected function clearWhiteSpace($string)
    {
        $s = trim($string);
        $s = str_replace("\n", '', $s);
        $s = str_replace("\r", '', $s);
        return preg_replace('/\s\s+/', ' ', $s);
    }
}