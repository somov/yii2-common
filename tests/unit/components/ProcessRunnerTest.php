<?php

namespace mtest\command;

use Codeception\TestCase\Test;
use somov\common\components\ProcessRunner;
use somov\common\exceptions\ProcessException;
use somov\common\process\LsProcess;


/**
 *
 * User: develop
 * Date: 02.07.2018
 */
class ProcessRunnerTest extends Test
{

    /**
     * @return array
     */
    public function getCommands()
    {
        return [
            'normal ls ' => [new LsProcess(['cwd' => __DIR__, 'blockSize' => '10M'])],
            'error ls' => [new LsProcess(['cwd' => 'not_exists']), ProcessException::class],
        ];
    }

    /**
     * @dataProvider  getCommands
     * @param $command
     * @param null $exception
     */
    public function testRun($command, $exception = null)
    {
        if (isset($exception)) {
            $this->expectException($exception);
        }

        $data = ProcessRunner::exec($command);
        $this->assertNotEmpty($data);

    }


}