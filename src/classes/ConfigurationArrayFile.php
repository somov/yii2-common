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
use yii\caching\CacheInterface;
use yii\caching\DummyCache;
use yii\caching\FileDependency;
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

    /**
     * @var string|CacheInterface|array|boolean
     */
    public $cache = 'cache';

    /**
     * @var string
     */
    public $cacheDuration = 3600;

    /**
     * @var null|array
     */
    public $variables = null;


    /** Конструктор
     * ConfigurationArrayFile constructor.
     * @param string $fileName
     * @param array $config
     */
    public function __construct($fileName, array $config = [])
    {
        $isRead = ArrayHelper::remove($config, 'read', true);
        parent::__construct($config);
        $this->_fileName = \Yii::getAlias($fileName);
        if ($isRead) {
            $this->read($this->_fileName);
        }
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
        $this->data = $this->configurationFileCacheInstance()->getOrSet([self::class, $fileName],
            function () use ($fileName) {
                if (file_exists($fileName)) {
                    if (isset($this->variables) && is_array($this->variables)) {
                        extract($this->variables);
                    }
                    return include $fileName;
                }
                return [];
            }, $this->cacheDuration, new FileDependency([
                'fileName' => $fileName
            ]));

        return $this;
    }


    /**
     * @return object|CacheInterface|DummyCache
     * @throws \yii\base\InvalidConfigException
     */
    private function configurationFileCacheInstance()
    {
        if ($this->cache !== false) {
            /** @var CacheInterface $cache */
            $cache = is_string($this->cache) ?
                \Yii::$app->get($this->cache, false) :
                $this->cache;

            if (is_array($cache)) {
                $cache = \Yii::createObject($cache);
            }

            if (!$cache instanceof CacheInterface) {
                $cache = new DummyCache();
                \Yii::warning([self::class, 'Cache not configured']);
            }
            return $cache;
        }
        return new DummyCache();
    }

    /**
     * @return string
     * @throws \yii\base\InvalidConfigException
     */
    public function getContent()
    {
        $content = "<?php\n";
        $content .= "// Updated at " . \Yii::$app->formatter->asDatetime(time()) . "\n";
        $content .= "return ";
        $content .= var_export($this->data, true);
        $content .= ";";
        return $content;
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

        if (!file_put_contents($this->fileName, $this->getContent())) {
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
        $this->data = ArrayHelper::merge($this->asArray(), $array);
        return $this;
    }

    /**
     * @return array
     */
    public function asArray()
    {
        return (array)$this->data;
    }

    /**
     * @param array|string|\Closure $key
     * @param mixed|null $default
     * @return mixed
     */
    public function getValue($key, $default = null)
    {
        return ArrayHelper::getValue($this->data, $key, $default);
    }

    /**
     * @return $this
     */
    public function clear()
    {
        $this->data = [];
        return $this;
    }

}