--TEST--
msgpacki_filter_register() / msgpacki_filter_append()
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

msgpacki_filter_register("test", 'test_filter');
msgpacki_filter_append("test");

$value = "Thank you";
$ser = msgpacki_serialize($value);
var_dump(bin2hex($ser));
var_dump(msgpacki_unserialize($ser));
?>
--EXPECT--
==CONSTRUCT==
string(20) "a95468616e6b20796f75"
string(9) "Thank you"
==DESTRUCT==
