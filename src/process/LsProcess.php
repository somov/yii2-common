<?php
/**
 *
 * User: develop
 * Date: 03.07.2018
 */

namespace somov\common\process;


class LsProcess extends BaseProcess
{
    use ArrayBuffered;

    public $all = true;

    public $detail = true;

    protected function prepareCommand()
    {
        $this->command = (DIRECTORY_SEPARATOR === '/') ? 'ls'  : 'dir';

        $this->addArgument($this->cwd);

        if ($this->all) {
            $this->addArgument('--all');
        }

        if ($this->detail) {
            $this->addArgument('-l');
        }

        return parent::prepareCommand();
    }
}