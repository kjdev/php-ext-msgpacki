--TEST--
Unserialization of classes derived from ArrayIterator fails
--FILE--
<?php
if (!extension_loaded('msgpacki')) {
    dl('msgpacki.' . PHP_SHLIB_SUFFIX);
}

class Foo1 extends ArrayIterator
{
}
class Foo2 {
}
$x = array(new Foo1(),new Foo2);
$s = msgpacki_serialize($x);
$b = bin2hex($s);
$b = str_replace(bin2hex("Foo"), bin2hex("Bar"), $b);
$s = pack("H*", $b);
$y = msgpacki_unserialize($s);
if (version_compare(PHP_VERSION, '5.3.0') < 0) {
    echo "Warning: MessagePack unserialize: Class __PHP_Incomplete_Class has no unserializer in ", __FILE__, " on line ", __LINE__, "\n";
}
var_dump($y);
--EXPECTF--
Warning: MessagePack unserialize: Class __PHP_Incomplete_Class has no unserializer in %s022.php on line %d
array(2) {
  [0]=>
  object(__PHP_Incomplete_Class)#%d (1) {
    ["__PHP_Incomplete_Class_Name"]=>
    string(4) "Bar1"
  }
  [1]=>
  object(__PHP_Incomplete_Class)#%d (1) {
    ["__PHP_Incomplete_Class_Name"]=>
    string(4) "Bar2"
  }
}
