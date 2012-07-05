--TEST--
serialize followed by unserialize with numeric object prop. gives integer prop
--FILE--
<?php
if (!extension_loaded('msgpacki')) {
    dl('msgpacki.' . PHP_SHLIB_SUFFIX);
}

$a = new stdClass();
$a->{0} = 'X';
$a->{1} = 'Y';
var_dump(bin2hex(msgpacki_serialize($a)));
var_dump($a->{0});
$b = msgpacki_unserialize(msgpacki_serialize($a));
var_dump(bin2hex(msgpacki_serialize($b)));
var_dump($b->{0});
--EXPECT--
string(38) "83c0a8737464436c617373a130a158a131a159"
string(1) "X"
string(38) "83c0a8737464436c617373a130a158a131a159"
string(1) "X"
