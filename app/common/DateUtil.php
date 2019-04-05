<?php

/**
 * A RESTful API template in PHP based on flight micro-framework.
 *
 * ANYONE IN THE DEVELOPER COMMUNITY MAY USE THIS PROJECT FREELY
 * FOR THEIR OWN DEVELOPMENT SELF-LEARNING OR DEVELOPMENT or LIVE PROJECT
 *
 * @author      Sabbir Hossain Rupom <sabbir.hossain.rupom@hotmail.com>
 * @license	http://www.opensource.org/licenses/mit-license.php ( MIT License )
 *
 * @since       Version 1.0.0
 */
(defined('APP_NAME')) or exit('Forbidden 403');

/**
 * A utility class that summarizes the processing related to Date..
 */
class Common_DateUtil {
    /**
     * Insert the current time with DateTime type when storing it in MySQL.。
     *
     * @param string $format [optional]
     *
     * @return string Formatted date
     */
    public static function getToday($format = null) {
        if (is_null($format)) {
            $format = 'Y-m-d H:i:s';
        }

        return date($format);
    }

    /**
     * Acquire after the specified date from the current date.。
     *
     * @param unknown_type $day
     * @param mixed        $ago
     * @param null|mixed   $format
     */
    public static function getNowModify($ago, $format = null) {
        if (is_null($format)) {
            $format = 'Y-m-d H:i:s';
        }

        $dateTime = new DateTime(self::getToday());
        $dateTime->modify($ago.' day');

        return $dateTime->format($format);
    }

    /**
     * Acquire after specified date from date.
     *
     * @param unknown_type $day
     * @param mixed        $ago
     * @param null|mixed   $format
     */
    public static function getModify($day, $ago, $format = null) {
        if (is_null($format)) {
            $format = 'Y-m-d H:i:s';
        }

        $dateTime = new DateTime($day);
        $dateTime->modify($ago.' day');

        return $dateTime->format($format);
    }

    /**
     * Acquire the specified month after the date.
     *
     * @param unknown_type $day
     * @param mixed        $ago
     * @param null|mixed   $format
     */
    public static function getMonthModify($day, $ago, $format = null) {
        if (is_null($format)) {
            $format = 'Y-m-d H:i:s';
        }

        $dateTime = new DateTime($day);
        $dateTime->modify($ago.' month');

        return $dateTime->format($format);
    }

    /**
     * Get the specified year after the argument.
     *
     * @param unknown_type $day
     * @param mixed        $ago
     * @param null|mixed   $format
     */
    public static function getModifyYear($day, $ago, $format = null) {
        if (is_null($format)) {
            $format = 'Y-m-d H:i:s';
        }

        $dateTime = new DateTime($day);
        $dateTime->modify($ago.' year');

        return $dateTime->format($format);
    }

    /**
     * Acquire after the specified date and time from the date.
     *
     * @param unknown_type $day
     * @param mixed        $ago
     * @param null|mixed   $format
     */
    public static function getModifyHour($day, $ago, $format = null) {
        if (is_null($format)) {
            $format = 'Y-m-d H:i:s';
        }

        $dateTime = new DateTime($day);
        $dateTime->modify($ago.' hour');

        return $dateTime->format($format);
    }

    /**
     * Retrieve after specified date from date.
     *
     * @param unknown_type $day
     * @param mixed        $ago
     * @param null|mixed   $format
     */
    public static function getModifySec($day, $ago, $format = null) {
        if (is_null($format)) {
            $format = 'Y-m-d H:i:s';
        }

        $dateTime = new DateTime($day);
        $dateTime->modify($ago.' second');

        return $dateTime->format($format);
    }

    /**
     * Output date in specified format.
     *
     * @param string       $date
     * @param unknown_type $format Formatted date
     */
    public static function getFormatDate($date, $format = null) {
        $dateTime = new DateTime($date);

        return $dateTime->format($format);
    }

