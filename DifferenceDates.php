<?php
/*
 * stdClass is the default PHP object.
 * stdClass has no properties, methods or parent.
 * It does not support magic methods, and implements no interfaces.
 * http://php.net/manual/en/language.oop5.basic.php#92123
 *
 * http://php.net/manual/en/reserved.classes.php
 * http://php.net/manual/ru/datetime.diff.php
 * http://krisjordan.com/dynamic-properties-in-php-with-stdclass
 * http://qaru.site/questions/3420/what-is-stdclass-in-php
 * http://qaru.site/questions/12475/how-to-calculate-the-difference-between-two-dates-using-php
 * */
class DifferenceDates
{
    const DAYS_IN_MONTH = [31, 28, 31, 30, 31, 30, 31, 31, 30, 31, 30, 31];
    const DAYS_IN_MONTH_LEAP = [31, 29, 31, 30, 31, 30, 31, 31, 30, 31, 30, 31];
    const MAX_MONTH = 12;// or count(self::DAYS_IN_MONTH) => 12;
    protected $dateFirst;
    protected $dateSecond;
    protected $yearsStart;
    protected $yearsEnd;
    protected $monthsStart;
    protected $monthsEnd;
    protected $daysStart;
    protected $daysEnd;
    protected $yearsBetween;
    protected $monthsBetween;
    protected $daysBetween;
    protected $totalDaysBetween;
    protected $result;
    protected $invert;
    protected $incorrectDaysCount;
    protected $equalZero;
    protected $checkMonthCount;
    protected $strErrorMsg = '<b>ERROR:</b> Dates format is not correct! <br>Try <i>YYYY-MM-DD</i>';
    //если необходимо можно измменить год{4} на любое кол-во символов
    private $strRegExp = "/^[0-9]{4}-[0-1][0-9]-[0-3][0-9]$/";//[0-9]{XXX}

    /**
     * DifferenceDates constructor.
     * @param $dateFirst
     * @param $dateSecond
     */
    public function __construct($dateFirst = false, $dateSecond = false)
    {
        $this->checkFormatDates($dateFirst, $dateSecond);
        $this->dateFirst = array_map('intval', explode("-",trim($dateFirst)));
        $this->dateSecond = array_map('intval', explode("-", trim($dateSecond)));
        $this->invert = $this->checkSmallerDate();
        $this->splitDates();
        $this->result = $this->calculateDifference();
        //$this->printResult(); // если делать extends stdClass, то в место __toString можнро использовать этот метод.
    }

    /**
     * @param $dateFirst
     * @param $dateSecond
     * @return bool
     */
    protected function checkFormatDates($dateFirst, $dateSecond)
    {
        if (preg_match($this->strRegExp, $dateFirst) && preg_match($this->strRegExp, $dateSecond)) return true;
        $this->terminateRun();
    }

    /**
     * @return bool
     */
    protected function checkSmallerDate()
    {
        return ($this->dateFirst[0] < $this->dateSecond[0] ||
            $this->dateFirst[0] === $this->dateSecond[0] && $this->dateFirst[1] < $this->dateSecond[1] ||
            $this->dateFirst[0] === $this->dateSecond[0] && $this->dateFirst[1] === $this->dateSecond[1] &&
            $this->dateFirst[2] < $this->dateSecond[2]) ? true : false;
    }

    /**
     * @return void
     */
    protected function invertDates()
    {
        $tmpDate = $this->dateFirst;
        $this->dateFirst = $this->dateSecond;
        $this->dateSecond = $tmpDate;
    }

    protected function splitDates()
    {
        if ($this->invert) {
            $this->invertDates();
        }
        $this->yearsStart = $this->dateFirst[0];
        $this->yearsEnd = $this->dateSecond[0];
        $this->monthsStart = strlen($this->dateFirst[1]) > 2 ?
            (int) substr($this->dateFirst[1], 0, 2) :
            (int) $this->dateFirst[1];
        $this->monthsEnd = strlen($this->dateSecond[1]) > 2 ?
            (int) substr($this->dateSecond[1], 0, 2) :
            (int) $this->dateSecond[1];
        $this->daysStart = strlen($this->dateFirst[2]) > 2 ?
            (int) substr($this->dateFirst[2], 0, 2) :
            (int) $this->dateFirst[2];
        $this->daysEnd = strlen($this->dateSecond[2]) > 2 ?
            (int) substr($this->dateSecond[2], 0, 2) :
            (int) $this->dateSecond[2];
        $this->validator();
    }

