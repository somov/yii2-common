<?php
/**
 * Created by PhpStorm.
 * User: web
 * Date: 30.10.19
 * Time: 13:24
 */




use Codeception\PHPUnit\TestCase;
use somov\common\helpers\DateTimeHelper;

class DateTimeHelperTest extends TestCase
{
    public function testCompare()
    {

        $date = DateTimeHelper::firstWeekDate();

        //$this->assertTrue( DateTimeHelper::isWeekInRange( new \DateTime('2019-10-28'), new \DateTime('2019-10-30') ));
        $this->assertTrue(DateTimeHelper::isSameDay(new \DateTime('2019-10-01'), new \DateTime('2019-10-01 15:15')));
        $this->assertFalse(DateTimeHelper::isSameDay(new \DateTime('2019-11-02'), new \DateTime('2019-10-01 15:15')));
        $this->assertFalse(DateTimeHelper::isSameDay(new \DateTime('2018-11-02'), new \DateTime('2019-10-01 15:15')));
        $this->assertFalse(DateTimeHelper::isSameDay(new \DateTime('201-05-02'), new \DateTime('2019-10-01 15:15')));
        $this->assertTrue(DateTimeHelper::isCurrentDay(new DateTime()));

        $this->assertFalse(DateTimeHelper::isSameMonth(new \DateTime('2019-10-01'), new \DateTime('2019-09-30')));
        $this->assertTrue(DateTimeHelper::isSameMonth(new \DateTime('2019-10-01'), new \DateTime('2019-10-27')));
        $this->assertTrue(DateTimeHelper::isCurrentMonth(new \DateTime()));
        $this->assertFalse(DateTimeHelper::isCurrentMonth(new \DateTime('2019-09-30')));

        $this->assertTrue(DateTimeHelper::isSameWeek(new \DateTime('2019-10-21'), new \DateTime('2019-10-27')));
        $this->assertTrue(DateTimeHelper::isSameWeek(new \DateTime('2019-10-21'), new \DateTime('2019-10-27')));
        $this->assertFalse(DateTimeHelper::isSameWeek(new \DateTime('2019-10-21'), new \DateTime('2019-10-28')));

        $this->assertTrue(DateTimeHelper::isCurrentWeek(new \DateTime()));
        $this->assertFalse(DateTimeHelper::isCurrentWeek(new \DateTime('2019-08-30')));


        $this->assertTrue( DateTimeHelper::isMonthInRange( new \DateTime('2019-10-01'), new \DateTime('2019-10-31'), new \DateTime('2019-10-01') ) );
        $this->assertFalse( DateTimeHelper::isMonthInRange( new \DateTime('2019-10-01'), new \DateTime('2019-10-31'), new \DateTime('2019-09-01') ) );
        $this->assertFalse( DateTimeHelper::isMonthInRange( new \DateTime('2019-10-01'), new \DateTime('2018-10-30') ) );

        $this->assertFalse( DateTimeHelper::isWeekInRange( new \DateTime('2019-10-01'), new \DateTime('2019-10-30') )  );
        $this->assertFalse( DateTimeHelper::isWeekInRange( new \DateTime('2019-10-01'), new \DateTime('2019-10-30') ) );
        $this->assertTrue( DateTimeHelper::isWeekInRange( new \DateTime('2019-10-21'), new \DateTime('2019-10-27'), new \DateTime('2019-10-26') )  );

        $this->assertTrue(DateTimeHelper::isCurrentYear(new DateTime()));

        $this->assertTrue( DateTimeHelper::isYearInRange( new \DateTime('2019-01-01'), new \DateTime('2019-12-31'), new \DateTime('2019-10-26') )  );



    }

}
