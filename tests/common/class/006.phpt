--TEST--
MessagePacki::pack() objects of incomplete class
--FILE--
<?php

if (!extension_loaded('msgpacki')) {
    dl('msgpacki.' . PHP_SHLIB_SUFFIX);
}

$m = new MessagePacki();

$str = pack('H*', '81c0a954657374436c617373');
$obj = $m->unpack($str);
var_dump($obj);
echo bin2hex($m->pack($obj))."\n";
var_dump($obj);
echo bin2hex($m->pack($obj))."\n";
var_dump($obj);
?>
--EXPECTF--
object(__PHP_Incomplete_Class)#%d (1) {
  ["__PHP_Incomplete_Class_Name"]=>
  string(9) "TestClass"
}
81c0a954657374436c617373
object(__PHP_Incomplete_Class)#%d (1) {
  ["__PHP_Incomplete_Class_Name"]=>
  string(9) "TestClass"
}
81c0a954657374436c617373
object(__PHP_Incomplete_Class)#%d (1) {
  ["__PHP_Incomplete_Class_Name"]=>
  string(9) "TestClass"
}
