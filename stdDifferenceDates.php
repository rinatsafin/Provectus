<?php

class stdDifferenceDates
{
    /**
     * diff from datetime
     * @param datetime $dt1
     * @param datetime $dt2
     * @return object $dtd (day, hour, min, sec / total)
     */
    static function datetimeDiff($dt1, $dt2){
        $t1 = strtotime($dt1);
        $t2 = strtotime($dt2);

        $dtd = new stdClass();
        $dtd->interval = $t2 - $t1;
        $dtd->total_sec = abs($t2-$t1);
        $dtd->total_min = floor($dtd->total_sec/60);
        $dtd->total_hour = floor($dtd->total_min/60);
        $dtd->total_day = floor($dtd->total_hour/24);

        $dtd->day = $dtd->total_day;
        $dtd->hour = $dtd->total_hour -($dtd->total_day*24);
        $dtd->min = $dtd->total_min -($dtd->total_hour*60);
        $dtd->sec = $dtd->total_sec -($dtd->total_min*60);
        return $dtd;
    }
}
/*
 * Here is how I solved the problem of missing date_diff function with php versions below 5.3.0
 * The function accepts two dates in string format (recognized by strtotime() hopefully),
 * and returns the date difference in an array with the years as first element,
 * respectively months as second, and days as last element.
 * It should be working in all cases, and seems to behave properly when moving through February.
 * */
function dateDifference($startDate, $endDate)
{
    $startDate = strtotime($startDate);
    $endDate = strtotime($endDate);
    if ($startDate === false || $startDate < 0 || $endDate === false || $endDate < 0 || $startDate > $endDate)
        return false;

    $years = date('Y', $endDate) - date('Y', $startDate);

    $endMonth = date('m', $endDate);
    $startMonth = date('m', $startDate);

    // Calculate months
    $months = $endMonth - $startMonth;
    if ($months <= 0)  {
        $months += 12;
        $years--;
    }
    if ($years < 0)
        return false;

    // Calculate the days
    $offsets = array();
    if ($years > 0)
        $offsets[] = $years . (($years == 1) ? ' year' : ' years');
    if ($months > 0)
        $offsets[] = $months . (($months == 1) ? ' month' : ' months');
    $offsets = count($offsets) > 0 ? '+' . implode(' ', $offsets) : 'now';

    $days = $endDate - strtotime($offsets, $startDate);
    $days = date('z', $days);

    return array($years, $months, $days);
}

/*
 * This is a very simple function to calculate the difference between two datetime values,
 * returning the result in seconds. To convert to minutes, just divide the result by 60.
 * In hours, by 3600 and so on.
 * */
function time_diff($dt1,$dt2){
    $y1 = substr($dt1,0,4);
    $m1 = substr($dt1,5,2);
    $d1 = substr($dt1,8,2);
    $h1 = substr($dt1,11,2);
    $i1 = substr($dt1,14,2);
    $s1 = substr($dt1,17,2);

    $y2 = substr($dt2,0,4);
    $m2 = substr($dt2,5,2);
    $d2 = substr($dt2,8,2);
    $h2 = substr($dt2,11,2);
    $i2 = substr($dt2,14,2);
    $s2 = substr($dt2,17,2);

    $r1=date('U',mktime($h1,$i1,$s1,$m1,$d1,$y1));
    $r2=date('U',mktime($h2,$i2,$s2,$m2,$d2,$y2));
    return ($r1-$r2);

}
/*
 * Even the top rated comment here, Sergio Abreu's, doesn't treat leap years entirely correctly.
 * It should work between 1901 and 2099, but outside that it'll be a little off.
 * If you want to find out the number of days between two dates, use below.
 * You can change to a different unit from that. It looks a little insane,
 * but keep in mind the full set of rules for leap years:
 * If the year is divisible by 4, it's a leap year...
 * - unless the year is divisible by 100, then it isn't...
 * - - unless the year is divisible by 400, then it really is.
 * So in the functions below, we find the total numbers of days in full years since the mythical 1/1/0001,
 * then add the number of days before the current one in the year passed. Do this for each date,
 * then return the absolute value of the difference.
*/
function days_diff($d1, $d2) {
    $x1 = days($d1);
    $x2 = days($d2);

    if ($x1 && $x2) {
        return abs($x1 - $x2);
    }
}

function days($x) {
    if (get_class($x) != 'DateTime') {
        return false;
    }

    $y = $x->format('Y') - 1;
    $days = $y * 365;
    $z = (int)($y / 4);
    $days += $z;
    $z = (int)($y / 100);
    $days -= $z;
    $z = (int)($y / 400);
    $days += $z;
    $days += $x->format('z');

    return $days;
}