<?php
/**
 *
 * User: develop
 * Date: 03.07.2018
 */

namespace somov\common\process;


trait ArrayBuffered
{

    /**
     * @var array
     */
    private $_buffer = [];

    /**
     * @param string $data
     */
    function writeBuffer($data)
    {
        $this->_buffer[] = $data;
    }


    /**
     * @return array|object
     */
    protected function getOutPutBuffer()
    {
        return $this->_buffer;
    }
}