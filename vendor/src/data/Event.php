<?php

class Event extends DatabaseDocument
{

    public const TABLE_NAME = "Events";

    /** @var int */
    public $ID;

    /** @var int */
    public $DateBegin;

    /** @var int */
    public $DateEnd;

    /** Translation token @var string */
    public $TitleText;

    /** @var string */
    public $ArticleUniqueName;

    /** @var int */
    public $Color;

    public static function createTable(): DatabaseTable
    {
        return DatabaseTable::create(Event::TABLE_NAME)->add(
            DatabaseField::createDatetime("DateBegin"),
            DatabaseField::createDatetime("DateEnd"),
            DatabaseField::createString("TitleText", 64),
            DatabaseField::createString("ArticleUniqueName", 32),
            DatabaseField::createInteger("Color")
        );
    }

    public static function computeStartDate(int $month, int $year)
    {
        $time = mktime(0, 0, 0, $month, 1, $year);

        $day = Utils::translateUTCWeekdayToEuropeWeekday(intval(date("w", $time)));

        $time = mktime(0, 0, 0, $month, 1 - $day, $year);

        return $time;
    }   

    public static function computeEndDate(int $month, int $year)
    {
        // 6 * 7 = six weeks (7 days)
        // -1 = we need to subtract one day otherwise we would end at the first "date" of the next month
        // 24 = each day has 24 hours
        // 60 = each hour has 60 minutes
        // 60 = each minute has 60 seconds
        $toAdd = (6 * 7 - 1) * 24 * 60 * 60;
        $time = Event::computeStartDate($month, $year) + $toAdd;

        return $time;
    }

    public static function computeTimestempArray(int $month, int $year)
    {
        $time = Event::computeStartDate($month, $year);

        $ret = [];

        for($week = 0; $week < 6; $week++)
        {
            $weekArr = [];

            for ($day = 0; $day < 7; $day++) 
            { 
                $add = ($week * 7 + $day) * 24 * 60 * 60;
                $weekArr[] = $time + $add; 
            }

            $ret[] = $weekArr;
        }

        return $ret;
    }

}