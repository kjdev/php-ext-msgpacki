--TEST--
serialize()/unserialize() floats in array.
--FILE--
<?php
namespace MessagePacki;

if (!extension_loaded('msgpacki')) {
    dl('msgpacki.' . PHP_SHLIB_SUFFIX);
}

error_reporting (E_ALL);
$a      = array(4);
$str    = serialize($a);
print('Serialized array: '.bin2hex($str)."\n");
$b      = unserialize($str);
print('Unserialized array: ');
var_dump($b);
print("\n");
$str    = serialize(array(4.5));
print('Serialized array: '.bin2hex($str)."\n");
$b      = unserialize($str);
print('Unserialized array: ')   ;
var_dump($b);
?>
--EXPECT--
Serialized array: 810004
Unserialized array: array(1) {
  [0]=>
  int(4)
}

Serialized array: 8100cb4012000000000000
Unserialized array: array(1) {
  [0]=>
  float(4.5)
}
