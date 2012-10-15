--TEST--
Test session_encode() function : variation
--SKIPIF--
<?php
if (!extension_loaded("session")) {
    echo "skip needs session enabled";
}
if (version_compare(PHP_VERSION, '5.3.0') < 0) {
    die("skip this test is for PHP 5.3 or newer");
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

function _session_encode() {
    $val = session_encode();
    var_dump(bin2hex($val));
    var_dump(msgpacki_unserialize($val));
}

echo "*** Testing session_encode() : variation ***\n";

var_dump(session_start());

$array = array(1,2,3);
$array["foo"] = &$array;
$array["blah"] = &$array;
$_SESSION["data"] = &$array;
_session_encode();
var_dump(session_destroy());

if (version_compare(PHP_VERSION, '5.3.0') < 0) {
    $array["foo"] = null;
    $array["blah"] = null;
}

echo "Done";
ob_end_flush();
?>
--EXPECTF--
*** Testing session_encode() : variation ***
bool(true)
string(64) "81a46461746185000101020203a3666f6f82c0010002a4626c616882c0010002"
array(1) {
  ["data"]=>
  &array(5) {
    [0]=>
    int(1)
    [1]=>
    int(2)
    [2]=>
    int(3)
    ["foo"]=>
    *RECURSION*
    ["blah"]=>
    *RECURSION*
  }
}
bool(true)
Done
