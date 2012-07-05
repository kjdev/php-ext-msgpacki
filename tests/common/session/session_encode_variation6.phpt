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
$_SESSION[] = 1234567890;
_session_encode();
var_dump(session_destroy());
var_dump(session_start());
$_SESSION[1234567890] = "Hello World!";
_session_encode();
var_dump(session_destroy());
var_dump(session_start());
$_SESSION[-1234567890] = 1234567890;
_session_encode();
var_dump(session_destroy());

echo "Done";
ob_end_flush();
?>
--EXPECTF--
*** Testing session_encode() : variation ***
bool(true)

Notice: Skipping numeric key 0 in %s on line %d
string(2) "81"

Notice: MessagePack unserialize: Error at offset %d of %d bytes in %s on line %d
bool(false)
bool(true)
bool(true)

Notice: Skipping numeric key 1234567890 in %s on line %d
string(2) "81"

Notice: MessagePack unserialize: Error at offset %d of %d bytes in %s on line %d
bool(false)
bool(true)
bool(true)

Notice: Skipping numeric key -1234567890 in %s on line %d
string(2) "81"

Notice: MessagePack unserialize: Error at offset %d of %d bytes in %s on line %d
bool(false)
bool(true)
Done
