<?php
/*
 * http://php.net/manual/en/reserved.classes.php
 * http://php.net/manual/ru/datetime.diff.php
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
    }

    /**
     * @return bool
     * @throws Exception
     */
    public function checkSmallYear($dateOne, $dateTwo)
    {
        if (!$this->dateOne || !$this->dateTwo) throw new Exception("Dates format is not correct! <br> Try 'YYYY-MM-DD'");
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
        $this->yearsStart = (int) $this->dateOne[0];
        $this->yearsEnd = (int) $this->dateOne[0];
        $this->monthsStart = strlen($this->dateOne[1]) > 2 ? (int) substr($this->dateOne[1], 0, 2) : (int) $this->dateOne[1];
        $this->monthsEnd = strlen($this->dateTwo[1]) > 2 ? (int) substr($this->dateTwo[1], 0, 2) : (int) $this->dateTwo[1];
        $this->daysStart = strlen($this->dateOne[2]) > 2 ? (int) substr($this->dateOne[2], 0, 2) : (int) $this->dateOne[2];
        $this->daysEnd = strlen($this->dateTwo[2]) > 2 ? (int) substr($this->dateTwo[2], 0, 2) : (int) $this->dateTwo[2];

        if ($this->monthsStart > 12 && $this->monthsEnd > 12 && $this->daysStart > 31 && $this->daysEnd > 31) {
            return "ERROR: NOT VALID FORMAT! TRY 'YYYY-MM-DD'";
        }

        if ($this->daysEnd > $this->daysStart) {
            if ($this->monthsEnd > $this->monthsStart) {
                $this->yearsBetween = --$this->yearsStart - $this->yearsEnd;
                $this->monthsBetween = ($this->monthsStart + 12) - $this->monthsEnd;
            }
        }

        $this->yearsBetween;
        $this->monthsBetween;
        $this->daysBetween;
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
}