    /**
     * Get the difference in seconds from two dates.
     *
     * @param string $toDate
     * @param string $fromDate
     *
     * @return int $intervalTime
     */
    public static function getSecDiff($toDate, $fromDate) {
        // Calculate UnixTime from date
        $fromDateTime = strtotime($fromDate);
        $toDateTime = strtotime($toDate);

        // Calculate the number of seconds of difference
        return $toDateTime - $fromDateTime;
    }

    /**
     * Get the number of days difference between two dates.
     *
     * @param string $toDate
     * @param string $fromDate
     *
     * @return int $intervalDay
     */
    public static function getDayDiff($toDate, $fromDate) {
        // Set the date separately
        $fromDateArray = explode('-', self::getFormatDate($fromDate, 'Y-m-d'));
        $toDateArray = explode('-', self::getFormatDate($toDate, 'Y-m-d'));

        // Calculate UnixTime from the arrayed date
        $fromDateTime = mktime(0, 0, 0, $fromDateArray[1], $fromDateArray[2], $fromDateArray[0]); // 1236265200
        $toDateTime = mktime(0, 0, 0, $toDateArray[1], $toDateArray[2], $toDateArray[0]); // 1236524400
        // Calculate the number of seconds of difference
        $intervalTime = $toDateTime - $fromDateTime;

        // Convert seconds to days (3600 seconds for 1 hour → 24 hours)
        return $intervalTime / 3600 / 24;
    }

    /**
     * Get the difference in month between two dates.
     *
     * @param string $toDate
     * @param string $fromDate
     *
     * @return int $diff
     */
    public static function getMonthDiff($toDate, $fromDate) {
        $date1 = strtotime($toDate);
        $date2 = strtotime($fromDate);
        $month1 = date('Y', $date1) * 12 + date('m', $date1);
        $month2 = date('Y', $date2) * 12 + date('m', $date2);

        return $month1 - $month2;
    }

    /**
     * Display time in days, hours, minutes, seconds.
     *
     * @param int $time (in seconds)
     *
     * @return array $diff
     */
    public static function convertTimeTodate($time) {
        $tempTime = $time;
        $seek = array(
            'day' => 'Day',
            'hour' => 'Hour',
            'minute' => 'Minute',
            'sec' => 'Second',
        );
        $res = array();

        //Day
        if ($tempTime >= 86400) {
            $res['day'] = floor($tempTime / 86400);
            $tempTime = $tempTime % 86400;
        }
        //time
        if ($tempTime >= 3600) {
            $res['hour'] = floor($tempTime / 3600);
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
                $resStr .= '' !== $resStr ? ' '.$res[$key].$val : $res[$key].$val;
            }
        }

