--TEST--
MessagePacki::append_filter()
--FILE--
<?php

class test_filter extends MessagePacki_Filter
{
    public function __construct()
    {
        echo "==CONSTRUCT==\n";
    }
    public function __destruct()
    {
        echo "==DESTRUCT==\n";
    }
}

msgpacki_filter_register("test", "test_filter");

$m = new MessagePacki();

$m->append_filter("test");

$value = "Thank you";
$ser = $m->pack($value);
var_dump(bin2hex($ser));
var_dump($m->unpack($ser));

$m->append_filter('test_filter');

$value = "Thank you";
$ser = $m->pack($value);
var_dump(bin2hex($ser));
var_dump($m->unpack($ser));
?>
--EXPECT--
==CONSTRUCT==
string(20) "a95468616e6b20796f75"
string(9) "Thank you"
==CONSTRUCT==
string(20) "a95468616e6b20796f75"
string(9) "Thank you"
==DESTRUCT==
==DESTRUCT==
