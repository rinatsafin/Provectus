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
 * */
class DifferenceDates
{
    public $dateFirst;
    public $dateSecond;
    public $yearsStart;
    public $yearsEnd;
    public $monthsStart;
    public $monthsEnd;
    public $daysStart;
    public $daysEnd;
    protected $yearsBetween;
    protected $monthsBetween;
    protected $daysBetween;
    protected $totalDaysBetween;
    protected $result;
    private $invert = false;
    private $strErrorMsg = '<b>ERROR:</b> Dates format is not correct! <br>Try <i>YYYY-MM-DD</i>';
    //если необходимо можно измменить год на любое кол-во символов
    private $strRegExp = "/^[0-9]{4}-[0-1][0-9]-[0-3][0-9]$/";//[0-9]{XXX}
    /**
     * DifferenceDates constructor.
     * @param $dateFirst
     * @param $dateSecond
     */
    public function __construct($dateFirst = false, $dateSecond = false)
    {
        if (!$this->checkDates($dateFirst, $dateSecond)) exit($this->strErrorMsg);
        $this->dateFirst = explode("-",trim($dateFirst));
        $this->dateSecond = explode("-", trim($dateSecond));
        $this->invert = $this->checkSmallYear($dateFirst, $dateSecond);
        $this->result = $this->calculateDifference();
        //$this->getResult(); // если делать extends stdClass, то в место __toString можнро использовать этот метод.
    }

    /**
     * @param $dateFirst
     * @param $dateSecond
     * @return bool
     */
    private function checkDates($dateFirst, $dateSecond) {
        if (preg_match($this->strRegExp, $dateFirst) && preg_match($this->strRegExp, $dateSecond)) return true;
        return false;
    }

    /**
     * @param $dateFirst
     * @param $dateSecond
     * @return bool
     * @throws Exception
     */
    public function checkSmallYear($dateFirst, $dateSecond)
    {
        return ($dateFirst > $dateSecond) || intval($this->dateFirst[0]) > intval($this->dateSecond[0]) ? false : true;
    }

    /**
     *
     * @return string
     */
    public function calculateDifference()
    {
        if ($this->invert) {
            $tmpDate = $this->dateFirst;
            $this->dateFirst = $this->dateSecond;
            $this->dateSecond = $tmpDate;
        }
        try {
            $this->yearsStart = (int) $this->dateFirst[0];
            $this->yearsEnd = (int) $this->dateSecond[0];
            $this->monthsStart = strlen($this->dateFirst[1]) > 2 ?
                (int) substr($this->dateFirst[1], 0, 2) : (int) $this->dateFirst[1];
            $this->monthsEnd = strlen($this->dateSecond[1]) > 2 ?
                (int) substr($this->dateSecond[1], 0, 2) : (int) $this->dateSecond[1];
            $this->daysStart = strlen($this->dateFirst[2]) > 2 ?
                (int) substr($this->dateFirst[2], 0, 2) : (int) $this->dateFirst[2];
            $this->daysEnd = strlen($this->dateSecond[2]) > 2 ?
                (int) substr($this->dateSecond[2], 0, 2) : (int) $this->dateSecond[2];

            if (($this->monthsStart || $this->monthsEnd) > 12 &&
                ($this->daysStart || $this->daysEnd) > 31) {
                return $this->strErrorMsg;
            }
            $this->yearsBetween = $this->yearsStart - $this->yearsEnd;
            $checkMonth = $this->yearsBetween > 0 && ($this->monthsStart - $this->monthsEnd) < 0 ? true : False;
            var_dump($checkMonth);
            if ($checkMonth) {
                $this->yearsBetween =  --$this->yearsStart - $this->yearsEnd;
                $this->monthsBetween = ($this->monthsStart + 12) - $this->monthsEnd;
                var_dump($this->monthsBetween % 12);
                die();
            } else $this->monthsBetween = $this->monthsStart - $this->monthsEnd;

            if ($this->daysEnd > $this->daysStart) {
                $this->daysBetween = ($this->daysStart +
                        $this->getDayInMonth(--$this->monthsStart, $this->yearsStart)) - $this->daysEnd;
            }
            if ($this->yearsStart < 0) return "Текущая дата: " . $this->yearsStart;
        } catch (\Exception $e) {
            return $e;
        }

//        $this->totalDaysBetween;
    }

    private function getDayInMonth($month, $year)
    {
        return $month == 2 ? ($year % 4 ? 28 : ($year % 100 ? 29 : ($year %400 ? 28 : 29))) : (($month - 1) % 7 % 2 ? 30 : 31);
    }

    private function isLeapYear($year)
    {
        return ((($year % 4) == 0) && ((($year % 100) != 0) || (($year % 400) == 0)));
    }

    public function getResult() {
        if (is_string($this->result)) return $this->result;
        echo "result is not correct";
    }

    public function __toString()
    {
        return $this->result;
    }
}
//class Foo{}
//$foo = new Foo();
//echo ($foo instanceof stdClass)?'Y':'N';
// outputs 'N'
$diff = new DifferenceDates("0001-12-12", "0001-11-11");;
//var_dump($diff);