        return $resStr;
    }

    /*
     * Return time difference between two dates
     * [NOTE] Date Should In YYYY-MM-DD [H:i:s] Format
     * RESULT FORMAT: From 2018-01-01 12:30:15 to 2019-03-02
     * '%y Year %m Month %d Day %h Hours %i Minute %s Seconds'  =>  1 Year 2 Month 1 Day 12 Hours 30 Minute 15 Seconds
     * '%y Year %m Month %d Day'                                =>  1 Year 2 Month 1 Day
     * '%m Month %d Day'                                        =>  2 Month 1 Day
     * '%d Day %h Hours'                                        =>  1 Day 12 Hours
     * '%d Day'                                                 =>  1 Day
     * '%h Hours %i Minute %s Seconds'                          =>  12 Hours 30 Minute 15 Seconds
     * '%i Minute %s Seconds'                                   =>  30 Minute 15 Seconds
     * '%h Hours                                                =>  12 Hours
     * '%a Days                                                 =>  425 Days
     *
     * @param string $toDate End date/datetime
     * @param string $fromDate Start date/datetime
     * @return unknown_type $differenceFormat
     */
    public static function dateDifference(
        $toDate,
        $fromDate,
        $differenceFormat = '%a'
    ) {
        $datetime1 = date_create($toDate);
        $datetime2 = date_create($fromDate);

        $interval = date_diff($datetime1, $datetime2);

        return $interval->format($differenceFormat);
    }

    /**
     * Data to be displayed on the application side is acquired "hh: mm" on the day of acquisition, "MM / DD" not on the day.
     *
     * @param string $date
     *
     * @return string formatted date
     */
    public static function getDisplayDate($date) {
        $today = self::getToday('Y-m-d');

        $displayDate = self::getFormatDate($date, 'Y-m-d');

        if ($displayDate < $today) {
            //That day
            return self::getFormatDate($date, 'm-d');
        }
        //Not on the day
        return self::getFormatDate($date, 'H:i');
    }

    /**
     * Specify the start and end dates and get sequential date arrays between them.
     *
     * @param $toDate
     * @param $fromDate
     * @param $format [optional]
     */
    public static function getSequenceDate($toDate, $fromDate, $format = null) {
        // $format When not specified, display in YYYY-MM-DD format
        if (is_null($format)) {
            $format = 'Y-m-d';
        }

        $dates = array();

        // Store the date difference between $fromDate and $toDate in $dates
        $dates[] = $fromDate;
        for ($i = 1; $i <= self::getDayDiff($toDate, $fromDate); ++$i) {
            $dates[] = self::getModify($fromDate, $i, $format);
        }

        return $dates;
    }

    /**
     * Returns the current Unix timestamp in microseconds.
     */
    public static function getMicroTime() {
        return microtime(true);
    }

    /**
     * Returns server Timezone.
     */
    public static function getServerTimeZone() {
        return date_default_timezone_get();
    }

    /**
     * Returns the elapsed time from the specified time stamp in microseconds.
     *
     * @param mixed $start
     */
    public static function getElapsedTime($start) {
        return DateUtil::getMicroTime() - $start;
    }

    /**
     * Get week number of specific month (From Sunday 0, 1, 2 ...).
     *
     * @param null|mixed $date
     *
     * @return string Week
     */
    public static function getDayOfWeek($date = null) {
        if (null == $date) {
            $date = self::getToday();
        }

        return date('w', strtotime($date));
    }

    /*
     * Convert datetime from one timezone to another timezone
     *
     * @param $fromTimeZone initial timezone
     * @param $toTimeZone new timezone
     * @param $dateTime datetime to be converted
     * @return string datetime
     */

    public static function getConvertTimeZone($fromTimeZone, $toTimeZone, $dateTime) {
        $t = new DateTime($dateTime, new DateTimeZone($fromTimeZone));
        $t->setTimezone(new DateTimeZone($toTimeZone));
        $format = 'Y-m-d H:i:s';

        return $t->format($format);
    }

    /**
     * The time string converted to a timestamp (in seconds).
     *
     * @param string $str DB time character string
     *
     * @return int Unix timestamp (in seconds)
     */
    public static function strToTime($str) {
        return strtotime($str);
    }

    /**
     * Conversion time stamp (in seconds) to datetime specific.
     *
     * @param int $time Unix timestamp
     *
     * @return DB registration for the time string
     */
    public static function timeToStr($time) {
        return date('Y-m-d H:i:s', $time);
    }

    /**
     * Convert time stamp (sec) to database registration date string.
     *
     * @param int $time Unix timestamp
     *
     * @return Datetime for DB registration
     */
    public static function timeToDateStr($time) {
        return strftime('%Y-%m-%d', $time);
    }

    /**
     * Check if the two specified time stamps (in seconds) are the same day.
     *
     * @param int $time1 Unix timestamp
     * @param int $time2 Unix timestamp
     *
     * @return bool Check result
     */
    public static function isSameDay($time1, $time2) {
        return self::timeToDateStr($time1) == self::timeToDateStr($time2);
    }
}
