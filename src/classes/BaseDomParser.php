<?php
/**
 * Created by PhpStorm.
 * User: web
 * Date: 12.07.20
 * Time: 20:05
 */

namespace somov\common\classes;

use somov\common\traits\HtmlDomPathTrait;
use yii\base\BaseObject;

/**
 * Class DomParserBase
 * @package somov\common\components
 *
 * @property-read \DOMDocument $dom
 * @property-red \DOMXPath $xpath
 */
class BaseDomParser extends BaseObject
{
    use HtmlDomPathTrait;

    /**
     * @var string
     */
    public $html;

    /**
     * @var \DOMXPath
     */
    protected $_xpath;

    /**
     * @var \DOMDocument
     */
    protected $_dom;

    /**
     * DomParserBase constructor.
     * @param string $html
     * @param array $config
     */
    public function __construct($html, array $config = [])
    {
        $this->_dom = $this->createDomDocument($html);
        $this->_xpath = $this->createXpath($this->_dom);
        parent::__construct($config);
    }

    /**
     * @return \DOMXPath
     */
    public function getXpath()
    {
        return $this->_xpath;
    }

    /**
     * @return \DOMDocument
     */
    public function getDom()
    {
        return $this->_dom;
    }

    /**
     * @param string $expression
     * @param null $contextnode
     * @param bool $registerNodeNS
     * @param bool|string $glue
     * @return array|string
     */
    public function textExpression($expression, $contextnode = null, $registerNodeNS = true, $glue = false)
    {
        $result = $this->getNodesText($this->getXpath()->query($expression, $contextnode, $registerNodeNS));

        if (is_string($glue)) {
            return implode('', $result);
        }
        return $result;
    }

    /**
     * @param string $expression
     * @param null $contextnode
     * @param bool $registerNodeNS
     * @param bool|string $glue
     * @return array|string
     */
    public function htmlExpression($expression, $contextnode = null, $registerNodeNS = true, $glue = false)
    {
        $result = [];
        foreach ($this->getXpath()->query($expression , $contextnode, $registerNodeNS) as $item) {
            $result[] = $this->getDom()->saveHTML($item);
        }
        if (is_string($glue)) {
            return implode('', $result);
        }
        return $result;
    }

}