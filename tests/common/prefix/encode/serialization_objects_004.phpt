--TEST--
Test msgpacki_encode() & msgpacki_decode() functions: objects - ensure that COW references of objects are not serialized separately (unlike other types).
--FILE--
<?php


if (!extension_loaded('msgpacki')) {
    dl('msgpacki.' . PHP_SHLIB_SUFFIX);
}

$x = new stdClass;
$ref = &$x;
var_dump(bin2hex(msgpacki_encode(array($x, $x))));

$x = 1;
$ref = &$x;
var_dump(bin2hex(msgpacki_encode(array($x, $x))));

$x = "a";
$ref = &$x;
var_dump(bin2hex(msgpacki_encode(array($x, $x))));

$x = true;
$ref = &$x;
var_dump(bin2hex(msgpacki_encode(array($x, $x))));

$x = null;
$ref = &$x;
var_dump(bin2hex(msgpacki_encode(array($x, $x))));

$x = array();
$ref = &$x;
var_dump(bin2hex(msgpacki_encode(array($x, $x))));

echo "Done";
?>
--EXPECTF--
string(6) "928080"
string(6) "920101"
string(10) "92a161a161"
string(6) "92c3c3"
string(6) "92c0c0"
string(6) "929090"
Done