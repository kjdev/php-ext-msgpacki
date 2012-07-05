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
$_SESSION["guff"] = &$array;
$_SESSION["blah"] = &$array;
_session_encode();
var_dump(session_destroy());

echo "Done";
ob_end_flush();
?>
--EXPECTF--
*** Testing session_encode() : variation ***
bool(true)
string(64) "83a3666f6f83000101020203a46775666682c0010002a4626c616882c0010002"
array(3) {
  ["foo"]=>
  &array(3) {
    [0]=>
    int(1)
    [1]=>
    int(2)
    [2]=>
    int(3)
  }
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
