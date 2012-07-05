--TEST--
Test session_encode() function : variation
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
$_SESSION["foo"] = &$array;
_session_encode();
var_dump(session_destroy());

echo "Done";
ob_end_flush();
?>
--EXPECTF--
*** Testing session_encode() : variation ***
bool(true)
string(24) "81a3666f6f83000101020203"
array(1) {
  ["foo"]=>
  array(3) {
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
