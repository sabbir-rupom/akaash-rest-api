<?php

(defined('APP_NAME')) OR exit('Forbidden 403');

/**
 * A utility class that summarizes the processing related to Date..
 *
 */
class Common_DateUtil {

    /**
     * Insert the current time with DateTime type when storing it in MySQL.。
     *
     * @return String
     */
    public static function getToday($format = null) {

        if (is_null($format)) {
            $format = "Y-m-d H:i:s";
        }

        return date($format);
    }

    /**
     * Acquire after the specified date from the current date.。
     *
     * @param unknown_type $day
     */
    public static function getNowModify($ago, $format = null) {

        if (is_null($format)) {
            $format = "Y-m-d H:i:s";
        }

        $dateTime = new DateTime(self::getToday());
        $dateTime->modify($ago . " day");
        return $dateTime->format($format);
    }

    /**
     * Acquire after specified date from date.
     *
     * @param unknown_type $day
     */
    public static function getModify($day, $ago, $format = null) {

        if (is_null($format)) {
            $format = "Y-m-d H:i:s";
        }

        $dateTime = new DateTime($day);
        $dateTime->modify($ago . " day");
        return $dateTime->format($format);
    }

    /**
     * Acquire the specified month after the date.
     *
     * @param unknown_type $day
     */
    public static function getMonthModify($day, $ago, $format = null) {

        if (is_null($format)) {
            $format = "Y-m-d H:i:s";
        }

        $dateTime = new DateTime($day);
        $dateTime->modify($ago . " month");
        return $dateTime->format($format);
    }

    /**
     * Get the specified year after the argument.
     *
     * @param unknown_type $day
     */
    public static function getModifyYear($day, $ago, $format = null) {

        if (is_null($format)) {
            $format = "Y-m-d H:i:s";
        }

        $dateTime = new DateTime($day);
        $dateTime->modify($ago . " year");
        return $dateTime->format($format);
    }

    /**
     * Acquire after the specified date and time from the date.
     *
     * @param unknown_type $day
     */
    public static function getModifyHour($day, $ago, $format = null) {

        if (is_null($format)) {
            $format = "Y-m-d H:i:s";
        }

        $dateTime = new DateTime($day);
        $dateTime->modify($ago . " hour");
        return $dateTime->format($format);
    }

    /**
     * Retrieve after specified date from date.
     *
     * @param unknown_type $day
     */
    public static function getModifySec($day, $ago, $format = null) {

        if (is_null($format)) {
            $format = "Y-m-d H:i:s";
        }

        $dateTime = new DateTime($day);
        $dateTime->modify($ago . " second");
        return $dateTime->format($format);
    }

    /**
     * Output date in specified format.
     *
     * @param unknown_type $date
     * @param unknown_type $format
     */
    public static function getFormatDate($date, $format = null) {

        $dateTime = new DateTime($date);
        return $dateTime->format($format);
    }

    /**
     * Get the difference seconds from two dates.
     * @param $toDate
     * @param $fromDate
     */
    public static function getSecDiff($toDate, $fromDate) {

        // Calculate UnixTime from date
        $fromDateTime = strtotime($fromDate);
        $toDateTime = strtotime($toDate);

        // Calculate the number of seconds of difference
        $intervalTime = $toDateTime - $fromDateTime;

        return $intervalTime;
    }

    /**
     * Get the number of days difference from two dates.
     * @param $toDate
     * @param $fromDate
     */
    public static function getDayDiff($toDate, $fromDate) {

        // Set the date separately
        $fromDateArray = explode("-", DateUtil::getFormatDate($fromDate, "Y-m-d"));
        $toDateArray = explode("-", DateUtil::getFormatDate($toDate, "Y-m-d"));

        // Calculate UnixTime from the arrayed date
        $fromDateTime = mktime(0, 0, 0, $fromDateArray[1], $fromDateArray[2], $fromDateArray[0]); // 1236265200
        $toDateTime = mktime(0, 0, 0, $toDateArray[1], $toDateArray[2], $toDateArray[0]); // 1236524400
        // Calculate the number of seconds of difference
        $intervalTime = $toDateTime - $fromDateTime;

        // Convert seconds to days (3600 seconds for 1 hour → 24 hours)
        $intervalDay = $intervalTime / 3600 / 24;

        return $intervalDay;
    }

