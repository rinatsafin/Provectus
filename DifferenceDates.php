<?php
/*
 *
 * Реализовал механизм: > Подсчитать разницу между 2-мя входными датами без использования любых PHP-функций
 * Для проверки верности расчетов можно воспользоваться онлайн сервисом:
 * https://planetcalc.ru/273/?license=1
 * Естественно можно сделать обработку полученных данных с формы, $_GET / $_POST, Ajax,
 * но это тестовое задание и я думаю, что тратить на это время не совсем целесообразно.
 *
 * stdClass is the default PHP object.
 * stdClass has no properties, methods or parent.
 * It does not support magic methods, and implements no interfaces.
 * http://php.net/manual/en/language.oop5.basic.php#92123
 *
 * stdClass Создается приведением типа к объекту.
 * http://php.net/manual/ru/language.types.object.php#language.types.object.casting
 * Если object преобразуется в object, он не изменяется.
 * Если значение другого типа преобразуется в object, создается новый экземпляр встроенного класса stdClass.
 * Если значение было NULL, новый экземпляр будет пустым. Массивы преобразуются в object с именами полей,
 * названными согласно ключам массива и соответствующими им значениям, за исключением числовых ключей,
 * которые не будут доступны пока не проитерировать объект.
 * частично используемый материал
 * http://php.net/manual/en/reserved.classes.php
 * http://php.net/manual/ru/datetime.diff.php
 * http://krisjordan.com/dynamic-properties-in-php-with-stdclass
 * http://qaru.site/questions/3420/what-is-stdclass-in-php
 * http://qaru.site/questions/12475/how-to-calculate-the-difference-between-two-dates-using-php
 *
 * */
class DifferenceDates
{
    const DAYS_IN_MONTH = [31, 28, 31, 30, 31, 30, 31, 31, 30, 31, 30, 31];
    const DAYS_IN_MONTH_LEAP = [31, 29, 31, 30, 31, 30, 31, 31, 30, 31, 30, 31];
    const MAX_MONTHS_COUNT = 12;// or count(self::DAYS_IN_MONTH) => 12;
    protected $dateStart;
    protected $dateEnd;
    protected $yearsStart;
    protected $yearsEnd;
    protected $monthsStart;
    protected $monthsEnd;
    protected $dayStart;
    protected $dayEnd;
    protected $yearsBetween = 0;
    protected $monthsBetween = 0;
    protected $daysBetween = 0;
    protected $totalDaysBetween = 0;
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
        if ($dateFirst === $dateSecond) $this->terminateRun("Notice: Dates are completely identical, no difference date: 0 Years 0 Months 0 Day.");
        $this->dateStart = array_map('intval', explode("-",trim($dateFirst)));
        $this->dateEnd = array_map('intval', explode("-", trim($dateSecond)));
        $this->invert = $this->checkSmallerDate();
        $this->splitDates();
        $this->result = $this->calculateDifference();
        $this->showResult($this->result); // если делать extends stdClass, то в место __toString нужно использовать этот метод.
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
     * array index: [0] - years / [1] - month / [2] - days
     * @return bool
     */
    protected function checkSmallerDate()
    {
        return ($this->dateStart[0] > $this->dateEnd[0] ||
            $this->dateStart[0] === $this->dateEnd[0] && $this->dateStart[1] > $this->dateEnd[1] ||
            $this->dateStart[0] === $this->dateEnd[0] && $this->dateStart[1] === $this->dateEnd[1] &&
            $this->dateStart[2] > $this->dateEnd[2]) ? true : false;
    }

    protected function invertDates()
    {
        $tmpDate = $this->dateStart;
        $this->dateStart = $this->dateEnd;
        $this->dateEnd = $tmpDate;
    }

    protected function splitDates()
    {
        if ($this->invert) $this->invertDates();
        $this->yearsStart = $this->dateStart[0];
        $this->yearsEnd = $this->dateEnd[0];
        $this->monthsStart = strlen($this->dateStart[1]) > 2 ?
            (int) substr($this->dateStart[1], 0, 2) :
            (int) $this->dateStart[1];
        $this->monthsEnd = strlen($this->dateEnd[1]) > 2 ?
            (int) substr($this->dateEnd[1], 0, 2) :
            (int) $this->dateEnd[1];
        $this->dayStart = strlen($this->dateStart[2]) > 2 ?
            (int) substr($this->dateStart[2], 0, 2) :
            (int) $this->dateStart[2];
        $this->dayEnd = strlen($this->dateEnd[2]) > 2 ?
            (int) substr($this->dateEnd[2], 0, 2) :
            (int) $this->dateEnd[2];
        $this->datesVerification();
    }

