<?php
/**
 *
 * User: develop
 * Date: 03.07.2018
 */

namespace somov\common\process;


trait StringBuffered
{

    /**
     * @var string
     */
    private $_buffer = '';

    /**
     * @var integer null
     */
    private $_bufferSize = null;


    /**
     * @param integer $value
     * @return mixed
     */
    public function setBufferSize($value)
    {
        return $this->_bufferSize = $value;
    }


    function writeBuffer($data)
    {
        if (isset($this->_bufferSize) && mb_strlen($this->_buffer) > $this->_bufferSize) {
            if ($this->hasMethod('beforeFlushBuffer')) {
                if (call_user_func([$this, 'beforeFlushBuffer']) !== false) {
                    $this->flushBuffer();
                }
            } else {
                $this->flushBuffer();
            }
        }

        $this->_buffer .= $data;
    }


    /**
     * @return array|object|mixed
     */
    protected function getOutPutBuffer()
    {
        return $this->_buffer;
    }

    private function flushBuffer()
    {
        $this->_buffer = '';
    }
}