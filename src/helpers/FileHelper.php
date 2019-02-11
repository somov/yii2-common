<?php
/**
 *
 * User: develop
 * Date: 22.01.2019
 */

namespace somov\common\helpers;


use yii\helpers\ArrayHelper;

class FileHelper extends \yii\helpers\FileHelper
{

    /** Синхронизация файлов каталогов
     * @param string $source
     * @param string $destination
     * @param array $options настройки поиска каталога источника [[FileHelper::findFiles]]
     * может быть указан атрибут delete, если false  запрещает  удалять файлы
     * @param callable $onProcess
     * @throws \Exception
     */
    public static function synchronizeDirectories($source, $destination, $options = [], $onProcess = null)
    {
        $source = \Yii::getAlias($source);
        $destination = \Yii::getAlias($destination);

        $deleteFiles = ArrayHelper::remove($options, 'delete', true);

        list($new, $updated, $deleted) = self::compareDirectories($source, $destination, $options);

        foreach (array_merge($new, $updated) as $file) {
            FileHelper::createDirectory(dirname($destination . $file));
            copy($source . $file, $destination . $file);
            if ($time = filemtime($source . $file)) {
                touch($destination . $file, $time);
            }

            if (isset($onProcess) && is_callable($onProcess)) {
                call_user_func($onProcess, 'Copy', $destination . $file);
            }
        }

        if ($deleteFiles) {
            foreach ($deleted as $file) {
                self::unlink($destination . $file);

                if (isset($onProcess) && is_callable($onProcess)) {
                    call_user_func($onProcess, 'Delete', $destination . $file);
                }
            }
            $dirs = self::findDirectories($destination);
            do {
                $done = true;
                foreach ($dirs as $index => $dir) {
                    if (!(new \FilesystemIterator($dir))->valid()) {
                        if (rmdir($dir)) {
                            unset($dirs[$index]);
                            $done = false;
                            if (isset($onProcess) && is_callable($onProcess)) {
                                call_user_func($onProcess, 'Delete empty directory', $dir);
                            }
                        }
                    }
                }
            } while (!$done);
        }
    }


    /**
     * Сравнение файлов в каталогах
     * @param string $source
     * @param string $destination
     * @param array $options настройки поиска каталога источника [[FileHelper::findFiles]]
     * @return array list [$new, $updated, $deleted] массива список новых, измененных, удаленных файлов
     */
    public static function compareDirectories($source, $destination, $options = [])
    {
        $source = \Yii::getAlias($source);
        $destination = \Yii::getAlias($destination);

        $sourceList = self::findFilesRelative($source, $options);
        $destinationList = is_dir($destination) ? self::findFilesRelative($destination, $options) : [];

        $updated = [];
        $compareTime = ArrayHelper::getValue($options, 'compareTime', true);

        foreach (array_intersect($sourceList, $destinationList) as $file) {
            if (filesize($source . $file) !== filesize($destination . $file)) {
                $updated[] = $file;
                continue;
            }
            if ($compareTime) {
                $ts = new \DateTime();
                $ts->setTimestamp(filemtime($source . $file));
                $ts->setTime($ts->format('H'), $ts->format('i'), 0);

                $td = new \DateTime();
                $td->setTimestamp(filemtime($destination . $file));
                $td->setTime($td->format('H'), $td->format('i'), 0);

                if ($ts != $td) {
                    $updated[] = $file;
                }
            }
        }

        return [
            array_diff($sourceList, $destinationList),
            $updated,
            array_diff($destinationList, $sourceList),
        ];
    }

    /** Поиск файлов в каталоге с обрезанием путей до относительных к поисковому каталогу
     * @param string $source
     * @param array $options
     * @return array
     */
    protected static function findFilesRelative($source, $options = [])
    {
        return array_map(function ($file) use ($source) {
            return str_replace($source, '', $file);
        }, self::findFiles($source, $options));
    }
}