    protected function validator()
    {
        $this->checkEqualZero();//set $this->equalZero
        $this->checkMaxMonthCounts();//set $this->checkMonthCount
        $this->checkCorrectsDaysCount();// set $this->incorrectDaysCount
        // disable custom message
        /*
         if ($this->equalZero ||
            $this->checkMonthCount ||
            $this->incorrectDaysCount) $this->terminateRun();
        return true; // useless, you can remove this line.
        */
    }

    protected function checkMaxMonthCounts()
    {
        $this->checkMonthCount = $this->monthsStart > self::MAX_MONTH ||
                                $this->monthsEnd > self::MAX_MONTH ? true : false;
        // custom error message
        if ($this->checkMonthCount) $this->terminateRun("<b>ERROR:</b> QUANTITY MONTH CAN NOT BE MORE THAN <b>12</b>");
    }
    protected function checkEqualZero() {
        $this->equalZero = $this->monthsStart == 0 ||
                            $this->monthsEnd == 0 ||
                            $this->daysStart == 0 ||
                            $this->daysEnd == 0 ? true : false;
        // custom error message
        if ($this->equalZero) $this->terminateRun("<b>ERROR:</b> DAYS OR MONTH CAN NOT BE ZERO");
    }

    protected function checkCorrectsDaysCount()
    {
        $startDays = $this->getDayInMonth($this->monthsStart, $this->yearsStart);
        $endDays = $this->getDayInMonth($this->monthsEnd, $this->yearsEnd);
        $this->incorrectDaysCount = $startDays < $this->daysStart ||
                                    $endDays < $this->daysEnd ? true : false;
        // custom error message
        if ($this->incorrectDaysCount) $this->terminateRun("<b>ERROR:</b> YOU ENTERED INVALID NUMBER OF DAYS IN MONTH");
    }

    /**
     *
     * @return array
     */
    protected function calculateDifference()
    {
        $this->yearsBetween = $this->yearsStart - $this->yearsEnd;
        $this->monthsBetween = $this->monthsStart - $this->monthsEnd;
        $checkMonth = $this->monthsStart < $this->monthsEnd ? true : False;
        $checkDays = $this->daysStart < $this->daysEnd ? true : false;
        if ($checkMonth) {
            --$this->yearsBetween;
            $this->monthsBetween += 12;
        }
        if ($checkDays) {
            $tmpMonth = $this->monthsStart;
            $this->daysBetween = $this->daysStart + $this->getDayInMonth(--$tmpMonth, $this->yearsStart);
            // if (start_day + prev_month_days(28/29)) < end_day (30-31)
            if ($this->daysBetween > $this->daysEnd) {
                $this->daysBetween -= $this->daysEnd;
            } else $this->daysBetween += $this->getDayInMonth(--$tmpMonth, $this->yearsStart);
        } else $this->daysBetween = $this->daysStart - $this->daysEnd;

        echo "Разница в между двумя годами: " . $this->yearsBetween;
        echo "<br>";
        echo "Разница в месяцах: " . $this->monthsBetween;
        echo "<br>";
        echo "Разница в днях: " . $this->daysBetween;
        return [];
    }

    private function getDayInMonth($month, $year)
    {
        return $month == 2 ? ($year % 4 ? 28 : ($year % 100 ? 29 : ($year %400 ? 28 : 29))) : (($month - 1) % 7 % 2 ? 30 : 31);
    }

    private function isLeapYear($year)
    {
        return ((($year % 4) == 0) && ((($year % 100) != 0) || (($year % 400) == 0)));
    }

    protected function terminateRun($message = false) {
        exit($message ? $message : $this->strErrorMsg);
    }

    public function printResult() {

    }

}
$diff = new DifferenceDates("2016-02-01", "2017-02-28");
//$diff->printResult();