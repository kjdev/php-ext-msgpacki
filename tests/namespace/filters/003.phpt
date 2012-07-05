--TEST--
filter_register() / filter_append()
--FILE--
<?php
namespace MessagePacki;

class test_filter extends Filter
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

filter_register("test", '\MessagePacki\test_filter');
filter_append("test");

$ser = serialize("Thank you");
var_dump(bin2hex($ser));
var_dump(unserialize($ser));
?>
--EXPECT--
==CONSTRUCT==
string(20) "a95468616e6b20796f75"
string(9) "Thank you"
==DESTRUCT==
