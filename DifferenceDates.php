<?php
/*
 * http://php.net/manual/en/reserved.classes.php
 * */
class DifferenceDates extends \stdClass
{
    public $dateOne;
    public $dateTwo;
    public $yearsStart;
    public $yearsEnd;
    public $monthsStart;
    public $monthsEnd;
    public $daysStart;
    public $daysEnd;
    public $yearsBetween;
    public $monthsBetween;
    public $daysBetween;
    public $totalDaysBetween;
    public $invert = false;

    /**
     * DifferenceDates constructor.
     * @param $dateOne
     * @param $dateTwo
     */
    public function __construct($dateOne = false, $dateTwo = false)
    {
        $this->dateOne = $dateOne;
        $this->dateTwo = $dateTwo;
    }

    public function checkSmallYear() {
        if (!$this->dateOne || !$this->dateTwo) throw new Exception("Dates format is not correct! <br> Try YYYY-MM-DD");
        $this->invert = intval(substr($this->dateOne, 0, 4)) > intval(substr($this->dateTwo, 0, 4)) ? false : true;
    }

    public function calculateDifference() {
        $this->yearsBetween;
        $this->monthsBetween;
        $this->daysBetween;
        $this->totalDaysBetween;
    }
}
