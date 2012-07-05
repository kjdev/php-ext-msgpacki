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

echo "*** Testing session_encode() : variation ***\n";

var_dump(session_start());
$_SESSION["foo"] = 1234567890;
$encoded = session_encode();
var_dump(base64_encode($encoded));
var_dump(session_destroy());

echo "Done";
ob_end_flush();
?>
--EXPECTF--
*** Testing session_encode() : variation ***
bool(true)
string(16) "gaNmb2/OSZYC0g=="
bool(true)
Done
