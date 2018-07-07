<?php
/**
 *
 * User: develop
 * Date: 03.07.2018
 */

namespace somov\common\interfaces;


interface ParserInterface
{
    /**
     * @param mixed $data
     * @return $this
     */
    public function parse($data);
}