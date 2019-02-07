<?php
/**
 *
 * User: develop
 * Date: 02.07.2018
 */

namespace somov\common\process;

use somov\common\interfaces\ParserInterface;
use yii\base\BaseObject;
use yii\base\InvalidConfigException;
use yii\helpers\Inflector;
use yii\helpers\StringHelper;

abstract class BaseProcess extends BaseObject
{

    const TYPE_PIPE_NORMAL = 'pipeNormal';

    /**
     * @var string
     */
    public $commandPath = '';

    /**
     * @var
     */
    protected $command;

    /**
     * @var array
     */
    private $_arguments = [];

    /**
     * @var string
     */
    public $cwd;

    /**
     * @var array
     */
    public $env;

    /**
     * @var string
     */
    public $writeData;

    /**
     * @var array
     */
    private $_status = null;

    /**
     * @var int
     */
    private $exitCodeNormal = 0;

    /**
     * @var array
     */
    private $_errors = [];

    /**
     * @var string class parser output
     */
    public $outputParser;

    /** Действие команды - строка с идентификтором или массив идентификатор => параметры действия
     * @var array|string
     */
    public $action;


    /**
     * @throws InvalidConfigException
     */
    private function initCommand()
    {
        $t = Inflector::camel2words(StringHelper::basename(static::class), false);
        if (!preg_match('/[a-z\d]+/', $t, $m)) {
            throw  new InvalidConfigException('Error detect command name ');
        }
        $this->command = $m[0];
    }

    /**
     * Init process command
     */
    public function init()
    {
        if (empty($this->command)) {
            $this->initCommand();
        }
        parent::init();
    }

    /**
     * @return string
     */
    public function compileCommand()
    {
        $this->_arguments = [];

        if (!isset($this->action)) {
            return $this->prepareCommand();
        }

        $action = 'action' . ucfirst($this->getActionId());
        if ($this->hasMethod($action)) {
            return call_user_func_array([$this, $action], $this->getActionParams());
        }

        return $this->prepareCommand();
    }

    /**
     * @return string
     */
    public function getActionId()
    {
        if (is_array($this->action)) {
            return key($this->action);
        }
        return $this->action;
    }

    /**
     * @return array
     */
    public function getActionParams()
    {
        if (is_string($this->action)) {
            return [];
        }
        return reset($this->action);
    }

    /**
     * @return string
     */
    protected function prepareCommand()
    {
        return $this->joinCommandAndArguments();
    }


    /** path and  filename binary file
     * @return string
     */
    protected function getFullCommand()
    {
        return ((!empty($this->commandPath)) ? $this->commandPath . DIRECTORY_SEPARATOR : '') . $this->command;
    }

    /** Get full command and clean arguments
     * @return string
     */
    protected function joinCommandAndArguments()
    {
        $c = '';
        foreach ($this->_arguments as $argument) {
            foreach ($argument as $key => $val) {
                $key = is_integer($key) ? '' : $key;
                $c .= " $key $val";
            }
        }
        $this->_arguments = [];
        return $this->getFullCommand() . ' ' . $c;
    }


    /**
     * @param resource $pipe
     * @param string $type
     * @internal param bool $isError
     */
    public function readPipe($pipe, $type = self::TYPE_PIPE_NORMAL)
    {
        if (!is_resource($pipe)) {
            return;
        }

        while ($s = fgets($pipe)) {
            if ($type === self::TYPE_PIPE_NORMAL) {
                $this->writeBuffer($s);
            } else {
                $this->addError($s);
            }
        }
    }

    /**
     * @param string $message
     */
    public function addError($message)
    {
        $this->_errors[] = $message;
    }


    /**
     * @param string $data
     * @return mixed
     */
    protected abstract function writeBuffer($data);

    /**
     * @return mixed
     */
    protected abstract function getOutPutBuffer();

    /**
     * @return  bool
     */
    public function isEmptyOutput()
    {
        return empty($this->getOutPutBuffer());
    }

    /**
     * @return array|object
     */
    public function getOutPutData()
    {
        if (!isset($this->outputParser)) {
            return $this->getOutPutBuffer();
        }

        /** @var ParserInterface $parser */
        $parser = ($this->outputParser instanceof ParserInterface) ? $this->outputParser : \Yii::createObject($this->outputParser);

        return $parser->parse($this->getOutPutBuffer());
    }

    /**
     * @param \resource $resource
     */
    public function setStatus($resource)
    {
        usleep(200);
        $this->_status = proc_get_status($resource);
    }

    /**
     * @return int|null
     */
    public function getExitCode()
    {
        return isset($this->_status['exitcode']) ? (int)$this->_status['exitcode'] : null;
    }

    /**
     * @return bool
     */
    public function isDoneProperly()
    {
        return isset($this->exitCode) ? $this->getExitCode() === $this->exitCodeNormal : false;
    }

    /**
     * @param string $glue
     * @return string
     */
    public function getErrors($glue = "\n")
    {
        return implode($glue, $this->_errors);
    }

    /** Add command argument
     * @param array|string $key
     * @param null $value
     * @return $this
     */
    protected function addArgument($key, $value = null)
    {
        if (is_array($key)) {
            foreach ($key as $k => $v) {
                $this->addArgument($k, $v);
            }
            return $this;
        }
        $this->_arguments[] = [$key => $value];
        return $this;
    }

    /**
     * @return array
     */
    public function getStatus()
    {
        return $this->_status;
    }
}