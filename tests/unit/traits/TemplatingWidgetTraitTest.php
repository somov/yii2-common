<?php
/**
 * Created by PhpStorm.
 * User: web
 * Date: 20.05.20
 * Time: 15:02
 */

use somov\common\traits\TemplatingWidgetTrait;
use yii\helpers\Json;

class TemplatingWidgetTraitTest extends \Codeception\Test\Unit
{

    use  TemplatingWidgetTrait;

    /**
     * @var
     */
    public $header = 'this is header';
    /**
     * @var bool
     */
    public $footer = false;


    const TEMPLATE = '{panel{heading<div class="must-be">{header}{headerButtons}</div>}{body<div class="fi">{firstItem</div><div class="si">{secondItem}</div>}}{footer}}';

    /**
     * @var array
     */
    public $csOptions = [


        'panel' => [
            'tag' => 'div',
            'class' => 'panel panel-default'
        ],

        'heading' => [
            'tag' => 'div',
            'class' => 'panel-heading'
        ],

        'header' => [
            'tag' => 'h5',
            'class' => 'panel-heading-title'
        ],

        'headerButtons' => [
            'tag' => 'div',
            'class' => 'header-buttons'
        ],

        'body' => [
            'tag' => 'div',
            'class' => 'panel-body'
        ],
    ];

    private function renderBody()
    {
        return 'BODY static';
    }


    public function testRenderTemplate()
    {
        $result = $this->renderTemplate(self::TEMPLATE);
        $expected = "<div class=\"panel panel-default\"><div class=\"panel-heading\"><div class=\"must-be\"><h5 class=\"panel-heading-title\">this is header</h5><div class=\"header-buttons\"></div></div></div><div class=\"panel-body\">BODY static<div class=\"fi\"></div><div class=\"si\"></div></div></div>";
        $this->assertSame($expected, $result);

    }


    public function testCompileTemplate()
    {
        $result = $this->compileTemplate(self::TEMPLATE);

        $this->assertSame(Json::decode('{"panel":{"templatePart":"{panel{heading<div class=\"must-be\">{header}{headerButtons}<\/div>}{body<div class=\"fi\">{firstItem<\/div><div class=\"si\">{secondItem}<\/div>}}{footer}}","offsetTotal":0,"offset":0,"name":"panel","children":{"heading":{"templatePart":"{heading<div class=\"must-be\">{header}{headerButtons}<\/div>}","offsetTotal":5,"offset":5,"name":"heading","children":{"header":{"templatePart":"{header}","offsetTotal":33,"offset":28,"name":"header"},"headerButtons":{"templatePart":"{headerButtons}","offsetTotal":69,"offset":36,"name":"headerButtons"}},"sub":"<div class=\"must-be\">{header}{headerButtons}<\/div>"},"body":{"templatePart":"{body<div class=\"fi\">{firstItem<\/div><div class=\"si\">{secondItem}<\/div>}}","offsetTotal":69,"offset":64,"name":"body","children":{"firstItem":{"templatePart":"{firstItem<\/div><div class=\"si\">{secondItem}<\/div>}","offsetTotal":89,"offset":20,"name":"firstItem","children":{"secondItem":{"templatePart":"{secondItem}","offsetTotal":120,"offset":31,"name":"secondItem"}},"sub":"<\/div><div class=\"si\">{secondItem}<\/div>"}},"sub":"<div class=\"fi\">{firstItem<\/div><div class=\"si\">{secondItem}<\/div>}"},"footer":{"templatePart":"{footer}","offsetTotal":206,"offset":137,"name":"footer"}},"sub":"{heading<div class=\"must-be\">{header}{headerButtons}<\/div>}{body<div class=\"fi\">{firstItem<\/div><div class=\"si\">{secondItem}<\/div>}}{footer}"}}'),
            $result);

    }
}
