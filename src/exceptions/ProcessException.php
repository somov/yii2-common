<?php


namespace somov\common\exceptions;

use somov\common\process\BaseProcess;


class ProcessException extends \yii\base\Exception
{
    /**
     * @var BaseProcess
     */
    public $process;

    public function __construct(BaseProcess $process, \Throwable $previous = null)
    {
        $this->process = $process;

        parent::__construct($this->process->getErrors(), $this->process->getExitCode(), $previous);
    }
}