<?php
/**
 *
 * User: develop
 * Date: 02.07.2018
 */

namespace somov\common\components;

use somov\common\exceptions\ProcessException;
use somov\common\process\BaseProcess;

use yii\base\BaseObject;
use yii\base\Component;


class ProcessRunner extends Component
{

    /**
     * @var resource
     */
    private $_resource;



    public function __destruct()
    {
        $this->freeResource();
    }

    /**
     * @param $process
     * @param null $terminationStatus
     * @return object|array
     */
    public static function exec($process, &$terminationStatus = null)
    {
        /** @var BaseProcess $process */
        $process = ($process instanceof BaseObject) ? $process : \Yii::createObject($process);
        $terminationStatus = (new static())->run($process, $data);
        return $data;
    }

    /**
     * @param BaseProcess $process
     * @param $data
     * @return int
     */
    public function run(BaseProcess $process, &$data)
    {
        $terminationStatus = $this->execute($process);
        $data = $process->getOutPutData();
        return $terminationStatus;
    }

    /**
     * @param BaseProcess $process
     * @return int
     * @throws ProcessException
     * @throws \Exception
     */
    private function execute(BaseProcess $process)
    {

        $cmd = $process->compileCommand();

        if (empty($cmd)) {
            $process->addError('\'Empty command request\'');
            throw new ProcessException($process);
        }

        $this->_resource = proc_open($cmd, [
            0 => ['pipe', 'r'],
            1 => ['pipe', 'w'],
            2 => ['pipe', 'w']
        ], $pipes, $process->cwd, $process->env);

        if (!is_resource($this->_resource)) {
            throw new \Exception('Error open process ');
        }

        if (isset($process->writeData)) {
            fwrite($pipes[0], $process->writeData);
            fclose($pipes[0]);
        }
        usleep(100);
        $process->readPipe($pipes[1]);
        $process->setStatus($this->_resource);
        $process->readPipe($pipes[2], 'error');

        if ($process->isEmptyOutput() && !$process->isDoneProperly()) {
            $this->freeResource();
            throw new ProcessException($process);
        }

        return $this->freeResource();
    }

    /**
     * @return int
     */
    private function freeResource()
    {
        if (isset($this->_resource) && is_resource($this->_resource)) {
            return proc_close($this->_resource);
        }
        return -1;
    }
}