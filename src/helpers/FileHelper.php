<?php
/**
 *
 * User: develop
 * Date: 22.01.2019
 */

namespace somov\common\helpers;


class FileHelper extends \yii\helpers\FileHelper
{

    /** Синхронизация файлов каталогов
     * @param string $source
     * @param string $destination
     */
    public static function synchronizeDirectories($source, $destination)
    {
        $source = \Yii::getAlias($source);
        $destination = \Yii::getAlias($destination);

        list($new, $updated, $deleted) = self::compareDirectories($source, $destination);

        foreach ($deleted as $file) {
            self::unlink($destination . $file);
        }

        foreach (array_merge($new, $updated) as $file) {
            FileHelper::createDirectory(dirname($destination . $file));
            copy($source . $file, $destination . $file);
            if ($time = filemtime($source . $file)) {
                touch($destination . $file, $time);
            }
        }
    }


    /**
     * Сравнение файлов в каталогах
     * @param string $source
     * @param string $destination
     * @return array list [$new, $updated, $deleted] массива список новых, измененных, удаленных файлов
     */
    public static function compareDirectories($source, $destination)
    {
        $source = \Yii::getAlias($source);
        $destination = \Yii::getAlias($destination);

        $sourceList = self::findFilesRelative($source);
        $destinationList = is_dir($destination) ? self::findFilesRelative($destination) : [];

        $updated = [];
        foreach (array_intersect($sourceList, $destinationList) as $file) {
            if (filesize($source . $file) !== filesize($destination . $file)) {
                $updated[] = $file;
                continue;
            }

            if (filemtime($source . $file) !== filemtime($destination . $file)) {
                $updated[] = $file;
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
     * @param bool $recursive
     * @return array
     */
    protected static function findFilesRelative($source, $options = [], $recursive = true)
    {
        $options = array_merge($options, ['recursive' => $recursive]);

        return array_map(function ($file) use ($source) {
            return str_replace($source, '', $file);
        }, self::findFiles($source, $options));
    }
}