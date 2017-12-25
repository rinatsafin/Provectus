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
        $this->dateOne = explode("-",trim($dateOne));
        $this->dateTwo = explode("-", trim($dateTwo));
        $this->invert = $this->checkSmallYear();
    }

    public function checkSmallYear() {
        if (!$this->dateOne || !$this->dateTwo) throw new Exception("Dates format is not correct! <br> Try YYYY-MM-DD");
        return intval($this->dateOne[0]) > intval($this->dateTwo[0]) ? false : true;
    }

    public function calculateDifference() {
        $this->daysStart = $this->invert ? $this->dateTwo[0] : $this->dateOne[0];
        $this->yearsBetween;
        $this->monthsBetween;
        $this->daysBetween;
        $this->totalDaysBetween;
    }
}