    protected function datesVerification()
    {
        $this->checkEqualZero();
        $this->allowedMonthCount();
        $this->checkCorrectsDaysCount();
        // disable custom message
        /*
         if ($this->equalZero ||
            $this->checkMonthCount ||
            $this->incorrectDaysCount) $this->terminateRun();
        return true; // useless, you can remove this line.
        */
    }

    protected function allowedMonthCount()
    {
        $this->checkMonthCount = $this->monthsStart > self::MAX_MONTHS_COUNT ||
                                $this->monthsEnd > self::MAX_MONTHS_COUNT ? true : false;
        if ($this->checkMonthCount) $this->terminateRun("<b>ERROR:</b> Quantity month can not be more than <b>12</b>");
    }

    protected function checkEqualZero() {
        $this->equalZero = $this->monthsStart == 0 ||
                            $this->monthsEnd == 0 ||
                            $this->dayStart == 0 ||
                            $this->dayEnd == 0 ? true : false;
        if ($this->equalZero) $this->terminateRun("<b>ERROR:</b> Days or month can not be zero");
    }

    protected function checkCorrectsDaysCount()
    {
        $startDays = $this->getDaysPerMonth($this->monthsStart, $this->yearsStart);
        $endDays = $this->getDaysPerMonth($this->monthsEnd, $this->yearsEnd);
        $this->incorrectDaysCount = $startDays < $this->dayStart ||
                                    $endDays < $this->dayEnd ? true : false;
        if ($this->incorrectDaysCount) $this->terminateRun("<b>ERROR:</b> You entered invalid number of days in the month");
    }

    public function checkFullYear()
    {
        return $this->dayStart <= $this->dayEnd && $this->monthsStart <= $this->monthsEnd &&
            $this->yearsStart !== $this->yearsEnd ||
            $this->dayStart >= $this->dayEnd &&
            $this->monthsStart < $this->monthsEnd && $this->yearsStart < $this->yearsEnd;
    }
    /**
     *
     * @return array
     */
    protected function calculateDifference()
    {
        $checkFullYear = $this->checkFullYear();
        $this->yearsBetween = $this->yearsEnd - $this->yearsStart;
        if (!$checkFullYear) --$this->yearsBetween;
        $bufferDays = $this->getDaysPerMonth($this->monthsStart, $this->yearsStart);
        $this->totalDaysBetween = $bufferDays - $this->dayStart + $this->dayEnd;
        $checkDays = $this->dayStart <= $this->dayEnd;
        $checkMonth = $this->monthsStart > $this->monthsEnd;
        $this->monthsBetween = $checkMonth ?
            self::MAX_MONTHS_COUNT - $this->monthsStart + $this->monthsEnd :
            $this->monthsEnd - $this->monthsStart;
        if (!$checkDays) {
            $this->daysBetween = $this->totalDaysBetween;
            --$this->monthsBetween;
            if ($this->monthsBetween < 0) {
                --$this->yearsBetween;
                $this->monthsBetween += self::MAX_MONTHS_COUNT;
            }
        } else $this->daysBetween = $this->dayEnd - $this->dayStart;
        if ($this->monthsBetween >= self::MAX_MONTHS_COUNT) {
            $this->monthsBetween -= self::MAX_MONTHS_COUNT;
            ++$this->yearsBetween;
        }
        $totalYears = $this->yearsBetween;
        while ($totalYears > 0) {
            $checkLeap = $this->isLeapYear($this->yearsStart + $totalYears--);
            $this->totalDaysBetween += $checkLeap ? 366 : 365;
        }
        return [
            "years" => $this->yearsBetween,
            "months" => $this->monthsBetween,
            "days" => $this->daysBetween,
            "totalDays" => $this->totalDaysBetween
        ];
    }

    private function getDaysPerMonth($month, $year)
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

    public function showResult($result)
    {
        $years = sprintf("%d" . (($result["years"] == 1) ? ' year' : ' years'), $result["years"]);
        $months = sprintf("%d" . (($result["months"] == 1) ? ' month' : ' months'), $result["months"]);
        $days = sprintf("%d" . (($result["days"] == 1) ? ' day' : ' days'), $result["days"]);
        printf("Inverting dates is %s <br/>", $this->invert ? "true" : "false");
        echo "Difference between dates " . $years . " " . $months . " " . $days . "<br/>";
        echo "Total days count between dates " . $result["totalDays"] . "<br/>";
        echo "From check result click to link: <a target='_blank' href='https://planetcalc.ru/273/?license=1'>diff dates online</a>";
    }

}
$diff = new DifferenceDates("2000-02-01", "2001-03-31");

// реальный рабочий пример подобной реализации занял бы несколько строчек,
// поэтому не вижу смысла особо в проделанной работе))
//http://php.net/manual/ru/datetime.diff.php
//$datetime1 = new DateTime('2009-10-11');
//$datetime2 = new DateTime('2009-10-13');
//$interval = $datetime1->diff($datetime2);
//echo $interval->format('%R%a дней');