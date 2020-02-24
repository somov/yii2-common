<?php
/**
 * Created by PhpStorm.
 * User: web
 * Date: 30.10.19
 * Time: 13:22
 */

namespace somov\common\helpers;


use DateTime;
use yii\base\InvalidConfigException;

class DateTimeHelper
{
    /**
     * @param bool $isStart
     * @return DateTime
     * @throws \Exception
     */
    public static function createDateTime($isStart = true)
    {
        if ($isStart) {
            return self::setStartTime(new DateTime());
        }

        return self::setEndTime(new DateTime());
    }

    /**
     * Дата\Время начала дня
     *
     * @param $date
     * @return DateTime DateTime
     */
    public static function beginDayDate(DateTime $date)
    {
        return self::setStartTime(clone $date);
    }

    /**
     * Дата/время последней минуты месяца от $date времени
     * @param DateTime $date
     * @return DateTime
     */
    public static function lastMonthDate(DateTime $date)
    {
        return self::setEndTime(clone $date->modify('last day of this month'));
    }

    /** Дата/время последней  минуты месяца от $date времени
     * @param DateTime $date
     * @return DateTime
     */
    public static function firstMonthDate(DateTime $date)
    {
        return self::setStartTime(clone $date->modify('first day of this month'));
    }

    /** Дата/время первой  минуты недели от $date времени
     * @param DateTime|null $date
     * @return DateTime
     * @throws \Exception
     */
    public static function firstWeekDate($date = null)
    {
        $date = (isset($date)) ? clone $date : self::createDateTime();
        return self::setStartTime($date->modify('this week'));
    }

    /** Дата/время последней  минуты недели от $date времени
     * @param DateTime|null $date
     * @return DateTime
     * @throws \Exception
     */
    public static function lastWeekDate($date = null)
    {
        return self::setEndTime(self::firstWeekDate($date)->modify('+6 days'));
    }

    /** Дата/время первой  минуты года от $date времени
     * @param DateTime|null $date
     * @return DateTime
     * @throws \Exception
     */
    public static function firstYearDate($date = null)
    {
        $date = (isset($date)) ? clone $date : self::createDateTime();
        return self::setStartTime($date->modify('first day of january this year'));
    }

    /** Дата/время последней  минуты года от $date времени
     * @param DateTime|null $date
     * @return DateTime
     * @throws \Exception
     */
    public static function lastYearDate($date = null)
    {
        return self::setEndTime(self::firstYearDate($date)->modify('last day of december this year'));
    }


    /** Дата/время окончания текущего месяца
     * @return DateTime
     */
    public static function lastDateCurrentMonth()
    {
        return self::lastMonthDate(self::createDateTime());
    }

    /** Дата/время  начала текущего месяца
     * @return DateTime
     */
    public static function fistDateCurrentMonth()
    {
        return self::firstMonthDate(self::createDateTime());
    }

    /** Дата/время  начала текущей недели
     * @return DateTime
     * @throws \Exception
     */
    public static function fistDateCurrentWeek()
    {
        return self::firstWeekDate(self::createDateTime());
    }

    /**
     * Дата/время  окончания  текущей недели
     * @return DateTime
     * @throws \Exception
     */
    public static function lastDateCurrentWeek()
    {
        return self::lastWeekDate(self::createDateTime());
    }

    /** Сравнивает дни  и з двух DateTime
     * @param $d1
     * @param $d2
     * @return bool
     */
    public static function isSameDay($d1, $d2)
    {
        $d1 = self::beginDayDate($d1);
        $d2 = self::beginDayDate($d2);

        return self::beginDayDate($d1) == self::beginDayDate($d2);
    }

    /**
     * Сравнивает недели  и з двух DateTime
     * @param DateTime $d1
     * @param DateTime $d2
     * @return bool
     * @throws \Exception
     */
    public static function isSameWeek(DateTime $d1, DateTime $d2)
    {
        $first = self::firstWeekDate($d1);
        $last = self::lastWeekDate($d1);

        return $d2 >= $first && $d2 <= $last;
    }

    /** Текущая ли день  в дате $date
     * @param DateTime $date
     * @return bool
     * @throws \Exception
     */
    public static function isCurrentDay(DateTime $date)
    {
        return self::isSameDay(new DateTime(), $date);
    }


    /** Текущая ли неделя  в дате $date
     * @param DateTime $date
     * @return bool
     * @throws \Exception
     */
    public static function isCurrentWeek(DateTime $date)
    {
        return self::isSameWeek(self::createDateTime(), $date);
    }


    /**
     * Текущий ли месяц в дате $date
     * /**
     * @param DateTime $date
     * @return bool
     */
    public static function isCurrentMonth(DateTime $date)
    {
        return self::isSameMonth(self::createDateTime(), $date);
    }


    /**
     * @param DateTime $d1
     * @param DateTime $d2
     * @return bool
     */
    public static function isSameMonth(DateTime $d1, DateTime $d2)
    {
        $last = self::lastMonthDate($d1);
        $first = self::firstMonthDate($d1);

        return $d2 >= $first && $d2 <= $last;
    }

    /**
     * @param DateTime $d1
     * @param DateTime $d2
     * @return bool
     */
    public static function isSameYear(DateTime $d1, DateTime $d2)
    {
        $last = self::lastYearDate($d1);
        $first = self::firstYearDate($d1);

        return $d2 >= $first && $d2 <= $last;
    }

    /**
     * @param $date
     * @return bool
     * @throws \Exception
     */
    public static function isCurrentYear($date)
    {
        return self::isSameYear(self::createDateTime(), $date);
    }


    /**
     * Сравнивает период DateTime с началом и концом месяца из $date
     * @param DateTime $start
     * @param DateTime $end
     * @param DateTime|null $date
     * @return bool
     */
    public static function isMonthInRange(DateTime $start, DateTime $end, $date = null)
    {
        $date = isset($date) ? $date : self::createDateTime();

        return $start->diff(self::firstMonthDate($date))->days === 0 && $end->diff(self::lastMonthDate($date))->days === 0;
    }

    /**
     * Сравнивает период DateTime с началом и концом  недели $date
     * @param DateTime $start
     * @param DateTime $end
     * @param null $date
     * @return bool
     * @throws \Exception
     */
    public static function isWeekInRange(DateTime $start, DateTime $end, $date = null)
    {
        $date = isset($date) ? $date : self::createDateTime();

        return $start->diff(self::firstWeekDate($date))->days === 0 && $end->diff(self::lastWeekDate($date))->days === 0;
    }

    /**
     * Сравнивает период DateTime с началом и концом года $date
     * @param DateTime $start
     * @param DateTime $end
     * @param null $date
     * @return bool
     * @throws \Exception
     */
    public static function isYearInRange(DateTime $start, DateTime $end, $date = null)
    {
        $date = isset($date) ? $date : self::createDateTime();

        return $start->diff(self::firstYearDate($date))->days === 0 && $end->diff(self::lastYearDate($date))->days === 0;
    }



    /**
     * @param DateTime $date
     * @return DateTime
     */
    protected static function setStartTime(DateTime $date)
    {
        return $date->setTime(0, 0, 0, 0);
    }

    /**
     * @param DateTime $date
     * @return DateTime|false
     */
    protected static function setEndTime(DateTime $date)
    {
        return $date->setTime(23, 59, 59, 0);
    }

}