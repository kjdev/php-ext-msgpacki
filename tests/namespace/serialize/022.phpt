--TEST--
Unserialization of classes derived from ArrayIterator fails
--FILE--
<?php
namespace MessagePacki;

if (!extension_loaded('msgpacki')) {
    dl('msgpacki.' . PHP_SHLIB_SUFFIX);
}

class Foo1 extends \ArrayIterator
{
}
class Foo2 {
}
$x = array(new Foo1(),new Foo2);
$s = serialize($x);
$b = bin2hex($s);
$b = str_replace(bin2hex("Foo"), bin2hex("Bar"), $b);
$s = pack("H*", $b);
$y = unserialize($s);
var_dump($y);
--EXPECTF--
Warning: MessagePack unserialize: Class __PHP_Incomplete_Class has no unserializer in %s022.php on line %d
array(2) {
  [0]=>
  object(__PHP_Incomplete_Class)#%d (1) {
    ["__PHP_Incomplete_Class_Name"]=>
    string(17) "MessagePacki\Bar1"
  }
  [1]=>
  object(__PHP_Incomplete_Class)#%d (1) {
    ["__PHP_Incomplete_Class_Name"]=>
    string(17) "MessagePacki\Bar2"
  }
}
