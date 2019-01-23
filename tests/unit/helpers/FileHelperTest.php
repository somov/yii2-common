<?php

use somov\common\helpers\FileHelper;

/**
 *
 * User: develop
 * Date: 22.01.2019
 */
class FileHelperTest extends Codeception\TestCase\Test
{


    public function testCompareDirectoriesUpdatedTime()
    {
        list(, $updated) = FileHelper::compareDirectories('@mtest/files/compare/updated_time/src',
            '@mtest/files/compare/updated_time/dst');
        $this->assertSame(reset($updated), '/first.txt');
    }


    public function getSyncIndexProvider()
    {
        return [
            '1' => [ 1],
            '2' => [ 2],
            '3' => [ 3],
            '4' => [ 4]
        ];
    }

    /**
     * @dataProvider getSyncIndexProvider
     * @param integer $testIndex
     */
    public function testSynchronizeDirectories($testIndex)
    {

        $source = Yii::getAlias("@mtest/files/sync/$testIndex");
        $testSource = Yii::getAlias("@mtest/files/sync/$testIndex-test");

        FileHelper::removeDirectory($testSource);
        FileHelper::copyDirectory($source, $testSource);
        FileHelper::synchronizeDirectories("$testSource/src", "$testSource/dst");

        $r = FileHelper::compareDirectories("$testSource/src", "$testSource/dst");
        $this->assertEmpty(array_filter($r));


    }


    public function testCompareDirectoriesDeleted()
    {
        list(, , $deleted) = FileHelper::compareDirectories(
            '@mtest/files/compare/deleted/src',
            '@mtest/files/compare/deleted/dst');
        $this->assertSame(reset($deleted), '/first.txt');
    }

    public function testCompareDirectoriesUpdatedSize()
    {
        list(, $updated) = FileHelper::compareDirectories('@mtest/files/compare/updated_size/src',
            '@mtest/files/compare/updated_size/dst');
        $this->assertSame(reset($updated), '/first.txt');
    }


    public function testCompareDirectoriesNew()
    {
        list($new) = FileHelper::compareDirectories('@mtest/files/compare/new/src', '@mtest/files/compare/new/dst');
        $this->assertSame(reset($new), '/second.txt');
    }

    public function testCompareDirectoriesSame()
    {
        $r = FileHelper::compareDirectories('@mtest/files/compare/same/src', '@mtest/files/compare/same/dst');
        $this->assertEmpty(array_filter($r));
    }


}