--TEST--
incorrect processing of numerical string keys of array in arbitrary serialized data
--FILE--
<?php
namespace MessagePacki;

if (!extension_loaded('msgpacki')) {
    dl('msgpacki.' . PHP_SHLIB_SUFFIX);
}

error_reporting(E_ALL);


var_dump($a = unserialize(pack('H*', '820a01a2303102')));
var_dump($a['10']);
var_dump($a[b'01']);

?>
--EXPECT--
array(2) {
  [10]=>
  int(1)
  ["01"]=>
  int(2)
}
int(1)
int(2)
