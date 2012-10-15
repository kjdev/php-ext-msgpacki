--TEST--
Test session_encode() function : variation
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

echo "*** Testing session_encode() : variation ***\n";

function _session_encode() {
    $val = session_encode();
    var_dump(bin2hex($val));
    var_dump(msgpacki_unserialize($val));
}

_session_encode();
var_dump(session_start());
_session_encode();
var_dump(session_write_close());
_session_encode();
var_dump(session_start());
_session_encode();
var_dump(session_destroy());
_session_encode();

echo "Done";
ob_end_flush();
?>
--EXPECTF--
*** Testing session_encode() : variation ***

Warning: session_encode(): Cannot encode non-existent session in %s on line %d
string(0) ""
bool(false)
bool(true)
string(2) "80"
array(0) {
}
NULL
string(2) "80"
array(0) {
}
bool(true)
string(2) "80"
array(0) {
}
bool(true)

Warning: session_encode(): Cannot encode non-existent session in %s on line %d
string(0) ""
bool(false)
Done
