--TEST--
Test session_decode() function : basic functionality
--SKIPIF--
<?php
if (!extension_loaded("session")) {
    echo "skip needs session enabled";
}
?>
--INI--
session.serialize_handler=msgpacki
--FILE--
<?php
if (!extension_loaded('msgpacki')) {
    dl('msgpacki.' . PHP_SHLIB_SUFFIX);
}

ob_start();

echo "*** Testing session_decode() : variation ***\n";

var_dump(session_start());
var_dump($_SESSION);
$_SESSION["foo"] = 1234567890;
$_SESSION["bar"] = "Hello World!";
$_SESSION["guff"] = 123.456;
var_dump($_SESSION);
var_dump(session_decode(pack('H*', '83a3666f6f83000101020203a46775666682c0010002a4626c616882c0010002')));
var_dump($_SESSION);
var_dump(session_destroy());

echo "Done";
ob_end_flush();
?>
--EXPECTF--
*** Testing session_decode() : variation ***
bool(true)
array(0) {
}
array(3) {
  ["foo"]=>
  int(1234567890)
  ["bar"]=>
  string(12) "Hello World!"
  ["guff"]=>
  float(123.456)
}
bool(true)
array(4) {
  ["foo"]=>
  &array(3) {
    [0]=>
    int(1)
    [1]=>
    int(2)
    [2]=>
    int(3)
  }
  ["bar"]=>
  string(12) "Hello World!"
  ["guff"]=>
  &array(3) {
    [0]=>
    int(1)
    [1]=>
    int(2)
    [2]=>
    int(3)
  }
  ["blah"]=>
  &array(3) {
    [0]=>
    int(1)
    [1]=>
    int(2)
    [2]=>
    int(3)
  }
}
bool(true)
Done

