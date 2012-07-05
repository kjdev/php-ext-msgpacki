--TEST--
Test serialize() & unserialize() functions: objects - ensure that COW references of objects are not serialized separately (unlike other types).
--FILE--
<?php
namespace MessagePacki;

if (!extension_loaded('msgpacki')) {
    dl('msgpacki.' . PHP_SHLIB_SUFFIX);
}

$x = new \stdClass;
$ref = &$x;
var_dump(bin2hex(serialize(array($x, $x))));

$x = 1;
$ref = &$x;
var_dump(bin2hex(serialize(array($x, $x))));

$x = "a";
$ref = &$x;
var_dump(bin2hex(serialize(array($x, $x))));

$x = true;
$ref = &$x;
var_dump(bin2hex(serialize(array($x, $x))));

$x = null;
$ref = &$x;
var_dump(bin2hex(serialize(array($x, $x))));

$x = array();
$ref = &$x;
var_dump(bin2hex(serialize(array($x, $x))));

echo "Done";
?>
--EXPECTF--
string(38) "820081c0a8737464436c6173730182c0020002"
string(10) "8200010101"
string(14) "8200a16101a161"
string(10) "8200c301c3"
string(10) "8200c001c0"
string(10) "8200900190"
Done