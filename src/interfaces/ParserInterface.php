<?php
/**
 *
 * User: develop
 * Date: 03.07.2018
 */

namespace somov\common\interfaces;


use somov\common\process\BaseProcess;

interface ParserInterface
{
    /**
     * @param mixed $data
     * @param BaseProcess $process
     * @return $this
     */
    public function parse($data, BaseProcess $process);
}