    /**
     * Acquire the difference month from two dates
     * @param $toDate
     * @param $fromDate
     */
    public static function getMonthDiff($toDate, $fromDate) {

        $date1 = strtotime($toDate);
        $date2 = strtotime($fromDate);
        $month1 = date("Y", $date1) * 12 + date("m", $date1);
        $month2 = date("Y", $date2) * 12 + date("m", $date2);

        $diff = $month1 - $month2;

        return $diff;
    }

    /**
     * Display time in days, hours, minutes, seconds
     * Enter description here ...
     */
    public static function convertTimeTodate($time, $seek) {

        $tempTime = $time;
        $seek = array(
            'day' => 'Day',
            'time' => 'Time',
            'minute' => 'Minute',
            'sec' => 'Second'
        );
        $res = array();

        //Day
        if ($tempTime >= 86400) {
            $res['day'] = floor($tempTime / 86400);
            $tempTime = $tempTime % 86400;
        }
        //time
        if ($tempTime >= 3600) {
            $res['time'] = floor($tempTime / 3600);
            $tempTime = $tempTime % 3600;
        }
        //Minute
        if ($tempTime >= 60) {
            $res['minute'] = floor($tempTime / 60);
            $tempTime = $tempTime % 60;
        }
        //Seconds
        if ($tempTime <= 60 && $tempTime > 0) {
            $res['sec'] = $tempTime;
        }

        $resStr = '';
        foreach ($seek as $key => $val) {
            if (array_key_exists($key, $res)) {
                $resStr .= $res[$key] . $val;
            }
        }
        return $resStr;
    }

    /**
     * Data to be displayed on the application side is acquired "hh: mm" on the day of acquisition, "MM / DD" not on the day
     * @param $toDate
     * @param $fromDate
     */
    public static function getDisplayDate($date) {

        $today = self::getToday("Y-m-d");

        $displayDate = self::getFormatDate($date, "Y-m-d");

        if ($displayDate < $today) {
            //That day
            return self::getFormatDate($date, "m-d");
        } else {
            //Not on the day
            return self::getFormatDate($date, "H:i");
        }
    }

    /**
     * Specify the start and end dates and get sequential date arrays between them
     * @param $toDate
     * @param $fromDate
     */
    public static function getSequenceDate($toDate, $fromDate, $format = null) {

        // $format When not specified, display in YYYY-MM-DD format
        if (is_null($format)) {
            $format = 'Y-m-d';
        }

        $dates = array();

        // Store the date difference between $fromDate and $toDate in $dates
        $dates[] = $fromDate;
        for ($i = 1; $i <= self::getDayDiff($toDate, $fromDate); $i++) {
            $dates[] = self::getModify($fromDate, $i, $format);
        }
        return $dates;
    }

    /**
     * Returns the current Unix timestamp in microseconds.
     *
     */
    public static function getMicroTime() {
        return microtime(true);
    }

    /**
     * Returns the elapsed time from the specified time stamp in microseconds.
     *
     */
    public static function getElapsedTime($start) {
        return DateUtil::getMicroTime() - $start;
    }

    /**
     * Get week (From Sunday 0, 1, 2 ...)
     * @return string
     */
    public static function getDayOfWeek($date = null) {
        if (null == $date) {
            $date = self::getToday();
        }

        return date('w', strtotime($date));
    }

    public static function getConvertTimeZone($fromTimeZone, $toTimeZone, $dateTime) {

        $t = new DateTime($dateTime, new DateTimeZone($fromTimeZone));
        $t->setTimezone(new DateTimeZone($toTimeZone));
        $format = "Y-m-d H:i:s";
        return $t->format($format);
    }

}
