<?php
/**
 *
 * User: develop
 * Date: 03.07.2018
 */

namespace somov\common\process;

/**
 * Class LsProcess
 * @package somov\common\process
 */
class LsProcess extends BaseProcess
{
    use ArrayBuffered;

    /**
     * @var bool
     */
    public $all = true;

    /**
     * @var bool
     */
    public $detail = true;

    /**
     * @var
     */
    public $blockSize;

    /**
     * @inheritdoc
     */
    protected function prepareCommand()
    {
        $this->command = (DIRECTORY_SEPARATOR === '/') ? 'ls'  : 'dir';

        $this->addArgument($this->cwd);

        if (isset($this->blockSize)) {
            $this->addArgument('--block-size', [$this->blockSize]);
        }

        if ($this->all) {
            $this->addArgument('--all');
        }

        if ($this->detail) {
            $this->addArgument('-l');
        }


        return parent::prepareCommand();
    }
}