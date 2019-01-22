<?php


namespace somov\common\exceptions;

use somov\common\process\BaseProcess;


class ProcessException extends \yii\base\Exception
{
    /**
     * @var BaseProcess
     */
    public $process;

    /**
     * ProcessException constructor.
     * @param BaseProcess $process
     * @param int $message
     * @param \Throwable|null $previous
     */
    public function __construct(BaseProcess $process, $message, \Throwable $previous = null)
    {
        $process->addError($message);
        $this->process = $process;
        parent::__construct($this->process->getErrors(), $this->process->getExitCode(), $previous);
    }
}