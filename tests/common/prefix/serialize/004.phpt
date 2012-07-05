--TEST--
msgpacki_serialize()/msgpacki_unserialize() floats in array.
--FILE--
<?php
if (!extension_loaded('msgpacki')) {
    dl('msgpacki.' . PHP_SHLIB_SUFFIX);
}

error_reporting (E_ALL);
$a      = array(4);
$str    = msgpacki_serialize($a);
print('Serialized array: '.bin2hex($str)."\n");
$b      = msgpacki_unserialize($str);
print('Unserialized array: ');
var_dump($b);
print("\n");
$str    = msgpacki_serialize(array(4.5));
print('Serialized array: '.bin2hex($str)."\n");
$b      = msgpacki_unserialize($str);
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
