<?php

use yii\helpers\ArrayHelper;

defined('YII_ENV') or define('YII_ENV', 'test');
defined('YII_DEBUG') or define('YII_DEBUG', true);


$dir =  dirname(dirname(__DIR__));
require_once "$dir/vendor/yiisoft/yii2/Yii.php";
require_once "$dir/vendor/autoload.php";

Yii::setAlias('@mtest', $dir  . '/tests');
Yii::setAlias('@ext', $dir );

$config = require_once "$dir/tests/console.php";
ArrayHelper::remove($config, 'class');

(new yii\console\Application($config))->init();
