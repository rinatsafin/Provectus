<?php
$date1 = "2008-11-01 22:45:00";

$date2 = "2009-12-04 13:44:01";

$diff = abs(strtotime($date2) - strtotime($date1));

$years   = floor($diff / (365*60*60*24));
$months  = floor(($diff - $years * 365*60*60*24) / (30*60*60*24));
$days    = floor(($diff - $years * 365*60*60*24 - $months*30*60*60*24)/ (60*60*24));

$hours   = floor(($diff - $years * 365*60*60*24 - $months*30*60*60*24 - $days*60*60*24)/ (60*60));

$minuts  = floor(($diff - $years * 365*60*60*24 - $months*30*60*60*24 - $days*60*60*24 - $hours*60*60)/ 60);

$seconds = floor(($diff - $years * 365*60*60*24 - $months*30*60*60*24 - $days*60*60*24 - $hours*60*60 - $minuts*60));

printf("%d years, %d months, %d days, %d hours, %d minuts\n, %d seconds\n", $years, $months, $days, $hours, $minuts, $seconds);

try
{
    throw new \Exception('Testing', -12.12);
}
catch (\Exception $exception)
{
    $code = $exception->getCode();
    if ($code < 0)
    {
        exit(abs($code));
    }
}


die();

$st = new stdClass();
$st::_toString("tt");

var_dump(method_exists($st, "__toString"));

die();
// Singleton класс => "Одиночка"
class diffDatesStd extends stdClass
{
    private $instance;
    public $var;

    private function __construct()
    {
    }

    /**
     * @return object
     */
    public static function getInstance() {
        if (self::$instance == null) {
            self::$instance = new self();
        }
        return self::$instance;
    }
}

class CTest {
    public $pubVar;
}

$ct = new CTest();
var_dump(method_exists($ct, "__toString"));
echo "<br><br>!!!!!!!<br>__toString<br>";
var_dump($ct instanceof stdClass); // false
echo "<br>";
var_dump(is_subclass_of($ct, "stdClass")); // false
echo "<br>";
echo get_class($ct) . "\n";// CTest
echo "<br>";
var_dump(get_parent_class($ct));// false
echo "<br>";
echo "<br>";


class stdExtend extends stdClass {
    public $propSTD;
}
$std = new stdExtend();
var_dump($std instanceof stdClass); // false
echo "<br>";
var_dump(is_subclass_of($std, "stdClass")); // false
echo "<br>";
echo get_class($std) . "\n";// CTest
echo "<br>";
var_dump(get_parent_class($std));// false

echo "STD_CLASS<br>";
$t = new stdClass();
var_dump(method_exists($t, "__toString"));
echo "<br><br>!!!!!!!<br>__toString<br>";
var_dump($t instanceof stdClass); // false
//$orm = diffDatesStd::getInstance();
//$orm->var = "Some text by PHP!";
//echo $orm->var;
