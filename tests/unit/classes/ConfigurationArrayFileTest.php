<?php

use somov\common\classes\ConfigurationArrayFile;

/**
 *
 * User: develop
 * Date: 18.01.2019
 */
class ConfigurationArrayFileTest extends Codeception\TestCase\Test
{


    public function testRead()
    {
        $config = new ConfigurationArrayFile('@mtest/files/testConfig.php');
        $this->assertSame('bar', $config['foo']);
        $this->assertSame('bar', $config->foo);
    }

    public function testWrite()
    {
        $file = '@mtest/_output/testWrite.php';

        if (file_exists($file)) {
            unlink($file);
        }

        $config = new ConfigurationArrayFile($file);
        $config['test'] = 'testing';
        $config['foo'] = 'bar';

        $config->write();

         $r =  new ConfigurationArrayFile($file);
         $this->assertSameSize($r, $config);

    }
}