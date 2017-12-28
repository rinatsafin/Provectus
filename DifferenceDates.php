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
    public $dateOne;
    public $dateTwo;
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
    /**
     * DifferenceDates constructor.
     * @param $dateOne
     * @param $dateTwo
     */
    public function __construct($dateOne = false, $dateTwo = false)
    {
        $this->dateOne = explode("-",trim($dateOne));
        $this->dateTwo = explode("-", trim($dateTwo));
        $this->invert = $this->checkSmallYear($dateOne, $dateTwo);
        $this->result = $this->calculateDifference();
    }

    /**
     * @param $dateOne
     * @param $dateTwo
     * @return bool
     * @throws Exception
     */
    public function checkSmallYear($dateOne, $dateTwo)
    {
        if (!$dateOne || !$dateTwo) throw new \Exception("Dates format is not correct! <br> Try 'YYYY-MM-DD'");
        return ($dateOne > $dateTwo) || intval($this->dateOne[0]) > intval($this->dateTwo[0]) ? false : true;
    }

    /**
     *
     * @return string
     */
    public function calculateDifference()
    {
        if ($this->invert) {
            $tmpDate = $this->dateOne;
            $this->dateOne = $this->dateTwo;
            $this->dateTwo = $tmpDate;
        }
        try {
            $this->yearsStart = (int) $this->dateOne[0];
            $this->yearsEnd = (int) $this->dateOne[0];
            $this->monthsStart = strlen($this->dateOne[1]) > 2 ?
                (int) substr($this->dateOne[1], 0, 2) : (int) $this->dateOne[1];
            $this->monthsEnd = strlen($this->dateTwo[1]) > 2 ?
                (int) substr($this->dateTwo[1], 0, 2) : (int) $this->dateTwo[1];
            $this->daysStart = strlen($this->dateOne[2]) > 2 ?
                (int) substr($this->dateOne[2], 0, 2) : (int) $this->dateOne[2];
            $this->daysEnd = strlen($this->dateTwo[2]) > 2 ?
                (int) substr($this->dateTwo[2], 0, 2) : (int) $this->dateTwo[2];
        } catch (Exception $exception) {
            echo $exception;
        }

        if ($this->monthsStart > 12 && $this->monthsEnd > 12 && $this->daysStart > 31 && $this->daysEnd > 31) {
            return "ERROR: NOT VALID FORMAT! <br> Try: 'YYYY-MM-DD'";
        }

        if ($this->daysEnd > $this->daysStart) {
            $this->daysBetween = ($this->daysStart +
                    $this->getDayInMonth(--$this->monthsStart, $this->yearsStart)) - $this->daysEnd;
        }
        if ($this->monthsEnd > $this->monthsStart) {
            $this->yearsBetween = --$this->yearsStart - $this->yearsEnd;
            $this->monthsBetween = ($this->monthsStart + 12) - $this->monthsEnd;
        }

        $this->totalDaysBetween;
    }

    private function getDayInMonth($month, $year)
    {
        return $month == 2 ? ($year % 4 ? 28 : ($year % 100 ? 29 : ($year %400 ? 28 : 29))) : (($month - 1) % 7 % 2 ? 30 : 31);
    }

    private function isLeapYear($year)
    {
        return ((($year % 4) == 0) && ((($year % 100) != 0) || (($year % 400) == 0)));
    }

    public function showResult() {
        echo $this->result;
    }
}
//class Foo{}
//$foo = new Foo();
//echo ($foo instanceof stdClass)?'Y':'N';
// outputs 'N'
$diff = new DifferenceDates("", "");