<?php
/**
 *
 * User: develop
 * Date: 18.01.2019
 */

namespace somov\common\classes;


use Exception;
use somov\common\traits\DynamicProperties;
use yii\base\ArrayAccessTrait;
use yii\base\BaseObject;
use yii\helpers\ArrayHelper;


/**
 * Создание и переписывание файлов конфигурации в формате  массив PHP
 * @property-read  string fileName
 */
class ConfigurationArrayFile extends BaseObject implements \ArrayAccess, \IteratorAggregate
{

    use ArrayAccessTrait, DynamicProperties;

    /** Массив данных файлов конфигурации
     * @var array
     */
    private $data = [];

    /** Имя файла
     * @var string
     */
    private $_fileName;

    /** Конструктор
     * ConfigurationArrayFile constructor.
     * @param string $fileName
     * @param array $config
     */
    public function __construct($fileName, array $config = [])
    {
        $this->_fileName = \Yii::getAlias($fileName);

        $this->read($this->fileName);
        parent::__construct($config);
    }

    /** Функция свойства имя файла
     * @return string
     */
    protected function getFileName()
    {
        return $this->_fileName;
    }

    /**
     * @param $fileName
     * @return $this
     */
    public function read($fileName)
    {
        if (file_exists($fileName)) {
            $this->data = require "$this->fileName";
        }
        return $this;
    }

    /**
     *Запись в файл
     * @param string|null $asFileName
     * @return $this
     * @throws \yii\base\InvalidConfigException
     */
    public function write($asFileName = null)
    {
        if (isset($asFileName)) {
            $this->_fileName = $asFileName;
        }
        $content = "<?php\n";
        $content .= "// Updated in " . \Yii::$app->formatter->asDatetime(time()) . "\n";
        $content .= "return ";
        $content .= var_export($this->data, true);
        $content .= ";";

        if (!file_put_contents($this->fileName, $content)) {
            throw new Exception('Cannot write config at ' . $this->fileName);
        }

        try {
            if (function_exists('opcache_invalidate')) {
                opcache_invalidate($this->fileName, true);
            }
            if (function_exists('apc_delete_file')) {
                apc_delete_file($this->fileName);
            }
        } catch (Exception $exception) {
            \Yii::warning($exception->getMessage());
        }

        return $this;
    }

    /** Слияние с архивом
     * @param array $array
     * @return $this
     */
    public function mergeWith(array $array)
    {
        $this->data = ArrayHelper::merge($this->data, $array);
        return $this;
    }

    /**
     * @return array
     */
    public function asArray()
    {
        return $this->data;
    }

}